<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\StoreProfileRequest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * ProfileController Constructor.
     *
     * @param ProfileService $service.
     */
    public function __construct(
        protected ProfileService $service
    ) {}

    /**
     * Display a paginated listing of Profiles.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Profile list fetched successfully');
    }

    /**
     * Store a newly created Profile in storage.
     *
     * @param StoreProfileRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreProfileRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'Profile created successfully');
    }

    /**
     * Display the specified Profile.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Profile fetched successfully');
    }

    /**
     * Update the specified Profile in storage.
     * 
     * @param UpdateProfileRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateProfileRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Profile updated successfully');
    }

    /**
     * Remove the specified Profile from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Profile deleted successfully');
    }
}