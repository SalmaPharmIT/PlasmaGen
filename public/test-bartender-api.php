<?php
/**
 * BarTender API Integration Test
 *
 * This script tests the connection to the BarTender API service.
 * Run this script from the command line to verify your configuration.
 */

// Configuration - Update these settings!
$config = [
    'api_url' => 'http://bartender-server/bartender-api.php', // Replace with your actual BarTender API URL
    'api_key' => 'your-secret-api-key-here',                  // Replace with your actual API key
];

// Test data
$testData = [
    'ar_number' => 'TEST-AR-123',
    'ref_number' => 'TEST-REF-456',
    'mega_pool' => 'TEST-MP-001',
    'mini_pools' => ['TEST-MP-001-1', 'TEST-MP-001-2', 'TEST-MP-001-3']
];

echo "BarTender API Integration Test\n";
echo "-----------------------------\n";
echo "API URL: {$config['api_url']}\n";
echo "Testing connection...\n\n";

// Initialize cURL session
$ch = curl_init($config['api_url']);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $config['api_key']
]);

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

// Close cURL session
curl_close($ch);

// Display results
echo "HTTP Status Code: $httpCode\n";

if ($error) {
    echo "Error: $error\n";
} else {
    echo "Response:\n";
    $responseData = json_decode($response, true);

    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Success: " . ($responseData['success'] ? 'Yes' : 'No') . "\n";
        echo "Message: " . ($responseData['message'] ?? 'N/A') . "\n";
        echo "Filename: " . ($responseData['filename'] ?? 'N/A') . "\n";
        echo "Reference: " . ($responseData['reference'] ?? 'N/A') . "\n";
    } else {
        echo "Raw response: $response\n";
    }
}

echo "\nTest completed.\n";
