<?php
// Delete Behavior Record Endpoint
header('Content-Type: application/json');

// Include the behavior functions
include 'behavior_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
    exit;
}

// Get behavior ID
$behavior_id = $_POST['behaviorId'] ?? '';

// Validate required fields
if (empty($behavior_id)) {
    echo json_encode(['success' => false, 'message' => 'Behavior ID is required.']);
    exit;
}

// Validate behavior ID format
if (!is_numeric($behavior_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid behavior ID format.']);
    exit;
}

// Delete the behavior record
$result = deleteBehaviorRecord($behavior_id);

echo json_encode($result);
?>