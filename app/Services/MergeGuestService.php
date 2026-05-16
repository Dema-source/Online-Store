<?php

namespace App\Services;

use App\Repositories\Eloquent\FavouriteRepository;

class MergeGuestService
{
    /**
     * MergeGuestService Constructor.
     *
     * @param FavouriteRepository $favouriteRepository
     */
    public function __construct(
        protected FavouriteRepository $favouriteRepository
    ) {}

    /**
     * Merge guest favourites to user favourites.
     *
     * @param int $userId The user ID to merge favourites to.
     * @param string|null $guestToken The guest token to merge from.
     * @return array{merged_count: int, skipped_count: int}
     */
    public function mergeFavourites(int $userId, ?string $guestToken): array
    {
        if (!$guestToken) {
            return [
                'merged_count' => 0,
                'skipped_count' => 0,
            ];
        }

        // Get all guest favourites
        $guestFavourites = $this->favouriteRepository->getAll(['guest_token' => $guestToken], 1000);

        if ($guestFavourites->isEmpty()) {
            return [
                'merged_count' => 0,
                'skipped_count' => 0,
            ];
        }

        $mergedCount = 0;
        $skippedCount = 0;

        foreach ($guestFavourites as $guestFavourite) {
            // Check if user already has this product in favourites
            $existingFavourites = $this->favouriteRepository->getAll([
                'user_id' => $userId,
                'product_id' => $guestFavourite->product_id,
            ], 1);

            if (!$existingFavourites->isEmpty()) {
                // User already has this product, skip and delete guest favourite
                $this->favouriteRepository->delete($guestFavourite->id);
                $skippedCount++;
            } else {
                // Transfer favourite to user
                $this->favouriteRepository->update($guestFavourite->id, [
                    'user_id' => $userId,
                    'guest_token' => null,
                ]);
                $mergedCount++;
            }
        }

        return [
            'merged_count' => $mergedCount,
            'skipped_count' => $skippedCount,
        ];
    }
}
