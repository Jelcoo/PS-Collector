<?php

namespace App\Controllers;

use App\Hooks\StampHook;
use App\Services\AssetService;
use Rakit\Validation\Validator;
use App\Repositories\StampRepository;

class StampController extends Controller
{
    private StampRepository $stampRepository;
    private AssetService $assetService;
    private StampHook $stampHook;

    public function __construct()
    {
        parent::__construct();
        $this->stampRepository = new StampRepository();
        $this->assetService = new AssetService();
        $this->stampHook = new StampHook();
    }

    public function get(int $id): array
    {
        $with = $this->getWithRelations();

        try {
            $stamp = $this->stampRepository->getStampById($id, $with);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return $stamp->toArray();
    }

    public function create(int $collectionId): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validation = $validator->validate($data, [
            'name' => 'required|max:255',
            'used' => 'required|boolean',
            'damaged' => 'required|boolean',
            'image' => 'required',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $stamp = $this->stampRepository->createStamp([
                'collection_id' => $collectionId,
                'name' => $data['name'],
                'used' => $data['used'],
                'damaged' => $data['damaged'],
            ]);

            $asset = $this->assetService->saveBase64Asset($data['image'], 'header', $stamp);
            $stamp->headerUrl = $asset->getUrl();

            $this->stampHook->afterSave($stamp);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return [
            'stamp' => $stamp->toArray(),
            'asset' => $asset->toArray(),
        ];
    }

    public function update(int $id): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validation = $validator->validate($data, [
            'name' => 'required|max:255',
            'used' => 'required|boolean',
            'damaged' => 'required|boolean',
            'image' => 'required|nullable',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $updatedStamp = $this->stampRepository->updateStamp($id, [
                'name' => $data['name'],
                'used' => $data['used'],
                'damaged' => $data['damaged'],
            ]);

            $existingAssets = $this->assetService->resolveAssets($updatedStamp);
            if (count($existingAssets) > 0) {
                $asset = $existingAssets[0];
            }

            if (!is_null($data['image'])) {
                if (isset($asset)) {
                    $this->assetService->deleteAsset($asset);
                }

                $asset = $this->assetService->saveBase64Asset($data['image'], 'header', $updatedStamp);
                $updatedStamp->headerUrl = $asset->getUrl();
            }

            $this->stampHook->afterSave($updatedStamp);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return [
            'stamp' => $updatedStamp->toArray(),
            'asset' => isset($asset) ? $asset->toArray() : null,
        ];
    }

    public function delete(int $stampId): array
    {
        try {
            $stamp = $this->stampRepository->getStampById($stampId);
            $assets = $this->assetService->resolveAssets($stamp);

            foreach ($assets as $asset) {
                $this->assetService->deleteAsset($asset);
            }

            $this->stampHook->afterDelete($stampId);
            $this->stampRepository->deleteStamp($stampId);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return [
            'message' => 'Collection deleted successfully',
        ];
    }
}
