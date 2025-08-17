<?php
header('Content-Type: application/json');

include "../../includes/session_manager.php";
requireParent(); // Ensure only parents can access this

include "../../connection/conn.php";
$conn = conn();

if (!isset($_GET['child_id']) || !is_numeric($_GET['child_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid child ID']);
    exit;
}

$child_id = (int)$_GET['child_id'];
$parent_user_id = getCurrentUserId();

// Verify that this parent has access to this child
$access_check = "SELECT COUNT(*) as count FROM parent_child_relationships 
                 WHERE parent_user_id = ? AND student_id = ?";
$stmt = mysqli_prepare($conn, $access_check);
mysqli_stmt_bind_param($stmt, "ii", $parent_user_id, $child_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$access = mysqli_fetch_assoc($result);

if ($access['count'] == 0) {
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// Get child information
$child_sql = "SELECT * FROM students_tbl WHERE id = ?";
$stmt = mysqli_prepare($conn, $child_sql);
mysqli_stmt_bind_param($stmt, "i", $child_id);
mysqli_stmt_execute($stmt);
$child_result = mysqli_stmt_get_result($stmt);
$child = mysqli_fetch_assoc($child_result);

if (!$child) {
    echo json_encode(['success' => false, 'message' => 'Child not found']);
    exit;
}

// Get recent grades
$grades_sql = "SELECT * FROM grades_tbl WHERE student_id = ? ORDER BY date_recorded DESC LIMIT 10";
$stmt = mysqli_prepare($conn, $grades_sql);
mysqli_stmt_bind_param($stmt, "i", $child_id);
mysqli_stmt_execute($stmt);
$grades_result = mysqli_stmt_get_result($stmt);

// Get recent attendance
$attendance_sql = "SELECT * FROM attendance_tbl WHERE LRN = ? ORDER BY date DESC LIMIT 10";
$stmt = mysqli_prepare($conn, $attendance_sql);
mysqli_stmt_bind_param($stmt, "s", $child['LRN']);
mysqli_stmt_execute($stmt);
$attendance_result = mysqli_stmt_get_result($stmt);

// Get recent anecdotal records
$records_sql = "SELECT * FROM anecdotal_records_tbl WHERE student_id = ? ORDER BY date_recorded DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $records_sql);
mysqli_stmt_bind_param($stmt, "i", $child_id);
mysqli_stmt_execute($stmt);
$records_result = mysqli_stmt_get_result($stmt);

// Build HTML content
ob_start();
?>

<div class="space-y-6">
    <!-- Child Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h4 class="font-medium text-gray-900 mb-2">Student Information</h4>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-600">Full Name:</span> 
                <span class="font-medium"><?= htmlspecialchars($child['Fname'] . ' ' . $child['Mname'] . ' ' . $child['Lname']) ?></span>
            </div>
            <div>
                <span class="text-gray-600">Grade Level:</span> 
                <span class="font-medium"><?= htmlspecialchars($child['GLevel']) ?></span>
            </div>
            <div>
                <span class="text-gray-600">LRN:</span> 
                <span class="font-medium"><?= htmlspecialchars($child['LRN']) ?></span>
            </div>
            <div>
                <span class="text-gray-600">Course:</span> 
                <span class="font-medium"><?= htmlspecialchars($child['Course']) ?></span>
            </div>
        </div>
    </div>

    <!-- Recent Grades -->
    <div>
        <h4 class="font-medium text-gray-900 mb-3">Recent Grades</h4>
        <?php if (mysqli_num_rows($grades_result) > 0): ?>
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Assessment</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($grade = mysqli_fetch_assoc($grades_result)): ?>
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($grade['subject']) ?></td>
                                <td class="px-4 py-2 text-sm text-gray-900"><?= htmlspecialchars($grade['assessment_name']) ?></td>
                                <td class="px-4 py-2 text-sm font-medium"><?= htmlspecialchars($grade['grade_value']) ?>/<?= htmlspecialchars($grade['max_points']) ?></td>
                                <td class="px-4 py-2 text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $grade['grade_status'] == 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= htmlspecialchars($grade['grade_status']) ?>
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900"><?= date('M j, Y', strtotime($grade['date_recorded'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-sm">No grades recorded yet.</p>
        <?php endif; ?>
    </div>

    <!-- Recent Attendance -->
    <div>
        <h4 class="font-medium text-gray-900 mb-3">Recent Attendance</h4>
        <?php if (mysqli_num_rows($attendance_result) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <?php while ($attendance = mysqli_fetch_assoc($attendance_result)): ?>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-sm text-gray-900"><?= date('M j, Y', strtotime($attendance['date'])) ?></span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                            <?= $attendance['attendance'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $attendance['attendance'] == 1 ? 'Present' : 'Absent' ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-sm">No attendance records found.</p>
        <?php endif; ?>
    </div>

    <!-- Recent Anecdotal Records -->
    <div>
        <h4 class="font-medium text-gray-900 mb-3">Recent Observations</h4>
        <?php if (mysqli_num_rows($records_result) > 0): ?>
            <div class="space-y-3">
                <?php while ($record = mysqli_fetch_assoc($records_result)): ?>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h5 class="font-medium text-gray-900"><?= htmlspecialchars($record['observation_title']) ?></h5>
                            <span class="text-xs text-gray-500"><?= date('M j, Y', strtotime($record['date_recorded'])) ?></span>
                        </div>
                        <p class="text-sm text-gray-700 mb-2"><?= htmlspecialchars($record['observation_details']) ?></p>
                        <div class="flex justify-between items-center">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                <?= $record['record_type'] == 'Behavioral' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' ?>">
                                <?= htmlspecialchars($record['record_type']) ?>
                            </span>
                            <span class="text-xs text-gray-500">Severity: <?= htmlspecialchars($record['severity_level']) ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-sm">No observations recorded.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$html_content = ob_get_clean();

echo json_encode([
    'success' => true,
    'child' => [
        'id' => $child['id'],
        'name' => $child['Fname'] . ' ' . $child['Lname']
    ],
    'html' => $html_content
]);

mysqli_close($conn);
?>