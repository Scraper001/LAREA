<?php
$current_page = basename($_SERVER['PHP_SELF']);
session_start();
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/attendance_functions.php" ?>

<?php
// Handle settings update
if ($_POST['action'] ?? '' === 'update_settings') {
    $settings = [
        'school_start_time' => $_POST['school_start_time'] ?? '08:00:00',
        'school_end_time' => $_POST['school_end_time'] ?? '17:00:00',
        'late_threshold_minutes' => (int)($_POST['late_threshold_minutes'] ?? 15),
        'half_day_hours' => (float)($_POST['half_day_hours'] ?? 4.0),
        'full_day_hours' => (float)($_POST['full_day_hours'] ?? 8.0),
        'break_duration_minutes' => (int)($_POST['break_duration_minutes'] ?? 60)
    ];

    if (updateAttendanceSettings($settings)) {
        $success_message = "Attendance settings updated successfully!";
    } else {
        $error_message = "Failed to update attendance settings. Please try again.";
    }
}

// Get current settings
$currentSettings = getAttendanceSettings();
?>

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
                    <h1 class="text-3xl font-bold text-gray-800">Attendance Settings</h1>
                    <p class="text-gray-600">Configure school hours and attendance rules</p>
                </div>
                <div class="text-right">
                    <a href="attendance.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        Back to Attendance
                    </a>
                </div>
            </div>
        </div>

        <!-- Settings Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- School Hours -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">School Hours</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">School Start Time</label>
                            <input type="time" name="school_start_time" 
                                   value="<?php echo $currentSettings['school_start_time']; ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">School End Time</label>
                            <input type="time" name="school_end_time" 
                                   value="<?php echo $currentSettings['school_end_time']; ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Break Duration (minutes)</label>
                            <input type="number" name="break_duration_minutes" min="0" max="180"
                                   value="<?php echo $currentSettings['break_duration_minutes']; ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Automatically deducted from total hours worked</p>
                        </div>
                    </div>

                    <!-- Attendance Rules -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Attendance Rules</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Late Threshold (minutes)</label>
                            <input type="number" name="late_threshold_minutes" min="1" max="60"
                                   value="<?php echo $currentSettings['late_threshold_minutes']; ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Students arriving after this time are marked as late</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Half Day Hours</label>
                            <input type="number" name="half_day_hours" min="1" max="8" step="0.5"
                                   value="<?php echo $currentSettings['half_day_hours']; ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Day Hours</label>
                            <input type="number" name="full_day_hours" min="4" max="12" step="0.5"
                                   value="<?php echo $currentSettings['full_day_hours']; ?>"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Current Settings Preview -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-semibold text-gray-800 mb-2">Current Settings Summary</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">School Hours:</span><br>
                            <span class="font-medium"><?php echo $currentSettings['school_start_time']; ?> - <?php echo $currentSettings['school_end_time']; ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Late After:</span><br>
                            <span class="font-medium"><?php echo $currentSettings['late_threshold_minutes']; ?> minutes</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Break Time:</span><br>
                            <span class="font-medium"><?php echo $currentSettings['break_duration_minutes']; ?> minutes</span>
                        </div>
                        <div>
                            <span class="text-gray-600">Daily Hours:</span><br>
                            <span class="font-medium"><?php echo $currentSettings['half_day_hours']; ?>h / <?php echo $currentSettings['full_day_hours']; ?>h</span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-green-600 text-white py-2 px-6 rounded-md text-lg font-semibold hover:bg-green-700 transition duration-200">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

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
</style>

<script>
    // Close alert function
    function closeAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        }
    }

    // Auto-close alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.classList.add('fade-out');
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const startTime = document.querySelector('input[name="school_start_time"]').value;
            const endTime = document.querySelector('input[name="school_end_time"]').value;
            
            if (startTime && endTime && startTime >= endTime) {
                alert('School end time must be after start time.');
                e.preventDefault();
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;
            
            // Re-enable button after delay (in case of errors)
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 5000);
        });
    });
</script>

<?php include "../includes/footer.php" ?>