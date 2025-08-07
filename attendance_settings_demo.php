<?php
// Demo settings data
$currentSettings = [
    'school_start_time' => '08:00:00',
    'school_end_time' => '15:30:00',
    'late_threshold_minutes' => 15,
    'half_day_hours' => 4.0,
    'full_day_hours' => 8.0,
    'break_duration_minutes' => 60
];

// Handle settings update for demo
if ($_POST['action'] ?? '' === 'update_settings') {
    $success_message = "Attendance settings updated successfully! (Demo Mode)";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Settings - Demo</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        .demo-banner {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 100;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="demo-banner">
        ⚙️ DEMO MODE - Attendance Settings Configuration
    </div>

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
                    <a href="attendance_demo.php" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
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

                <!-- Features Demo -->
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">✨ Enhanced Features Demonstration</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <h5 class="font-medium text-blue-700">Automatic Features:</h5>
                            <ul class="text-blue-600 space-y-1">
                                <li>• Late detection based on start time + threshold</li>
                                <li>• Hours calculation with break deduction</li>
                                <li>• Real-time attendance statistics</li>
                                <li>• Bulk attendance operations</li>
                            </ul>
                        </div>
                        <div>
                            <h5 class="font-medium text-blue-700">Teacher-Friendly UI:</h5>
                            <ul class="text-blue-600 space-y-1">
                                <li>• One-click time setting with current time</li>
                                <li>• Visual status indicators with animations</li>
                                <li>• Enhanced custom alerts</li>
                                <li>• Hours worked display for each student</li>
                            </ul>
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

        <!-- Back to Attendance -->
        <div class="mt-6 text-center">
            <a href="attendance_demo.php" class="text-blue-600 hover:text-blue-800 underline text-lg">
                ← Back to Enhanced Attendance System
            </a>
        </div>
    </div>

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

        // Form validation and live preview updates
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input');

            // Update preview when inputs change
            inputs.forEach(input => {
                input.addEventListener('change', updatePreview);
            });

            function updatePreview() {
                const startTime = document.querySelector('input[name="school_start_time"]').value;
                const endTime = document.querySelector('input[name="school_end_time"]').value;
                const lateThreshold = document.querySelector('input[name="late_threshold_minutes"]').value;
                const breakDuration = document.querySelector('input[name="break_duration_minutes"]').value;
                const halfDayHours = document.querySelector('input[name="half_day_hours"]').value;
                const fullDayHours = document.querySelector('input[name="full_day_hours"]').value;

                // Update preview values
                const preview = document.querySelector('.bg-gray-50');
                if (preview) {
                    preview.innerHTML = `
                        <h4 class="font-semibold text-gray-800 mb-2">Updated Settings Preview</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">School Hours:</span><br>
                                <span class="font-medium">${startTime} - ${endTime}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Late After:</span><br>
                                <span class="font-medium">${lateThreshold} minutes</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Break Time:</span><br>
                                <span class="font-medium">${breakDuration} minutes</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Daily Hours:</span><br>
                                <span class="font-medium">${halfDayHours}h / ${fullDayHours}h</span>
                            </div>
                        </div>
                    `;
                }
            }

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
                
                // Re-enable button after delay (for demo)
                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }, 2000);
            });
        });
    </script>
</body>
</html>