<?php
include('connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['eventId'] ?? '';
    $title = $_POST['title'] ?? '';
    $startDate = $_POST['startDate'] ?? '';
    $startTime = $_POST['startTime'] ?? '';
    $endDate = $_POST['endDate'] ?? '';
    $endTime = $_POST['endTime'] ?? '';
    $color = $_POST['color'] ?? '#3788d8'; // Default blue color
    
    // Validate required fields
    if (empty($eventId) || empty($title) || empty($startDate) || empty($startTime) || empty($endDate) || empty($endTime)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Combine date and time for database storage
    $startDateTime = $startDate . ' ' . $startTime . ':00';
    $endDateTime = $endDate . ' ' . $endTime . ':00';
    
    // Validate that end datetime is after start datetime (allows same day events)
    if (strtotime($endDateTime) <= strtotime($startDateTime)) {
        echo json_encode(['success' => false, 'message' => 'End date/time must be after start date/time']);
        exit;
    }
    
    // Prepare and execute update query
    $query = "UPDATE table_event SET title = ?, start_date = ?, start_time = ?, end_date = ?, end_time = ?, color = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssssss", $title, $startDate, $startTime, $endDate, $endTime, $color, $eventId);
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(['success' => true, 'message' => 'Event updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'No event found with that ID or no changes made']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing update: ' . mysqli_error($connection)]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . mysqli_error($connection)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($connection);
?>
