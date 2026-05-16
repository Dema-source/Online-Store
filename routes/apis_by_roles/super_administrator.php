  <?php

  use App\Http\Controllers\Api\RolesPermissions\RoleController;
  use App\Http\Controllers\Api\RolesPermissions\RolePermissionController;
  use App\Http\Controllers\Api\UserController;
  use App\Http\Controllers\Api\ProfileController;
  use App\Http\Controllers\Api\CartController;
  use App\Http\Controllers\Api\CategoryController;
  use App\Http\Controllers\Api\CategoryProductController;
  use App\Http\Controllers\Api\CartItemController;
  use App\Http\Controllers\Api\FavouriteController;
  use App\Http\Controllers\Api\OrderController;
  use App\Http\Controllers\Api\PaymentController;
  use App\Http\Controllers\Api\ProductController;
  use App\Http\Controllers\Api\ProductImageController;
  use App\Http\Controllers\Api\ReviewController;
  use Illuminate\Support\Facades\Route;

  /*
|--------------------------------------------------------------------------
| Roles & Permissions - Full Access
|--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/roles
  Route::post('assign-permission-to-role', [RolePermissionController::class, 'assignPermissionToRole']);
  Route::post('remove-permission-from-role', [RolePermissionController::class, 'removePermissionFromRole']);
  Route::post('assign-permission-to-user', [RolePermissionController::class, 'assignPermissionToUser']);
  Route::post('remove-permission-from-user', [RolePermissionController::class, 'revokePermissionFromUser']);
  Route::get('check-permission', [RolePermissionController::class, 'checkPermission']);
  Route::get('get-user-permissions/{user}', [RolePermissionController::class, 'getUserPermissions']);
  Route::apiResource('roles', RoleController::class);

  /*
  |--------------------------------------------------------------------------
  | User - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/users
  Route::get('users/with-relations', [UserController::class, 'indexWithRelations']);
  Route::get('users/{id}/with-relations', [UserController::class, 'showWithRelations']);
  Route::get('users/by-ids', [UserController::class, 'indexByIds']);
  Route::get('users/recent', [UserController::class, 'indexRecent']);
  Route::apiResource('users', UserController::class);

  /*
  |--------------------------------------------------------------------------
  | Profile - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/profiles
  Route::get('profiles/with-relations', [ProfileController::class, 'indexWithRelations']);
  Route::get('profiles/{id}/with-relations', [ProfileController::class, 'showWithRelations']);
  Route::get('profiles/statistics', [ProfileController::class, 'statistics']);
  Route::apiResource('profiles', ProfileController::class);

  /*
  |--------------------------------------------------------------------------
  | Cart - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/carts
  Route::get('carts/with-relations', [CartController::class, 'indexWithRelations']);
  Route::get('carts/{id}/with-relations', [CartController::class, 'showWithRelations']);
  Route::get('carts/by-ids', [CartController::class, 'indexByIds']);
  Route::apiResource('carts', CartController::class);

  /*
  |--------------------------------------------------------------------------
  | Category - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/categories
  Route::get('categories/with-relations', [CategoryController::class, 'indexWithRelations']);
  Route::get('categories/{id}/with-relations', [CategoryController::class, 'showWithRelations']);
  Route::get('categories/by-ids', [CategoryController::class, 'indexByIds']);
  Route::apiResource('categories', CategoryController::class);

  /*
  |--------------------------------------------------------------------------
  | Product - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/products
  Route::get('products/with-relations', [ProductController::class, 'indexWithRelations']);
  Route::get('products/{id}/with-relations', [ProductController::class, 'showWithRelations']);
  Route::get('products/by-ids', [ProductController::class, 'indexByIds']);
  Route::apiResource('products', ProductController::class);

  /*
  |--------------------------------------------------------------------------
  | Product Image - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/product-images
  Route::get('product-images/with-relations', [ProductImageController::class, 'indexWithRelations']);
  Route::get('product-images/{id}/with-relations', [ProductImageController::class, 'showWithRelations']);
  Route::get('product-images/by-ids', [ProductImageController::class, 'indexByIds']);
  Route::apiResource('product-images', ProductImageController::class);

  /*
  |--------------------------------------------------------------------------
  | Category-Product Relationship - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/categories/{id}/products
  Route::get('categories/{category_id}/products', [CategoryProductController::class, 'index']);
  Route::get('products/{product_id}/categories', [CategoryProductController::class, 'productCategories']);
  Route::post('categories/{category_id}/products/attach', [CategoryProductController::class, 'attach']);
  Route::post('categories/{category_id}/products/detach', [CategoryProductController::class, 'detach']);
  Route::post('categories/{category_id}/products/sync', [CategoryProductController::class, 'sync']);
  Route::get('categories/{category_id}/products/{product_id}/check', [CategoryProductController::class, 'check']);

  /*
  |--------------------------------------------------------------------------
  | Cart Items - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/cart-items
  Route::post('cart-items/check-products', [CartItemController::class, 'checkProductsInCart']);
  Route::post('cart-items/clear-cart/{cart_id}', [CartItemController::class, 'clearCart']);
  Route::get('cart-items/get-total/{cart_id}', [CartItemController::class, 'getCartTotal']);
  Route::get('cart-items/get-count/{cart_id}', [CartItemController::class, 'getCartItemsCount']);
  Route::apiResource('cart-items', CartItemController::class);

  /*
  |--------------------------------------------------------------------------
  | Orders - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/orders
  Route::post('orders/{orderId}/cancel', [OrderController::class, 'cancel']);
  Route::get('orders/{orderId}/details', [OrderController::class, 'getDetails']);
  Route::get('orders/{orderId}/exists', [OrderController::class, 'checkExists']);
  Route::get('orders/by-ids', [OrderController::class, 'indexByIds']);
  Route::get('orders/{orderId}/status', [OrderController::class, 'getStatus']);
  Route::patch('orders/{orderId}/status', [OrderController::class, 'updateStatus']);
  Route::apiResource('orders', OrderController::class)->only(['index', 'show', 'destroy']);

  /*
  |--------------------------------------------------------------------------
  | Payments - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/payments
  Route::get('payments/by-order/{orderId}', [PaymentController::class, 'getByOrderId']);
  Route::get('payments/{paymentId}/status', [PaymentController::class, 'getStatus']);
  Route::patch('payments/{paymentId}/status', [PaymentController::class, 'updateStatus']);
  Route::apiResource('payments', PaymentController::class)->only(['index', 'show', 'destroy']);

  /*
  |--------------------------------------------------------------------------
  | Favourites - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/favourites
  Route::apiResource('favourites', FavouriteController::class)->only(['index', 'show', 'destroy']);

  /*
  |--------------------------------------------------------------------------
  | Reviews - Full Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/admin/reviews
  Route::apiResource('reviews', ReviewController::class)->only(['index', 'show', 'destroy']);
