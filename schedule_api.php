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
            ["time" => "07:30", "end" => "09:00", "subject" => "IT EVD 31", "teacher" => "Mr. Jade Louis S. Cabucos"],
            ["time" => "09:01", "end" => "10:30", "subject" => "IT IAS 31", "teacher" => "Mr. Jade Louis S. Cabucos"],
            ["time" => "10:31", "end" => "12:00", "subject" => "ITSIA 31", "teacher" => "Mr. Yestin Roy A. Prado"],
            ["time" => "12:01", "end" => "14:59", "subject" => "VACANT TIME", "teacher" => "NONE"],
            ["time" => "15:00", "end" => "16:30", "subject" => "IT NET 31", "teacher" => "Ms. Gladymay S. Sadorra"],
            ["time" => "16:31", "end" => "18:00", "subject" => "IT SPI 31", "teacher" => "Mr. Yestin Roy A. Prado"],
            ["time" => "18:01", "end" => "19:30", "subject" => "IT ELEC 1", "teacher" => "Ms. En Catarungan"],
            ["time" => "19:31", "end" => "21:00", "subject" => "FREE ELEC 1", "teacher" => "Mr. Jade Louis S. Cabucos"],
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
    if (isset($_GET['time'])) {
        // Handle request for specific time
        $inputTime = $_GET['time'];
        $inputTimestamp = strtotime($inputTime);
        $found = false;

        foreach ($schedule as $entry) {
            $start = strtotime($entry['time']);
            $end = strtotime($entry['end']);

            if ($inputTimestamp >= $start && $inputTimestamp <= $end) {
                echo json_encode([
                    'subject' => $entry['subject'],
                    'teacher' => $entry['teacher'],
                    'time' => $entry['time'] . " - " . $entry['end']
                ]);
                $found = true;
                break;
            }
        }

        if (!$found) {
            echo json_encode(['subject' => 'No class at the moment']);
        }
    } else {
        // If no 'time' query parameter is provided, return the full schedule
        echo json_encode($schedule);
    }
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['time'], $input['end'], $input['subject'], $input['teacher'])) {
        respond(["error" => "Invalid input, required: time, end, subject, teacher"], 400);
    }

    $time = $input['time'];
    $end = $input['end'];
    $inputTimestampStart = strtotime($time);
    $inputTimestampEnd = strtotime($end);

    // Check if the time range overlaps with any existing schedule
    foreach ($schedule as $entry) {
        $entryTimestampStart = strtotime($entry['time']);
        $entryTimestampEnd = strtotime($entry['end']);

        // If there is any overlap, return an error
        if (($inputTimestampStart < $entryTimestampEnd) && ($inputTimestampEnd > $entryTimestampStart)) {
            respond(["error" => "Schedule already exists for this time range"], 409);
        }
    }

    // If no overlap, proceed to add the new entry
    $newEntry = [
        "time" => $time,
        "end" => $end,
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
