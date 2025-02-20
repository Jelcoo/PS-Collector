<?php

namespace App\Controllers;

use Rakit\Validation\Validator;
use App\Helpers\PaginationHelper;
use App\Enum\CollectionAccessEnum;
use App\Enum\CollectionAccessLevelEnum;
use App\Repositories\CollectionRepository;
use App\Repositories\UserRepository;

class CollectionController extends Controller
{
    private CollectionRepository $collectionRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        parent::__construct();

        $this->collectionRepository = new CollectionRepository();
        $this->userRepository = new UserRepository();
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

    public function update(int $id): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

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

    public function addMember(int $id): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validation = $validator->validate($data, [
            'username' => 'required',
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $username = $data['username'];
            $user = $this->userRepository->getUserByUsernameOrEmail($username);

            if (!$user) {
                return [
                    'status' => 404,
                    'error' => 'User not found',
                ];
            }

            $accessLevel = $this->collectionRepository->getCollectionAccess($id, $user->id);

            if ($accessLevel !== CollectionAccessLevelEnum::NONE) {
                return [
                    'status' => 400,
                    'error' => 'User is already a member of this collection',
                ];
            }

            $this->collectionRepository->addMemberToCollection($id, $user->id);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return [
            'message' => 'Member added successfully',
        ];
    }
}
