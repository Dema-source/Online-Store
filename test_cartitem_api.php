<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Requests\Api\CartItem\StoreCartItemRequest;

echo "=== Testing CartItem Store Request Validation ===\n\n";

// Test 1: Create request with admin user simulation
echo "Test 1: Simulating Admin User Request\n";
echo "=====================================\n";

// Mock request data
$requestData = [
    'cart_id' => 1,
    'products' => [
        ['product_id' => 1, 'quantity' => 5],
        ['product_id' => 2, 'quantity' => 3]
    ]
];

echo "Request Data:\n";
echo json_encode($requestData, JSON_PRETTY_PRINT) . "\n\n";

// Create a mock request
$request = Request::create('/api/admin/cart-items', 'POST', $requestData);
$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

// Create validation request instance
$storeRequest = new StoreCartItemRequest();

// Manually set the request instance (bypassing Laravel's dependency injection)
$reflection = new ReflectionClass($storeRequest);
$containerProperty = $reflection->getProperty('container');
$containerProperty->setAccessible(true);
$containerProperty->setValue($storeRequest, app());

$requestProperty = $reflection->getProperty('request');
$requestProperty->setAccessible(true);
$requestProperty->setValue($storeRequest, $request);

// Test validation without authentication first
echo "Testing validation WITHOUT authentication:\n";
try {
    $rules = $storeRequest->rules();
    echo "Rules applied:\n";
    print_r($rules);
    
    // Check if cart_id rule exists
    if (isset($rules['cart_id'])) {
        echo "✓ cart_id rule exists: " . implode(', ', $rules['cart_id']) . "\n";
    } else {
        echo "✗ cart_id rule NOT found\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check user method behavior
echo "Test 2: Checking User Method\n";
echo "============================\n";

$user = $storeRequest->user();
if ($user) {
    echo "✓ User found: " . get_class($user) . "\n";
    echo "User ID: " . $user->id . "\n";
    echo "User Email: " . $user->email . "\n";
    
    if (method_exists($user, 'hasRole')) {
        $hasAdminRole = $user->hasRole('super_administrator');
        echo "Has super_administrator role: " . ($hasAdminRole ? 'YES' : 'NO') . "\n";
    } else {
        echo "✗ hasRole method not found\n";
    }
} else {
    echo "✗ No user found (not authenticated)\n";
}

echo "\n";

// Test 3: Simulate authenticated admin user
echo "Test 3: Simulating Authenticated Admin User\n";
echo "==========================================\n";

// This is a simplified test - in real scenario, the user would be authenticated via Sanctum
echo "Note: In real API, user authentication happens via Sanctum middleware\n";
echo "The validation rules depend on:\n";
echo "1. User being authenticated (auth:sanctum middleware)\n";
echo "2. User having super_administrator role (role middleware)\n";
echo "3. Proper Authorization: Bearer {token} header\n\n";

echo "Expected behavior:\n";
echo "- Authenticated admin: cart_id is REQUIRED\n";
echo "- Unauthenticated or non-admin: cart_id validation may not apply properly\n";

echo "\n=== Test Complete ===\n";
