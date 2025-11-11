<?php
/**
 * SQLite Database Connection
 * This file-based database makes your calendar portable across any device
 * No MySQL/phpMyAdmin setup required!
 */

// Database file path - stored in same directory as your code
$db_file = __DIR__ . '/onlyplans.db';

try {
    // Create SQLite connection
    $sqlite_connection = new PDO('sqlite:' . $db_file);
    
    // Set error mode to exceptions
    $sqlite_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if it doesn't exist (auto-setup on first run)
    $sqlite_connection->exec("
        CREATE TABLE IF NOT EXISTS table_event (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            start_date DATE NOT NULL,
            start_time TIME NOT NULL,
            end_date DATE NOT NULL,
            end_time TIME NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            color VARCHAR(7) DEFAULT '#3788d8'
        )
    ");
    
    echo "<!-- SQLite Database Connected Successfully -->\n";
    
} catch(PDOException $e) {
    die("SQLite Connection Failed: " . $e->getMessage());
}

/**
 * Helper function to convert SQLite PDO to mysqli-style for existing code
 * This allows you to keep most of your existing code unchanged
 */
function sqlite_query($query) {
    global $sqlite_connection;
    
    try {
        $result = $sqlite_connection->query($query);
        return $result;
    } catch(PDOException $e) {
        error_log("SQLite Query Error: " . $e->getMessage());
        return false;
    }
}

function sqlite_fetch_assoc($result) {
    if ($result) {
        return $result->fetch(PDO::FETCH_ASSOC);
    }
    return false;
}

function sqlite_escape_string($string) {
    global $sqlite_connection;
    return $sqlite_connection->quote($string);
}
?>
