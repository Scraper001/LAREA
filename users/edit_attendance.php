<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/attendance_functions.php" ?>

<?php
// Get the attendance record ID
$attendance_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$return_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

if (!$attendance_id) {
    // Redirect if no ID provided
    header('Location: attendance_report.php');
    exit;
}

// Get the attendance record
$conn = conn();
$query = "SELECT a.*, s.Fname, s.Mname, s.Lname, s.LRN, s.GLevel, s.photo_path
          FROM attendance a
          JOIN students_tbl s ON a.student_id = s.id
          WHERE a.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $attendance_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Redirect if record not found
    header('Location: attendance_report.php');
    exit;
}

$record = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_attendance'])) {
        $status = $_POST['status'];
        $time_in = !empty($_POST['time_in']) ? $_POST['time_in'] : null;
        $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] : null;
        $remarks = !empty($_POST['remarks']) ? $_POST['remarks'] : null;
        $recorded_by = 1; // Replace with actual user ID
        
        // Update the record
        $update_query = "UPDATE attendance SET 
            status = ?,
            time_in = ?,
            time_out = ?,
            hours_worked = IF(? IS NOT NULL AND ? IS NOT NULL, TIMESTAMPDIFF(MINUTE, ?, ?)/60, NULL),
            remarks = ?,
            recorded_by = ?
            WHERE id = ?";
        
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("sssssssii", $status, $time_in, $time_out, $time_in, $time_out, $time_in, $time_out, $remarks, $recorded_by, $attendance_id);
        
        if ($update_stmt->execute()) {
            $alert_status = 'success';
            $alert_message = 'Attendance record updated successfully!';
        } else {
            $alert_status = 'error';
            $alert_message = 'Error updating attendance record: ' . $conn->error;
        }
        
        // Redirect back to report page
        header("Location: attendance_report.php?date=" . $return_date . "&status=" . $alert_status . "&message=" . urlencode($alert_message));
        exit;
    }
}
?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <div class="p-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-bold">Edit Attendance</h1>
            <a href="attendance_report.php?date=<?php echo $return_date; ?>" 
               class="bg-gray-200 text-gray-700 py-2 px-4 rounded">
                Back
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <!-- Student Info -->
            <div class="flex items-center mb-4">
                <?php if ($record['photo_path']): ?>
                <div class="w-16 h-16 rounded-full overflow-hidden mr-4">
                    <img src="../<?php echo $record['photo_path']; ?>" alt="" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                    <span class="text-gray-600 font-bold text-xl">
                        <?php echo substr($record['Fname'], 0, 1) . substr($record['Lname'], 0, 1); ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <div>
                    <h2 class="text-lg font-bold"><?php echo $record['Fname'] . ' ' . $record['Lname']; ?></h2>
                    <p class="text-sm text-gray-600">
                        <?php echo $record['GLevel']; ?> | LRN: <?php echo $record['LRN']; ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Date: <?php echo date('F d, Y', strtotime($record['date'])); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form method="post" class="bg-white rounded-lg shadow p-4">
            <input type="hidden" name="update_attendance" value="1">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center bg-green-50 p-3 rounded border">
                        <input type="radio" name="status" value="present" class="mr-2"
                              <?php echo $record['status'] === 'present' ? 'checked' : ''; ?>>
                        <span>Present</span>
                    </label>
                    <label class="flex items-center bg-red-50 p-3 rounded border">
                        <input type="radio" name="status" value="absent" class="mr-2"
                              <?php echo $record['status'] === 'absent' ? 'checked' : ''; ?>>
                        <span>Absent</span>
                    </label>
                    <label class="flex items-center bg-yellow-50 p-3 rounded border">
                        <input type="radio" name="status" value="late" class="mr-2"
                              <?php echo $record['status'] === 'late' ? 'checked' : ''; ?>>
                        <span>Late</span>
                    </label>
                    <label class="flex items-center bg-blue-50 p-3 rounded border">
                        <input type="radio" name="status" value="excused" class="mr-2"
                              <?php echo $record['status'] === 'excused' ? 'checked' : ''; ?>>
                        <span>Excused</span>
                    </label>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
                    <input type="time" name="time_in" value="<?php echo $record['time_in']; ?>" 
                           class="w-full border rounded py-2 px-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
                    <input type="time" name="time_out" value="<?php echo $record['time_out']; ?>" 
                           class="w-full border rounded py-2 px-3">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" class="w-full border rounded py-2 px-3 min-h-[80px]"><?php echo $record['remarks']; ?></textarea>
            </div>
            
            <div class="flex gap-3">
                <a href="attendance_report.php?date=<?php echo $return_date; ?>" 
                   class="flex-1 bg-gray-500 hover:bg-gray-600 text-white text-center py-3 px-4 rounded font-medium">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-main hover:bg-blue-700 text-white py-3 px-4 rounded font-medium">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</main>

<script>
// Show/hide time fields based on status
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const timeFields = document.querySelectorAll('input[name="time_in"], input[name="time_out"]');
        if (this.value === 'absent' || this.value === 'excused') {
            timeFields.forEach(field => {
                field.value = '';
                field.parentElement.style.opacity = '0.5';
            });
        } else {
            timeFields.forEach(field => {
                field.parentElement.style.opacity = '1';
            });
            // Don't set default times when editing - keep existing values
        }
    });
});

// Trigger the event for the selected radio button to apply the appropriate styling
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="status"]:checked');
    if (checkedRadio) {
        checkedRadio.dispatchEvent(new Event('change'));
    }
});
</script>

<?php include "../includes/footer.php" ?>