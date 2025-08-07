<?php
$current_page = basename($_SERVER['PHP_SELF']);
session_start();
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/select_users.php" ?>
<?php include "functions/attendance_functions.php" ?>
<?php include "functions/time_calculation_utils.php" ?>

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
                'break_start' => $data['break_start'] ?? null,
                'break_end' => $data['break_end'] ?? null,
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
$attendanceSettings = getAttendanceSettings();
$timeSuggestions = getTimeSuggestions();
$isHolidayToday = isHoliday($selected_date);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Attendance System - LAREA</title>
    <link href="../assets/css/enhanced-alerts.css" rel="stylesheet">
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

        .status-half_day {
            background-color: #e2e3e5;
            border-left: 4px solid #6c757d;
        }

        .holiday-banner {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        .quick-time-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
            margin-top: 5px;
        }

        .quick-time-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .quick-time-btn:hover {
            background: #0056b3;
        }

        .hours-display {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        .time-validation-error {
            color: #dc3545;
            font-size: 11px;
            margin-top: 2px;
        }

        .analytics-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .analytics-item {
            text-align: center;
        }

        .analytics-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .analytics-label {
            font-size: 12px;
            opacity: 0.9;
        }
    </style>
</head>
<body>

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
        <!-- Holiday Banner -->
        <?php if ($isHolidayToday): ?>
            <div class="holiday-banner">
                🎉 Today is a Holiday: <?php echo $isHolidayToday['name']; ?>
                <?php if ($isHolidayToday['description']): ?>
                    - <?php echo $isHolidayToday['description']; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Analytics Card -->
        <div class="analytics-card">
            <h3 class="text-xl font-bold mb-4">Today's Analytics</h3>
            <div class="analytics-grid">
                <div class="analytics-item">
                    <div class="analytics-number"><?php echo $todayStats['present'] ?? 0; ?></div>
                    <div class="analytics-label">Present</div>
                </div>
                <div class="analytics-item">
                    <div class="analytics-number"><?php echo $todayStats['absent'] ?? 0; ?></div>
                    <div class="analytics-label">Absent</div>
                </div>
                <div class="analytics-item">
                    <div class="analytics-number"><?php echo $todayStats['late'] ?? 0; ?></div>
                    <div class="analytics-label">Late</div>
                </div>
                <div class="analytics-item">
                    <div class="analytics-number"><?php echo round($todayStats['avg_hours'] ?? 0, 1); ?>h</div>
                    <div class="analytics-label">Avg Hours</div>
                </div>
                <div class="analytics-item">
                    <div class="analytics-number"><?php echo round($todayStats['total_overtime'] ?? 0, 1); ?>h</div>
                    <div class="analytics-label">Total OT</div>
                </div>
                <?php
                $total_students = $todayStats['total_students'] ?? 0;
                $present_students = ($todayStats['present'] ?? 0) + ($todayStats['late'] ?? 0);
                $attendance_rate = $total_students > 0 ? round(($present_students / $total_students) * 100, 1) : 0;
                ?>
                <div class="analytics-item">
                    <div class="analytics-number"><?php echo $attendance_rate; ?>%</div>
                    <div class="analytics-label">Attendance Rate</div>
                </div>
            </div>
        </div>

        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Enhanced Attendance System</h1>
                    <p class="text-gray-600">Date: <span
                            class="font-semibold"><?php echo date('F j, Y', strtotime($selected_date)); ?></span></p>
                    <p class="text-sm text-gray-500">School Time: <?php echo $attendanceSettings['school_start_time']; ?> - <?php echo $attendanceSettings['school_end_time']; ?></p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-500">Current Time</div>
                    <div class="text-xl font-bold" id="currentTime"></div>
                    <div class="text-xs text-gray-400" id="currentDate"></div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
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
                <div class="bg-gray-100 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-gray-600"><?php echo $todayStats['half_day'] ?? 0; ?></div>
                    <div class="text-sm text-gray-600">Half Day</div>
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
                    <button type="button" onclick="setRegularDay()"
                        class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                        Set Regular Day Times
                    </button>
                    <button type="button" onclick="setHalfDay()"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Set Half Day Times
                    </button>
                    <button type="button" onclick="calculateAllHours()"
                        class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                        Calculate All Hours
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
                                <div class="lg:col-span-2 flex items-center space-x-3">
                                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white">
                                        <img src="../<?php echo $student['photo_path'] ?: 'default-avatar.png'; ?>"
                                            class="w-full h-full object-cover" alt="Student Photo">
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-800">
                                            <?php echo $student['Fname'] . ' ' . $student['Lname'] . ' ' . $student['Mname']; ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">Grade <?php echo $student['GLevel']; ?></p>
                                        <p class="text-xs text-gray-500">LRN: <?php echo $student['LRN']; ?></p>
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
                                        <option value="half_day" <?php echo ($student['status'] ?? '') == 'half_day' ? 'selected' : ''; ?>>Half Day</option>
                                    </select>
                                </div>

                                <!-- Time In -->
                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Time In</label>
                                    <input type="time" name="students[<?php echo $student['id']; ?>][time_in]"
                                        value="<?php echo $student['time_in'] ?? ''; ?>"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 time-in-input"
                                        data-student-id="<?php echo $student['id']; ?>">
                                    <div class="quick-time-buttons">
                                        <button type="button" class="quick-time-btn" onclick="setCurrentTime(<?php echo $student['id']; ?>, 'time_in')">Now</button>
                                        <button type="button" class="quick-time-btn" onclick="setTimeValue(<?php echo $student['id']; ?>, 'time_in', '<?php echo $attendanceSettings['school_start_time']; ?>')">School Start</button>
                                    </div>
                                </div>

                                <!-- Time Out -->
                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Time Out</label>
                                    <input type="time" name="students[<?php echo $student['id']; ?>][time_out]"
                                        value="<?php echo $student['time_out'] ?? ''; ?>"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 time-out-input"
                                        data-student-id="<?php echo $student['id']; ?>">
                                    <div class="quick-time-buttons">
                                        <button type="button" class="quick-time-btn" onclick="setCurrentTime(<?php echo $student['id']; ?>, 'time_out')">Now</button>
                                        <button type="button" class="quick-time-btn" onclick="setTimeValue(<?php echo $student['id']; ?>, 'time_out', '<?php echo $attendanceSettings['school_end_time']; ?>')">School End</button>
                                    </div>
                                </div>

                                <!-- Break Times -->
                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Break Start</label>
                                    <input type="time" name="students[<?php echo $student['id']; ?>][break_start]"
                                        value="<?php echo $student['break_start'] ?? ''; ?>"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 break-start-input"
                                        data-student-id="<?php echo $student['id']; ?>">
                                    <div class="quick-time-buttons">
                                        <button type="button" class="quick-time-btn" onclick="setTimeValue(<?php echo $student['id']; ?>, 'break_start', '<?php echo $attendanceSettings['break_start_time']; ?>')">Default</button>
                                    </div>
                                </div>

                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Break End</label>
                                    <input type="time" name="students[<?php echo $student['id']; ?>][break_end]"
                                        value="<?php echo $student['break_end'] ?? ''; ?>"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 break-end-input"
                                        data-student-id="<?php echo $student['id']; ?>">
                                    <div class="quick-time-buttons">
                                        <button type="button" class="quick-time-btn" onclick="setTimeValue(<?php echo $student['id']; ?>, 'break_end', '<?php echo $attendanceSettings['break_end_time']; ?>')">Default</button>
                                    </div>
                                </div>

                                <!-- Hours Display -->
                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Hours</label>
                                    <div class="hours-display" id="hours_<?php echo $student['id']; ?>">
                                        <?php if ($student['total_hours']): ?>
                                            Total: <?php echo $student['total_hours']; ?>h
                                            <?php if ($student['overtime_hours'] > 0): ?>
                                                <br>OT: <?php echo $student['overtime_hours']; ?>h
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">Not calculated</span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="quick-time-btn mt-1" onclick="calculateStudentHours(<?php echo $student['id']; ?>)">Calculate</button>
                                </div>

                                <!-- Remarks -->
                                <div class="lg:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                    <input type="text" name="students[<?php echo $student['id']; ?>][remarks]"
                                        value="<?php echo htmlspecialchars($student['remarks'] ?? ''); ?>"
                                        placeholder="Optional remarks..."
                                        class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <div class="time-validation-error" id="validation_<?php echo $student['id']; ?>"></div>
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

<script src="../assets/js/enhanced-alerts.js"></script>
<script>
    // Enhanced attendance system JavaScript
    const attendanceSettings = <?php echo json_encode($attendanceSettings); ?>;
    const timeSuggestions = <?php echo json_encode($timeSuggestions); ?>;

    // Update current time and date
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const dateString = now.toLocaleDateString();
        document.getElementById('currentTime').textContent = timeString;
        if (document.getElementById('currentDate')) {
            document.getElementById('currentDate').textContent = dateString;
        }
    }

    setInterval(updateTime, 1000);
    updateTime();

    // Enhanced quick action functions
    function markAllStatus(status) {
        const selects = document.querySelectorAll('.status-select');
        selects.forEach(select => {
            select.value = status;
            updateCardStatus(select);
            
            // Auto-set times for present/late status
            if (status === 'present' || status === 'late') {
                const studentId = select.dataset.studentId;
                if (!getTimeValue(studentId, 'time_in')) {
                    setCurrentTime(studentId, 'time_in');
                }
            }
        });
    }

    function setCurrentTimeForAll() {
        const now = new Date();
        const timeString = now.toTimeString().slice(0, 5);
        const statusSelects = document.querySelectorAll('.status-select');

        statusSelects.forEach(select => {
            if (select.value === 'present' || select.value === 'late') {
                const studentId = select.dataset.studentId;
                setTimeValue(studentId, 'time_in', timeString);
            }
        });
    }

    function setRegularDay() {
        const statusSelects = document.querySelectorAll('.status-select');
        
        statusSelects.forEach(select => {
            if (select.value === 'present' || select.value === 'late') {
                const studentId = select.dataset.studentId;
                setTimeValue(studentId, 'time_in', timeSuggestions.regular_day.time_in);
                setTimeValue(studentId, 'time_out', timeSuggestions.regular_day.time_out);
                setTimeValue(studentId, 'break_start', timeSuggestions.regular_day.break_start);
                setTimeValue(studentId, 'break_end', timeSuggestions.regular_day.break_end);
                calculateStudentHours(studentId);
            }
        });
    }

    function setHalfDay() {
        const statusSelects = document.querySelectorAll('.status-select');
        
        statusSelects.forEach(select => {
            if (select.value === 'present' || select.value === 'late') {
                const studentId = select.dataset.studentId;
                select.value = 'half_day';
                updateCardStatus(select);
                setTimeValue(studentId, 'time_in', timeSuggestions.half_day.time_in);
                setTimeValue(studentId, 'time_out', timeSuggestions.half_day.time_out);
                setTimeValue(studentId, 'break_start', '');
                setTimeValue(studentId, 'break_end', '');
                calculateStudentHours(studentId);
            }
        });
    }

    function calculateAllHours() {
        const statusSelects = document.querySelectorAll('.status-select');
        
        statusSelects.forEach(select => {
            if (select.value !== 'absent') {
                const studentId = select.dataset.studentId;
                calculateStudentHours(studentId);
            }
        });
    }

    // Time manipulation functions
    function setCurrentTime(studentId, field) {
        const now = new Date();
        const timeString = now.toTimeString().slice(0, 5);
        setTimeValue(studentId, field, timeString);
    }

    function setTimeValue(studentId, field, value) {
        const input = document.querySelector(`input[name="students[${studentId}][${field}]"]`);
        if (input) {
            input.value = value;
            if (field === 'time_in' || field === 'time_out') {
                calculateStudentHours(studentId);
            }
        }
    }

    function getTimeValue(studentId, field) {
        const input = document.querySelector(`input[name="students[${studentId}][${field}]"]`);
        return input ? input.value : '';
    }

    // Hours calculation
    function calculateStudentHours(studentId) {
        const timeIn = getTimeValue(studentId, 'time_in');
        const timeOut = getTimeValue(studentId, 'time_out');
        const breakStart = getTimeValue(studentId, 'break_start');
        const breakEnd = getTimeValue(studentId, 'break_end');
        
        // Validate times
        const validationErrors = validateTimes(timeIn, timeOut, breakStart, breakEnd);
        const validationDiv = document.getElementById(`validation_${studentId}`);
        
        if (validationErrors.length > 0) {
            validationDiv.textContent = validationErrors.join(', ');
            validationDiv.style.display = 'block';
            return;
        } else {
            validationDiv.style.display = 'none';
        }
        
        if (!timeIn || !timeOut) {
            updateHoursDisplay(studentId, 0, 0);
            return;
        }
        
        // Calculate hours
        const hours = calculateWorkingHours(timeIn, timeOut, breakStart, breakEnd);
        updateHoursDisplay(studentId, hours.total, hours.overtime);
    }

    function validateTimes(timeIn, timeOut, breakStart, breakEnd) {
        const errors = [];
        
        if (timeIn && timeOut && timeIn >= timeOut) {
            errors.push("Time out must be after time in");
        }
        
        if (breakStart && breakEnd && breakStart >= breakEnd) {
            errors.push("Break end must be after break start");
        }
        
        if (timeIn && breakStart && breakStart < timeIn) {
            errors.push("Break start cannot be before time in");
        }
        
        if (timeOut && breakEnd && breakEnd > timeOut) {
            errors.push("Break end cannot be after time out");
        }
        
        return errors;
    }

    function calculateWorkingHours(timeIn, timeOut, breakStart = '', breakEnd = '') {
        const start = new Date(`1970-01-01T${timeIn}:00`);
        const end = new Date(`1970-01-01T${timeOut}:00`);
        
        let totalMs = end.getTime() - start.getTime();
        
        // Subtract break time if provided
        if (breakStart && breakEnd) {
            const bStart = new Date(`1970-01-01T${breakStart}:00`);
            const bEnd = new Date(`1970-01-01T${breakEnd}:00`);
            const breakMs = bEnd.getTime() - bStart.getTime();
            totalMs -= breakMs;
        }
        
        const totalHours = totalMs / (1000 * 60 * 60);
        const regularHours = parseFloat(attendanceSettings.full_day_hours);
        const overtimeHours = Math.max(0, totalHours - regularHours);
        
        return {
            total: Math.round(totalHours * 100) / 100,
            overtime: Math.round(overtimeHours * 100) / 100
        };
    }

    function updateHoursDisplay(studentId, totalHours, overtimeHours) {
        const hoursDiv = document.getElementById(`hours_${studentId}`);
        if (hoursDiv) {
            let html = '';
            if (totalHours > 0) {
                html = `Total: ${totalHours}h`;
                if (overtimeHours > 0) {
                    html += `<br>OT: ${overtimeHours}h`;
                }
            } else {
                html = '<span class="text-gray-400">Not calculated</span>';
            }
            hoursDiv.innerHTML = html;
        }
    }

    // Update card status visual
    function updateCardStatus(selectElement) {
        const card = selectElement.closest('.attendance-card');
        const status = selectElement.value;

        // Remove existing status classes
        card.classList.remove('status-present', 'status-absent', 'status-late', 'status-excused', 'status-half_day');
        // Add new status class
        card.classList.add('status-' + status);
    }

    // Add event listeners
    document.addEventListener('DOMContentLoaded', function () {
        // Status select change events
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function () {
                updateCardStatus(this);
                const studentId = this.dataset.studentId;
                
                // Auto-set current time for present/late if not already set
                if ((this.value === 'present' || this.value === 'late') && !getTimeValue(studentId, 'time_in')) {
                    setCurrentTime(studentId, 'time_in');
                }
                
                // Clear times for absent/excused
                if (this.value === 'absent' || this.value === 'excused') {
                    setTimeValue(studentId, 'time_in', '');
                    setTimeValue(studentId, 'time_out', '');
                    setTimeValue(studentId, 'break_start', '');
                    setTimeValue(studentId, 'break_end', '');
                    updateHoursDisplay(studentId, 0, 0);
                }
            });
        });

        // Time input change events
        const timeInputs = document.querySelectorAll('input[type="time"]');
        timeInputs.forEach(input => {
            input.addEventListener('change', function () {
                const studentId = this.dataset.studentId;
                if (studentId) {
                    calculateStudentHours(studentId);
                }
            });
        });

        // Initial card status setup
        statusSelects.forEach(updateCardStatus);
    });

    // Form validation
    document.getElementById('attendanceForm').addEventListener('submit', function (e) {
        const statusSelects = document.querySelectorAll('.status-select');
        let hasSelection = false;
        let hasErrors = false;

        statusSelects.forEach(select => {
            if (select.value && select.value !== 'absent') {
                hasSelection = true;
            }
            
            // Check for validation errors
            const studentId = select.dataset.studentId;
            const validationDiv = document.getElementById(`validation_${studentId}`);
            if (validationDiv && validationDiv.style.display !== 'none' && validationDiv.textContent) {
                hasErrors = true;
            }
        });

        if (hasErrors) {
            e.preventDefault();
            showError('Please fix the time validation errors before submitting.', 'Validation Error');
            return;
        }

        if (!hasSelection) {
            if (!confirm('No attendance has been marked. Are you sure you want to continue?')) {
                e.preventDefault();
            }
        }
    });

    // Enhanced alert support
    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }
    }

    // Auto-close legacy alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.id) { // Only close legacy alerts
                closeAlert(alert.id);
            }
        });
    }, 5000);
</script>

<?php include "../includes/footer.php" ?>