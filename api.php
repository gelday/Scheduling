<?php
header('Content-Type: application/json');

include 'schedule.php';

if (!isset($_GET['time'])) {
    echo json_encode(['error' => 'Please provide a time in HH:MM format (12-hour)']);
    exit;
}

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
    echo json_encode(['subject' => 'NO CLASS AT THE MOMENT']);
}
