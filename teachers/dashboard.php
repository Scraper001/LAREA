<?php
include "../includes/session_check.php";
// Allow both admin (1) and teacher (2) to access teacher dashboard
if (!isset($_SESSION['user_level']) || ($_SESSION['user_level'] != 1 && $_SESSION['user_level'] != 2)) {
    header("Location: ../auth/login.php");
    exit();
}

include "functions/get_teacher_stats.php";
include "functions/get_class_data.php";

$stats = get_teacher_stats();
$class_data = get_class_data();
?>

<?php include "../includes/header.php" ?>
<?php include "includes/teacher_navbar.php" ?>

<main class="min-h-screen main-font bg-gray-50">
    
    <div class="px-4 py-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Teacher Dashboard</h1>
            <p class="text-gray-600">Overview of your classes and students</p>
        </div>

        <!-- Quick Stats Cards -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Assigned Students -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100">
                        <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Assigned Students</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['total_assigned_students'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Today's Present -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100">
                        <i class="fa-solid fa-check text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Present Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['present_today'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Recent Behavior Reports -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-yellow-100">
                        <i class="fa-solid fa-clipboard-list text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Recent Reports</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['recent_behavior_reports'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Pending Follow-ups -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-100">
                        <i class="fa-solid fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Follow-ups</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['pending_followups'] ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts and Class Overview -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Weekly Attendance Chart -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Weekly Attendance Overview</h3>
                <canvas id="attendanceChart" class="w-full h-[300px]"></canvas>
            </div>

            <!-- Class Attendance by Grade -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Today's Attendance by Grade</h3>
                <div class="space-y-4">
                    <?php if (!empty($class_data['class_attendance'])): ?>
                        <?php foreach ($class_data['class_attendance'] as $class): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">Grade <?= htmlspecialchars($class['grade_level']) ?></p>
                                    <p class="text-sm text-gray-500"><?= $class['total_students'] ?> students</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-green-600"><?= $class['present_today'] ?> Present</p>
                                    <p class="text-sm text-red-600"><?= $class['absent_today'] ?> Absent</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No attendance data for today</p>
                    <?php endif; ?>
                </div>
            </div>

        </section>

        <!-- Recent Activity and Student Alerts -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Recent Student Activity -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Recent Student Activity</h3>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    <?php if (!empty($stats['recent_activity'])): ?>
                        <?php foreach ($stats['recent_activity'] as $activity): ?>
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-user text-blue-500"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($activity['observation_title']) ?></p>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($activity['fname'] . ' ' . $activity['lname']) ?> - Grade <?= htmlspecialchars($activity['grade_level']) ?></p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="px-2 py-1 text-xs rounded-full <?= 
                                            $activity['severity_level'] == 'High' ? 'bg-red-100 text-red-800' : 
                                            ($activity['severity_level'] == 'Medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') 
                                        ?>">
                                            <?= htmlspecialchars($activity['severity_level']) ?>
                                        </span>
                                        <span class="text-xs text-gray-400"><?= date('M j, Y g:i A', strtotime($activity['date_recorded'])) ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Students Needing Attention -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Students Needing Attention</h3>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    <?php if (!empty($stats['students_needing_attention'])): ?>
                        <?php foreach ($stats['students_needing_attention'] as $student): ?>
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div>
                                    <p class="font-medium text-gray-900"><?= htmlspecialchars($student['fname'] . ' ' . $student['lname']) ?></p>
                                    <p class="text-sm text-gray-600">Grade <?= htmlspecialchars($student['grade_level']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-orange-600"><?= $student['incident_count'] ?> incidents</p>
                                    <p class="text-xs text-gray-500">Last: <?= date('M j', strtotime($student['last_incident'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No students requiring immediate attention</p>
                    <?php endif; ?>
                </div>
            </div>

        </section>

        <!-- Upcoming Deadlines and Quick Actions -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Upcoming Deadlines -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Upcoming Deadlines</h3>
                <div class="space-y-3">
                    <?php foreach ($class_data['upcoming_deadlines'] as $deadline): ?>
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900"><?= htmlspecialchars($deadline['title']) ?></p>
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($deadline['class']) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-blue-600"><?= date('M j, Y', strtotime($deadline['due_date'])) ?></p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($deadline['status']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="../users/attendance.php" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        <i class="fa-solid fa-clipboard-check text-green-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-green-700">Take Attendance</span>
                    </a>
                    <a href="../users/student_behavior.php" class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        <i class="fa-solid fa-exclamation-circle text-yellow-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-yellow-700">Behavior Report</span>
                    </a>
                    <a href="../users/grades.php" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        <i class="fa-solid fa-chart-bar text-purple-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-purple-700">Enter Grades</span>
                    </a>
                    <a href="../users/student_management.php" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        <i class="fa-solid fa-users text-blue-600 text-2xl mb-2"></i>
                        <span class="text-sm font-medium text-blue-700">My Students</span>
                    </a>
                </div>
            </div>

        </section>

    </div>

</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($stats['weekly_attendance'], 'day_name')); ?>,
            datasets: [{
                label: 'Present',
                data: <?php echo json_encode(array_column($stats['weekly_attendance'], 'present')); ?>,
                backgroundColor: '#10b981'
            }, {
                label: 'Absent',
                data: <?php echo json_encode(array_column($stats['weekly_attendance'], 'absent')); ?>,
                backgroundColor: '#ef4444'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Weekly Attendance Overview'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include "../includes/footer.php" ?>