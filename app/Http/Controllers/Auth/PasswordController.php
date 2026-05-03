<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class PasswordController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Send password reset link.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $token = $this->authService->sendPasswordResetLink($request->email);

        if (!$token) {
            return $this->error('If an account with that email exists, a password reset link was sent', 200);
        }

        return $this->success(null, 'Password reset link sent successfully');
    }

    /**
     * Reset password with token.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $success = $this->authService->resetPassword(
            $request->token,
            $request->email,
            $request->password
        );

        if (!$success) {
            return $this->error('Invalid token or email', 422);
        }

        return $this->success(null, 'Password reset successfully');
    }
}
