<?php
// Simple test to see if smartSuggestions.php works
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Smart Suggestions Backend</h2>";

// Test 1: Check if connection.php exists and works
echo "<h3>Test 1: Database Connection</h3>";
if (file_exists('connection.php')) {
    echo "✓ connection.php exists<br>";
    include('connection.php');
    if (isset($connection) && $connection) {
        echo "✓ Database connected successfully<br>";
        echo "Connection type: " . get_class($connection) . "<br>";
    } else {
        echo "✗ Database connection failed<br>";
    }
} else {
    echo "✗ connection.php not found<br>";
}

// Test 2: Try to query the table
echo "<h3>Test 2: Query Events Table</h3>";
if (isset($connection)) {
    $query = "SELECT id, title, start_date, start_time, end_date, end_time FROM table_event LIMIT 3";
    $result = mysqli_query($connection, $query);
    
    if ($result) {
        $count = mysqli_num_rows($result);
        echo "✓ Query successful - found $count events<br>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "Event: {$row['title']} | {$row['start_date']} {$row['start_time']}<br>";
        }
    } else {
        echo "✗ Query failed: " . mysqli_error($connection) . "<br>";
    }
}

// Test 3: Test date functions
echo "<h3>Test 3: Date Functions</h3>";
try {
    $today = new DateTime();
    echo "✓ DateTime works - Today is: " . $today->format('Y-m-d') . "<br>";
    
    $tomorrow = clone $today;
    $tomorrow->modify('+1 day');
    echo "✓ Date modification works - Tomorrow is: " . $tomorrow->format('Y-m-d') . "<br>";
} catch (Exception $e) {
    echo "✗ DateTime error: " . $e->getMessage() . "<br>";
}

// Test 4: Test JSON encoding
echo "<h3>Test 4: JSON Encoding</h3>";
$testData = [
    [
        'start_datetime' => '2025-11-12 09:00:00',
        'end_datetime' => '2025-11-12 10:00:00',
        'quality' => 'excellent',
        'reason' => 'Test slot'
    ]
];
$json = json_encode($testData);
if ($json) {
    echo "✓ JSON encoding works<br>";
    echo "Sample JSON: " . htmlspecialchars($json) . "<br>";
} else {
    echo "✗ JSON encoding failed<br>";
}

echo "<h3>All Tests Complete</h3>";
echo "<p><a href='smartSuggestionsForm.php'>Back to Smart Suggestions</a></p>";
