<?php
$current_page = basename($_SERVER['PHP_SELF']);

include "functions/grade_functions.php";

$conn = conn();

// Get student info if student_id is provided
$student_info = null;
$student_grades = [];
$student_stats = [];
$grade_distribution = [];
$improvement_trends = [];

if (isset($_GET['student_id']) && !empty($_GET['student_id'])) {
    $student_id = (int)$_GET['student_id'];
    
    // Get student information
    $sql = "SELECT * FROM students_tbl WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student_info = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($student_info) {
        $student_grades = getStudentGrades($student_id);
        $student_stats = getStudentGradeStatistics($student_id);
        $grade_distribution = getGradeDistribution($student_id);
        $improvement_trends = getGradeImprovementTrends($student_id);
    }
}

// Get all students for dropdown
$students = getAllStudents();
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <div class="px-4 pb-20">
        <!-- Header Section -->
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">STUDENT GRADE SUMMARY</h1>

            <!-- Student Selection -->
            <div class="mb-4">
                <label for="studentSelect" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Student
                </label>
                <select id="studentSelect" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Choose a student...</option>
                    <?php foreach ($students as $student) { ?>
                        <option value="<?= $student['id'] ?>" <?= (isset($student_id) && $student_id == $student['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?> - <?= $student['GLevel'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <?php if ($student_info) { ?>
                <!-- Student Info Card -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 mb-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <?php if ($student_info['photo_path'] && file_exists("../" . $student_info['photo_path'])) { ?>
                                <img src="<?= htmlspecialchars("../" . $student_info['photo_path']) ?>" 
                                     alt="Student Photo" class="w-16 h-16 rounded-full object-cover">
                            <?php } else { ?>
                                <i class="fa-solid fa-user text-blue-600 text-2xl"></i>
                            <?php } ?>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-bold text-gray-900">
                                <?= htmlspecialchars($student_info['Fname'] . ' ' . $student_info['Mname'] . ' ' . $student_info['Lname']) ?>
                            </h2>
                            <p class="text-gray-600">
                                LRN: <?= htmlspecialchars($student_info['LRN']) ?> | 
                                Grade Level: <?= htmlspecialchars($student_info['GLevel']) ?> |
                                Course: <?= htmlspecialchars($student_info['Course']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Overview -->
                <?php if (!empty($student_stats) && $student_stats['total_assessments'] > 0) { ?>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <!-- Overall Performance -->
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                            <div class="text-center">
                                <div class="text-2xl font-bold <?= $student_stats['average_percentage'] >= 75 ? 'text-green-600' : 'text-red-600' ?>">
                                    <?= round($student_stats['average_percentage'], 1) ?>%
                                </div>
                                <p class="text-xs text-gray-500">Overall Average</p>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $student_stats['average_percentage'] >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $student_stats['average_percentage'] >= 75 ? 'PASSING' : 'FAILING' ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Pass Rate -->
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">
                                    <?= $student_stats['pass_rate'] ?>%
                                </div>
                                <p class="text-xs text-gray-500">Pass Rate</p>
                                <p class="text-xs text-gray-400">
                                    <?= $student_stats['passed_assessments'] ?>/<?= $student_stats['total_assessments'] ?> assessments
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Grade Distribution -->
                    <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200 mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Grade Distribution</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Excellent (90-100%)</span>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-medium">
                                        <?= $student_stats['excellent_grades'] ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Very Good (85-89%)</span>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-medium">
                                        <?= $student_stats['very_good_grades'] ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Good (80-84%)</span>
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full font-medium">
                                        <?= $student_stats['good_grades'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Satisfactory (75-79%)</span>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full font-medium">
                                        <?= $student_stats['satisfactory_grades'] ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Needs Improvement (70-74%)</span>
                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full font-medium">
                                        <?= $student_stats['needs_improvement_grades'] ?>
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Failed (<70%)</span>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
                                        <?= $student_stats['failed_grades'] ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 text-center">
                            <div class="text-lg font-bold text-green-600"><?= $student_stats['highest_grade'] ?>%</div>
                            <p class="text-xs text-gray-500">Highest Grade</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 text-center">
                            <div class="text-lg font-bold text-red-600"><?= $student_stats['lowest_grade'] ?>%</div>
                            <p class="text-xs text-gray-500">Lowest Grade</p>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200 text-center">
                            <div class="text-lg font-bold text-gray-600"><?= $student_stats['total_assessments'] ?></div>
                            <p class="text-xs text-gray-500">Total Assessments</p>
                        </div>
                    </div>
                <?php } ?>

                <!-- Action Buttons -->
                <div class="flex gap-2 mb-4">
                    <button onclick="exportGradeReport(<?= $student_id ?>)"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fa-solid fa-download mr-2"></i>Export Report
                    </button>
                    <button onclick="showGradeHistory(<?= $student_id ?>)"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fa-solid fa-chart-line mr-2"></i>Grade History
                    </button>
                </div>

                <!-- Individual Grades List -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Individual Grades</h3>
                    </div>
                    
                    <?php if (!empty($student_grades)) { ?>
                        <div class="max-h-96 overflow-y-auto">
                            <?php 
                            $grouped_grades = [];
                            foreach ($student_grades as $grade) {
                                $key = $grade['subject'] . '|' . $grade['grading_period'];
                                $grouped_grades[$key][] = $grade;
                            }
                            ?>
                            
                            <?php foreach ($grouped_grades as $key => $grades) { 
                                list($subject, $period) = explode('|', $key);
                                $subject_average = getStudentSubjectAverage($student_id, $subject, $period);
                            ?>
                                <div class="border-b border-gray-100 last:border-b-0">
                                    <!-- Subject Header -->
                                    <div class="p-4 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($subject) ?></h4>
                                                <p class="text-sm text-gray-600"><?= htmlspecialchars($period) ?></p>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-lg font-bold <?= $subject_average >= 75 ? 'text-green-600' : 'text-red-600' ?>">
                                                    <?= $subject_average ?>%
                                                </div>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $subject_average >= 75 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                                    <?= $subject_average >= 75 ? 'PASS' : 'FAIL' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Individual Assessments -->
                                    <div class="divide-y divide-gray-100">
                                        <?php foreach ($grades as $grade) { 
                                            $percentage = ($grade['grade_value'] / $grade['max_points']) * 100;
                                            $grade_color = '';
                                            $grade_category = '';
                                            
                                            if ($percentage >= 90) {
                                                $grade_color = 'text-green-600';
                                                $grade_category = 'Excellent';
                                            } elseif ($percentage >= 85) {
                                                $grade_color = 'text-blue-600';
                                                $grade_category = 'Very Good';
                                            } elseif ($percentage >= 80) {
                                                $grade_color = 'text-indigo-600';
                                                $grade_category = 'Good';
                                            } elseif ($percentage >= 75) {
                                                $grade_color = 'text-yellow-600';
                                                $grade_category = 'Satisfactory';
                                            } elseif ($percentage >= 70) {
                                                $grade_color = 'text-orange-600';
                                                $grade_category = 'Needs Improvement';
                                            } else {
                                                $grade_color = 'text-red-600';
                                                $grade_category = 'Failed';
                                            }
                                        ?>
                                            <div class="p-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <h5 class="font-medium text-gray-900"><?= htmlspecialchars($grade['assessment_name']) ?></h5>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                                                <?= htmlspecialchars($grade['assessment_type']) ?>
                                                            </span>
                                                            <span class="text-xs text-gray-500">
                                                                <?= date('M j, Y', strtotime($grade['date_recorded'])) ?>
                                                            </span>
                                                        </div>
                                                        <?php if (!empty($grade['remarks'])) { ?>
                                                            <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($grade['remarks']) ?></p>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="text-right ml-4">
                                                        <div class="text-lg font-bold <?= $grade_color ?>">
                                                            <?= number_format($percentage, 1) ?>%
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            <?= $grade['grade_value'] ?>/<?= $grade['max_points'] ?>
                                                        </div>
                                                        <div class="text-xs font-medium <?= $grade_color ?> mt-1">
                                                            <?= $grade_category ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="p-8 text-center">
                            <i class="fa-solid fa-chart-line text-4xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Grades Found</h3>
                            <p class="text-gray-500">This student has no recorded grades yet.</p>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <!-- No Student Selected -->
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm text-center">
                    <i class="fa-solid fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Student</h3>
                    <p class="text-gray-500">Choose a student from the dropdown above to view their grade summary and analytics.</p>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<script>
    // Handle student selection
    document.getElementById('studentSelect').addEventListener('change', function() {
        const studentId = this.value;
        if (studentId) {
            window.location.href = `student_grades.php?student_id=${studentId}`;
        }
    });

    // Export grade report function
    function exportGradeReport(studentId) {
        // Create form for report generation
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'functions/grade_functions.php';
        form.target = '_blank';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'generate_report';

        const studentIdInput = document.createElement('input');
        studentIdInput.type = 'hidden';
        studentIdInput.name = 'student_id';
        studentIdInput.value = studentId;

        form.appendChild(actionInput);
        form.appendChild(studentIdInput);
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }

    // Show grade history function
    function showGradeHistory(studentId) {
        fetch('functions/grade_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_improvement_trends&student_id=${studentId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showGradeHistoryModal(data.data);
            } else {
                alert('Failed to load grade history');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load grade history');
        });
    }

    // Show grade history modal
    function showGradeHistoryModal(historyData) {
        // Create and show modal with history data
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        
        let historyHtml = '<div class="space-y-3">';
        if (historyData.length > 0) {
            historyData.forEach(record => {
                const improvementClass = record.improvement > 0 ? 'text-green-600' : 'text-red-600';
                const improvementIcon = record.improvement > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                
                historyHtml += `
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-medium text-gray-900">${record.subject}</h5>
                                <p class="text-sm text-gray-600">${record.grading_period}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-900">${record.new_percentage}%</div>
                                ${record.improvement != 0 ? `
                                    <div class="text-sm ${improvementClass}">
                                        <i class="fa-solid ${improvementIcon}"></i> ${Math.abs(record.improvement)}%
                                    </div>
                                ` : ''}
                                <div class="text-xs text-gray-500">${new Date(record.date_changed).toLocaleDateString()}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            historyHtml += '<p class="text-gray-500 text-center">No grade history available</p>';
        }
        historyHtml += '</div>';

        modal.innerHTML = `
            <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Grade History</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    ${historyHtml}
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
</script>

<?php include "../includes/footer.php" ?>