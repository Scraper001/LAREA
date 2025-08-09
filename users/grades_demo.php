<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Mock data for demo purposes
$grades = [
    [
        'grade_id' => 1,
        'Fname' => 'Charles',
        'Lname' => 'Babbage',
        'Mname' => 'H',
        'GLevel' => 'Grade 7',
        'Course' => 'N/A',
        'LRN' => 123123123123,
        'subject' => 'Mathematics',
        'assessment_type' => 'Quiz',
        'assessment_name' => 'Algebra Quiz 1',
        'grade_value' => 85.00,
        'max_points' => 100.00,
        'grading_period' => '1st Quarter',
        'remarks' => 'Good understanding of basic algebra',
        'date_recorded' => '2025-08-01 10:30:00'
    ],
    [
        'grade_id' => 2,
        'Fname' => 'Charles',
        'Lname' => 'Babbage',
        'Mname' => 'H',
        'GLevel' => 'Grade 7',
        'Course' => 'N/A',
        'LRN' => 123123123123,
        'subject' => 'Science',
        'assessment_type' => 'Exam',
        'assessment_name' => 'Physics Midterm',
        'grade_value' => 92.50,
        'max_points' => 100.00,
        'grading_period' => '1st Quarter',
        'remarks' => 'Excellent performance in physics concepts',
        'date_recorded' => '2025-08-02 14:15:00'
    ],
    [
        'grade_id' => 3,
        'Fname' => 'Charles',
        'Lname' => 'Babbage',
        'Mname' => 'H',
        'GLevel' => 'Grade 7',
        'Course' => 'N/A',
        'LRN' => 123123123123,
        'subject' => 'English',
        'assessment_type' => 'Assignment',
        'assessment_name' => 'Essay Writing',
        'grade_value' => 88.00,
        'max_points' => 100.00,
        'grading_period' => '1st Quarter',
        'remarks' => 'Creative and well-structured essay',
        'date_recorded' => '2025-08-03 09:45:00'
    ]
];

$students = [
    [
        'id' => 3,
        'LRN' => 123123123123,
        'Fname' => 'Charles',
        'Lname' => 'Babbage',
        'Mname' => 'H',
        'GLevel' => 'Grade 7',
        'Course' => 'N/A'
    ]
];
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <div class="px-4 pb-20">
        <!-- Demo Notice -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex items-center">
                <i class="fa-solid fa-info-circle text-blue-600 mr-2"></i>
                <span class="text-sm text-blue-800">
                    <strong>Demo Mode:</strong> This is a demonstration of the Grade Management System with sample data.
                </span>
            </div>
        </div>

        <!-- Header Section -->
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">GRADE MANAGEMENT</h1>
            
            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fa-solid fa-star text-blue-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Total Grades</p>
                            <p class="text-lg font-bold text-gray-900"><?= count($grades) ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i class="fa-solid fa-graduation-cap text-green-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Students</p>
                            <p class="text-lg font-bold text-gray-900"><?= count($students) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="flex gap-2 mb-4">
                <!-- Add Grade Button -->
                <button id="addGradeButton"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Grade
                </button>

                <!-- Add Anecdotal Record Button -->
                <button id="addAnecdotalButton"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-clipboard mr-2"></i>
                    Add Record
                </button>
                
                <!-- Filter Button -->
                <button id="filterButton"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchGrades"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search grades, students, or subjects...">
            </div>
        </div>

        <!-- Grades List -->
        <div class="space-y-3 h-[500px] overflow-y-auto" id="gradesList">
            <?php foreach ($grades as $grade) { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm grade-card">
                    <!-- Student Info Header -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900">
                                    <?= htmlspecialchars($grade['Fname'] . ' ' . $grade['Lname']) ?>
                                </h3>
                                <p class="text-xs text-gray-500">
                                    <?= htmlspecialchars($grade['GLevel']) ?> - LRN: <?= htmlspecialchars($grade['LRN']) ?>
                                </p>
                            </div>
                        </div>
                        
                        <!-- Grade Score -->
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">
                                <?= number_format(($grade['grade_value'] / $grade['max_points']) * 100, 1) ?>%
                            </div>
                            <div class="text-xs text-gray-500">
                                <?= $grade['grade_value'] ?>/<?= $grade['max_points'] ?>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Details -->
                    <div class="mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                    <?= htmlspecialchars($grade['subject']) ?>
                                </span>
                                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded">
                                    <?= htmlspecialchars($grade['assessment_type']) ?>
                                </span>
                            </div>
                            <span class="text-xs text-gray-500">
                                <?= htmlspecialchars($grade['grading_period']) ?>
                            </span>
                        </div>
                        
                        <h4 class="text-sm font-medium text-gray-900 mb-1">
                            <?= htmlspecialchars($grade['assessment_name']) ?>
                        </h4>
                        
                        <?php if (!empty($grade['remarks'])) { ?>
                            <p class="text-xs text-gray-600">
                                <?= htmlspecialchars($grade['remarks']) ?>
                            </p>
                        <?php } ?>
                    </div>

                    <!-- Date and Actions -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-500">
                            <i class="fa-solid fa-clock mr-1"></i>
                            <?= date('M j, Y', strtotime($grade['date_recorded'])) ?>
                        </span>
                        
                        <div class="flex space-x-2">
                            <button onclick="showDemoAlert('Edit functionality coming soon!')"
                                class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fa-solid fa-edit"></i>
                            </button>
                            <button onclick="showDemoAlert('Delete functionality coming soon!')"
                                class="text-red-600 hover:text-red-800 text-sm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Demo Modal -->
        <div id="demoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                <div class="text-center">
                    <i class="fa-solid fa-info-circle text-blue-600 text-4xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Demo Mode</h3>
                    <p class="text-gray-600 mb-4">This is a demonstration of the Grade Management System interface.</p>
                    <p class="text-sm text-gray-500 mb-6">Full functionality requires database setup.</p>
                    <button onclick="closeDemoModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                        Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Demo alert function
    function showDemoAlert(message) {
        alert(message);
    }

    // Modal functions
    function openDemoModal() {
        document.getElementById('demoModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDemoModal() {
        document.getElementById('demoModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Button event listeners
    document.getElementById('addGradeButton').addEventListener('click', function() {
        openDemoModal();
    });

    document.getElementById('addAnecdotalButton').addEventListener('click', function() {
        openDemoModal();
    });

    document.getElementById('filterButton').addEventListener('click', function() {
        showDemoAlert('Filter functionality coming soon!');
    });

    // Search functionality (demo)
    document.getElementById('searchGrades').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const gradeCards = document.querySelectorAll('.grade-card');
        
        gradeCards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Close modal when clicking outside
    document.getElementById('demoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDemoModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!document.getElementById('demoModal').classList.contains('hidden')) {
                closeDemoModal();
            }
        }
    });
</script>

<?php include "../includes/footer.php" ?>