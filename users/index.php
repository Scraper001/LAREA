<?php
$current_page = basename($_SERVER['PHP_SELF']);
include "../connection/conn.php";
$conn = conn();

$present_sql = "SELECT COUNT(*) AS present_total FROM attendance_tbl WHERE attendance = 1";
$present_result = mysqli_query($conn, $present_sql);
$present_count = mysqli_fetch_assoc($present_result)['present_total'];

$absent_sql = "SELECT COUNT(*) AS absent_total FROM attendance_tbl WHERE attendance = 0";
$absent_result = mysqli_query($conn, $absent_sql);
$absent_count = mysqli_fetch_assoc($absent_result)['absent_total'];

?>
<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>

<main class="min-h-screen main-font  bg-gray-50">

    <?php include "../includes/navbar2.php" ?>



    <div class="px-4">
        <!-- Navigation Bar -->


        <!-- Dashboard Content -->
        <section class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

            <!-- Cards -->

            <!-- From Uiverse.io by micaelgomestavares -->

            <div class="card w-full">
                <div class="title">
                    <span class="bg-green-400">
                        <i class="fa-solid fa-users text-white"></i>
                    </span>
                    <p class="title-text text-2xl font-bold">Present</p>
                </div>
                <div class="data">
                    <p><?= $present_count ?></p>
                    <a href="#"><span>Click here to see</span></a>
                </div>
            </div>

            <div class="card w-full">
                <div class="title">
                    <span class="bg-red-400">
                        <i class="fa-solid fa-users text-white"></i>
                    </span>
                    <p class="title-text text-2xl font-bold">Absents</p>
                </div>
                <div class="data">
                    <p><?= $absent_count ?></p>
                    <a href="#"><span>Click here to see</span></a>
                </div>
            </div>

        </section>

        <!-- Chart and Table -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

            <!-- Attendance Chart -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Weekly Attendance Chart</h3>
                <canvas id="attendanceChart" class="w-full h-[250px]"></canvas>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 overflow-auto">
                <h3 class="text-lg font-bold text-gray-700 mb-4">Today’s Attendance</h3>
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600 font-semibold">
                        <tr>
                            <th class="text-left p-2">Name</th>
                            <th class="text-left p-2">Status</th>
                            <th class="text-left p-2">Time In</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="p-2">Juan Dela Cruz</td>
                            <td class="p-2 text-green-600 font-medium">Present</td>
                            <td class="p-2">07:59 AM</td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">Maria Santos</td>
                            <td class="p-2 text-red-500 font-medium">Absent</td>
                            <td class="p-2">-</td>
                        </tr>
                        <tr class="border-b">
                            <td class="p-2">Jose Rizal</td>
                            <td class="p-2 text-yellow-500 font-medium">Late</td>
                            <td class="p-2">08:20 AM</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>


</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            datasets: [{
                label: 'Present',
                data: [100, 95, 90, 98, 102],
                backgroundColor: '#3b82f6'
            }, {
                label: 'Absent',
                data: [20, 25, 30, 22, 18],
                backgroundColor: '#ef4444'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Attendance per Day'
                }
            }
        }
    });



</script>


<?php include "../includes/footer.php" ?>