<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Profile\StoreProfileRequest;
use App\Http\Requests\Api\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
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

        return $this->paginate(ProfileResource::collection($data), 'Profile list fetched successfully');
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

        return $this->success(new ProfileResource($item), 'Profile created successfully');
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

        return $this->success(new ProfileResource($item), 'Profile fetched successfully');
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

        return $this->success(new ProfileResource($item), 'Profile updated successfully');
    }

    /**
     * Get current user's profile.
     *
     * @return JsonResponse
     */
    public function getMyProfile(): JsonResponse
    {
        $profile = $this->service->getMyProfile();

        if (!$profile) {
            return $this->error('Profile not found', 404);
        }

        return $this->success(new ProfileResource($profile), 'My profile fetched successfully');
    }

    /**
     * Update current user's profile.
     *
     * @param UpdateProfileRequest $request Validated input data.
     * @return JsonResponse
     */
    public function updateMyProfile(UpdateProfileRequest $request): JsonResponse
    {
        $item = $this->service->updateMyProfile($request->validated());

        return $this->success(new ProfileResource($item), 'My Profile updated successfully');
    }

        /**
     * Remove the specified Profile from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $profile = $this->service->findById($id);
        $user = auth()->user();

        // Check if user is admin or profile owner
        if (!$this->authorize($user, $profile)) {
            return $this->error('Unauthorized to delete this profile', 403);
        }

        $this->service->delete($id);

        return $this->success(null, 'Profile deleted successfully with related cart');
    }
    
    /**
     * Display a paginated listing of Profiles with relationships.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['user','cart']);
        $filters = $request->except(['page', 'per_page', 'relations']);
        $perPage = (int) $request->input('per_page', 15);

        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate(ProfileResource::collection($data), 'Profile list with relations fetched successfully');
    }

    /**
     * Display the specified Profile with relationships.
     *
     * @param int|string $id The primary key value.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['user','cart']);

        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success(new ProfileResource($item), 'Profile fetched successfully with relations');
    }

    /**
     * Get profiles statistics.
     *
     * @return JsonResponse
     */
    public function statistics(): JsonResponse
    {
        $data = $this->service->getStatistics();

        return $this->success($data, 'Profile statistics fetched successfully');
    }
    
    /**
     * Check if current user can delete the profile.
     *
     * @param mixed $user The authenticated user.
     * @param mixed $profile The target profile.
     * @return bool
     */
    private function authorize($user, $profile): bool
    {
        // If profile doesn't exist, deny
        if (!$profile) {
            return false;
        }

        // Super admins can delete any profile
        if ($user && $user->hasRole('super_administrator')) {
            return true;
        }

        // Users can only delete their own profile
        if ($user && $profile->user_id === $user->id) {
            return true;
        }

        return false;
    }
}
