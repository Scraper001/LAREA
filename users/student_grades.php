<?php
$current_page = basename($_SERVER['PHP_SELF']);
include "../connection/conn.php";
include "functions/grade_functions.php";

$conn = conn();

// Get student ID from URL parameter
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($student_id <= 0) {
    header("Location: student_management.php");
    exit;
}

// Get student information
$student_sql = "SELECT * FROM students_tbl WHERE id = ?";
$stmt = mysqli_prepare($conn, $student_sql);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$student_result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($student_result);

if (!$student) {
    header("Location: student_management.php");
    exit;
}

// Get student grades and anecdotal records
$grades = getStudentGrades($student_id);
$anecdotalRecords = getStudentAnecdotalRecords($student_id);

// Calculate grade summary statistics
$gradingPeriods = ['1st Quarter', '2nd Quarter', '3rd Quarter', '4th Quarter'];
$subjects = [];
$gradeStats = [];

foreach ($grades as $grade) {
    $subject = $grade['subject'];
    $period = $grade['grading_period'];
    
    if (!isset($subjects[$subject])) {
        $subjects[$subject] = [];
    }
    
    if (!isset($subjects[$subject][$period])) {
        $subjects[$subject][$period] = [];
    }
    
    $subjects[$subject][$period][] = ($grade['grade_value'] / $grade['max_points']) * 100;
}

// Calculate averages
foreach ($subjects as $subject => $periods) {
    foreach ($periods as $period => $gradeList) {
        $average = array_sum($gradeList) / count($gradeList);
        $gradeStats[$subject][$period] = round($average, 2);
    }
}
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <div class="px-4 pb-20">
        <!-- Header Section -->
        <div class="mb-6">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="student_management.php" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                    <i class="fa-solid fa-arrow-left mr-2"></i>
                    <span class="text-sm font-medium">Back to Students</span>
                </a>
            </div>

            <!-- Student Info Card -->
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <img class="w-16 h-16 rounded-full object-cover" 
                             src="../<?= $student['photo_path'] ?>" 
                             alt="Profile picture"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzIiIGN5PSIzMiIgcj0iMzIiIGZpbGw9IiNGM0Y0RjYiLz4KPHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSIxNiIgeT0iMTYiPgo8cGF0aCBkPSJNMTYgMTZDMjAuNDE4MyAxNiAyNCAxMi40MTgzIDI0IDhDMjQgMy41ODE3IDIwLjQxODMgMCAxNiAwQzExLjU4MTcgMCA4IDMuNTgxNyA4IDhDOCAxMi40MTgzIDExLjU4MTcgMTYgMTYgMTZaIiBmaWxsPSIjOUNBM0FGIi8+Cjxzdmcgd2lkdGg9IjE2IiBoZWlnaHQ9IjEwIiB2aWV3Qm94PSIwIDAgMTYgMTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeD0iOCIgeT0iMjIiPgo8cGF0aCBkPSJNMCAwSDI0VjEwSDBWMFoiIGZpbGw9IiM5Q0EzQUYiLz4KPC9zdmc+Cjwvc3ZnPgo='" />
                    </div>
                    <div class="flex-1">
                        <h1 class="text-xl font-bold text-gray-900">
                            <?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname'] . ' ' . $student['Mname']) ?>
                        </h1>
                        <p class="text-sm text-gray-600">
                            LRN: <?= htmlspecialchars($student['LRN']) ?>
                        </p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <?= htmlspecialchars($student['GLevel']) ?>
                            </span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <?= htmlspecialchars($student['Course']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 text-center">
                    <div class="text-2xl font-bold text-blue-600"><?= count($grades) ?></div>
                    <div class="text-xs text-gray-500">Total Grades</div>
                </div>
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 text-center">
                    <div class="text-2xl font-bold text-green-600"><?= count(array_unique(array_column($grades, 'subject'))) ?></div>
                    <div class="text-xs text-gray-500">Subjects</div>
                </div>
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 text-center">
                    <div class="text-2xl font-bold text-orange-600"><?= count($anecdotalRecords) ?></div>
                    <div class="text-xs text-gray-500">Records</div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('grades')" id="gradesTab"
                        class="tab-button border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600">
                        Grades Summary
                    </button>
                    <button onclick="showTab('detailed')" id="detailedTab"
                        class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Detailed Grades
                    </button>
                    <button onclick="showTab('anecdotal')" id="anecdotalTab"
                        class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Anecdotal Records
                    </button>
                </nav>
            </div>
        </div>

        <!-- Grades Summary Tab -->
        <div id="gradesContent" class="tab-content">
            <?php if (!empty($gradeStats)) { ?>
                <div class="space-y-4">
                    <?php foreach ($gradeStats as $subject => $periods) { ?>
                        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3"><?= htmlspecialchars($subject) ?></h3>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <?php foreach ($gradingPeriods as $period) { ?>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <div class="text-xs text-gray-500 mb-1"><?= $period ?></div>
                                        <?php if (isset($periods[$period])) { ?>
                                            <?php 
                                            $average = $periods[$period];
                                            $color = $average >= 90 ? 'text-green-600' : ($average >= 80 ? 'text-blue-600' : ($average >= 75 ? 'text-yellow-600' : 'text-red-600'));
                                            ?>
                                            <div class="text-lg font-bold <?= $color ?>"><?= $average ?>%</div>
                                        <?php } else { ?>
                                            <div class="text-lg font-bold text-gray-400">--</div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm text-center">
                    <i class="fa-solid fa-chart-bar text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Grades Yet</h3>
                    <p class="text-gray-500">Grades will appear here once they are recorded.</p>
                </div>
            <?php } ?>
        </div>

        <!-- Detailed Grades Tab -->
        <div id="detailedContent" class="tab-content hidden">
            <?php if (!empty($grades)) { ?>
                <div class="space-y-3">
                    <?php foreach ($grades as $grade) { ?>
                        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">
                                        <?= htmlspecialchars($grade['subject']) ?>
                                    </span>
                                    <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2 py-1 rounded">
                                        <?= htmlspecialchars($grade['assessment_type']) ?>
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        <?= htmlspecialchars($grade['grading_period']) ?>
                                    </span>
                                </div>
                                
                                <div class="text-right">
                                    <?php 
                                    $percentage = ($grade['grade_value'] / $grade['max_points']) * 100;
                                    $color = $percentage >= 90 ? 'text-green-600' : ($percentage >= 80 ? 'text-blue-600' : ($percentage >= 75 ? 'text-yellow-600' : 'text-red-600'));
                                    ?>
                                    <div class="text-lg font-bold <?= $color ?>">
                                        <?= number_format($percentage, 1) ?>%
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?= $grade['grade_value'] ?>/<?= $grade['max_points'] ?>
                                    </div>
                                </div>
                            </div>

                            <h4 class="text-sm font-medium text-gray-900 mb-2">
                                <?= htmlspecialchars($grade['assessment_name']) ?>
                            </h4>
                            
                            <?php if (!empty($grade['remarks'])) { ?>
                                <p class="text-xs text-gray-600 mb-2">
                                    <strong>Remarks:</strong> <?= htmlspecialchars($grade['remarks']) ?>
                                </p>
                            <?php } ?>

                            <div class="flex items-center justify-between text-xs text-gray-500 pt-2 border-t border-gray-100">
                                <span>
                                    <i class="fa-solid fa-clock mr-1"></i>
                                    <?= date('M j, Y g:i A', strtotime($grade['date_recorded'])) ?>
                                </span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm text-center">
                    <i class="fa-solid fa-clipboard-list text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Detailed Grades</h3>
                    <p class="text-gray-500">Individual grade entries will appear here.</p>
                </div>
            <?php } ?>
        </div>

        <!-- Anecdotal Records Tab -->
        <div id="anecdotalContent" class="tab-content hidden">
            <?php if (!empty($anecdotalRecords)) { ?>
                <div class="space-y-3">
                    <?php foreach ($anecdotalRecords as $record) { ?>
                        <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-2">
                                    <?php 
                                    $typeColors = [
                                        'Behavioral' => 'bg-blue-100 text-blue-800',
                                        'Academic' => 'bg-green-100 text-green-800',
                                        'Social' => 'bg-purple-100 text-purple-800',
                                        'Achievement' => 'bg-yellow-100 text-yellow-800',
                                        'Concern' => 'bg-red-100 text-red-800'
                                    ];
                                    $colorClass = $typeColors[$record['record_type']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="<?= $colorClass ?> text-xs font-medium px-2 py-1 rounded">
                                        <?= htmlspecialchars($record['record_type']) ?>
                                    </span>
                                    
                                    <?php 
                                    $severityColors = [
                                        'Low' => 'bg-green-100 text-green-700',
                                        'Medium' => 'bg-yellow-100 text-yellow-700',
                                        'High' => 'bg-orange-100 text-orange-700',
                                        'Critical' => 'bg-red-100 text-red-700'
                                    ];
                                    $severityColor = $severityColors[$record['severity_level']] ?? 'bg-gray-100 text-gray-700';
                                    ?>
                                    <span class="<?= $severityColor ?> text-xs font-medium px-2 py-1 rounded">
                                        <?= htmlspecialchars($record['severity_level']) ?>
                                    </span>
                                    
                                    <?php if ($record['follow_up_required']) { ?>
                                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2 py-1 rounded">
                                            <i class="fa-solid fa-flag mr-1"></i>Follow-up
                                        </span>
                                    <?php } ?>
                                </div>
                                
                                <span class="text-xs text-gray-500">
                                    <?= date('M j, Y', strtotime($record['date_recorded'])) ?>
                                </span>
                            </div>

                            <h4 class="text-sm font-semibold text-gray-900 mb-2">
                                <?= htmlspecialchars($record['observation_title']) ?>
                            </h4>
                            
                            <p class="text-sm text-gray-700 mb-3">
                                <?= htmlspecialchars($record['observation_details']) ?>
                            </p>

                            <?php if (!empty($record['follow_up_notes'])) { ?>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                                    <p class="text-xs text-yellow-800">
                                        <strong>Follow-up Notes:</strong> <?= htmlspecialchars($record['follow_up_notes']) ?>
                                    </p>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm text-center">
                    <i class="fa-solid fa-clipboard text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Anecdotal Records</h3>
                    <p class="text-gray-500">Behavioral and academic observations will appear here.</p>
                </div>
            <?php } ?>
        </div>

        <!-- Quick Actions -->
        <div class="fixed bottom-20 right-4 space-y-2">
            <a href="grades.php"
                class="bg-blue-600 hover:bg-blue-700 text-white w-12 h-12 rounded-full flex items-center justify-center shadow-lg transition-colors duration-200">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>
    </div>
</main>

<script>
    // Tab switching functionality
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active styles from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Show selected tab content
        document.getElementById(tabName + 'Content').classList.remove('hidden');
        
        // Add active styles to selected tab
        const activeTab = document.getElementById(tabName + 'Tab');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-blue-500', 'text-blue-600');
    }

    // Initialize with grades tab active
    document.addEventListener('DOMContentLoaded', function() {
        showTab('grades');
    });
</script>

<?php include "../includes/footer.php" ?>