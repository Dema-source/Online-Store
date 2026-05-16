  <?php

  use App\Http\Controllers\Api\CartItemController;
  use App\Http\Controllers\Api\CategoryController;
  use App\Http\Controllers\Api\CategoryProductController;
  use App\Http\Controllers\Api\CheckoutController;
  use App\Http\Controllers\Api\OrderController;
  use App\Http\Controllers\Api\PaymentController;
  use App\Http\Controllers\Api\ProductController;
  use App\Http\Controllers\Api\ProductImageController;
  use App\Http\Controllers\Api\UserController;
  use App\Http\Controllers\Api\ProfileController;
  use App\Http\Controllers\Api\FavouriteController;
  use App\Http\Controllers\Api\ReviewController;
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
  Route::put('cart-items/update', [CartItemController::class, 'updateCartItemForCustomer']);
  Route::post('cart-items/store', [CartItemController::class, 'storeForCustomer']);
  Route::delete('cart-items/delete/{productId}', [CartItemController::class, 'removeProductFromMyCart']);
  Route::post('cart-items/clear-cart', [CartItemController::class, 'clearMyCart']);
  Route::post('cart-items/check-products', [CartItemController::class, 'checkProductsInMyCart']);
  Route::get('cart-items/get-total', [CartItemController::class, 'getMyCartTotal']);
  Route::get('cart-items/get-count', [CartItemController::class, 'getMyCartItemsCount']);

  /*
  |--------------------------------------------------------------------------
  | Checkout - Customer Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/checkout
  Route::post('/checkout', [CheckoutController::class, 'checkout']);
  Route::get('/checkout/status/{orderId}', [CheckoutController::class, 'getStatus']);

  /*
  |--------------------------------------------------------------------------
  | Orders - Restrict Access - Full Access for customer related orders
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/orders
  Route::post('orders/{orderId}/cancel', [OrderController::class, 'cancel']);
  Route::put('orders/{orderId}', [OrderController::class, 'update']);
  Route::get('orders/{orderId}', [OrderController::class, 'show']);
  Route::get('orders/{orderId}/status', [OrderController::class, 'getStatus']);
  Route::get('orders/my-orders', [OrderController::class, 'indexMyOrders']);

  /*
  |--------------------------------------------------------------------------
  | Payments - Restrict Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/payments
  Route::get('payments/my-payments', [PaymentController::class, 'indexMyPayment']);
  Route::get('payments/{paymentId}/status', [PaymentController::class, 'getStatus']);
  Route::get('payments/{paymentId}', [PaymentController::class, 'show']);

  /*
  |--------------------------------------------------------------------------
  | Favourites - Customer Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/favourites
  Route::get('favourites/my-favourites', [FavouriteController::class, 'indexMyFavourites']);
  Route::apiResource('favourites', FavouriteController::class)->only(['show', 'store', 'destroy']);

  /*
  |--------------------------------------------------------------------------
  | Reviews - Customer Access
  |--------------------------------------------------------------------------
  */
  // API: {{baseURL}}/api/customer/reviews
  Route::get('reviews/my-reviews', [ReviewController::class, 'indexMyReviews']);
  Route::apiResource('reviews', ReviewController::class);
