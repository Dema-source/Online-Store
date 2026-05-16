<?php

namespace App\Http\Controllers\Api\RolesPermissions ;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Role\StoreRoleRequest;
use App\Http\Requests\Api\Role\UpdateRoleRequest;
use App\Services\RolesPermissions\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * RoleController Constructor.
     *
     * @param RoleService $service.
     */
    public function __construct(
        protected RoleService $service
    ) {}

    /**
     * Display a paginated listing of Roles.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate($data, 'Role list fetched successfully');
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param StoreRoleRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        $item = $this->service->create($request->validated());

        return $this->success($item, 'Role created successfully');
    }

    /**
     * Display the specified Role.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success($item, 'Role fetched successfully');
    }

    /**
     * Update the specified Role in storage.
     * 
     * @param UpdateRoleRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateRoleRequest $request, int|string $id): JsonResponse
    {
        $item = $this->service->update($id, $request->validated());

        return $this->success($item, 'Role updated successfully');
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->success(null, 'Role deleted successfully');
    }
}