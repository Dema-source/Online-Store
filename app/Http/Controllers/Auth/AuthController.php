<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminRegistrationRequest;
use App\Http\Requests\Auth\CustomerRegistrationRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user login.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $result = $this->authService->authenticate(
            $request->email,
            $request->password,
            $request->boolean('remember')
        );

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ], 'Login successful');
    }

    /**
     * Handle customer registration.
     */
    public function registerCustomer(CustomerRegistrationRequest $request): JsonResponse
    {
        $result = $this->authService->registerCustomer($request->validated());

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ], 'Customer registration successful');
    }

    /**
     * Register new admin user.
     */
    public function registerAdmin(AdminRegistrationRequest $request): JsonResponse
    {
        // Additional check: Ensure the authenticated user is a super administrator
        $authenticatedUser = $request->user();

        if (!$authenticatedUser->hasRole('super_administrator')) {
            return $this->error('Only super administrators can register new admin users', 403);
        }

        $result = $this->authService->registerAdmin($request->validated());

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ], 'Admin registration successful');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authService->logout($user);

        return $this->success(null, 'Logout successful');
    }

    /**
     * Refresh user token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $result = $this->authService->refreshToken($user);

        return $this->success([
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
            'token_type' => $result['token_type'],
        ], 'Token refreshed successfully');
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $this->authService->getCurrentUser();

        return $this->success([
            'user' => new UserResource($user),
        ], 'User profile retrieved successfully');
    }

    /**
     * Send email verification notification.
     */
    public function sendEmailVerification(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 422);
        }

        $user->sendEmailVerificationNotification();

        return $this->success(null, 'Email verification link sent');
    }

    /**
     * Verify email with token.
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $request->validate([
            'id' => ['required', 'integer'],
            'hash' => ['required', 'string'],
        ]);

        $user = $this->authService->getCurrentUser();

        if (!hash_equals((string) $request->id, (string) $user->getKey())) {
            return $this->error('Invalid verification link', 422);
        }

        if (!hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            return $this->error('Invalid verification link', 422);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified', 422);
        }

        $this->authService->verifyEmail($user->id);

        return $this->success(null, 'Email verified successfully');
    }
}
