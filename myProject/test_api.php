<?php

// Simple API test script
$baseUrl = 'http://127.0.0.1:8001/api';

function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $headers[] = 'Content-Type: application/json';
    }
    
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

echo "=== Laravel RBAC API Test ===\n\n";

// Test 1: Register a new user
echo "1. Testing user registration...\n";
$registerData = [
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'role' => 'user'
];

$response = makeRequest($baseUrl . '/auth/register', 'POST', $registerData);
echo "Status: " . $response['code'] . "\n";
if ($response['code'] === 201) {
    echo "✅ User registered successfully\n";
    $token = $response['body']['token'];
    echo "Token: " . substr($token, 0, 20) . "...\n";
} else {
    echo "❌ Registration failed\n";
    print_r($response['body']);
}
echo "\n";

// Test 2: Get public posts
echo "2. Testing public posts endpoint...\n";
$response = makeRequest($baseUrl . '/posts');
echo "Status: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Posts endpoint accessible\n";
    echo "Posts count: " . count($response['body']['data']) . "\n";
} else {
    echo "❌ Posts endpoint failed\n";
    print_r($response['body']);
}
echo "\n";

// Test 3: Get categories
echo "3. Testing categories endpoint...\n";
$response = makeRequest($baseUrl . '/categories');
echo "Status: " . $response['code'] . "\n";
if ($response['code'] === 200) {
    echo "✅ Categories endpoint accessible\n";
    echo "Categories count: " . count($response['body']) . "\n";
} else {
    echo "❌ Categories endpoint failed\n";
    print_r($response['body']);
}
echo "\n";

// Test 4: Test authentication required endpoint
if (isset($token)) {
    echo "4. Testing authenticated endpoint...\n";
    $headers = ['Authorization: Bearer ' . $token];
    $response = makeRequest($baseUrl . '/auth/user', 'GET', null, $headers);
    echo "Status: " . $response['code'] . "\n";
    if ($response['code'] === 200) {
        echo "✅ Authentication working\n";
        echo "User: " . $response['body']['user']['name'] . "\n";
    } else {
        echo "❌ Authentication failed\n";
        print_r($response['body']);
    }
    echo "\n";
}

echo "=== API Test Complete ===\n";
