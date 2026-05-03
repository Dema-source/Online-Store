<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Create a new user with the given data and role.
     *
     * @param array $userData
     * @param string $role
     * @return User
     */
    public function createUser(array $userData, string $role): User
    {
        return DB::transaction(function () use ($userData, $role) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);

            $user->assignRole($role);

            return $user;
        });
    }

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::byEmail($email)->first();
    }

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User
    {
        return User::byId($id)->first();
    }

    /**
     * Update user's email verification status.
     *
     * @param int $userId
     * @return bool
     */
    public function verifyEmail(int $userId): bool
    {
        $user = $this->findById($userId);

        if (!$user) {
            return false;
        }

        $user->email_verified_at = now();
        return $user->save();
    }

    /**
     * Create password reset token for user and send email.
     *
     * @param string $email
     * @return string|null
     */
    public function createPasswordResetToken(string $email): ?string
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return null;
        }

        // This will create the token AND send the email
        $status = Password::sendResetLink(['email' => $email]);
        
        // If email was sent successfully, get the token
        if ($status === Password::RESET_LINK_SENT) {
            $resetRecord = DB::table('password_reset_tokens')
                ->where('email', $email)
                ->first();
            
            return $resetRecord ? $resetRecord->token : null;
        }

        return null;
    }

    /**
     * Find user by password reset token.
     *
     * @param string $token
     * @return User|null
     */
    public function findByPasswordResetToken(string $token): ?User
    {
        // Use Laravel's built-in token verification
        $resetRecord = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        if (!$resetRecord) {
            return null;
        }

        // Verify the token is not expired (tokens expire after 1 hour by default)
        if (strtotime($resetRecord->created_at) < strtotime('-1 hour')) {
            // Delete expired token
            $this->deletePasswordResetToken($resetRecord->email);
            return null;
        }

        return $this->findByEmail($resetRecord->email);
    }

    /**
     * Reset user password.
     *
     * @param int $userId
     * @param string $password
     * @return bool
     */
    public function resetPassword(int $userId, string $password): bool
    {
        $user = $this->findById($userId);

        if (!$user) {
            return false;
        }

        $user->password = Hash::make($password);
        $user->save();

        event(new PasswordReset($user));

        return true;
    }

    /**
     * Delete password reset token.
     *
     * @param string $email
     * @return bool
     */
    public function deletePasswordResetToken(string $email): bool
    {
        $deleted = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        return $deleted > 0;
    }

    /**
     * Revoke all user tokens.
     *
     * @param int $userId
     * @return bool
     */
    public function revokeAllTokens(int $userId): bool
    {
        $user = $this->findById($userId);

        if (!$user) {
            return false;
        }

        $user->tokens()->delete();
        return true;
    }
}
