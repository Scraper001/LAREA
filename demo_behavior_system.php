<?php
// Test page to demonstrate the enhanced behavior management interface
$current_page = basename($_SERVER['PHP_SELF']);

// Mock data for demonstration
$mock_behavior_records = [
    [
        'id' => 1,
        'LRN' => '123123123123',
        'Fname' => 'Charles',
        'Lname' => 'Babage',
        'Mname' => 'H',
        'behavior_type' => 'Commendable',
        'behavior_category' => 'Academic Excellence',
        'severity_level' => 'Low',
        'status' => 'Active',
        'follow_up_required' => 0,
        'remarks' => 'Outstanding performance in Mathematics class. Helped peers understand complex problems.',
        'date_entry' => '2025-01-15 10:30:00',
        'photo_path' => 'uploads/student_photos/default.jpg',
        'GLevel' => 'Grade 7',
        'Course' => 'N/A',
        'follow_up_notes' => ''
    ],
    [
        'id' => 2,
        'LRN' => '123123123123',
        'Fname' => 'Charles',
        'Lname' => 'Babage',
        'Mname' => 'H',
        'behavior_type' => 'Needs Improvement',
        'behavior_category' => 'Tardiness',
        'severity_level' => 'Medium',
        'status' => 'Follow-up Required',
        'follow_up_required' => 1,
        'remarks' => 'Student has been arriving late to morning classes consistently for the past week.',
        'date_entry' => '2025-01-14 08:15:00',
        'photo_path' => 'uploads/student_photos/default.jpg',
        'GLevel' => 'Grade 7',
        'Course' => 'N/A',
        'follow_up_notes' => 'Contact parents and discuss morning routine.'
    ]
];

$has_records = count($mock_behavior_records) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAREA - Student Behavior Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">

<main class="min-h-screen main-font bg-gray-50">
    <div class="bg-white shadow-sm border-b">
        <div class="px-4 py-3">
            <h1 class="text-xl font-bold text-gray-900">LAREA - Learning Area Record and Evaluation Application</h1>
            <p class="text-sm text-gray-600">Student Behavior Management System</p>
        </div>
    </div>

    <div class="px-4 pb-20 pt-6">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">STUDENT BEHAVIOR RECORDS</h1>
            <div class="flex flex-wrap gap-2 mb-4">
                <button id="addBehaviorButton"
                    class="flex-1 min-w-0 bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Behavior
                </button>
                <button id="filterButton"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <button id="reportButton"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-chart-bar mr-1"></i>
                    Report
                </button>
            </div>
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                <input type="text" id="table-search-behavior"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search for behavior records">
            </div>
        </div>

        <div class="space-y-3 h-[500px] overflow-y-auto">
            <?php if ($has_records): ?>
                <?php foreach ($mock_behavior_records as $record): ?>
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="flex-shrink-0">
                                <input type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold text-lg">
                                    <?php echo substr($record['Fname'], 0, 1) . substr($record['Lname'], 0, 1); ?>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                    <?php echo $record['Fname'] . " " . $record['Lname'] . " " . $record['Mname'] ?>
                                </h3>
                                <p class="text-sm text-gray-500 truncate">
                                    LRN: <?php echo $record['LRN'] ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <!-- Behavior Type and Category Badges -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    <?php 
                                    echo match($record['behavior_type']) {
                                        'Commendable' => 'bg-green-100 text-green-800 border border-green-200',
                                        'Needs Improvement' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                        'Violation' => 'bg-red-100 text-red-800 border border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border border-gray-200'
                                    };
                                    ?>">
                                    <?php echo $record['behavior_type'] ?>
                                </span>
                                
                                <?php if (!empty($record['behavior_category'])): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <?php echo $record['behavior_category'] ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($record['severity_level'])): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    <?php 
                                    echo match($record['severity_level']) {
                                        'Low' => 'bg-green-50 text-green-700 border border-green-200',
                                        'Medium' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                        'High' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                        'Critical' => 'bg-red-50 text-red-700 border border-red-200',
                                        default => 'bg-gray-50 text-gray-700 border border-gray-200'
                                    };
                                    ?>">
                                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                    <?php echo $record['severity_level'] ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($record['status']) && $record['status'] !== 'Active'): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    <?php 
                                    echo match($record['status']) {
                                        'Resolved' => 'bg-green-50 text-green-700 border border-green-200',
                                        'Follow-up Required' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                        'Archived' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                        default => 'bg-blue-50 text-blue-700 border border-blue-200'
                                    };
                                    ?>">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    <?php echo $record['status'] ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($record['follow_up_required']) && $record['follow_up_required'] == 1): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                    <i class="fa-solid fa-bell mr-1"></i>
                                    Follow-up Required
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <p class="text-sm text-gray-700">
                                <strong>Date:</strong> <?php echo date('M d, Y', strtotime($record['date_entry'])) ?>
                            </p>
                            
                            <?php if (!empty($record['remarks'])): ?>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Remarks:</strong> <?php echo htmlspecialchars($record['remarks']) ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($record['follow_up_notes'])): ?>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Follow-up Notes:</strong> <?php echo htmlspecialchars($record['follow_up_notes']) ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($record['GLevel']) || !empty($record['Course'])): ?>
                            <p class="text-xs text-gray-500 mt-2">
                                <?php if (!empty($record['GLevel'])): ?>
                                    <span class="mr-3">Grade: <?php echo $record['GLevel'] ?></span>
                                <?php endif; ?>
                                <?php if (!empty($record['Course']) && $record['Course'] !== 'N/A'): ?>
                                    <span>Course: <?php echo $record['Course'] ?></span>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex space-x-2">
                            <button onclick="showAlert('info', 'Demo Mode', 'This is a demonstration of the enhanced behavior management system.')"
                                class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-edit mr-1"></i>
                                Edit
                            </button>
                            <button onclick="showAlert('info', 'Demo Mode', 'This is a demonstration of the enhanced behavior management system.')"
                                class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-trash mr-1"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm text-center">
                    <i class="fa-solid fa-clipboard-list text-4xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Behavior Records Found</h3>
                    <p class="text-gray-500 mb-4">Start by adding a new behavior record for a student.</p>
                    <button id="addFirstRecord" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Add First Record
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-gray-700">
                Showing <span class="font-medium">1</span> to <span class="font-medium"><?php echo count($mock_behavior_records); ?></span> of <span
                    class="font-medium"><?php echo count($mock_behavior_records); ?></span> results
            </p>
            <div class="flex space-x-1">
                <button
                    class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </button>
                <button
                    class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600 text-white font-medium">
                    1
                </button>
                <button
                    class="w-10 h-10 flex items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </button>
            </div>
        </div>

        <div class="mt-4 bg-white rounded-lg border border-gray-200 p-4">
            <label class="flex items-center space-x-3">
                <input type="checkbox" id="checkbox-all-search"
                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Select All Records</span>
            </label>
        </div>
    </div>

</main>

<script>
    // Custom Alert Function
    function showAlert(type, title, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'fixed top-4 right-4 z-50 max-w-sm w-full';
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 
                       type === 'info' ? 'bg-blue-50 border-blue-200' :
                       'bg-red-50 border-red-200';
        const iconColor = type === 'success' ? 'text-green-500' : 
                         type === 'info' ? 'text-blue-500' :
                         'text-red-500';
        const textColor = type === 'success' ? 'text-green-800' : 
                         type === 'info' ? 'text-blue-800' :
                         'text-red-800';
        const icon = type === 'success' ? 'fa-check-circle' : 
                    type === 'info' ? 'fa-info-circle' :
                    'fa-exclamation-circle';
        alertDiv.innerHTML = `
            <div class="${bgColor} border rounded-lg p-4 shadow-lg animate-slide-in">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fa-solid ${icon} ${iconColor} text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-sm font-semibold ${textColor}">${title}</h3>
                        <p class="text-sm ${textColor} mt-1">${message}</p>
                    </div>
                    <button onclick="this.closest('.fixed').remove()" class="ml-3 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(alertDiv);
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Demo functionality for buttons
    document.getElementById('addBehaviorButton')?.addEventListener('click', function() {
        showAlert('info', 'Demo Mode', 'This demonstrates the Add Behavior functionality. In the full system, this would open a form to record new behavior incidents.');
    });

    document.getElementById('filterButton')?.addEventListener('click', function() {
        showAlert('info', 'Filter Feature', 'This would open a comprehensive filter dialog allowing you to filter by behavior type, category, severity, status, date range, and more.');
    });

    document.getElementById('reportButton')?.addEventListener('click', function() {
        showAlert('info', 'Reporting Feature', 'This would generate detailed behavior reports with statistics, trends, and charts for administrative review.');
    });

    document.getElementById('addFirstRecord')?.addEventListener('click', function() {
        showAlert('info', 'Demo Mode', 'This demonstrates the Add Behavior functionality for new users.');
    });

    // Show welcome message
    setTimeout(() => {
        showAlert('success', 'Enhanced Behavior Management', 'Welcome to the comprehensive behavior management system with severity levels, categories, status tracking, and reporting features!');
    }, 1000);
</script>

<style>
    @keyframes slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out;
    }
</style>

</body>
</html>