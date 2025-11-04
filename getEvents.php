<?php
include('connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all events from database
    $query = "SELECT * FROM table_event ORDER BY start_date ASC";
    $result = mysqli_query($connection, $query);
    
    $events = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $events[] = $row;
        }
        
        echo json_encode($events);
    } else {
        echo json_encode(['error' => 'Error fetching events: ' . mysqli_error($connection)]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method']);
}

mysqli_close($connection);
?>
