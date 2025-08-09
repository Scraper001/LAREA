<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Mock student data for demo
$students = [
    [
        'id' => 3,
        'Fname' => 'Charles',
        'Lname' => 'Babbage',
        'Mname' => 'H',
        'GLevel' => 'Grade 7',
        'Course' => 'N/A',
        'LRN' => 123123123123,
        'photo_path' => 'uploads/student_photos/123123123123_1753529988.jpg'
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
                    <strong>Demo Mode:</strong> This shows the student management integration with grades.
                </span>
            </div>
        </div>

        <!-- Header Section -->
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">STUDENT INFORMATION</h1>

            <!-- Action Buttons Row -->
            <div class="flex flex-wrap gap-2 mb-4">
                <!-- Add Button -->
                <button onclick="alert('Add student functionality (demo mode)')"
                    class="flex-1 min-w-0 bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Student
                </button>

                <!-- Filter Button -->
                <button onclick="alert('Filter functionality (demo mode)')"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                <input type="text" id="table-search-users"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search for users">
            </div>
        </div>

        <!-- Mobile-Optimized User Cards -->
        <div class="space-y-3 h-[500px] overflow-y-auto">
            <!-- Single User Card -->
            <?php foreach ($students as $student) { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <!-- User Info -->
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="flex-shrink-0">
                            <input type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-user text-blue-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">
                                <?= htmlspecialchars($student['Fname'] . " " . $student['Lname'] . " " . $student['Mname']) ?>
                            </h3>
                            <p class="text-sm text-gray-500 truncate">
                                LRN: <?= htmlspecialchars($student['LRN']) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Position Badge -->
                    <div class="mb-3">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?= htmlspecialchars($student['GLevel'] . " " . $student['Course']) ?>
                        </span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <button
                            onclick="window.location.href='student_grades_demo.php?student_id=<?= $student['id'] ?>'"
                            class="flex-1 bg-green-50 hover:bg-green-100 text-green-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fa-solid fa-star mr-1"></i>
                            Grades
                        </button>

                        <button
                            onclick="alert('Edit functionality (demo mode)')"
                            class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fa-solid fa-edit mr-1"></i>
                            Edit
                        </button>

                        <button
                            onclick="alert('Delete functionality (demo mode)')"
                            class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fa-solid fa-trash mr-1"></i>
                            Delete
                        </button>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-gray-700">
                Showing <span class="font-medium">1</span> to <span class="font-medium">1</span> of <span
                    class="font-medium">1</span> results
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

        <!-- Select All Section (Mobile) -->
        <div class="mt-4 bg-white rounded-lg border border-gray-200 p-4">
            <label class="flex items-center space-x-3">
                <input type="checkbox" id="checkbox-all-search"
                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Select All Users</span>
            </label>
        </div>
    </div>

</main>

<?php include "../includes/footer.php" ?>