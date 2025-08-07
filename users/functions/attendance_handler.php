<?php
// functions/attendance_handler.php

header('Content-Type: application/json');

// Try different paths for the attendance functions
if (file_exists("attendance_functions.php")) {
    include_once "attendance_functions.php";
} elseif (file_exists("functions/attendance_functions.php")) {
    include_once "functions/attendance_functions.php";
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'submit_attendance':
        handleSubmitAttendance();
        break;
    case 'get_stats':
        handleGetStats();
        break;
    case 'get_attendance_data':
        handleGetAttendanceData();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

function handleSubmitAttendance()
{
    $attendanceData = $_POST['attendance_data'] ?? '[]';
    $date = $_POST['date'] ?? date('Y-m-d');

    // Decode JSON string to array
    $attendanceArray = json_decode($attendanceData, true);

    if (empty($attendanceArray) || !is_array($attendanceArray)) {
        echo json_encode(['success' => false, 'message' => 'No attendance data provided or invalid format']);
        return;
    }

    // Parse attendance data
    $processedData = [];
    foreach ($attendanceArray as $item) {
        $processedData[] = [
            'studentName' => $item['name'],
            'LRN' => $item['lrn'],
            'attendance' => (int) $item['status'] // 1 for present, 0 for absent
        ];
    }

    $success = bulkInsertAttendance($processedData, $date);

    if ($success) {
        $stats = getAttendanceStats($date);
        echo json_encode([
            'success' => true,
            'message' => 'Attendance saved successfully',
            'stats' => $stats
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save attendance']);
    }
}

function handleGetStats()
{
    $date = $_POST['date'] ?? date('Y-m-d');
    $stats = getAttendanceStats($date);

    echo json_encode([
        'success' => true,
        'stats' => $stats
    ]);
}

function handleGetAttendanceData()
{
    $date = $_POST['date'] ?? date('Y-m-d');

    $presentStudents = getPresentStudents($date);
    $absentStudents = getAbsentStudents($date);
    $stats = getAttendanceStats($date);

    echo json_encode([
        'success' => true,
        'present' => $presentStudents,
        'absent' => $absentStudents,
        'stats' => $stats
    ]);
}
?>