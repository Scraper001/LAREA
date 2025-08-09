<nav class="w-full bg-white shadow-lg border-b border-gray-200 mb-4 py-5">
    <div class="grid grid-cols-5 h-16">
        <!-- Home -->
        <a href="index.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'index.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-house text-xl"></i>
        </a>

        <!-- Students -->
        <a href="student_management.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'student_management.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-graduation-cap text-xl"></i>
        </a>

        <!-- Grades -->
        <a href="grades.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'grades.php' || $current_page == 'student_grades.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-star text-xl"></i>
        </a>

        <!-- Attendance -->
        <a href="attendance.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'attendance.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-clipboard-user text-xl"></i>
        </a>

        <!-- Student Behavior -->
        <a href="student_behavior.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'student_behavior.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-user text-xl"></i>
        </a>
    </div>
    
    <!-- Logout Button - Separate row for better mobile UX -->
    <div class="mt-2 px-4">
        <a href="../includes/logout.php" class="w-full flex items-center justify-center py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-all duration-200">
            <i class="fa-solid fa-right-from-bracket text-gray-600 mr-2"></i>
            <span class="text-sm text-gray-600 font-medium">Logout</span>
        </a>
    </div>
</nav>