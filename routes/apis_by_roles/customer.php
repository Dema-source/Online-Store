  <?php

  use App\Http\Controllers\Api\CartItemController;
  use App\Http\Controllers\Api\CategoryController;
  use App\Http\Controllers\Api\CategoryProductController;
  use App\Http\Controllers\Api\ProductController;
  use App\Http\Controllers\Api\ProductImageController;
  use App\Http\Controllers\Api\UserController;
  use App\Http\Controllers\Api\ProfileController;
  use Illuminate\Support\Facades\Route;

  /*
  |--------------------------------------------------------------------------
  | User - Limited Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/users
  Route::get('users/with-relations', [UserController::class, 'indexWithRelations']);
  Route::get('users/{id}/with-relations', [UserController::class, 'showWithRelations']);
  Route::put('users/update_me', [UserController::class, 'updateMe']);
  Route::apiResource('users', UserController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Profile - Limited Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/profiles
  Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
  Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
  Route::get('profiles/my-profile', [ProfileController::class, 'getMyProfile']);
  Route::put('profiles/update-me', [ProfileController::class, 'updateMyProfile']);
  Route::apiResource('profiles', ProfileController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Category - Read only
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/categories
  Route::get('categories/with-relations', [CategoryController::class, 'indexWithRelations']);
  Route::get('categories/{id}/with-relations', [CategoryController::class, 'showWithRelations']);
  Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Product - Read only
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/products
  Route::get('products/with-relations', [ProductController::class, 'indexWithRelations']);
  Route::get('products/{id}/with-relations', [ProductController::class, 'showWithRelations']);
  Route::apiResource('products', ProductController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Product Image - Read only
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/product-images
  Route::get('product-images/with-relations', [ProductImageController::class, 'indexWithRelations']);
  Route::get('product-images/{id}/with-relations', [ProductImageController::class, 'showWithRelations']);
  Route::apiResource('product-images', ProductImageController::class)->only(['index', 'show']);

  /*
  |--------------------------------------------------------------------------
  | Category-Product Relationship - Read only
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/categories/{id}/products
  Route::get('categories/{category_id}/products', [CategoryProductController::class, 'index']);
  Route::get('products/{product_id}/categories', [CategoryProductController::class, 'productCategories']);
  Route::get('categories/{category_id}/products/{product_id}/check', [CategoryProductController::class, 'check']);

  /*
  |--------------------------------------------------------------------------
  | Cart Items - Customer Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/cart-items
  Route::get('cart-items/index', [CartItemController::class, 'indexForCustomer']);
  Route::put('cart-items', [CartItemController::class, 'updateCartItemForCustomer']);
  Route::post('cart-items/check-products', [CartItemController::class, 'checkProductsInMyCart']);
  Route::get('cart-items/get-total/{cart_id}', [CartItemController::class, 'getMyCartTotal']);
  Route::get('cart-items/get-count', [CartItemController::class, 'getMyCartItemsCount']);
  Route::post('cart-items/clear-cart/{cart_id}', [CartItemController::class, 'clearMyCart']);
  Route::apiResource('cart-items', CartItemController::class)->except(['index', 'show','update']);
