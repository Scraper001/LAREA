<?php
$current_page = basename($_SERVER['PHP_SELF']);
session_start();
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/attendance_functions.php" ?>
<?php include "functions/attendance_reports.php" ?>
<?php include "functions/time_calculation_utils.php" ?>

<?php
// Handle report generation
if ($_POST['action'] ?? '' === 'generate_report') {
    $report_type = $_POST['report_type'];
    $format = $_POST['format'] ?? 'html';
    
    switch ($report_type) {
        case 'daily':
            $date = $_POST['date'];
            $report_content = generateDailyReport($date, $format);
            $filename = "daily_report_" . $date;
            break;
            
        case 'weekly':
            $start_date = $_POST['start_date'];
            $report_content = generateWeeklyReport($start_date, $format);
            $filename = "weekly_report_" . $start_date;
            break;
            
        case 'monthly':
            $year = $_POST['year'];
            $month = $_POST['month'];
            $report_content = generateMonthlyReport($year, $month, $format);
            $filename = "monthly_report_" . $year . "_" . sprintf('%02d', $month);
            break;
            
        case 'custom':
            $start_date = $_POST['custom_start_date'];
            $end_date = $_POST['custom_end_date'];
            $grade_filter = $_POST['grade_filter'] ?? '';
            $student_filter = $_POST['student_filter'] ?? '';
            $report_content = generateCustomReport($start_date, $end_date, $grade_filter, $student_filter, $format);
            $filename = "custom_report_" . $start_date . "_to_" . $end_date;
            break;
    }
    
    if ($format === 'html' && isset($report_content)) {
        // Save and display HTML report
        $saved_file = saveReport($filename, $report_type, 
            $start_date ?? $date ?? $year.'-'.$month.'-01', 
            $end_date ?? $date ?? date('Y-m-t', strtotime($year.'-'.$month.'-01')), 
            $grade_filter ?? '', $student_filter ?? '', 
            $report_content, $_SESSION['user_id'] ?? null);
        
        if ($saved_file) {
            $success_message = "Report generated successfully!";
            $report_url = $saved_file;
        } else {
            $error_message = "Failed to save report.";
        }
    } elseif ($format === 'csv' && isset($report_content)) {
        // Download CSV
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        echo $report_content;
        exit;
    }
}

// Get saved reports
$savedReports = getSavedReports(20);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Reports - LAREA</title>
    <link href="../assets/css/enhanced-alerts.css" rel="stylesheet">
    <style>
        .report-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .report-type-card {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .report-type-card:hover {
            border-color: #4f46e5;
            background: #f8fafc;
        }
        
        .report-type-card.active {
            border-color: #4f46e5;
            background: #eef2ff;
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
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
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Attendance Reports & Analytics</h1>
            <p class="text-gray-600">Generate comprehensive attendance reports and analyze student data.</p>
        </div>

        <!-- Report Generation -->
        <div class="report-card">
            <h2 class="text-2xl font-bold mb-6">Generate New Report</h2>
            
            <!-- Report Type Selection -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="report-type-card" data-type="daily">
                    <h3 class="font-bold text-lg mb-2">📅 Daily Report</h3>
                    <p class="text-sm text-gray-600">Detailed attendance for a specific date</p>
                </div>
                <div class="report-type-card" data-type="weekly">
                    <h3 class="font-bold text-lg mb-2">📊 Weekly Report</h3>
                    <p class="text-sm text-gray-600">Week-by-week attendance summary</p>
                </div>
                <div class="report-type-card" data-type="monthly">
                    <h3 class="font-bold text-lg mb-2">📈 Monthly Report</h3>
                    <p class="text-sm text-gray-600">Complete monthly attendance analysis</p>
                </div>
                <div class="report-type-card" data-type="custom">
                    <h3 class="font-bold text-lg mb-2">⚙️ Custom Report</h3>
                    <p class="text-sm text-gray-600">Custom date range with filters</p>
                </div>
            </div>

            <form method="POST" id="reportForm">
                <input type="hidden" name="action" value="generate_report">
                <input type="hidden" name="report_type" id="selectedReportType">

                <!-- Daily Report Form -->
                <div class="form-section" id="daily-form">
                    <h3 class="text-lg font-semibold mb-4">Daily Report Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                            <select name="format" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="html">HTML Report</option>
                                <option value="csv">CSV Export</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Weekly Report Form -->
                <div class="form-section" id="weekly-form">
                    <h3 class="text-lg font-semibold mb-4">Weekly Report Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Week Start Date</label>
                            <input type="date" name="start_date" value="<?php echo date('Y-m-d', strtotime('monday this week')); ?>" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                            <select name="format" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="html">HTML Report</option>
                                <option value="csv">CSV Export</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Monthly Report Form -->
                <div class="form-section" id="monthly-form">
                    <h3 class="text-lg font-semibold mb-4">Monthly Report Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select name="year" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <?php for ($y = 2020; $y <= date('Y') + 1; $y++): ?>
                                    <option value="<?php echo $y; ?>" <?php echo $y == date('Y') ? 'selected' : ''; ?>>
                                        <?php echo $y; ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                            <select name="month" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php echo $m; ?>" <?php echo $m == date('n') ? 'selected' : ''; ?>>
                                        <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                            <select name="format" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="html">HTML Report</option>
                                <option value="csv">CSV Export</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Custom Report Form -->
                <div class="form-section" id="custom-form">
                    <h3 class="text-lg font-semibold mb-4">Custom Report Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="custom_start_date" value="<?php echo date('Y-m-01'); ?>" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="custom_end_date" value="<?php echo date('Y-m-d'); ?>" 
                                class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Grade Filter</label>
                            <select name="grade_filter" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="">All Grades</option>
                                <?php foreach (getGradeLevels() as $level): ?>
                                    <option value="<?php echo $level; ?>">Grade <?php echo $level; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student Search</label>
                            <input type="text" name="student_filter" placeholder="Student name..." 
                                class="w-full border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Format</label>
                            <select name="format" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                <option value="html">HTML Report</option>
                                <option value="csv">CSV Export</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 text-white py-3 px-6 rounded-md text-lg font-semibold hover:bg-blue-700 transition duration-200">
                        Generate Report
                    </button>
                </div>
            </form>
        </div>

        <!-- Generated Report Display -->
        <?php if (isset($report_url)): ?>
            <div class="report-card">
                <h2 class="text-2xl font-bold mb-4">Generated Report</h2>
                <iframe src="<?php echo $report_url; ?>" class="w-full h-96 border rounded"></iframe>
                <div class="mt-4">
                    <a href="<?php echo $report_url; ?>" target="_blank" 
                        class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700">
                        Open in New Window
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Saved Reports -->
        <div class="report-card">
            <h2 class="text-2xl font-bold mb-6">Recent Reports</h2>
            <?php if ($savedReports->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($report = $savedReports->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($report['report_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo ucfirst($report['report_type']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $report['date_from'] . ' to ' . $report['date_to']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y H:i', strtotime($report['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if (file_exists($report['file_path'])): ?>
                                            <a href="<?php echo $report['file_path']; ?>" target="_blank" 
                                                class="text-blue-600 hover:text-blue-900">View</a>
                                        <?php else: ?>
                                            <span class="text-gray-400">File not found</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-8">No reports generated yet.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="../assets/js/enhanced-alerts.js"></script>
<script>
    // Report type selection
    document.addEventListener('DOMContentLoaded', function() {
        const reportCards = document.querySelectorAll('.report-type-card');
        const formSections = document.querySelectorAll('.form-section');
        const reportTypeInput = document.getElementById('selectedReportType');

        reportCards.forEach(card => {
            card.addEventListener('click', function() {
                const type = this.dataset.type;
                
                // Remove active class from all cards
                reportCards.forEach(c => c.classList.remove('active'));
                // Add active class to clicked card
                this.classList.add('active');
                
                // Hide all form sections
                formSections.forEach(section => section.classList.remove('active'));
                // Show selected form section
                document.getElementById(type + '-form').classList.add('active');
                
                // Set hidden input value
                reportTypeInput.value = type;
            });
        });

        // Auto-close legacy alerts
        function closeAlert(alertId) {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 500);
            }
        }

        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.id) {
                    closeAlert(alert.id);
                }
            });
        }, 5000);
    });
</script>

<?php include "../includes/footer.php" ?>
</body>
</html>