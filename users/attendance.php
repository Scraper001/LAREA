<?php
$current_page = basename($_SERVER['PHP_SELF']);
session_start();
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/select_users.php" ?>
<?php include "functions/attendance_functions.php" ?>

<?php
$selected_date = $_GET['date'] ?? date('Y-m-d');
$search = $_GET['search'] ?? '';
$grade_level = $_GET['grade_level'] ?? '';

// Handle attendance submission
if ($_POST['action'] ?? '' === 'mark_attendance') {
    $attendanceData = [];

    foreach ($_POST['students'] as $student_id => $data) {
        if (isset($data['status'])) {
            $attendanceData[] = [
                'student_id' => $student_id,
                'status' => $data['status'],
                'time_in' => $data['time_in'] ?? null,
                'time_out' => $data['time_out'] ?? null,
                'remarks' => $data['remarks'] ?? ''
            ];
        }
    }

    if (bulkMarkAttendance($attendanceData, $selected_date, $_SESSION['user_id'] ?? null)) {
        $success_message = "Attendance marked successfully!";
    } else {
        $error_message = "Failed to mark attendance. Please try again.";
    }
}

// Get students and stats
$students = getStudentsForAttendance($search, $grade_level, $selected_date);
$todayStats = getAttendanceStats($selected_date);
$gradeLevels = getGradeLevels();
?>

<style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        min-width: 300px;
    }

    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }

    .alert-error {
        color: #a94442;
        background-color: #f2dede;
        border-color: #ebccd1;
    }

    .fade-out {
        animation: fadeOut 0.5s ease-out forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    .attendance-card {
        transition: all 0.3s ease;
    }

    .attendance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .status-present {
        background-color: #d4edda;
        border-left: 4px solid #28a745;
    }

    .status-absent {
        background-color: #f8d7da;
        border-left: 4px solid #dc3545;
    }

    .status-late {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }

    .status-excused {
        background-color: #d1ecf1;
        border-left: 4px solid #17a2b8;
    }
</style>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <!-- Custom Alerts -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success" id="successAlert">
            <?php echo $success_message; ?>
            <button onclick="closeAlert('successAlert')"
                style="float: right; background: none; border: none; font-size: 18px;">&times;</button>
        </div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error" id="errorAlert">
            <?php echo $error_message; ?>
            <button onclick="closeAlert('errorAlert')"
                style="float: right; background: none; border: none; font-size: 18px;">&times;</button>
        </div>
    <?php endif; ?>

    <div class="container mx-auto py-4 px-4">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Student Attendance</h1>
                    <p class="text-gray-600">Date: <span
                            class="font-semibold"><?php echo date('F j, Y', strtotime($selected_date)); ?></span></p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Current Time</div>
                    <div class="text-xl font-bold" id="currentTime"></div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600"><?php echo $todayStats['present'] ?? 0; ?></div>
                    <div class="text-sm text-green-600">Present</div>
                </div>
                <div class="bg-red-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600"><?php echo $todayStats['absent'] ?? 0; ?></div>
                    <div class="text-sm text-red-600">Absent</div>
                </div>
                <div class="bg-yellow-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600"><?php echo $todayStats['late'] ?? 0; ?></div>
                    <div class="text-sm text-yellow-600">Late</div>
                </div>
                <div class="bg-blue-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600"><?php echo $todayStats['excused'] ?? 0; ?></div>
                    <div class="text-sm text-blue-600">Excused</div>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" value="<?php echo $selected_date; ?>"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Student</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Student name..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grade Level</label>
                    <select name="grade_level"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Grades</option>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?php echo $level; ?>" <?php echo $grade_level == $level ? 'selected' : ''; ?>>
                                Grade <?php echo $level; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Attendance Form -->
        <form method="POST" id="attendanceForm">
            <input type="hidden" name="action" value="mark_attendance">

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <h3 class="text-lg font-semibold mb-3">Quick Actions</h3>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="markAllStatus('present')"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Mark All Present
                    </button>
                    <button type="button" onclick="markAllStatus('absent')"
                        class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Mark All Absent
                    </button>
                    <button type="button" onclick="setCurrentTimeForAll()"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Set Current Time for Present
                    </button>
                </div>
            </div>

            <!-- Students List -->
            <div class="space-y-4">
                <?php if ($students->num_rows > 0): ?>
                    <?php while ($student = $students->fetch_assoc()): ?>
                        <div
                            class="attendance-card bg-white rounded-lg shadow-md p-4 <?php echo 'status-' . ($student['status'] ?? 'absent'); ?>">
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                                <!-- Student Info -->
                                <div class="lg:col-span-3 flex items-center space-x-3">
                                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white">
                                        <img src="../<?php echo $student['photo_path'] ?: 'default-avatar.png'; ?>"
                                            class="w-full h-full object-cover" alt="Student Photo">
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800">
                                            <?php echo $student['Fname'] . ' ' . $student['Lname'] . ' ' . $student['Mname']; ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">Grade <?php echo $student['GLevel']; ?></p>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select name="students[<?php echo $student['id']; ?>][status]"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 status-select"
                                        data-student-id="<?php echo $student['id']; ?>">
                                        <option value="present" <?php echo ($student['status'] ?? '') == 'present' ? 'selected' : ''; ?>>Present</option>
                                        <option value="absent" <?php echo ($student['status'] ?? '') == 'absent' ? 'selected' : ''; ?>>Absent</option>
                                        <option value="late" <?php echo ($student['status'] ?? '') == 'late' ? 'selected' : ''; ?>>Late</option>
                                        <option value="excused" <?php echo ($student['status'] ?? '') == 'excused' ? 'selected' : ''; ?>>Excused</option>
                                    </select>
                                </div>

                                <!-- Time In -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
                                    <input type="time" name="students[<?php echo $student['id']; ?>][time_in]"
                                        value="<?php echo $student['time_in'] ?? ''; ?>"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 time-in-input">
                                </div>

                                <!-- Time Out -->
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
                                    <input type="time" name="students[<?php echo $student['id']; ?>][time_out]"
                                        value="<?php echo $student['time_out'] ?? ''; ?>"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>

                                <!-- Remarks -->
                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                    <input type="text" name="students[<?php echo $student['id']; ?>][remarks]"
                                        value="<?php echo htmlspecialchars($student['remarks'] ?? ''); ?>"
                                        placeholder="Optional remarks..."
                                        class="w-full border border-gray-300 rounded-md px-3 py-2">
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                    <!-- Submit Button -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <button type="submit"
                            class="w-full bg-green-600 text-white py-3 px-6 rounded-md text-lg font-semibold hover:bg-green-700 transition duration-200">
                            Save Attendance
                        </button>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <p class="text-gray-500 text-lg">No students found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </form>
    </div>
</main>

<script>
    // Update current time
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        document.getElementById('currentTime').textContent = timeString;
    }

    setInterval(updateTime, 1000);
    updateTime();

    // Close alert function
    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        alert.classList.add('fade-out');
        setTimeout(() => alert.remove(), 500);
    }

    // Auto-close alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Quick action functions
    function markAllStatus(status) {
        const selects = document.querySelectorAll('.status-select');
        selects.forEach(select => {
            select.value = status;
            updateCardStatus(select);
        });
    }

    function setCurrentTimeForAll() {
        const now = new Date();
        const timeString = now.toTimeString().slice(0, 5);
        const timeInputs = document.querySelectorAll('.time-in-input');
        const statusSelects = document.querySelectorAll('.status-select');

        statusSelects.forEach((select, index) => {
            if (select.value === 'present' || select.value === 'late') {
                timeInputs[index].value = timeString;
            }
        });
    }

    // Update card status visual
    function updateCardStatus(selectElement) {
        const card = selectElement.closest('.attendance-card');
        const status = selectElement.value;

        // Remove existing status classes
        card.classList.remove('status-present', 'status-absent', 'status-late', 'status-excused');
        // Add new status class
        card.classList.add('status-' + status);
    }

    // Add event listeners to status selects
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                updateCardStatus(this);
            });
        });
    });

    // Form validation
    document.getElementById('attendanceForm').addEventListener('submit', function (e) {
        const statusSelects = document.querySelectorAll('.status-select');
        let hasSelection = false;

        statusSelects.forEach(select => {
            if (select.value && select.value !== 'absent') {
                hasSelection = true;
            }
        });

        if (!hasSelection) {
            if (!confirm('No attendance has been marked. Are you sure you want to continue?')) {
                e.preventDefault();
            }
        }
    });
</script>

<?php include "../includes/footer.php" ?>