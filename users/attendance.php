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
        $success_message = "Attendance marked successfully! " . count($attendanceData) . " students updated.";
    } else {
        $error_message = "Failed to mark attendance. Please try again.";
    }
}

// Handle bulk actions
if ($_POST['action'] ?? '' === 'mark_all_present') {
    if (markAllPresent($selected_date, $grade_level, $_SESSION['user_id'] ?? null)) {
        $success_message = "All students marked as present successfully!";
    } else {
        $error_message = "Failed to mark all students as present.";
    }
}

if ($_POST['action'] ?? '' === 'mark_all_absent') {
    if (markAllAbsent($selected_date, $grade_level, $_SESSION['user_id'] ?? null)) {
        $success_message = "All students marked as absent successfully!";
    } else {
        $error_message = "Failed to mark all students as absent.";
    }
}

// Get students and stats
$students = getStudentsForAttendance($search, $grade_level, $selected_date);
$todayStats = getAttendanceStats($selected_date);
$gradeLevels = getGradeLevels();
?>

<style>
    .alert {
        padding: 15px 20px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 8px;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        min-width: 350px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        animation: slideIn 0.3s ease-out;
    }

    .alert-success {
        color: #155724;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-color: #c3e6cb;
    }

    .alert-error {
        color: #721c24;
        background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
        border-color: #f1aeb5;
    }

    .alert::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: currentColor;
        border-radius: 4px 0 0 4px;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .fade-out {
        animation: fadeOut 0.5s ease-out forwards;
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    .attendance-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .attendance-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .status-present {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-left: 4px solid #28a745;
    }

    .status-absent {
        background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
        border-left: 4px solid #dc3545;
    }

    .status-late {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-left: 4px solid #ffc107;
    }

    .status-excused {
        background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
        border-left: 4px solid #17a2b8;
    }

    .quick-action-btn {
        transition: all 0.2s ease;
        border-radius: 8px;
        font-weight: 600;
    }

    .quick-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .time-display {
        font-family: 'Courier New', monospace;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stats-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .stats-card:hover {
        transform: scale(1.05);
    }

    .hours-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e9ecef;
        color: #495057;
    }

    .hours-worked {
        color: #28a745;
        font-weight: 600;
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
                    <div class="text-xl font-bold time-display" id="currentTime"></div>
                    <div class="text-xs text-gray-400" id="currentDate"></div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="stats-card bg-green-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600"><?php echo $todayStats['present'] ?? 0; ?></div>
                    <div class="text-sm text-green-600">Present</div>
                </div>
                <div class="stats-card bg-red-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600"><?php echo $todayStats['absent'] ?? 0; ?></div>
                    <div class="text-sm text-red-600">Absent</div>
                </div>
                <div class="stats-card bg-yellow-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600"><?php echo $todayStats['late'] ?? 0; ?></div>
                    <div class="text-sm text-yellow-600">Late</div>
                </div>
                <div class="stats-card bg-blue-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600"><?php echo $todayStats['excused'] ?? 0; ?></div>
                    <div class="text-sm text-blue-600">Excused</div>
                </div>
                <div class="stats-card bg-purple-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600"><?php echo number_format($todayStats['avg_hours'] ?? 0, 1); ?>h</div>
                    <div class="text-sm text-purple-600">Avg Hours</div>
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
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="mark_all_present">
                        <button type="submit" 
                            class="quick-action-btn bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600"
                            onclick="return confirm('Mark all students as present?')">
                            Mark All Present
                        </button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="mark_all_absent">
                        <button type="submit"
                            class="quick-action-btn bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600"
                            onclick="return confirm('Mark all students as absent?')">
                            Mark All Absent
                        </button>
                    </form>
                    <button type="button" onclick="setCurrentTimeForAll()"
                        class="quick-action-btn bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Set Current Time for Present
                    </button>
                    <button type="button" onclick="calculateAllHours()"
                        class="quick-action-btn bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                        Calculate Hours
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
                                        <img src="../<?php echo $student['photo_path'] ?: 'assets/images/default-avatar.png'; ?>"
                                            class="w-full h-full object-cover" alt="Student Photo">
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800">
                                            <?php echo $student['Fname'] . ' ' . $student['Lname'] . ' ' . $student['Mname']; ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">Grade <?php echo $student['GLevel']; ?></p>
                                        <?php if ($student['hours_worked']): ?>
                                            <span class="hours-badge hours-worked">
                                                <?php echo number_format($student['hours_worked'], 1); ?>h worked
                                            </span>
                                        <?php endif; ?>
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
    // Update current time and date
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const dateString = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        document.getElementById('currentTime').textContent = timeString;
        document.getElementById('currentDate').textContent = dateString;
    }

    setInterval(updateTime, 1000);
    updateTime();

    // Enhanced alert handling
    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }
    }

    // Auto-close alerts after 5 seconds with progress indication
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
            updateTimeInputs(select);
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
                calculateHours(timeInputs[index]);
            }
        });
    }

    function calculateAllHours() {
        const timeInputs = document.querySelectorAll('.time-in-input');
        timeInputs.forEach(input => {
            if (input.value) {
                calculateHours(input);
            }
        });
        showAlert('Hours calculated for all students with time entries!', 'success');
    }

    function calculateHours(timeInInput) {
        const row = timeInInput.closest('.attendance-card');
        const timeOutInput = row.querySelector('input[name*="[time_out]"]');
        
        if (timeInInput.value && timeOutInput.value) {
            const timeIn = new Date('2000-01-01 ' + timeInInput.value);
            const timeOut = new Date('2000-01-01 ' + timeOutInput.value);
            
            if (timeOut > timeIn) {
                const diffMs = timeOut - timeIn;
                const diffHours = diffMs / (1000 * 60 * 60);
                const hoursWorked = Math.max(0, diffHours - 1); // Subtract 1 hour for break
                
                // Display calculated hours
                let hoursDisplay = row.querySelector('.hours-display');
                if (!hoursDisplay) {
                    hoursDisplay = document.createElement('span');
                    hoursDisplay.className = 'hours-display hours-badge hours-worked';
                    row.querySelector('.lg\\:col-span-3 > div:last-child').appendChild(hoursDisplay);
                }
                hoursDisplay.textContent = hoursWorked.toFixed(1) + 'h worked';
            }
        }
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

    function updateTimeInputs(selectElement) {
        const card = selectElement.closest('.attendance-card');
        const timeInInput = card.querySelector('input[name*="[time_in]"]');
        const status = selectElement.value;

        // Auto-fill time in for present/late status
        if ((status === 'present' || status === 'late') && !timeInInput.value) {
            const now = new Date();
            timeInInput.value = now.toTimeString().slice(0, 5);
        }
        
        // Clear time inputs for absent/excused
        if (status === 'absent' || status === 'excused') {
            timeInInput.value = '';
            const timeOutInput = card.querySelector('input[name*="[time_out]"]');
            if (timeOutInput) timeOutInput.value = '';
        }
    }

    function showAlert(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `
            ${message}
            <button onclick="closeAlert('${alertDiv.id}')" 
                style="float: right; background: none; border: none; font-size: 18px;">&times;</button>
        `;
        alertDiv.id = 'alert-' + Date.now();
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.classList.add('fade-out');
                setTimeout(() => alertDiv.remove(), 500);
            }
        }, 5000);
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                updateCardStatus(this);
                updateTimeInputs(this);
            });
        });

        // Add time change listeners for automatic hour calculation
        const timeInputs = document.querySelectorAll('input[type="time"]');
        timeInputs.forEach(input => {
            input.addEventListener('change', function () {
                calculateHours(this);
            });
        });
    });

    // Enhanced form validation
    document.getElementById('attendanceForm').addEventListener('submit', function (e) {
        const statusSelects = document.querySelectorAll('.status-select');
        let hasSelection = false;
        let presentCount = 0;

        statusSelects.forEach(select => {
            if (select.value && select.value !== 'absent') {
                hasSelection = true;
                if (select.value === 'present' || select.value === 'late') {
                    presentCount++;
                }
            }
        });

        if (!hasSelection) {
            if (!confirm('No attendance has been marked. Are you sure you want to continue?')) {
                e.preventDefault();
                return;
            }
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Saving...';
        submitBtn.disabled = true;

        // Re-enable button after a delay (in case of errors)
        setTimeout(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });
</script>

<?php include "../includes/footer.php" ?>