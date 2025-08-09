<?php
$current_page = basename($_SERVER['PHP_SELF']);

include "functions/grade_functions.php";

$conn = conn();

// Get all grades for display
$grades = getAllGrades(50);
$students = getAllStudents();
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>

<main class="min-h-screen main-font bg-gray-50">
    <?php include "../includes/navbar2.php" ?>

    <div class="px-4 pb-20">
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

                <!-- Bulk Grade Entry Button -->
                <button id="bulkGradeButton"
                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-list mr-2"></i>
                    Bulk Entry
                </button>

                <!-- Filter Button -->
                <button id="filterButton"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-filter"></i>
                </button>

                <!-- Analytics Button -->
                <button id="analyticsButton"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-chart-bar"></i>
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
            <?php if (!empty($grades)) { ?>
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
                                <?php 
                                $percentage = ($grade['grade_value'] / $grade['max_points']) * 100;
                                $is_passing = $percentage >= 75;
                                $grade_color = $is_passing ? 'text-green-600' : 'text-red-600';
                                $status_bg = $is_passing ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                $status_text = $is_passing ? 'PASS' : 'FAIL';
                                
                                // Determine grade category
                                $grade_category = '';
                                if ($percentage >= 90) {
                                    $grade_category = 'Excellent';
                                } elseif ($percentage >= 85) {
                                    $grade_category = 'Very Good';
                                } elseif ($percentage >= 80) {
                                    $grade_category = 'Good';
                                } elseif ($percentage >= 75) {
                                    $grade_category = 'Satisfactory';
                                } elseif ($percentage >= 70) {
                                    $grade_category = 'Needs Improvement';
                                } else {
                                    $grade_category = 'Failed';
                                }
                                ?>
                                <div class="text-lg font-bold <?= $grade_color ?>">
                                    <?= number_format($percentage, 1) ?>%
                                </div>
                                <div class="text-xs text-gray-500 mb-1">
                                    <?= $grade['grade_value'] ?>/<?= $grade['max_points'] ?>
                                </div>
                                <div class="flex flex-col space-y-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $status_bg ?>">
                                        <?= $status_text ?>
                                    </span>
                                    <span class="text-xs font-medium <?= $grade_color ?>">
                                        <?= $grade_category ?>
                                    </span>
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
                                <button onclick="editGrade(<?= htmlspecialchars(json_encode($grade)) ?>)"
                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fa-solid fa-edit"></i>
                                </button>
                                <button
                                    onclick="deleteGrade(<?= $grade['grade_id'] ?>, '<?= htmlspecialchars($grade['assessment_name']) ?>')"
                                    class="text-red-600 hover:text-red-800 text-sm">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-8 shadow-sm text-center">
                    <i class="fa-solid fa-chart-line text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Grades Yet</h3>
                    <p class="text-gray-500 mb-4">Start adding grades to track student performance.</p>
                    <button onclick="document.getElementById('addGradeButton').click()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fa-solid fa-plus mr-2"></i>Add First Grade
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Add Grade Modal -->
    <div id="addGradeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New Grade</h3>
                <button id="closeAddGradeModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="addGradeForm" class="space-y-4">
                <!-- Student Selection -->
                <div>
                    <label for="gradeStudent" class="block text-sm font-medium text-gray-700 mb-1">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select id="gradeStudent" name="student_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Student</option>
                        <?php foreach ($students as $student) { ?>
                            <option value="<?= $student['id'] ?>" data-lrn="<?= $student['LRN'] ?>">
                                <?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?> -
                                <?= $student['GLevel'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="gradeStudentLRN" name="lrn">
                </div>

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
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-plus mr-1"></i>
                        Add Grade
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Anecdotal Record Modal -->
    <div id="addAnecdotalModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add Anecdotal Record</h3>
                <button id="closeAddAnecdotalModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="addAnecdotalForm" class="space-y-4">
                <!-- Student Selection -->
                <div>
                    <label for="anecdotalStudent" class="block text-sm font-medium text-gray-700 mb-1">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select id="anecdotalStudent" name="student_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Student</option>
                        <?php foreach ($students as $student) { ?>
                            <option value="<?= $student['id'] ?>" data-lrn="<?= $student['LRN'] ?>">
                                <?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?> -
                                <?= $student['GLevel'] ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="hidden" id="anecdotalStudentLRN" name="lrn">
                </div>

                <!-- Record Type -->
                <div>
                    <label for="anecdotalType" class="block text-sm font-medium text-gray-700 mb-1">
                        Record Type <span class="text-red-500">*</span>
                    </label>
                    <select id="anecdotalType" name="record_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Behavioral">Behavioral</option>
                        <option value="Academic">Academic</option>
                        <option value="Social">Social</option>
                        <option value="Achievement">Achievement</option>
                        <option value="Concern">Concern</option>
                    </select>
                </div>

                <!-- Observation Title -->
                <div>
                    <label for="anecdotalTitle" class="block text-sm font-medium text-gray-700 mb-1">
                        Observation Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="anecdotalTitle" name="observation_title" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Brief title describing the observation">
                </div>

                <!-- Observation Details -->
                <div>
                    <label for="anecdotalDetails" class="block text-sm font-medium text-gray-700 mb-1">
                        Observation Details <span class="text-red-500">*</span>
                    </label>
                    <textarea id="anecdotalDetails" name="observation_details" required rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Detailed description of the observation..."></textarea>
                </div>

                <!-- Severity Level -->
                <div>
                    <label for="anecdotalSeverity" class="block text-sm font-medium text-gray-700 mb-1">
                        Severity Level
                    </label>
                    <select id="anecdotalSeverity" name="severity_level"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Critical">Critical</option>
                    </select>
                </div>

                <!-- Follow-up Required -->
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="anecdotalFollowUp" name="follow_up_required" value="1"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Follow-up required</span>
                    </label>
                </div>

                <!-- Modal Footer -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="cancelAddAnecdotalModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                        <i class="fa-solid fa-plus mr-1"></i>
                        Add Record
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Grade Entry Modal -->
    <div id="bulkGradeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Bulk Grade Entry</h3>
                <button id="closeBulkGradeModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Bulk Entry Instructions -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                <h4 class="font-medium text-blue-900 mb-2">Instructions:</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• Fill in the common information below</li>
                    <li>• Add students and their individual grades</li>
                    <li>• Click "Add Grade Row" to add more students</li>
                    <li>• Review all entries before submitting</li>
                </ul>
            </div>

            <!-- Common Grade Information -->
            <form id="bulkGradeForm" class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Common Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Subject -->
                        <div>
                            <label for="bulkSubject" class="block text-sm font-medium text-gray-700 mb-1">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <select id="bulkSubject" name="subject" required
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
                            <label for="bulkAssessmentType" class="block text-sm font-medium text-gray-700 mb-1">
                                Assessment Type <span class="text-red-500">*</span>
                            </label>
                            <select id="bulkAssessmentType" name="assessment_type" required
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
                            <label for="bulkAssessmentName" class="block text-sm font-medium text-gray-700 mb-1">
                                Assessment Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="bulkAssessmentName" name="assessment_name" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g., Midterm Exam, Quiz 1">
                        </div>

                        <!-- Max Points -->
                        <div>
                            <label for="bulkMaxPoints" class="block text-sm font-medium text-gray-700 mb-1">
                                Max Points <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="bulkMaxPoints" name="max_points" required min="1" step="0.01"
                                value="100"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="100">
                        </div>

                        <!-- Grading Period -->
                        <div>
                            <label for="bulkGradingPeriod" class="block text-sm font-medium text-gray-700 mb-1">
                                Grading Period <span class="text-red-500">*</span>
                            </label>
                            <select id="bulkGradingPeriod" name="grading_period" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="1st Quarter">1st Quarter</option>
                                <option value="2nd Quarter">2nd Quarter</option>
                                <option value="3rd Quarter">3rd Quarter</option>
                                <option value="4th Quarter">4th Quarter</option>
                                <option value="Final">Final</option>
                            </select>
                        </div>

                        <!-- Remarks (Optional) -->
                        <div>
                            <label for="bulkRemarks" class="block text-sm font-medium text-gray-700 mb-1">
                                Common Remarks
                            </label>
                            <input type="text" id="bulkRemarks" name="remarks"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Optional common remarks">
                        </div>
                    </div>
                </div>

                <!-- Student Grades Section -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-gray-900">Student Grades</h4>
                        <button type="button" id="addGradeRow" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                            <i class="fa-solid fa-plus mr-1"></i>Add Student
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-300 rounded-lg">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Student</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Score</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Percentage</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Status</th>
                                    <th class="px-3 py-2 text-left text-sm font-medium text-gray-700 border-b">Action</th>
                                </tr>
                            </thead>
                            <tbody id="gradeRowsContainer">
                                <!-- Grade rows will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex space-x-3 pt-4 border-t">
                    <button type="button" id="cancelBulkGradeModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500">
                        <i class="fa-solid fa-save mr-1"></i>
                        Save All Grades
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Analytics Modal -->
    <div id="analyticsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-5 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Grade Analytics</h3>
                <button id="closeAnalyticsModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Analytics Content -->
            <div id="analyticsContent">
                <div class="text-center py-8">
                    <i class="fa-solid fa-spinner fa-spin text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Loading analytics...</p>
                </div>
            </div>
        </div>
    </div>

</main>

<script>
    // Modal elements
    const addGradeModal = document.getElementById('addGradeModal');
    const addAnecdotalModal = document.getElementById('addAnecdotalModal');
    const bulkGradeModal = document.getElementById('bulkGradeModal');
    const analyticsModal = document.getElementById('analyticsModal');
    const addGradeButton = document.getElementById('addGradeButton');
    const addAnecdotalButton = document.getElementById('addAnecdotalButton');
    const bulkGradeButton = document.getElementById('bulkGradeButton');
    const analyticsButton = document.getElementById('analyticsButton');

    // Modal control functions
    function openAddGradeModal() {
        addGradeModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddGradeModal() {
        addGradeModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('addGradeForm').reset();
    }

    function openAddAnecdotalModal() {
        addAnecdotalModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeAddAnecdotalModal() {
        addAnecdotalModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('addAnecdotalForm').reset();
    }

    function openBulkGradeModal() {
        bulkGradeModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        // Add initial grade row
        addGradeRow();
    }

    function closeBulkGradeModal() {
        bulkGradeModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        document.getElementById('bulkGradeForm').reset();
        document.getElementById('gradeRowsContainer').innerHTML = '';
    }

    function openAnalyticsModal() {
        analyticsModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        loadAnalytics();
    }

    function closeAnalyticsModal() {
        analyticsModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Event listeners
    addGradeButton.addEventListener('click', openAddGradeModal);
    addAnecdotalButton.addEventListener('click', openAddAnecdotalModal);
    bulkGradeButton.addEventListener('click', openBulkGradeModal);
    analyticsButton.addEventListener('click', openAnalyticsModal);

    document.getElementById('closeAddGradeModal').addEventListener('click', closeAddGradeModal);
    document.getElementById('cancelAddGradeModal').addEventListener('click', closeAddGradeModal);
    document.getElementById('closeAddAnecdotalModal').addEventListener('click', closeAddAnecdotalModal);
    document.getElementById('cancelAddAnecdotalModal').addEventListener('click', closeAddAnecdotalModal);
    document.getElementById('closeBulkGradeModal').addEventListener('click', closeBulkGradeModal);
    document.getElementById('cancelBulkGradeModal').addEventListener('click', closeBulkGradeModal);
    document.getElementById('closeAnalyticsModal').addEventListener('click', closeAnalyticsModal);

    // Add grade row button
    document.getElementById('addGradeRow').addEventListener('click', addGradeRow);

    // Handle student selection for grades
    document.getElementById('gradeStudent').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const lrn = selectedOption.getAttribute('data-lrn');
        document.getElementById('gradeStudentLRN').value = lrn || '';
    });

    // Handle student selection for anecdotal records
    document.getElementById('anecdotalStudent').addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        const lrn = selectedOption.getAttribute('data-lrn');
        document.getElementById('anecdotalStudentLRN').value = lrn || '';
    });

    // Form submission handlers
    document.getElementById('addGradeForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add_grade');

        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Adding...';
        submitButton.disabled = true;

        fetch('functions/grade_functions.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Success!', data.message);
                    closeAddGradeModal();
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

    document.getElementById('addAnecdotalForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'add_anecdotal');

        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Adding...';
        submitButton.disabled = true;

        fetch('functions/grade_functions.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Success!', data.message);
                    closeAddAnecdotalModal();
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

    // Edit and delete functions (placeholders)
    function editGrade(grade) {
        console.log('Edit grade:', grade);
        // TODO: Implement edit modal
    }

    function deleteGrade(gradeId, assessmentName) {
        if (confirm(`Are you sure you want to delete "${assessmentName}"?`)) {
            const formData = new FormData();
            formData.append('action', 'delete_grade');
            formData.append('grade_id', gradeId);

            fetch('functions/grade_functions.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Success!', data.message);
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

    // Search functionality
    document.getElementById('searchGrades').addEventListener('input', function () {
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

    // Close modals with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (!addGradeModal.classList.contains('hidden')) {
                closeAddGradeModal();
            }
            if (!addAnecdotalModal.classList.contains('hidden')) {
                closeAddAnecdotalModal();
            }
        }
    });

    // Close modals when clicking outside
    addGradeModal.addEventListener('click', function (e) {
        if (e.target === addGradeModal) {
            closeAddGradeModal();
        }
    });

    addAnecdotalModal.addEventListener('click', function (e) {
        if (e.target === addAnecdotalModal) {
            closeAddAnecdotalModal();
        }
    });

    // Bulk grade functionality
    let gradeRowCounter = 0;

    function addGradeRow() {
        gradeRowCounter++;
        const container = document.getElementById('gradeRowsContainer');
        const row = document.createElement('tr');
        row.className = 'border-b';
        row.id = `gradeRow${gradeRowCounter}`;

        row.innerHTML = `
            <td class="px-3 py-2">
                <select name="grades[${gradeRowCounter}][student_id]" required class="w-full px-2 py-1 border border-gray-300 rounded text-sm" onchange="updateLRN(this, ${gradeRowCounter})">
                    <option value="">Select Student</option>
                    <?php foreach ($students as $student) { ?>
                        <option value="<?= $student['id'] ?>" data-lrn="<?= $student['LRN'] ?>">
                            <?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?> - <?= $student['GLevel'] ?>
                        </option>
                    <?php } ?>
                </select>
                <input type="hidden" name="grades[${gradeRowCounter}][lrn]" id="lrn${gradeRowCounter}">
            </td>
            <td class="px-3 py-2">
                <input type="number" name="grades[${gradeRowCounter}][grade_value]" required min="0" step="0.01" 
                       class="w-full px-2 py-1 border border-gray-300 rounded text-sm" 
                       placeholder="85" onchange="calculatePercentage(${gradeRowCounter})">
            </td>
            <td class="px-3 py-2">
                <span id="percentage${gradeRowCounter}" class="text-sm font-medium">-</span>
            </td>
            <td class="px-3 py-2">
                <span id="status${gradeRowCounter}" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">-</span>
            </td>
            <td class="px-3 py-2">
                <button type="button" onclick="removeGradeRow(${gradeRowCounter})" 
                        class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;

        container.appendChild(row);
    }

    function removeGradeRow(rowId) {
        const row = document.getElementById(`gradeRow${rowId}`);
        if (row) {
            row.remove();
        }
    }

    function updateLRN(selectElement, rowId) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const lrn = selectedOption.getAttribute('data-lrn');
        document.getElementById(`lrn${rowId}`).value = lrn || '';
    }

    function calculatePercentage(rowId) {
        const gradeInput = document.querySelector(`input[name="grades[${rowId}][grade_value]"]`);
        const maxPointsInput = document.getElementById('bulkMaxPoints');
        const percentageSpan = document.getElementById(`percentage${rowId}`);
        const statusSpan = document.getElementById(`status${rowId}`);

        if (gradeInput.value && maxPointsInput.value) {
            const percentage = (parseFloat(gradeInput.value) / parseFloat(maxPointsInput.value)) * 100;
            const isPass = percentage >= 75;
            
            percentageSpan.textContent = percentage.toFixed(1) + '%';
            percentageSpan.className = `text-sm font-medium ${isPass ? 'text-green-600' : 'text-red-600'}`;
            
            statusSpan.textContent = isPass ? 'PASS' : 'FAIL';
            statusSpan.className = `inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${isPass ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
        }
    }

    // Update all percentages when max points changes
    document.getElementById('bulkMaxPoints').addEventListener('input', function() {
        for (let i = 1; i <= gradeRowCounter; i++) {
            calculatePercentage(i);
        }
    });

    // Bulk grade form submission
    document.getElementById('bulkGradeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Collect form data
        const formData = new FormData(this);
        const commonData = {
            subject: formData.get('subject'),
            assessment_type: formData.get('assessment_type'),
            assessment_name: formData.get('assessment_name'),
            max_points: formData.get('max_points'),
            grading_period: formData.get('grading_period'),
            remarks: formData.get('remarks') || ''
        };

        const gradesData = [];
        const rows = document.querySelectorAll('#gradeRowsContainer tr');
        
        rows.forEach((row, index) => {
            const inputs = row.querySelectorAll('input, select');
            const gradeData = { ...commonData };
            
            inputs.forEach(input => {
                const name = input.name;
                if (name && name.includes('[')) {
                    const fieldName = name.split('[')[2].replace(']', '');
                    gradeData[fieldName] = input.value;
                }
            });

            if (gradeData.student_id && gradeData.grade_value) {
                gradesData.push(gradeData);
            }
        });

        if (gradesData.length === 0) {
            showAlert('error', 'Error', 'Please add at least one student grade.');
            return;
        }

        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Saving...';
        submitButton.disabled = true;

        fetch('functions/grade_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=add_bulk_grades&grades_data=${encodeURIComponent(JSON.stringify(gradesData))}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Success!', data.message);
                closeBulkGradeModal();
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('error', 'Error', data.message + (data.errors ? '\\n' + data.errors.join('\\n') : ''));
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

    // Analytics functionality
    function loadAnalytics() {
        const analyticsContent = document.getElementById('analyticsContent');
        
        fetch('functions/grade_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_class_performance'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAnalytics(data.data);
            } else {
                analyticsContent.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fa-solid fa-exclamation-triangle text-3xl text-red-400 mb-4"></i>
                        <p class="text-red-500">Failed to load analytics data</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            analyticsContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fa-solid fa-wifi text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Connection error</p>
                </div>
            `;
        });
    }

    function displayAnalytics(performanceData) {
        const analyticsContent = document.getElementById('analyticsContent');
        
        if (performanceData.length === 0) {
            analyticsContent.innerHTML = `
                <div class="text-center py-8">
                    <i class="fa-solid fa-chart-bar text-3xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No data available for analytics</p>
                </div>
            `;
            return;
        }

        let analyticsHtml = '<div class="space-y-6">';
        
        // Overall summary
        const totalStudents = performanceData.reduce((sum, item) => sum + parseInt(item.total_students), 0);
        const averageClassPerformance = performanceData.reduce((sum, item) => sum + parseFloat(item.class_average), 0) / performanceData.length;
        const totalPassed = performanceData.reduce((sum, item) => sum + parseInt(item.students_passed), 0);
        const overallPassRate = totalStudents > 0 ? (totalPassed / totalStudents) * 100 : 0;

        analyticsHtml += `
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-medium text-blue-900 mb-3">Overall Performance Summary</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">${totalStudents}</div>
                        <p class="text-sm text-blue-800">Total Students</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">${averageClassPerformance.toFixed(1)}%</div>
                        <p class="text-sm text-blue-800">Average Performance</p>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">${overallPassRate.toFixed(1)}%</div>
                        <p class="text-sm text-blue-800">Pass Rate</p>
                    </div>
                </div>
            </div>
        `;

        // Subject breakdown
        analyticsHtml += `
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Subject Performance Breakdown</h4>
                <div class="space-y-3">
        `;

        performanceData.forEach(item => {
            const passRateColor = parseFloat(item.pass_rate) >= 75 ? 'text-green-600' : 'text-red-600';
            const passRateBg = parseFloat(item.pass_rate) >= 75 ? 'bg-green-100' : 'bg-red-100';
            
            analyticsHtml += `
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h5 class="font-medium text-gray-900">${item.subject}</h5>
                            <p class="text-sm text-gray-600">${item.grading_period}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${passRateBg} ${passRateColor}">
                            ${parseFloat(item.pass_rate).toFixed(1)}% Pass Rate
                        </span>
                    </div>
                    <div class="grid grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500">Students:</span>
                            <span class="font-medium">${item.total_students}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Average:</span>
                            <span class="font-medium">${parseFloat(item.class_average).toFixed(1)}%</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Highest:</span>
                            <span class="font-medium text-green-600">${parseFloat(item.highest_grade).toFixed(1)}%</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Lowest:</span>
                            <span class="font-medium text-red-600">${parseFloat(item.lowest_grade).toFixed(1)}%</span>
                        </div>
                    </div>
                </div>
            `;
        });

        analyticsHtml += '</div></div></div>';
        analyticsContent.innerHTML = analyticsHtml;
    }

    // Close modals when clicking outside
    bulkGradeModal.addEventListener('click', function (e) {
        if (e.target === bulkGradeModal) {
            closeBulkGradeModal();
        }
    });

    analyticsModal.addEventListener('click', function (e) {
        if (e.target === analyticsModal) {
            closeAnalyticsModal();
        }
    });
</script>

<?php include "../includes/footer.php" ?>