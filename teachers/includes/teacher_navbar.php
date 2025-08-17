<?php
// teacher_navbar.php
?>

<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button-->
            </div>
            <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex-shrink-0">
                    <a href="/LAREA/teachers/dashboard.php" class="text-xl font-bold text-green-600">LAREA Teacher</a>
                </div>
                <div class="hidden sm:block sm:ml-6">
                    <div class="flex space-x-4">
                        <a href="/LAREA/teachers/dashboard.php" class="text-gray-900 hover:bg-gray-100 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="/LAREA/users/attendance.php" class="text-gray-900 hover:bg-gray-100 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium">Take Attendance</a>
                        <a href="/LAREA/users/student_behavior.php" class="text-gray-900 hover:bg-gray-100 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium">Behavior Records</a>
                        <a href="/LAREA/users/grades.php" class="text-gray-900 hover:bg-gray-100 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium">Grades</a>
                        <a href="/LAREA/users/student_management.php" class="text-gray-900 hover:bg-gray-100 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium">My Students</a>
                    </div>
                </div>
            </div>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                <div class="ml-3 relative">
                    <div class="flex items-center">
                        <span class="text-gray-700 text-sm mr-4">Welcome, Teacher</span>
                        <a href="/LAREA/includes/logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>