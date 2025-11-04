<?php
include('connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventIds = $_POST['eventIds'] ?? [];
    
    // Validate required fields
    if (empty($eventIds) || !is_array($eventIds)) {
        echo json_encode(['success' => false, 'message' => 'No event IDs provided']);
        exit;
    }
    
    // Sanitize event IDs - convert to integers and filter out invalid ones
    $validEventIds = array_filter(array_map('intval', $eventIds), function($id) {
        return $id > 0;
    });
    
    if (empty($validEventIds)) {
        echo json_encode(['success' => false, 'message' => 'No valid event IDs provided']);
        exit;
    }
    
    // Create placeholders for the IN clause
    $placeholders = str_repeat('?,', count($validEventIds) - 1) . '?';
    
    // Prepare and execute delete query
    $query = "DELETE FROM table_event WHERE id IN ($placeholders)";
    $stmt = mysqli_prepare($connection, $query);
    
    if ($stmt) {
        // Create type string (all integers)
        $types = str_repeat('i', count($validEventIds));
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, $types, ...$validEventIds);
        
        if (mysqli_stmt_execute($stmt)) {
            $deletedCount = mysqli_stmt_affected_rows($stmt);
            
            if ($deletedCount > 0) {
                echo json_encode([
                    'success' => true, 
                    'message' => "$deletedCount event(s) deleted successfully",
                    'deletedCount' => $deletedCount
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No events found with the provided IDs']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error executing delete: ' . mysqli_error($connection)]);
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
