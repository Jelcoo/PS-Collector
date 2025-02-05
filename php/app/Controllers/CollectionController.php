<?php

namespace App\Controllers;

use App\Enum\CollectionAccessEnum;
use App\Helpers\PaginationHelper;
use App\Repositories\CollectionRepository;
use Rakit\Validation\Validator;

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

        try {
            $collections = $this->collectionRepository->getAll();
            $pagedCollections = PaginationHelper::paginate($collections, count($collections), $perPage, $page);
        } catch (\Exception) {
            return [
                'status' => 500,
                'error' => 'Something went wrong',
            ];
        }

        return $pagedCollections;
    }

    public function create(): array
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $validator = new Validator();
        $validation = $validator->validate($data, [
            'name' => 'required|max:255',
            'access' => 'required|in:'.implode(',', array_column(CollectionAccessEnum::cases(), 'value')),
        ]);

        if ($validation->fails()) {
            return [
                'status' => 422,
                'errors' => $validation->errors()->toArray(),
            ];
        }

        try {
            $createdCollection = $this->collectionRepository->createCollection([
                'name' => $data['name'],
                'access' => $data['access'],
            ]);
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
}
