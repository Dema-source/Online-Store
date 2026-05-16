<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected AuthRepositoryInterface $authRepository;
    protected MergeGuestService $mergeGuestService;

    public function __construct(
        AuthRepositoryInterface $authRepository,
        MergeGuestService $mergeGuestService
    ) {
        $this->authRepository = $authRepository;
        $this->mergeGuestService = $mergeGuestService;
    }

    /**
     * Authenticate user with credentials using Sanctum.
     * This method validates credentials and creates API token.
     *
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @return array
     * @throws ValidationException
     */
    public function authenticate(string $email, string $password, bool $remember = false): array
    {
        $this->ensureIsNotRateLimited($email);

        // Find user by email
        $user = $this->authRepository->findByEmail($email);
        
        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey($email));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey($email));

        // Create Sanctum token
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Register a new customer with profile and cart.
     *
     * @param array $userData
     * @param string|null $guestToken The guest token to merge favourites from.
     * @return array
     */
    public function registerCustomer(array $userData, ?string $guestToken = null): array
    {
        return DB::transaction(function () use ($userData, $guestToken) {
            // Create user
            $user = $this->authRepository->createUser($userData, 'customer');
            
            // Create profile if profile data is provided
            if (isset($userData['profile'])) {
                $user->profile()->create($userData['profile']);
            }
            
            // Create cart for customer
            $user->profile->cart()->create();
            
            // Merge guest favourites to user favourites
            $this->mergeGuestService->mergeFavourites($user->id, $guestToken);
            
            // Fire registration event
            event(new Registered($user));

            // Create API token
            $token = $user->createToken('api-token')->plainTextToken;

            return [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ];
        });
    }

    /**
     * Register a new admin.
     *
     * @param array $userData
     * @return array
     */
    public function registerAdmin(array $userData): array
    {
        $user = $this->authRepository->createUser($userData, 'super_administrator');
        
        event(new Registered($user));

        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Logout user and revoke tokens.
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        return $this->authRepository->revokeAllTokens($user->id);
    }

    /**
     * Refresh user token.
     *
     * @param User $user
     * @return array
     */
    public function refreshToken(User $user): array
    {
        $this->authRepository->revokeAllTokens($user->id);
        
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Send password reset link.
     *
     * @param string $email
     * @return string|null
     */
    public function sendPasswordResetLink(string $email): ?string
    {
        $user = $this->authRepository->findByEmail($email);
        
        if (!$user) {
            return null;
        }

        return $this->authRepository->createPasswordResetToken($email);
    }

    /**
     * Reset password with token.
     *
     * @param string $token
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function resetPassword(string $token, string $email, string $password): bool
    {
        $status = Password::reset([
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ], function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });

        return $status === Password::PASSWORD_RESET;
    }

    /**
     * Verify user email.
     *
     * @param int $userId
     * @return bool
     */
    public function verifyEmail(int $userId): bool
    {
        return $this->authRepository->verifyEmail($userId);
    }

    /**
     * Get authenticated user.
     *
     * @return User|null
     */
    public function getCurrentUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Ensure login request is not rate limited.
     *
     * @param string $email
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(string $email): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($email), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($email));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @param string $email
     * @return string
     */
    protected function throttleKey(string $email): string
    {
        return Str::transliterate(Str::lower($email) . '|' . request()->ip());
    }
}
