<?php
include('connection.php');

// Sm    // Fetch existing events in the date range
    $query = "SELECT * FROM table_event WHERE DATE(start_date) >= ? AND DATE(start_date) <= ? ORDER BY start_date ASC";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ss", $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);ggestions API endpoint
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $eventName = $input['eventName'] ?? '';
    $duration = (int)($input['duration'] ?? 60); // duration in minutes
    $timeRange = $input['timeRange'] ?? 'week'; // 'day', 'week', 'month'
    $preferredStartHour = (int)($input['preferredStartHour'] ?? 9);
    $preferredEndHour = (int)($input['preferredEndHour'] ?? 17);
    
    $suggestions = findBestTimeSlots($connection, $eventName, $duration, $timeRange, $preferredStartHour, $preferredEndHour);
    
    header('Content-Type: application/json');
    echo json_encode($suggestions);
    exit;
}

function findBestTimeSlots($connection, $eventName, $duration, $timeRange, $preferredStartHour, $preferredEndHour) {
    // Get date range based on timeRange parameter
    $startDate = new DateTime();
    $endDate = new DateTime();
    
    switch ($timeRange) {
        case 'day':
            $endDate->modify('+1 day');
            break;
        case 'week':
            $endDate->modify('+7 days');
            break;
        case 'month':
            $endDate->modify('+30 days');
            break;
        default:
            $endDate->modify('+7 days');
    }
    
    // Fetch existing events in the date range
    $query = "SELECT * FROM table_event WHERE DATE(start_date) >= ? AND DATE(start_date) <= ? ORDER BY start_date";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "ss", $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $existingEvents = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $existingEvents[] = $row;
    }
    
    // Generate time slots and find best options
    $suggestions = [];
    $currentDate = clone $startDate;
    
    while ($currentDate <= $endDate && count($suggestions) < 5) {
        // Skip weekends for work-related events (optional logic)
        $dayOfWeek = $currentDate->format('N');
        if ($dayOfWeek >= 6) { // Saturday = 6, Sunday = 7
            $currentDate->modify('+1 day');
            continue;
        }
        
        $dailySuggestions = findDailyTimeSlots(
            $currentDate, 
            $existingEvents, 
            $duration, 
            $preferredStartHour, 
            $preferredEndHour
        );
        
        $suggestions = array_merge($suggestions, $dailySuggestions);
        $currentDate->modify('+1 day');
    }
    
    // Score and sort suggestions
    $scoredSuggestions = [];
    foreach ($suggestions as $suggestion) {
        $score = calculateTimeSlotScore($suggestion, $eventName, $preferredStartHour, $preferredEndHour);
        $suggestion['score'] = $score;
        $suggestion['confidence'] = min(100, max(0, $score));
        $scoredSuggestions[] = $suggestion;
    }
    
    // Sort by score (highest first) and return top 5
    usort($scoredSuggestions, function($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    
    return array_slice($scoredSuggestions, 0, 5);
}

function findDailyTimeSlots($date, $existingEvents, $duration, $preferredStartHour, $preferredEndHour) {
    $suggestions = [];
    $dateStr = $date->format('Y-m-d');
    
    // Get events for this specific date
    $dayEvents = array_filter($existingEvents, function($event) use ($dateStr) {
        return date('Y-m-d', strtotime($event['start_date'])) === $dateStr;
    });
    
    // Convert duration from minutes to hours for easier calculation
    $durationHours = $duration / 60;
    
    // Generate potential time slots (every 30 minutes)
    for ($hour = $preferredStartHour; $hour <= $preferredEndHour - $durationHours; $hour += 0.5) {
        $startTime = sprintf('%02d:%02d', floor($hour), ($hour - floor($hour)) * 60);
        $endHour = $hour + $durationHours;
        $endTime = sprintf('%02d:%02d', floor($endHour), ($endHour - floor($endHour)) * 60);
        
        // Check if this slot conflicts with existing events
        $hasConflict = false;
        foreach ($dayEvents as $event) {
            // Extract time from datetime if available
            $eventStartTime = '';
            $eventEndTime = '';
            
            if (isset($event['start_time']) && isset($event['end_time'])) {
                // If separate time fields exist
                $eventStartTime = $event['start_time'];
                $eventEndTime = $event['end_time'];
            } else {
                // Extract time from datetime field
                $eventStartTime = date('H:i', strtotime($event['start_date']));
                $eventEndTime = date('H:i', strtotime($event['end_date']));
            }
            
            // Check for time overlap
            if ($eventStartTime && $eventEndTime) {
                $slotStart = strtotime($startTime);
                $slotEnd = strtotime($endTime);
                $eventStart = strtotime($eventStartTime);
                $eventEnd = strtotime($eventEndTime);
                
                // Check if time slots overlap
                if (($slotStart < $eventEnd) && ($slotEnd > $eventStart)) {
                    $hasConflict = true;
                    break;
                }
            }
        }
        
        if (!$hasConflict) {
            $suggestions[] = [
                'date' => $dateStr,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'dayOfWeek' => $date->format('l'),
                'conflicts' => 0
            ];
        }
    }
    
    return $suggestions;
}

function calculateTimeSlotScore($timeSlot, $eventName, $preferredStartHour, $preferredEndHour) {
    $score = 50; // Base score
    
    // Time preference scoring
    $startHour = (float)str_replace(':', '.', str_replace(':', '', substr($timeSlot['startTime'], 0, 2) . '.' . substr($timeSlot['startTime'], 3, 2)));
    
    // Prefer times closer to preferred range
    if ($startHour >= $preferredStartHour && $startHour <= $preferredEndHour) {
        $score += 30;
    }
    
    // Prefer mid-morning and early afternoon
    if ($startHour >= 9 && $startHour <= 11) {
        $score += 20; // Morning boost
    } elseif ($startHour >= 14 && $startHour <= 16) {
        $score += 15; // Afternoon boost
    }
    
    // Day of week preferences
    $dayOfWeek = date('N', strtotime($timeSlot['date']));
    if ($dayOfWeek >= 2 && $dayOfWeek <= 4) { // Tuesday to Thursday
        $score += 10;
    }
    
    // Event name analysis for time preferences
    $eventNameLower = strtolower($eventName);
    if (strpos($eventNameLower, 'meeting') !== false || strpos($eventNameLower, 'call') !== false) {
        // Meetings prefer mid-morning or early afternoon
        if ($startHour >= 10 && $startHour <= 11 || $startHour >= 14 && $startHour <= 15) {
            $score += 15;
        }
    } elseif (strpos($eventNameLower, 'lunch') !== false) {
        // Lunch events prefer lunch time
        if ($startHour >= 12 && $startHour <= 13) {
            $score += 25;
        }
    } elseif (strpos($eventNameLower, 'workout') !== false || strpos($eventNameLower, 'gym') !== false) {
        // Workout events prefer early morning or evening
        if ($startHour <= 8 || $startHour >= 17) {
            $score += 20;
        }
    }
    
    return $score;
}

// AI-powered event categorization and smart suggestions
function getEventCategory($eventName) {
    $eventNameLower = strtolower($eventName);
    
    $categories = [
        'meeting' => ['meeting', 'call', 'conference', 'discussion', 'standup', 'sync'],
        'work' => ['work', 'project', 'task', 'deadline', 'presentation', 'review'],
        'personal' => ['lunch', 'break', 'personal', 'appointment', 'errands'],
        'fitness' => ['workout', 'gym', 'exercise', 'run', 'sport', 'fitness'],
        'social' => ['party', 'dinner', 'hangout', 'social', 'drinks', 'celebration'],
        'learning' => ['study', 'course', 'training', 'workshop', 'seminar', 'class']
    ];
    
    foreach ($categories as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (strpos($eventNameLower, $keyword) !== false) {
                return $category;
            }
        }
    }
    
    return 'general';
}

?>
