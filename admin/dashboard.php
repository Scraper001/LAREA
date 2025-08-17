<?php
include "../includes/session_check.php";
check_user_role(1); // Admin only

include "functions/get_admin_stats.php";
include "functions/system_health.php";

$stats = get_admin_stats();
$health = get_system_health();
?>

<?php include "../includes/header.php" ?>
<?php include "includes/admin_navbar.php" ?>

<main class="min-h-screen main-font bg-gray-50">
    
    <div class="px-4 py-6">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="text-gray-600">Overview of system statistics and management tools</p>
        </div>

        <!-- Quick Stats Cards -->
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Users -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-blue-100">
                        <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['total_users'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Total Students -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-green-100">
                        <i class="fa-solid fa-graduation-cap text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['total_students'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Today's Present -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-emerald-100">
                        <i class="fa-solid fa-check text-emerald-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Present Today</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['present_today'] ?></p>
                    </div>
                </div>
            </div>

            <!-- Active Issues -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center">
                    <div class="p-3 rounded-lg bg-red-100">
                        <i class="fa-solid fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Issues</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $stats['active_issues'] ?></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts and Tables Section -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Weekly Attendance Chart -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Weekly Attendance Trend</h3>
                <canvas id="attendanceChart" class="w-full h-[300px]"></canvas>
            </div>

            <!-- User Statistics by Role -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Users by Role</h3>
                <canvas id="userRoleChart" class="w-full h-[300px]"></canvas>
            </div>

        </section>

        <!-- Recent Activity and System Health -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            
            <!-- Recent Activity Feed -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Recent Activity</h3>
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    <?php if (!empty($stats['recent_activity'])): ?>
                        <?php foreach ($stats['recent_activity'] as $activity): ?>
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-clipboard-list text-blue-500"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($activity['observation_title']) ?></p>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($activity['fname'] . ' ' . $activity['lname']) ?></p>
                                    <p class="text-xs text-gray-400"><?= date('M j, Y g:i A', strtotime($activity['date_recorded'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- System Health Status -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">System Health</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Database</span>
                        <span class="px-2 py-1 text-xs rounded-full <?= $health['database_status'] == 'Connected' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $health['database_status'] ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Uploads Directory</span>
                        <span class="px-2 py-1 text-xs rounded-full <?= $health['uploads_directory'] == 'Writable' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                            <?= $health['uploads_directory'] ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Storage</span>
                        <span class="px-2 py-1 text-xs rounded-full <?= $health['storage_status'] == 'OK' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $health['storage_status'] ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600">Database Size</span>
                        <span class="text-sm text-gray-700"><?= $health['database_size'] ?> MB</span>
                    </div>
                </div>
            </div>

        </section>

        <!-- Quick Action Buttons -->
        <section class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="../users/student_management.php" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fa-solid fa-user-plus text-blue-600 text-2xl mb-2"></i>
                    <span class="text-sm font-medium text-blue-700">Add Student</span>
                </a>
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
                    <span class="text-sm font-medium text-purple-700">View Grades</span>
                </a>
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
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($stats['weekly_attendance'], 'day_name')); ?>,
            datasets: [{
                label: 'Present',
                data: <?php echo json_encode(array_column($stats['weekly_attendance'], 'present')); ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }, {
                label: 'Absent',
                data: <?php echo json_encode(array_column($stats['weekly_attendance'], 'absent')); ?>,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Weekly Attendance Trend'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // User Role Chart
    const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
    const userRoleChart = new Chart(userRoleCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode(array_keys($stats['users_by_role'])); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($stats['users_by_role'])); ?>,
                backgroundColor: [
                    '#3b82f6',
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Users by Role Distribution'
                }
            }
        }
    });
</script>

<?php include "../includes/footer.php" ?>