<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class GuestController extends Controller
{  
    /**
     * Initialize a new guest session.
     *
     * @return JsonResponse
     */
    public function initGuest(): JsonResponse
    {
        return $this->success([
            'guest_token' => (string) Str::uuid(),
            'token_type' => 'Guest',
        ], 'Guest token generated successfully');
    }
}
