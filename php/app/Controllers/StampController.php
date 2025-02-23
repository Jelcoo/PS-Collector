<?php

namespace App\Controllers;

use App\Config\Config;
use App\Repositories\StampRepository;
use App\Services\FileService;
use Rakit\Validation\Validator;

class StampController extends Controller
{
    private StampRepository $stampRepository;
    private FileService $fileService;

    public function __construct()
    {
        parent::__construct();
        $this->stampRepository = new StampRepository();
        $this->fileService = new FileService();
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

            $this->fileService->saveBase64($data['image'], '1234', Config::getKey('STORAGE_PATH'));
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return [
            'stamp' => $stamp->toArray(),
        ];
    }
}
