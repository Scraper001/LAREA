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

</main>

<script>
    // Modal elements
    const addGradeModal = document.getElementById('addGradeModal');
    const addAnecdotalModal = document.getElementById('addAnecdotalModal');
    const addGradeButton = document.getElementById('addGradeButton');
    const addAnecdotalButton = document.getElementById('addAnecdotalButton');

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

    // Event listeners
    addGradeButton.addEventListener('click', openAddGradeModal);
    addAnecdotalButton.addEventListener('click', openAddAnecdotalModal);

    document.getElementById('closeAddGradeModal').addEventListener('click', closeAddGradeModal);
    document.getElementById('cancelAddGradeModal').addEventListener('click', closeAddGradeModal);
    document.getElementById('closeAddAnecdotalModal').addEventListener('click', closeAddAnecdotalModal);
    document.getElementById('cancelAddAnecdotalModal').addEventListener('click', closeAddAnecdotalModal);

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
</script>

<?php include "../includes/footer.php" ?>