<?php

namespace App\Controllers;

use Rakit\Validation\Validator;
use App\Helpers\PaginationHelper;
use App\Enum\CollectionAccessEnum;
use App\Repositories\CollectionRepository;

class CollectionController extends Controller
{
    private CollectionRepository $collectionRepository;

    public function __construct()
    {
        parent::__construct();

        $this->collectionRepository = new CollectionRepository();
    }

    public function index(): array
    {
        $page = $_GET['page'] ?? 1;
        $perPage = $_GET['size'] ?? 25;
        $with = $this->getWithRelations();

        try {
            $collectionsForUser = $this->collectionRepository->getAllForUser($this->getSession()?->id, $with);
            $pagedCollections = PaginationHelper::paginate($collectionsForUser, count($collectionsForUser), $perPage, $page);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return $pagedCollections;
    }

    public function get(int $id): array
    {
        $with = $this->getWithRelations();

        try {
            $collection = $this->collectionRepository->getCollectionById($id, $with);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return $collection->toArray();
    }

    public function create(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validation = $validator->validate($data, [
            'name' => 'required|max:255',
            'access' => 'required|in:' . implode(',', array_column(CollectionAccessEnum::cases(), 'value')),
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $createdCollection = $this->collectionRepository->createCollection(
                [
                    'name' => $data['name'],
                    'access' => $data['access'],
                ],
                $this->getSession()->id
            );
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return [
            'collection' => $createdCollection,
        ];
    }

    public function update(int $id, array $data): array
    {
        $validator = new Validator();
        $validation = $validator->validate($data, [
            'name' => 'required|max:255',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $updatedCollection = $this->collectionRepository->updateCollection($id, [
                'name' => $data['name'],
            ]);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return [
            'collection' => $updatedCollection,
        ];
    }

    public function delete(int $id): array
    {
        try {
            $this->collectionRepository->deleteCollection($id);
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
