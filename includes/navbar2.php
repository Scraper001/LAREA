<nav class="w-full bg-white shadow-lg border-b border-gray-200 mb-4 py-5">
    <div class="grid grid-cols-4 h-16">
        <!-- Home -->
        <a href="index.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'index.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-house text-xl"></i>
        </a>

        <!-- Dashboard -->
        <a href="student_management.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'student_management.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-graduation-cap text-xl"></i>
        </a>

        <!-- Settings -->
        <a href="attendance.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'attendance.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-clipboard-user text-xl"></i>
        </a>

        <!-- Student Behavior -->
        <a href="student_behavior.php" class="flex items-center justify-center transition-all duration-200  py-2
           <?php echo ($current_page == 'logout.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-user text-xl"></i>
        </a>

        <!-- Logout -->
        <a href="../includes/logout.php" class="flex items-center justify-center transition-all duration-200 py-2
           <?php echo ($current_page == 'logout.php') ? 'bg-black text-white' : 'hover:bg-gray-100'; ?>">
            <i class="fa-solid fa-right-from-bracket text-xl"></i>
        </a>

    </div>
</nav>