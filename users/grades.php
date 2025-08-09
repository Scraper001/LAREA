<?php
$current_page = basename($_SERVER['PHP_SELF']);
include "../connection/conn.php";
include "functions/grade_functions.php";

$conn = conn();

// Get all students with their grade summaries
$students = getAllStudentsWithSummaries();
$statistics = getGradeStatistics();
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <div class="px-4 pb-20">
        <!-- Header Section -->
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">STUDENT GRADE DASHBOARD</h1>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Total Students</p>
                            <p class="text-lg font-bold text-gray-900"><?= count($students) ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i class="fa-solid fa-chart-line text-green-600 text-sm"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-500">Avg. Performance</p>
                            <p class="text-lg font-bold text-gray-900"><?= $statistics['avg_percentage'] ?? 0 ?>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchStudents"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search students by name, LRN, or grade level...">
            </div>
        </div>

        <!-- Student Cards Grid -->
        <div class="space-y-4" id="studentsGrid">
            <?php if (!empty($students)) { ?>
                <?php foreach ($students as $student) { ?>
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm student-card cursor-pointer hover:shadow-md transition-shadow duration-200"
                        onclick="openStudentGradingSheet(<?= $student['id'] ?>, '<?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?>')">

                        <!-- Student Header -->
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">
                                        <?= strtoupper(substr($student['Fname'], 0, 1) . substr($student['Lname'], 0, 1)) ?>
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        LRN: <?= htmlspecialchars($student['LRN']) ?> •
                                        <?= htmlspecialchars($student['GLevel']) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex space-x-2">
                                <button
                                    onclick="event.stopPropagation(); addQuickGrade(<?= $student['id'] ?>, '<?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?>')"
                                    class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50 transition-colors">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                                <button onclick="event.stopPropagation(); exportStudentGrades(<?= $student['id'] ?>)"
                                    class="text-green-600 hover:text-green-800 p-2 rounded-lg hover:bg-green-50 transition-colors">
                                    <i class="fa-solid fa-download"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Grade Summary Stats -->
                        <div class="grid grid-cols-4 gap-3 mb-3">
                            <div class="text-center">
                                <p class="text-xs text-gray-500">Overall</p>
                                <p class="text-lg font-bold text-gray-900">
                                    <?= round($student['overall_average'] ?? 0, 1) ?>%
                                </p>
                                <span
                                    class="text-xs px-2 py-1 rounded-full 
                                    <?= ($student['overall_average'] ?? 0) >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= ($student['overall_average'] ?? 0) >= 60 ? 'Passing' : 'Needs Help' ?>
                                </span>
                            </div>

                            <div class="text-center">
                                <p class="text-xs text-gray-500">Total Grades</p>
                                <p class="text-lg font-bold text-blue-600"><?= $student['total_grades'] ?? 0 ?></p>
                            </div>

                            <div class="text-center">
                                <p class="text-xs text-gray-500">Passed</p>
                                <p class="text-lg font-bold text-green-600"><?= $student['status_passed'] ?? 0 ?></p>
                            </div>

                            <div class="text-center">
                                <p class="text-xs text-gray-500">Failed</p>
                                <p class="text-lg font-bold text-red-600"><?= $student['status_failed'] ?? 0 ?></p>
                            </div>
                        </div>

                        <!-- Subject Performance Preview -->
                        <?php if (!empty($student['subject_averages'])) { ?>
                            <div class="border-t border-gray-100 pt-3">
                                <p class="text-xs text-gray-500 mb-2">Subject Performance</p>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach (array_slice($student['subject_averages'], 0, 3) as $subject => $average) { ?>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between">
                                                <span class="text-xs text-gray-600 truncate"><?= htmlspecialchars($subject) ?></span>
                                                <span
                                                    class="text-xs font-medium <?= $average >= 60 ? 'text-green-600' : 'text-red-600' ?>">
                                                    <?= round($average, 1) ?>%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                                <div class="h-1 rounded-full <?= $average >= 60 ? 'bg-green-500' : 'bg-red-500' ?>"
                                                    style="width: <?= min($average, 100) ?>%"></div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (count($student['subject_averages']) > 3) { ?>
                                        <span class="text-xs text-gray-400">+<?= count($student['subject_averages']) - 3 ?> more</span>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Recent Activity -->
                        <?php if (!empty($student['recent_grade'])) { ?>
                            <div class="border-t border-gray-100 pt-3 mt-3">
                                <p class="text-xs text-gray-500 mb-1">Latest Grade</p>
                                <div class="flex items-center justify-between">
                                    <span
                                        class="text-sm text-gray-700"><?= htmlspecialchars($student['recent_grade']['assessment_name']) ?></span>
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="text-sm font-medium"><?= round($student['recent_grade']['percentage'], 1) ?>%</span>
                                        <span
                                            class="text-xs px-2 py-1 rounded-full 
                                            <?= $student['recent_grade']['grade_status'] === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= $student['recent_grade']['grade_status'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Click to View Hint -->
                        <div class="border-t border-gray-100 pt-3 mt-3 text-center">
                            <p class="text-xs text-gray-400">
                                <i class="fa-solid fa-mouse-pointer mr-1"></i>
                                Click to view full grading sheet
                            </p>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm text-center">
                    <i class="fa-solid fa-user-graduate text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Students Found</h3>
                    <p class="text-gray-500 mb-4">Add students to start tracking their grades.</p>
                    <a href="student_management.php"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fa-solid fa-plus mr-2"></i>Add Students
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Student Grading Sheet Modal -->
    <div id="gradingSheetModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-4 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900" id="modalStudentName">Student Grading Sheet</h3>
                    <p class="text-sm text-gray-500" id="modalStudentInfo">Loading...</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="addGradeToSheet()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium">
                        <i class="fa-solid fa-plus mr-1"></i>Add Grade
                    </button>
                    <button id="closeGradingSheetModal" type="button" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Grading Sheet Content -->
            <div id="gradingSheetContent" class="max-h-96 overflow-y-auto">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    <span class="ml-2 text-gray-600">Loading grading sheet...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Grade Modal -->
    <div id="addGradeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900" id="addGradeModalTitle">Add New Grade</h3>
                <button id="closeAddGradeModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="addGradeForm" class="space-y-4">
                <input type="hidden" id="modalStudentId" name="student_id">
                <input type="hidden" id="modalStudentLRN" name="lrn">
                <input type="hidden" id="editGradeId" name="grade_id">

                <!-- Subject -->
                <div>
                    <label for="gradeSubject" class="block text-sm font-medium text-gray-700 mb-1">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select id="gradeSubject" name="subject" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Subject</option>
                        <option value="Mathematics">Mathematics</option>
                        <option value="Science">Science</option>
                        <option value="English">English</option>
                        <option value="Filipino">Filipino</option>
                        <option value="Social Studies">Social Studies</option>
                        <option value="PE">Physical Education</option>
                        <option value="Arts">Arts</option>
                        <option value="Music">Music</option>
                        <option value="Computer">Computer</option>
                        <option value="Research">Research</option>
                    </select>
                </div>

                <!-- Assessment Type -->
                <div>
                    <label for="gradeAssessmentType" class="block text-sm font-medium text-gray-700 mb-1">
                        Assessment Type <span class="text-red-500">*</span>
                    </label>
                    <select id="gradeAssessmentType" name="assessment_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Quiz">Quiz</option>
                        <option value="Exam">Exam</option>
                        <option value="Assignment">Assignment</option>
                        <option value="Project">Project</option>
                        <option value="Recitation">Recitation</option>
                        <option value="Performance Task">Performance Task</option>
                        <option value="Laboratory">Laboratory</option>
                    </select>
                </div>

                <!-- Assessment Name -->
                <div>
                    <label for="gradeAssessmentName" class="block text-sm font-medium text-gray-700 mb-1">
                        Assessment Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="gradeAssessmentName" name="assessment_name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., Midterm Exam, Quiz 1">
                </div>

                <!-- Grade and Max Points -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="gradeValue" class="block text-sm font-medium text-gray-700 mb-1">
                            Score <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="gradeValue" name="grade_value" required min="0" step="0.01"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="85">
                    </div>
                    <div>
                        <label for="gradeMaxPoints" class="block text-sm font-medium text-gray-700 mb-1">
                            Max Points <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="gradeMaxPoints" name="max_points" required min="1" step="0.01"
                            value="100"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="100">
                    </div>
                </div>

                <!-- Grade Preview -->
                <div id="gradePreview" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-3">
                    <div class="text-sm">
                        <span class="text-gray-600">Percentage: </span>
                        <span id="previewPercentage" class="font-semibold text-gray-900">0%</span>
                        <span class="ml-3 text-gray-600">Status: </span>
                        <span id="previewStatus" class="font-semibold"></span>
                        <span class="ml-3 text-gray-600">Category: </span>
                        <span id="previewCategory" class="font-semibold"></span>
                    </div>
                </div>

                <!-- Grading Period -->
                <div>
                    <label for="gradingPeriod" class="block text-sm font-medium text-gray-700 mb-1">
                        Grading Period <span class="text-red-500">*</span>
                    </label>
                    <select id="gradingPeriod" name="grading_period" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1st Quarter">1st Quarter</option>
                        <option value="2nd Quarter">2nd Quarter</option>
                        <option value="3rd Quarter">3rd Quarter</option>
                        <option value="4th Quarter">4th Quarter</option>
                        <option value="Final">Final</option>
                    </select>
                </div>

                <!-- Remarks -->
                <div>
                    <label for="gradeRemarks" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks
                    </label>
                    <textarea id="gradeRemarks" name="remarks" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Optional remarks about the grade..."></textarea>
                </div>

                <!-- Modal Footer -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="cancelAddGradeModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit" id="submitGradeBtn"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-plus mr-1"></i>
                        Add Grade
                    </button>
                </div>
            </form>
        </div>
    </div>

</main>

<script>
    let currentStudentId = null;
    let currentStudentName = '';

    // Grade calculation function (60% passing)
    function calculateGradePreview() {
        const gradeValue = parseFloat(document.getElementById('gradeValue').value) || 0;
        const maxPoints = parseFloat(document.getElementById('gradeMaxPoints').value) || 100;

        if (gradeValue && maxPoints) {
            const percentage = (gradeValue / maxPoints) * 100;
            const status = percentage >= 60 ? 'Passed' : 'Failed';

            let category = 'Failed';
            if (percentage >= 95) category = 'Outstanding';
            else if (percentage >= 90) category = 'Very Satisfactory';
            else if (percentage >= 85) category = 'Satisfactory';
            else if (percentage >= 80) category = 'Fairly Satisfactory';
            else if (percentage >= 75) category = 'Did Not Meet Expectations';
            else if (percentage >= 60) category = 'Beginning';

            document.getElementById('previewPercentage').textContent = percentage.toFixed(1) + '%';
            document.getElementById('previewStatus').textContent = status;
            document.getElementById('previewStatus').className = `font-semibold ${status === 'Passed' ? 'text-green-600' : 'text-red-600'}`;
            document.getElementById('previewCategory').textContent = category;

            // Category colors
            const categoryColors = {
                'Outstanding': 'text-purple-600',
                'Very Satisfactory': 'text-indigo-600',
                'Satisfactory': 'text-blue-600',
                'Fairly Satisfactory': 'text-green-600',
                'Did Not Meet Expectations': 'text-yellow-600',
                'Beginning': 'text-orange-600',
                'Failed': 'text-red-600'
            };
            document.getElementById('previewCategory').className = `font-semibold ${categoryColors[category]}`;

            document.getElementById('gradePreview').classList.remove('hidden');
        } else {
            document.getElementById('gradePreview').classList.add('hidden');
        }
    }

    // Add event listeners for grade calculation
    document.getElementById('gradeValue').addEventListener('input', calculateGradePreview);
    document.getElementById('gradeMaxPoints').addEventListener('input', calculateGradePreview);

    // Search functionality
    document.getElementById('searchStudents').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const studentCards = document.querySelectorAll('.student-card');

        studentCards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Open student grading sheet
    function openStudentGradingSheet(studentId, studentName) {
        currentStudentId = studentId;
        currentStudentName = studentName;
        document.getElementById('modalStudentName').textContent = studentName + ' - Grading Sheet';
        document.getElementById('modalStudentInfo').textContent = 'Loading student information...';

        // Show modal
        document.getElementById('gradingSheetModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Load grading sheet
        loadGradingSheet(studentId);
    }

    // Load grading sheet data
    function loadGradingSheet(studentId) {
        fetch('functions/grade_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_grading_sheet&student_id=${studentId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderGradingSheet(data.data);
                } else {
                    document.getElementById('gradingSheetContent').innerHTML =
                        '<div class="text-center py-8 text-red-600">Failed to load grading sheet</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('gradingSheetContent').innerHTML =
                    '<div class="text-center py-8 text-red-600">Error loading grading sheet</div>';
            });
    }

    // Render grading sheet as structured table
    function renderGradingSheet(gradingData) {
        let html = '';

        if (Object.keys(gradingData).length === 0) {
            html = `
                <div class="text-center py-8">
                    <i class="fa-solid fa-chart-line text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Grades Yet</h3>
                    <p class="text-gray-500 mb-4">Start adding grades for this student.</p>
                    <button onclick="addGradeToSheet()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fa-solid fa-plus mr-2"></i>Add First Grade
                    </button>
                </div>
            `;
        } else {
            // Group by grading period
            Object.keys(gradingData).forEach(period => {
                html += `
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-3 border-b border-gray-200 pb-2">
                            ${period}
                        </h4>
                        <div class="grid gap-4">
                `;

                // Group by subject within each period
                Object.keys(gradingData[period]).forEach(subject => {
                    const grades = gradingData[period][subject];
                    const subjectAverage = grades.reduce((sum, grade) => sum + parseFloat(grade.percentage), 0) / grades.length;

                    html += `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-medium text-gray-900">${subject}</h5>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">Average:</span>
                                    <span class="font-semibold ${subjectAverage >= 60 ? 'text-green-600' : 'text-red-600'}">
                                        ${subjectAverage.toFixed(1)}%
                                    </span>
                                    <span class="text-xs px-2 py-1 rounded-full ${subjectAverage >= 60 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${subjectAverage >= 60 ? 'Passing' : 'Failing'}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-2 px-3 font-medium text-gray-700">Assessment</th>
                                            <th class="text-center py-2 px-3 font-medium text-gray-700">Type</th>
                                            <th class="text-center py-2 px-3 font-medium text-gray-700">Score</th>
                                            <th class="text-center py-2 px-3 font-medium text-gray-700">Percentage</th>
                                            <th class="text-center py-2 px-3 font-medium text-gray-700">Status</th>
                                            <th class="text-center py-2 px-3 font-medium text-gray-700">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                    `;

                    grades.forEach(grade => {
                        html += `
                            <tr class="border-b border-gray-100 hover:bg-white">
                                <td class="py-2 px-3">
                                    <div>
                                        <div class="font-medium text-gray-900">${grade.assessment_name}</div>
                                        ${grade.remarks ? `<div class="text-xs text-gray-500">${grade.remarks}</div>` : ''}
                                    </div>
                                                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">${grade.assessment_type}</span>
                                </td>
                                <td class="py-2 px-3 text-center font-medium">${grade.grade_value}/${grade.max_points}</td>
                                <td class="py-2 px-3 text-center">
                                    <span class="font-semibold ${parseFloat(grade.percentage) >= 60 ? 'text-green-600' : 'text-red-600'}">
                                        ${parseFloat(grade.percentage).toFixed(1)}%
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="text-xs px-2 py-1 rounded-full ${grade.grade_status === 'Passed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${grade.grade_status}
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <div class="flex items-center justify-center space-x-1">
                                        <button onclick="editGradeInSheet(${grade.grade_id})" class="text-blue-600 hover:text-blue-800 p-1">
                                            <i class="fa-solid fa-edit text-xs"></i>
                                        </button>
                                        <button onclick="deleteGradeInSheet(${grade.grade_id}, '${grade.assessment_name}')" class="text-red-600 hover:text-red-800 p-1">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });

                    html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            });
        }

        document.getElementById('gradingSheetContent').innerHTML = html;
    }

    // Add grade to sheet function
    function addGradeToSheet() {
        if (!currentStudentId) {
            showAlert('error', 'Error', 'No student selected');
            return;
        }

        // Reset form and set student data
        document.getElementById('addGradeForm').reset();
        document.getElementById('modalStudentId').value = currentStudentId;
        document.getElementById('editGradeId').value = '';
        document.getElementById('addGradeModalTitle').textContent = `Add Grade for ${currentStudentName}`;
        document.getElementById('submitGradeBtn').innerHTML = '<i class="fa-solid fa-plus mr-1"></i>Add Grade';
        document.getElementById('gradePreview').classList.add('hidden');

        // Get student LRN
        fetch('functions/grade_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_student_summary&student_id=${currentStudentId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    document.getElementById('modalStudentLRN').value = data.data.LRN;
                }
            });

        // Show add grade modal
        document.getElementById('addGradeModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Quick add grade function
    function addQuickGrade(studentId, studentName) {
        currentStudentId = studentId;
        currentStudentName = studentName;
        addGradeToSheet();
    }

    // Edit grade in sheet
    function editGradeInSheet(gradeId) {
        // Get grade data
        fetch('functions/grade_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_grade_by_id&grade_id=${gradeId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const grade = data.data;

                    // Fill form with existing data
                    document.getElementById('modalStudentId').value = grade.student_id;
                    document.getElementById('modalStudentLRN').value = grade.LRN;
                    document.getElementById('editGradeId').value = grade.grade_id;
                    document.getElementById('gradeSubject').value = grade.subject;
                    document.getElementById('gradeAssessmentType').value = grade.assessment_type;
                    document.getElementById('gradeAssessmentName').value = grade.assessment_name;
                    document.getElementById('gradeValue').value = grade.grade_value;
                    document.getElementById('gradeMaxPoints').value = grade.max_points;
                    document.getElementById('gradingPeriod').value = grade.grading_period;
                    document.getElementById('gradeRemarks').value = grade.remarks || '';

                    // Update modal title and button
                    document.getElementById('addGradeModalTitle').textContent = `Edit Grade for ${grade.Fname} ${grade.Lname}`;
                    document.getElementById('submitGradeBtn').innerHTML = '<i class="fa-solid fa-save mr-1"></i>Update Grade';

                    // Calculate preview
                    calculateGradePreview();

                    // Show modal
                    document.getElementById('addGradeModal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Error', 'Failed to load grade data');
            });
    }

    // Delete grade in sheet
    function deleteGradeInSheet(gradeId, assessmentName) {
        if (confirm(`Are you sure you want to delete "${assessmentName}"?`)) {
            fetch('functions/grade_functions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=delete_grade&grade_id=${gradeId}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Success!', data.message);
                        // Reload grading sheet
                        loadGradingSheet(currentStudentId);
                        // Reload page to update student cards
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert('error', 'Error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Connection Error', 'Unable to connect to server.');
                });
        }
    }

    // Export student grades
    function exportStudentGrades(studentId) {
        window.open(`functions/grade_functions.php?action=export_csv&student_id=${studentId}`, '_blank');
    }

    // Form submission handler
    document.getElementById('addGradeForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);
        const isEdit = document.getElementById('editGradeId').value;

        if (isEdit) {
            formData.append('action', 'update_grade');
        } else {
            formData.append('action', 'add_grade');
        }

        const submitButton = document.getElementById('submitGradeBtn');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> ' + (isEdit ? 'Updating...' : 'Adding...');
        submitButton.disabled = true;

        fetch('functions/grade_functions.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Success!', data.message + (data.percentage ? ` (${data.percentage}% - ${data.status})` : ''));
                    closeAddGradeModal();

                    // If grading sheet is open, reload it
                    if (currentStudentId && !document.getElementById('gradingSheetModal').classList.contains('hidden')) {
                        loadGradingSheet(currentStudentId);
                    }

                    // Reload page to update student cards
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('error', 'Error', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'Connection Error', 'Unable to connect to server.');
            })
            .finally(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
    });

    // Modal close functions
    function closeAddGradeModal() {
        document.getElementById('addGradeModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('addGradeForm').reset();
        document.getElementById('gradePreview').classList.add('hidden');
    }

    // Close grading sheet modal
    document.getElementById('closeGradingSheetModal').addEventListener('click', function () {
        document.getElementById('gradingSheetModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
        currentStudentId = null;
        currentStudentName = '';
    });

    // Close add grade modal
    document.getElementById('closeAddGradeModal').addEventListener('click', closeAddGradeModal);
    document.getElementById('cancelAddGradeModal').addEventListener('click', closeAddGradeModal);

    // Alert function
    function showAlert(type, title, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'fixed top-4 right-4 z-50 max-w-sm w-full';

        const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
        const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
        const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

        alertDiv.innerHTML = `
            <div class="${bgColor} border rounded-lg p-4 shadow-lg">
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
        setTimeout(() => alertDiv.remove(), 5000);
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const gradingModal = document.getElementById('gradingSheetModal');
            const addGradeModal = document.getElementById('addGradeModal');

            if (!gradingModal.classList.contains('hidden')) {
                gradingModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentStudentId = null;
                currentStudentName = '';
            }

            if (!addGradeModal.classList.contains('hidden')) {
                closeAddGradeModal();
            }
        }
    });

    // Close modals when clicking outside
    document.getElementById('gradingSheetModal').addEventListener('click', function (e) {
        if (e.target === this) {
            this.classList.add('hidden');
            document.body.style.overflow = 'auto';
            currentStudentId = null;
            currentStudentName = '';
        }
    });

    document.getElementById('addGradeModal').addEventListener('click', function (e) {
        if (e.target === this) {
            closeAddGradeModal();
        }
    });

    // Auto-refresh statistics every 30 seconds
    setInterval(function () {
        fetch('functions/grade_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_statistics'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update statistics without full page reload
                    const stats = data.data;
                    document.querySelectorAll('.text-lg.font-bold.text-gray-900')[1].textContent = (stats.avg_percentage || 0) + '%';
                }
            })
            .catch(error => console.log('Stats update failed:', error));
    }, 30000);
</script>

<?php include "../includes/footer.php" ?>