<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * UserController Constructor.
     *
     * @param UserService $service.
     */
    public function __construct(
        protected UserService $service
    ) {}

    /**
     * Display a paginated listing of Users.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(UserResource::collection($data), 'User list fetched successfully');
    }

    /**
     * Store a newly created User in storage.
     *
     * @param StoreUserRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'User created successfully');
    }

    /**
     * Display the specified User.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new UserResource($item), 'User fetched successfully');
    }

    /**
     * Update any user (admin only).
     * 
     * @param UpdateUserRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, int|string $id): JsonResponse
    {
        $user = Auth::user();

        // Check if current user is a super administrator
        if (!$user->hasRole('super_administrator')) {
            return $this->error('Unauthorized. Only administrators can update any user.', 403);
        }

        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'User updated successfully by administrator');
    }

    /**
     * Update current user's own profile.
     * 
     * @param UpdateUserRequest $request Validated input data.
     * @return JsonResponse
     */
    public function updateMe(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        // Users can only update their own profile
        $item = $this->service->update($user->id, $request->validated());

        return $this->success($item, 'User profile updated successfully');
    }

    /**
     * Remove the specified User from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'User deleted successfully');
    }

    /**
     * Display a paginated listing of Users with relationships.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexWithRelations(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page', 'relations']);
        $perPage = (int) $request->input('per_page', 15);
        $relations = $request->input('relations', ['profile', 'cart']);

        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $data = $this->service->getAllWithRelations($relations, $filters, $perPage);

        return $this->paginate($data, 'User list with relations fetched successfully');
    }

    /**
     * Display the specified User with relationships.
     *
     * @param int|string $id The primary key value.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function showWithRelations(int|string $id, Request $request): JsonResponse
    {
        $relations = $request->input('relations', ['profile', 'cart']);

        if (is_string($relations)) {
            $relations = explode(',', $relations);
        }

        $item = $this->service->findByIdWithRelations($id, $relations);

        return $this->success($item, 'User with relations fetched successfully');
    }

    /**
     * Display a paginated listing of Users by multiple IDs.
     *
     * @param Request $request The HTTP request containing user IDs.
     * @return JsonResponse
     */
    public function indexByIds(Request $request): JsonResponse
    {
        $userIds = $request->input('user_ids', []);
        $perPage = (int) $request->input('per_page', 15);

        if (!is_array($userIds)) {
            return $this->error('user_ids must be an array', 400);
        }

        $data = $this->service->getByIds($userIds, $perPage);

        return $this->paginate($data, 'Users by IDs fetched successfully');
    }

    /**
     * Display a paginated listing of recent Users.
     *
     * @param Request $request The HTTP request containing days parameter.
     * @return JsonResponse
     */
    public function indexRecent(Request $request): JsonResponse
    {
        $days = (int) $request->input('days', 30);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getRecentUsers($days, $perPage);

        return $this->paginate($data, 'Recent users fetched successfully');
    }

}
