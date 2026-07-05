<?php

use App\Models\SiteSetting;

$key = 'logo_scroll_speed';
$testValue = 10;

// Set value
echo "Setting $key to $testValue...\n";
SiteSetting::set($key, $testValue, 'homepage', 'integer');

// Get value
$retrievedValue = SiteSetting::get($key);
echo "Retrieved value: $retrievedValue\n";

if ($retrievedValue == $testValue) {
    echo "SUCCESS: Value saved and retrieved correctly.\n";
} else {
    echo "FAILURE: Value mismatch. Expected $testValue, got $retrievedValue.\n";
}

// Reset to default 5
echo "Resetting to default 5...\n";
SiteSetting::set($key, 5, 'homepage', 'integer');
