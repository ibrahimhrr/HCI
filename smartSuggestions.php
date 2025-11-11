<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('connection.php');
header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if ($data === null) {
        throw new Exception('Invalid JSON');
    }

    $eventName = $data['eventName'] ?? 'New Event';
    $duration = $data['duration'] ?? 1;
    $searchRange = $data['searchRange'] ?? 'this_week';
    $preferredStartTime = $data['preferredStartTime'] ?? 9;
    $preferredEndTime = $data['preferredEndTime'] ?? 17;
    $maxSuggestions = $data['maxSuggestions'] ?? 5;

    list($startDate, $endDate) = getSearchDateRange($searchRange);
    $existingEvents = getExistingEvents($connection, $startDate, $endDate);
    $potentialSlots = generateTimeSlots($startDate, $endDate, $duration, $preferredStartTime, $preferredEndTime);
    $availableSlots = filterAvailableSlots($potentialSlots, $existingEvents);
    $rankedSlots = rankSlots($availableSlots, $preferredStartTime, $preferredEndTime);
    $topSuggestions = array_slice($rankedSlots, 0, $maxSuggestions);

    echo json_encode($topSuggestions);
    mysqli_close($connection);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
    if (isset($connection)) mysqli_close($connection);
}

function getSearchDateRange($range) {
    $today = new DateTime();
    $startDate = clone $today;
    $endDate = clone $today;
    
    switch ($range) {
        case 'today': $endDate = clone $startDate; break;
        case 'tomorrow': $startDate->modify('+1 day'); $endDate = clone $startDate; break;
        case 'this_week': $endDate->modify('+7 days'); break;
        case 'next_week': $startDate->modify('+8 days'); $endDate->modify('+14 days'); break;
        case 'this_month': $endDate = new DateTime('last day of this month'); break;
        case 'next_month': $startDate = new DateTime('first day of next month'); $endDate = new DateTime('last day of next month'); break;
        default: $endDate->modify('+7 days');
    }
    
    return [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')];
}

function getExistingEvents($connection, $startDate, $endDate) {
    $events = [];
    $query = "SELECT id, title, start_date, start_time, end_date, end_time FROM table_event WHERE start_date BETWEEN ? AND ? ORDER BY start_date, start_time";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $events[] = ['id' => $row['id'], 'title' => $row['title'], 'start' => $row['start_date'] . ' ' . $row['start_time'], 'end' => $row['end_date'] . ' ' . $row['end_time']];
    }
    
    mysqli_stmt_close($stmt);
    return $events;
}

function generateTimeSlots($startDate, $endDate, $duration, $preferredStart, $preferredEnd) {
    $slots = [];
    $currentDate = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    
    while ($currentDate <= $endDateTime) {
        for ($hour = $preferredStart; $hour < $preferredEnd; $hour++) {
            $slotStart = clone $currentDate;
            $slotStart->setTime($hour, 0, 0);
            $slotEnd = clone $slotStart;
            $slotEnd->modify('+' . ($duration * 60) . ' minutes');
            $slotEndHour = (int)$slotEnd->format('H');
            
            if ($slotEndHour <= $preferredEnd || $preferredEnd == 24) {
                $slots[] = ['start' => $slotStart->format('Y-m-d H:i:s'), 'end' => $slotEnd->format('Y-m-d H:i:s')];
            }
        }
        $currentDate->modify('+1 day');
    }
    
    return $slots;
}

function filterAvailableSlots($potentialSlots, $existingEvents) {
    $availableSlots = [];
    
    foreach ($potentialSlots as $slot) {
        $slotStart = strtotime($slot['start']);
        $slotEnd = strtotime($slot['end']);
        $hasConflict = false;
        
        foreach ($existingEvents as $event) {
            $eventStart = strtotime($event['start']);
            $eventEnd = strtotime($event['end']);
            
            if ($slotStart < $eventEnd && $slotEnd > $eventStart) {
                $hasConflict = true;
                break;
            }
        }
        
        if (!$hasConflict) {
            $availableSlots[] = $slot;
        }
    }
    
    return $availableSlots;
}

function rankSlots($slots, $preferredStart, $preferredEnd) {
    $rankedSlots = [];
    $now = time();
    
    foreach ($slots as $slot) {
        $startTime = strtotime($slot['start']);
        $startHour = (int)date('H', $startTime);
        $daysDiff = ($startTime - $now) / 86400;
        
        $quality = 'excellent';
        $reason = '';
        $idealStart = $preferredStart + 1;
        $idealEnd = $preferredEnd - 2;
        
        if ($startHour >= $idealStart && $startHour <= $idealEnd) {
            $quality = 'excellent';
            $reason = '⭐ Perfect timing - within your preferred hours';
        } elseif ($startHour >= $preferredStart && $startHour < $preferredEnd) {
            $quality = 'good';
            $reason = '👍 Good timing - within your available hours';
        } else {
            $quality = 'fair';
            $reason = '✓ Available - outside preferred hours but works';
        }
        
        if ($daysDiff < 1) {
            $reason .= ' • Today';
        } elseif ($daysDiff < 2) {
            $reason .= ' • Tomorrow';
        } else {
            $reason .= ' • ' . date('l', $startTime);
        }
        
        $rankedSlots[] = [
            'start_datetime' => $slot['start'],
            'end_datetime' => $slot['end'],
            'quality' => $quality,
            'reason' => $reason,
            'score' => ($quality === 'excellent' ? 100 : ($quality === 'good' ? 75 : 50)) - $daysDiff
        ];
    }
    
    usort($rankedSlots, function($a, $b) { return $b['score'] - $a['score']; });
    return $rankedSlots;
}
