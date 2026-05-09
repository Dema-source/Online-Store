  <?php

  use App\Http\Controllers\Auth\GuestController;
  use Illuminate\Support\Facades\Route;

  // Guest init - no authentication required
  Route::post('/init', [GuestController::class, 'initGuest'])->name('guest.init');

  // Guest routes - require guest token or authentication
  Route::middleware('guest.token')->group(function () {
    // Add other guest routes here that require guest token
  });
