<?php
// Edit Behavior Record Endpoint
header('Content-Type: application/json');

// Include the behavior functions
include 'behavior_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
    exit;
}

// Get form data
$behavior_id = $_POST['behaviorId'] ?? '';
$lrn = $_POST['behaviorLRN'] ?? '';
$behavior_type = $_POST['behaviorType'] ?? '';
$behavior_category = $_POST['behaviorCategory'] ?? 'General Observation';
$severity_level = $_POST['severityLevel'] ?? 'Low';
$remarks = $_POST['behaviorRemarks'] ?? '';
$follow_up_required = isset($_POST['followUpRequired']) ? 1 : 0;
$follow_up_notes = $_POST['followUpNotes'] ?? '';
$status = $_POST['status'] ?? 'Active';

// Validate required fields
if (empty($behavior_id) || empty($lrn) || empty($behavior_type)) {
    echo json_encode(['success' => false, 'message' => 'Behavior ID, LRN, and Behavior Type are required fields.']);
    exit;
}

// Validate LRN format
if (!is_numeric($lrn) || strlen($lrn) !== 12) {
    echo json_encode(['success' => false, 'message' => 'LRN must be exactly 12 digits.']);
    exit;
}

// Validate behavior ID
if (!is_numeric($behavior_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid behavior ID.']);
    exit;
}

// Update the behavior record
$result = updateBehaviorRecord(
    $behavior_id,
    $lrn,
    $behavior_type,
    $behavior_category,
    $severity_level,
    $remarks,
    $follow_up_required,
    $follow_up_notes,
    $status
);

echo json_encode($result);
?>