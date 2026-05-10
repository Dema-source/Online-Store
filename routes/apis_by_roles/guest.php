  <?php

  use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CategoryProductController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Auth\GuestController;
  use Illuminate\Support\Facades\Route;

  // Guest init - no authentication required
  Route::post('/init', [GuestController::class, 'initGuest'])->name('guest.init');

  // Guest routes - require guest token or authentication
  Route::middleware('guest.token')->group(function () {
    // Guest routes here that require guest token

  /*
  |--------------------------------------------------------------------------
  | Category - Read only Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/guest/categories
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Product - Read only Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/guest/products
  Route::apiResource('products', ProductController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Product Image - Read only Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/guest/product-images
  Route::apiResource('product-images', ProductImageController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Category-Product Relationship - Read only Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/guest/categories/{id}/products
  Route::get('categories/{category_id}/products', [CategoryProductController::class, 'index']);
  Route::get('products/{product_id}/categories', [CategoryProductController::class, 'productCategories']);
  Route::get('categories/{category_id}/products/{product_id}/check', [CategoryProductController::class, 'check']);
  
  });
