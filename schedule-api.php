<?php
// Path to JSON file that stores the schedule
define('SCHEDULE_FILE', 'schedule.json');

// Allow CORS (optional)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Handle preflight requests for DELETE or POST if needed
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    exit(0);
}

// Load schedule from JSON file or create default if missing
function loadSchedule() {
    if (!file_exists(SCHEDULE_FILE)) {
        // Default schedule array
        $default = [
            ["time" => "07:30", "end" => "09:00 am", "subject" => "EVD", "teacher" => "Mr. Jade Louis Cabucos"],
            ["time" => "09:00", "end" => "10:30 am", "subject" => "IAS", "teacher" => "Mr. Jade Louis Cabucos"],
            ["time" => "10:30", "end" => "12:00 pm", "subject" => "SIA", "teacher" => "Mr. Yestin Roy Prado"],
            ["time" => "12:01", "end" => "14:59 pm", "subject" => "Vacant Time", "teacher" => ""],
            ["time" => "15:00", "end" => "16:30 pm", "subject" => "NET", "teacher" => "Ms. Gladymay Sadorra"],
            ["time" => "16:30", "end" => "18:00 pm", "subject" => "SPI", "teacher" => "Mr. Yestin Roy Prado"],
            ["time" => "18:00", "end" => "19:30 pm", "subject" => "ELEC", "teacher" => "Ms. En Catarungan"],
            ["time" => "19:30", "end" => "21:00 pm", "subject" => "FREE ELEC 1", "teacher" => "Mr. Jade Louis Cabucos"],
        ];
        file_put_contents(SCHEDULE_FILE, json_encode($default, JSON_PRETTY_PRINT));
        return $default;
    }

    $json = file_get_contents(SCHEDULE_FILE);
    $data = json_decode($json, true);
    return $data ?: [];
}

// Save schedule back to JSON file
function saveSchedule($schedule) {
    file_put_contents(SCHEDULE_FILE, json_encode($schedule, JSON_PRETTY_PRINT));
}

// Helper to send JSON response and exit
function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Load schedule
$schedule = loadSchedule();

// Map schedule by 'time' for easier lookup
$scheduleMap = [];
foreach ($schedule as $entry) {
    $scheduleMap[$entry['time']] = $entry;
}

// Handle HTTP request method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // If 'time' param provided, return specific entry
    if (isset($_GET['time'])) {
        $time = $_GET['time'];
        if (isset($scheduleMap[$time])) {
            respond($scheduleMap[$time]);
        } else {
            respond(["error" => "Schedule not found for time $time"], 404);
        }
    } else {
        // Return all entries
        respond(array_values($scheduleMap));
    }
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['time'], $input['end'], $input['subject'], $input['teacher'])) {
        respond(["error" => "Invalid input, required: time, end, subject, teacher"], 400);
    }

    $time = $input['time'];
    if (isset($scheduleMap[$time])) {
        respond(["error" => "Schedule already exists for time $time"], 409);
    }

    // Add new entry
    $newEntry = [
        "time" => $input['time'],
        "end" => $input['end'],
        "subject" => $input['subject'],
        "teacher" => $input['teacher']
    ];

    $schedule[] = $newEntry;
    saveSchedule($schedule);

    respond($newEntry, 201);
}

if ($method === 'DELETE') {
    if (!isset($_GET['time'])) {
        respond(["error" => "Missing 'time' parameter for deletion"], 400);
    }

    $time = $_GET['time'];
    if (!isset($scheduleMap[$time])) {
        respond(["error" => "Schedule not found for time $time"], 404);
    }

    // Remove entry by time
    $schedule = array_filter($schedule, fn($entry) => $entry['time'] !== $time);
    // Re-index array to keep numeric keys clean
    $schedule = array_values($schedule);
    saveSchedule($schedule);

    respond(["message" => "Schedule deleted for time $time"]);
}

// Method not allowed
respond(["error" => "Method not allowed"], 405);
