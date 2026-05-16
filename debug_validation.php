<?php

echo "=== Debugging CartItem Validation Issue ===\n\n";

// Test the validation rules directly
echo "1. Testing StoreCartItemRequest rules() method:\n";
echo "============================================\n";

// Check if the class exists
if (class_exists('App\Http\Requests\Api\CartItem\StoreCartItemRequest')) {
    echo "✓ StoreCartItemRequest class exists\n";
    
    // Create an instance (without full Laravel context)
    try {
        $request = new \App\Http\Requests\Api\CartItem\StoreCartItemRequest();
        
        // Try to get rules - this will show us what rules are being applied
        $rules = $request->rules();
        
        echo "Validation Rules:\n";
        foreach ($rules as $field => $fieldRules) {
            echo "  $field: " . implode(', ', $fieldRules) . "\n";
        }
        
        // Check specifically for cart_id
        if (isset($rules['cart_id'])) {
            echo "\n✓ cart_id rule found: " . implode(', ', $rules['cart_id']) . "\n";
            if (in_array('required', $rules['cart_id'])) {
                echo "  → cart_id is REQUIRED\n";
            } else {
                echo "  → cart_id is OPTIONAL\n";
            }
        } else {
            echo "\n✗ cart_id rule NOT found\n";
        }
        
    } catch (Exception $e) {
        echo "Error creating request: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ StoreCartItemRequest class not found\n";
}

echo "\n2. Testing Authentication Simulation:\n";
echo "=================================\n";

// Simulate what happens when user() is called
echo "When \$this->user() is called in validation:\n";
echo "- If user is authenticated AND has super_administrator role: cart_id becomes REQUIRED\n";
echo "- If user is not authenticated OR doesn't have role: cart_id may not be set properly\n\n";

echo "3. Root Cause Analysis:\n";
echo "=====================\n";
echo "The issue is likely:\n";
echo "a) User is not properly authenticated (missing Sanctum token)\n";
echo "b) User doesn't have super_administrator role\n";
echo "c) The auth middleware is not working as expected\n\n";

echo "4. Solution Checklist:\n";
echo "====================\n";
echo "□ Ensure you have a valid Sanctum token\n";
echo "□ Include 'Authorization: Bearer {token}' header\n";
echo "□ User must have 'super_administrator' role\n";
echo "□ Test with a simple authenticated endpoint first\n\n";

echo "5. Alternative Debugging:\n";
echo "======================\n";
echo "Add this to StoreCartItemRequest::rules() temporarily:\n";
echo "
```php
public function rules(): array
{
    \$user = \$this->user();
    \$userEmail = \$user ? \$user->email : 'NO USER';
    \$hasRole = \$user && \$user->hasRole('super_administrator') ? 'YES' : 'NO';
    
    // Log for debugging
    error_log('CartItem Debug - User: ' . \$userEmail . ', Has Admin Role: ' . \$hasRole);
    
    \$rules = [
        // ... existing rules
    ];
    
    if (\$user && \$user->hasRole('super_administrator')) {
        \$rules['cart_id'] = ['required', 'integer', 'exists:carts,id'];
    }
    
    return \$rules;
}
```\n";

echo "\n=== Debug Complete ===\n";
