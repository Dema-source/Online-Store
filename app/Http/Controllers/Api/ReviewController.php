<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Review\StoreReviewRequest;
use App\Http\Requests\Api\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * ReviewController Constructor.
     *
     * @param ReviewService $service.
     */
    public function __construct(
        protected ReviewService $service
    ) {}

    /**
     * Display a paginated listing of Reviews.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->except(['page', 'per_page']);
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(ReviewResource::collection($data), 'Review list fetched successfully');
    }

    /**
     * Store a newly created Review in storage.
     *
     * @param StoreReviewRequest $request The validated form request.
     * @return JsonResponse
     */
    public function store(StoreReviewRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Automatically set profile_id from authenticated user if not provided (for customers)
        if (!isset($data['profile_id'])) {
            $user = auth()->user();
            if ($user && $user->profile) {
                $data['profile_id'] = $user->profile->id;
            }
        }

        $item = $this->service->create($data);

        return $this->success(new ReviewResource($item), 'Review created successfully');
    }

    /**
     * Display the specified Review.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function show(int|string $id): JsonResponse
    {
        $item = $this->service->findById($id);

        return $this->success(new ReviewResource($item), 'Review fetched successfully');
    }

    /**
     * Update the specified Review in storage.
     * 
     * @param UpdateReviewRequest $request Validated input data.
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function update(UpdateReviewRequest $request, int|string $id): JsonResponse
    {
        $this->checkOwnership($id);
        $data = $request->validated();

        // Automatically set profile_id from authenticated user if not provided (for customers)
        if (!isset($data['profile_id'])) {
            $user = auth()->user();
            if ($user && $user->profile) {
                $data['profile_id'] = $user->profile->id;
            }
        }

        $item = $this->service->update($id, $data);

        return $this->success(new ReviewResource($item), 'Review updated successfully');
    }

    /**
     * Remove the specified Review from storage.
     *
     * @param int|string $id The primary key value.
     * @return JsonResponse
     */
    public function destroy(int|string $id): JsonResponse
    {
        $this->checkOwnership($id);
        $this->service->delete($id);

        return $this->success(null, 'Review deleted successfully');
    }

    /**
     * Display a paginated listing of Reviews for the authenticated user.
     *
     * @param Request $request The HTTP request containing query filters.
     * @return JsonResponse
     */
    public function indexMyReviews(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || !$user->profile) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                $this->error('Profile not found', 404)
            );
        }

        $filters = $request->except(['page', 'per_page']);
        $filters['profile_id'] = $user->profile->id;
        $perPage = (int) $request->input('per_page', 15);

        $data = $this->service->getAll($filters, $perPage);

        return $this->paginate(ReviewResource::collection($data), 'My reviews fetched successfully');
    }

    /**
     * Check if the authenticated user owns the review.
     *
     * @param int|string $id The review ID.
     * @return \App\Models\Review The review if owned by the user.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the user does not have permission.
     */
    private function checkOwnership(int|string $id): \App\Models\Review
    {
        $review = $this->service->findById($id);

        // Load profile relationship to avoid N+1 queries
        $review->load('profile');

        $user = Auth::user();

        // Allow super administrators to access any review
        if ($user && $user->hasRole('super_administrator')) {
            return $review;
        }

        // Check if user is authenticated and owns the review
        if (!$user || !$review->profile || $review->profile->user_id !== $user->id) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                $this->error('You do not have permission to access this favourite', 403)
            );
        }

        return $review;
    }
}
