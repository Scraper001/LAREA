<?php
// navbar.php

?>

<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                <!-- Mobile menu button-->
            </div>
            <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex-shrink-0">
                    <a href="/LAREA/index.php" class="text-xl font-bold text-blue-600">School Management</a>
                </div>
                <div class="hidden sm:block sm:ml-6">
                    <div class="flex space-x-4">
                        <a href="/LAREA/users/student_management.php" class="text-gray-900 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Student Management</a>
                        <a href="/LAREA/users/student_behavior.php" class="text-gray-900 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Student Behavior</a>
                        <a href="/LAREA/users/functions/logout.php" class="text-gray-900 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>