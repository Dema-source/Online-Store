<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     * Create a new user with the given data and role.
     *
     * @param array $userData
     * @param string $role
     * @return User
     */
    public function createUser(array $userData, string $role): User;

    /**
     * Find user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Update user's email verification status.
     *
     * @param int $userId
     * @return bool
     */
    public function verifyEmail(int $userId): bool;

    /**
     * Create password reset token for user.
     *
     * @param string $email
     * @return string|null
     */
    public function createPasswordResetToken(string $email): ?string;

    /**
     * Find user by password reset token.
     *
     * @param string $token
     * @return User|null
     */
    public function findByPasswordResetToken(string $token): ?User;

    /**
     * Reset user password.
     *
     * @param int $userId
     * @param string $password
     * @return bool
     */
    public function resetPassword(int $userId, string $password): bool;

    /**
     * Delete password reset token.
     *
     * @param string $email
     * @return bool
     */
    public function deletePasswordResetToken(string $email): bool;

    /**
     * Revoke all user tokens.
     *
     * @param int $userId
     * @return bool
     */
    public function revokeAllTokens(int $userId): bool;
}
