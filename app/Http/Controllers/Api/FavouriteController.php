<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Favourite\StoreFavouriteRequest;
use App\Http\Resources\FavouriteResource;
use App\Services\FavouriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    /**
     * FavouriteController Constructor.
     *
     * @param FavouriteService $service.
     */
    public function __construct(
        protected FavouriteService $service
    ) {}

    /**
     * Display a paginated listing of Favourites.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);
        return $this->paginate(FavouriteResource::collection($data), 'Favourite list fetched successfully');
    }

    /**
     * Store a newly created Favourite in storage.
     *
     * @param StoreFavouriteRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreFavouriteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        if ($user) {
            // Authenticated user - set user_id
            $data['user_id'] = $user->id;
        } else {
            // Guest user - get guest_token from header (handled by ensureGuestToken middleware)
            $guestToken = $request->header('X-Guest-Token');
            
            if (!$guestToken) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    $this->error('Guest token is required', 422)
                );
            }
            
            $data['guest_token'] = $guestToken;
        }

        $item = $this->service->create($data);

        return $this->success(new FavouriteResource($item), 'Favourite created successfully');
    }

    /**
     * Display the specified Favourite.
     *
     * @param int|string $id The primary key value.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function show(int|string $id, Request $request): JsonResponse
    {
        $this->checkOwnership($id, $request);
        $item = $this->service->findById($id);

        return $this->success(new FavouriteResource($item), 'Favourite fetched successfully');
    }

    /**
     * Remove the specified Favourite from storage.
     *
     * @param int|string $id The primary key value.
     * @param Request $request The HTTP request.
     * @return JsonResponse
     */
    public function destroy(int|string $id, Request $request): JsonResponse
    {
        $this->checkOwnership($id, $request);
        $this->service->delete($id);

        return $this->success(null, 'Favourite deleted successfully');
    }

    /**
     * Display a paginated listing of Favourites for the authenticated user or guest.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexMyFavourites(Request $request): JsonResponse
    {
        $user = Auth::user();
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        if ($user) {
            // Authenticated user - filter by user_id
            $filters['user_id'] = $user->id;
        } else {
            // Guest user - filter by guest_token from header
            $guestToken = $request->header('X-Guest-Token');
            
            if (!$guestToken) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    $this->error('Guest token is required', 422)
                );
            }
            
            $filters['guest_token'] = $guestToken;
        }

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(FavouriteResource::collection($data), 'My favourites fetched successfully');
    }

    /**
     * Check if the authenticated user owns the favourite.
     *
     * @param int|string $id The favourite ID.
     * @param Request $request The HTTP request.
     * @return \App\Models\Favourite The favourite if owned by the user.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the user does not have permission.
     */
    private function checkOwnership(int|string $id, Request $request): \App\Models\Favourite
    {
        $favourite = $this->service->findById($id);

        // Load user relationship to avoid N+1 queries
        $favourite->load('user');

        $user = Auth::user();

        // Allow super administrators to access any favourite
        if ($user && $user->hasRole('super_administrator')) {
            return $favourite;
        }

        // Check if user is authenticated and owns the favourite
        if ($user) {
            if ($favourite->user_id !== $user->id) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    $this->error('You do not have permission to access this favourite', 403)
                );
            }
        } else {
            // Guest user - check guest_token from header
            $guestToken = $request->header('X-Guest-Token');
            
            if (!$guestToken || $favourite->guest_token !== $guestToken) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException(
                    $this->error('You do not have permission to access this favourite', 403)
                );
            }
        }

        return $favourite;
    }
}
