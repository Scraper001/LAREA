<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/select_users.php" ?>
<?php include "functions/attendance_functions.php" ?>

<?php
// Set the date (default to today)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Get students for attendance
$conn = conn();
$query = "SELECT * FROM students_tbl ORDER BY Lname, Fname";
$result = $conn->query($query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['record_attendance']) && isset($_POST['student_ids'])) {
        $student_ids = $_POST['student_ids'];
        $status = $_POST['status'];
        $time_in = !empty($_POST['time_in']) ? $_POST['time_in'] : null;
        $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] : null;
        $remarks = !empty($_POST['remarks']) ? $_POST['remarks'] : null;
        $recorded_by = 1; // Replace with actual user ID
        $success_count = 0;
        $error_message = '';

        foreach ($student_ids as $student_id) {
            $result = recordAttendance($student_id, $selected_date, $status, $time_in, $time_out, $remarks, $recorded_by);
            if ($result['success']) {
                $success_count++;
            } else {
                $error_message = $result['message'];
            }
        }

        // Set alert message
        if ($success_count > 0) {
            $alert_status = 'success';
            $alert_message = "Attendance recorded for $success_count students";
        } else {
            $alert_status = 'error';
            $alert_message = "Failed to record attendance: " . $error_message;
        }
    } else {
        $alert_status = 'warning';
        $alert_message = "Please select at least one student";
    }
}
?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <!-- Custom Alerts -->
    <div id="alert-container" class="fixed top-16 right-0 left-0 mx-auto z-50 max-w-md"></div>

    <div class="p-4">
        <!-- Date and Actions -->
        <div class="flex flex-col mb-4">
            <h1 class="text-xl font-bold text-gray-800">Student Attendance</h1>
            <p class="text-gray-600">Date: <span
                    class="font-medium"><?php echo date('F d, Y', strtotime($selected_date)); ?></span></p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 mb-4">
            <form method="get" class="flex flex-1">
                <input type="date" name="date" value="<?php echo $selected_date; ?>"
                    class="w-full border rounded-l py-3 px-3">
                <button type="submit" class="bg-main text-white py-3 px-4 rounded-r -ml-1">
                    Change Date
                </button>
            </form>

            <a href="attendance_report.php"
                class="bg-gray-700 text-white flex items-center justify-center px-4 rounded">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </a>
        </div>

        <!-- Attendance Form -->
        <form method="post" class="bg-white rounded-lg shadow p-4 mb-6">
            <h2 class="text-lg font-semibold mb-2">Mark Attendance</h2>

            <!-- Attendance Controls -->
            <div class="mb-4 p-3 bg-gray-50 rounded">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center bg-green-50 p-3 rounded border">
                            <input type="radio" name="status" value="present" class="mr-2" checked>
                            <span>Present</span>
                        </label>
                        <label class="flex items-center bg-red-50 p-3 rounded border">
                            <input type="radio" name="status" value="absent" class="mr-2">
                            <span>Absent</span>
                        </label>
                        <label class="flex items-center bg-yellow-50 p-3 rounded border">
                            <input type="radio" name="status" value="late" class="mr-2">
                            <span>Late</span>
                        </label>
                        <label class="flex items-center bg-blue-50 p-3 rounded border">
                            <input type="radio" name="status" value="excused" class="mr-2">
                            <span>Excused</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
                        <input type="time" name="time_in" class="w-full border rounded py-2 px-3" value="08:00">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
                        <input type="time" name="time_out" class="w-full border rounded py-2 px-3" value="17:00">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
                    <input type="text" name="remarks" class="w-full border rounded py-2 px-3">
                </div>
            </div>

            <!-- Student Selection -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold">Select Students</h3>
                    <div>
                        <button type="button" id="selectAll" class="text-sm text-blue-600">Select All</button> |
                        <button type="button" id="deselectAll" class="text-sm text-blue-600">Deselect All</button>
                    </div>
                </div>

                <div class="border rounded overflow-y-auto max-h-[60vh]">
                    <div class="divide-y">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($student = $result->fetch_assoc()): ?>
                                <label class="flex items-center p-3 hover:bg-gray-50">
                                    <input type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>"
                                        class="student-checkbox mr-3 h-5 w-5">
                                    <div>
                                        <div class="font-medium"><?php echo $student["Fname"] . " " . $student["Lname"]; ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo $student["GLevel"] ?> | LRN: <?php echo $student["LRN"] ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-4 text-center text-gray-500">No students found</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <input type="hidden" name="record_attendance" value="1">
            <button type="submit"
                class="mt-4 w-full bg-main hover:bg-blue-700 text-white py-3 px-4 rounded font-medium text-lg">
                Save Attendance
            </button>
        </form>
    </div>
</main>

<script>
    // Select/deselect all functionality
    document.getElementById('selectAll').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = true);
    });

    document.getElementById('deselectAll').addEventListener('click', function () {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(checkbox => checkbox.checked = false);
    });

    // Show/hide time fields based on status
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', function () {
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
                // Set default times if empty
                if (document.querySelector('input[name="time_in"]').value === '') {
                    document.querySelector('input[name="time_in"]').value = '08:00';
                }
                if (document.querySelector('input[name="time_out"]').value === '') {
                    document.querySelector('input[name="time_out"]').value = '17:00';
                }
            }
        });
    });

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