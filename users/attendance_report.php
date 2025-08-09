<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/attendance_functions.php" ?>

<?php
// Default to today's date
$search_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get attendance records for the selected date
$conn = conn();
$query = "SELECT a.*, s.Fname, s.Lname, s.Mname, s.GLevel, s.LRN, s.photo_path 
          FROM attendance a 
          JOIN students_tbl s ON a.student_id = s.id 
          WHERE a.date = ?
          ORDER BY s.Lname, s.Fname";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $search_date);
$stmt->execute();
$attendance_records = $stmt->get_result();

// Get summary statistics for the day
$stats_query = "SELECT 
    SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
    SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
    SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
    SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count,
    COUNT(*) as total_records
    FROM attendance 
    WHERE date = ?";

$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("s", $search_date);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$stats = $stats_result->fetch_assoc();

// Handle delete request
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $delete_id = intval($_GET['id']);
    $delete_query = "DELETE FROM attendance WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        $alert_status = 'success';
        $alert_message = 'Attendance record deleted successfully';

        // Refresh data after delete
        $stmt->execute();
        $attendance_records = $stmt->get_result();
        $stats_stmt->execute();
        $stats_result = $stats_stmt->get_result();
        $stats = $stats_result->fetch_assoc();
    } else {
        $alert_status = 'error';
        $alert_message = 'Error deleting record: ' . $conn->error;
    }
}

// Initialize stats if none found
if (!$stats || $stats['total_records'] == 0) {
    $stats = [
        'present_count' => 0,
        'absent_count' => 0,
        'late_count' => 0,
        'excused_count' => 0,
        'total_records' => 0
    ];
}
?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <!-- Custom Alerts -->
    <div id="alert-container" class="fixed top-16 right-0 left-0 mx-auto z-50 max-w-md"></div>

    <div class="p-4">
        <!-- Header -->
        <div class="flex flex-col mb-4">
            <h1 class="text-xl font-bold text-gray-800">Attendance Report</h1>
            <p class="text-sm text-gray-600">View and edit attendance records</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-2 mb-4">
            <form method="get" class="flex-1">
                <input type="date" name="date" value="<?php echo $search_date; ?>"
                    class="w-full border rounded-l py-3 px-3">
                <button type="submit" class="bg-main text-white py-3 px-4 rounded-r -ml-1">
                    Search Date
                </button>
            </form>

            <a href="attendance.php" class="bg-gray-700 text-white flex items-center justify-center px-4 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </a>
        </div>

        <!-- Date Display -->
        <div class="bg-white rounded-lg shadow p-4 mb-4 text-center">
            <h2 class="text-lg font-bold">
                <?php echo date('l, F d, Y', strtotime($search_date)); ?>
            </h2>
            <p class="text-sm text-gray-600">
                Total Records: <?php echo $stats['total_records']; ?>
            </p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-white rounded-lg shadow p-3">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-green-100 mr-2">
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Present</p>
                        <p class="text-base font-bold text-gray-800"><?php echo $stats['present_count']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-3">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-red-100 mr-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Absent</p>
                        <p class="text-base font-bold text-gray-800"><?php echo $stats['absent_count']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-3">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-yellow-100 mr-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Late</p>
                        <p class="text-base font-bold text-gray-800"><?php echo $stats['late_count']; ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-3">
                <div class="flex items-center">
                    <div class="p-2 rounded-full bg-blue-100 mr-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Excused</p>
                        <p class="text-base font-bold text-gray-800"><?php echo $stats['excused_count']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Records -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-4 border-b">
                <h2 class="text-lg font-semibold">Attendance Records</h2>
            </div>

            <div class="divide-y">
                <?php if ($attendance_records->num_rows > 0): ?>
                    <?php while ($record = $attendance_records->fetch_assoc()): ?>
                        <div class="p-4">
                            <div class="flex items-center mb-3">
                                <?php if ($record['photo_path']): ?>
                                    <div class="w-12 h-12 rounded-full overflow-hidden mr-3">
                                        <img src="../<?php echo $record['photo_path']; ?>" alt=""
                                            class="w-full h-full object-cover">
                                    </div>
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                        <span class="text-gray-600 font-bold text-lg">
                                            <?php echo substr($record['Fname'], 0, 1) . substr($record['Lname'], 0, 1); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <div>
                                    <h3 class="font-bold"><?php echo $record['Fname'] . ' ' . $record['Lname']; ?></h3>
                                    <p class="text-xs text-gray-500">
                                        <?php echo $record['GLevel']; ?> | LRN: <?php echo $record['LRN']; ?>
                                    </p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <!-- Status Badge -->
                                <?php
                                $statusColors = [
                                    'present' => 'bg-green-100 text-green-800 border-green-200',
                                    'absent' => 'bg-red-100 text-red-800 border-red-200',
                                    'late' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                    'excused' => 'bg-blue-100 text-blue-800 border-blue-200'
                                ];
                                $statusColor = $statusColors[$record['status']] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                ?>
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm font-medium <?php echo $statusColor; ?> mb-2">
                                    <?php echo ucfirst($record['status']); ?>
                                </span>

                                <!-- Time Information -->
                                <div class="grid grid-cols-2 gap-2 text-sm mb-2">
                                    <?php if ($record['time_in']): ?>
                                        <div>
                                            <span class="font-medium">Time In:</span>
                                            <span
                                                class="text-gray-700"><?php echo date('h:i A', strtotime($record['time_in'])); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($record['time_out']): ?>
                                        <div>
                                            <span class="font-medium">Time Out:</span>
                                            <span
                                                class="text-gray-700"><?php echo date('h:i A', strtotime($record['time_out'])); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($record['hours_worked']): ?>
                                        <div>
                                            <span class="font-medium">Hours:</span>
                                            <span
                                                class="text-gray-700"><?php echo number_format($record['hours_worked'], 2); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Remarks -->
                                <?php if ($record['remarks']): ?>
                                    <div class="text-sm mb-2">
                                        <span class="font-medium">Remarks:</span>
                                        <span class="text-gray-700"><?php echo $record['remarks']; ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="edit_attendance.php?id=<?php echo $record['id']; ?>&date=<?php echo $search_date; ?>"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded">
                                    Edit
                                </a>
                                <a href="?date=<?php echo $search_date; ?>&delete=1&id=<?php echo $record['id']; ?>"
                                    onclick="return confirm('Are you sure you want to delete this record?')"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white text-center py-2 px-4 rounded">
                                    Delete
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <p class="text-gray-500">No attendance records found for this date.</p>
                        <a href="attendance.php?date=<?php echo $search_date; ?>"
                            class="mt-4 inline-block bg-main hover:bg-blue-700 text-white py-2 px-4 rounded">
                            Record Attendance
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script>
    // Custom alert function
    function showAlert(message, type = 'success') {
        const alertContainer = document.getElementById('alert-container');
        const alertDiv = document.createElement('div');

        // Set class based on alert type
        let bgColor, textColor;
        if (type === 'success') {
            bgColor = 'bg-green-100 border-green-500';
            textColor = 'text-green-700';
        } else if (type === 'error') {
            bgColor = 'bg-red-100 border-red-500';
            textColor = 'text-red-700';
        } else if (type === 'warning') {
            bgColor = 'bg-yellow-100 border-yellow-500';
            textColor = 'text-yellow-700';
        } else {
            bgColor = 'bg-blue-100 border-blue-500';
            textColor = 'text-blue-700';
        }

        alertDiv.className = `${bgColor} border-l-4 p-4 mb-4 rounded shadow-md ${textColor}`;
        alertDiv.innerHTML = `
        <div class="flex items-center">
            <div class="py-1">
                <svg class="w-6 h-6 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' : ''}
                    ${type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' : ''}
                    ${type === 'warning' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>' : ''}
                    ${type === 'info' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>' : ''}
                </svg>
            </div>
            <div>
                <p class="font-bold">${message}</p>
            </div>
            <button class="ml-auto" onclick="this.parentElement.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

        alertContainer.appendChild(alertDiv);

        // Automatically remove after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Show alert if there's a message
    <?php if (isset($alert_status) && isset($alert_message)): ?>
        document.addEventListener('DOMContentLoaded', function () {
            showAlert('<?php echo $alert_message; ?>', '<?php echo $alert_status; ?>');
        });
    <?php endif; ?>
</script>

<?php include "../includes/footer.php" ?>