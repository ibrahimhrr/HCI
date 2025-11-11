<?php
include('connection.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$title = $_POST['title'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $event_color = $_POST['event_color'] ?? '#3788d8';
    
    // Validate required fields
    if (empty($title) || empty($start_date) || empty($start_time) || empty($end_date) || empty($end_time)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Validate time format
    if (!preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $start_time) || 
        !preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', $end_time)) {
        echo json_encode(['success' => false, 'message' => 'Invalid time format. Use HH:MM format.']);
        exit;
    }
    
    // Validate that end datetime is after start datetime (allows same day events)
    $start_datetime = $start_date . ' ' . $start_time . ':00';
    $end_datetime = $end_date . ' ' . $end_time . ':00';
    
    if (strtotime($end_datetime) <= strtotime($start_datetime)) {
        echo json_encode(['success' => false, 'message' => 'End date/time must be after start date/time']);
        exit;
    }
    
    // Prepare and execute insert query
    $query = "INSERT INTO table_event (title, start_date, start_time, end_date, end_time, color) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connection, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssssss", $title, $start_date, $start_time, $end_date, $end_time, $event_color);
        
        if (mysqli_stmt_execute($stmt)) {
            $event_id = mysqli_insert_id($connection);
            
            // Return the new event data for immediate calendar update
            echo json_encode([
                'success' => true, 
                'message' => 'Event created successfully',
                'event' => [
                    'id' => $event_id,
                    'title' => $title,
                    'start' => $start_date . 'T' . $start_time,
                    'end' => $end_date . 'T' . $end_time,
                    'color' => $event_color,
                    'textColor' => ($event_color === '#ffc107') ? 'black' : 'white'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating event: ' . mysqli_error($connection)]);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . mysqli_error($connection)]);
    }

mysqli_close($connection);
?>
