<?php
// Add Behavior Record Endpoint
header('Content-Type: application/json');

// Include the behavior functions
include 'behavior_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
    exit;
}

// Get form data
$lrn = $_POST['behaviorLRN'] ?? '';
$behavior_type = $_POST['behaviorType'] ?? '';
$behavior_category = $_POST['behaviorCategory'] ?? 'General Observation';
$severity_level = $_POST['severityLevel'] ?? 'Low';
$remarks = $_POST['behaviorRemarks'] ?? '';
$follow_up_required = isset($_POST['followUpRequired']) ? 1 : 0;
$follow_up_notes = $_POST['followUpNotes'] ?? '';

// Validate required fields
if (empty($lrn) || empty($behavior_type)) {
    echo json_encode(['success' => false, 'message' => 'LRN and Behavior Type are required fields.']);
    exit;
}

// Validate LRN format
if (!is_numeric($lrn) || strlen($lrn) !== 12) {
    echo json_encode(['success' => false, 'message' => 'LRN must be exactly 12 digits.']);
    exit;
}

// Add the behavior record
$result = addBehaviorRecord(
    $lrn,
    $behavior_type,
    $behavior_category,
    $severity_level,
    $remarks,
    $follow_up_required,
    $follow_up_notes
);

echo json_encode($result);
?>