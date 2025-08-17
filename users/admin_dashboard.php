<?php
include "../includes/session_manager.php";
requireAdmin(); // Ensure only admins can access this page

include "../connection/conn.php";
$conn = conn();

// Get statistics
$stats = [];

// Total users by type
$user_stats_sql = "SELECT userLevel_col, COUNT(*) as count FROM tbl_user GROUP BY userLevel_col";
$result = mysqli_query($conn, $user_stats_sql);
while ($row = mysqli_fetch_assoc($result)) {
    $stats['users'][$row['userLevel_col']] = $row['count'];
}

// Total students
$student_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM students_tbl"))['count'];
$stats['students'] = $student_count;

// Total parent-child relationships
$relationships_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM parent_child_relationships"))['count'];
$stats['relationships'] = $relationships_count;

$admin_profile = getUserProfile();
?>

<?php include "../includes/header.php" ?>

<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex-shrink-0">
                    <a href="/LAREA/users/admin_dashboard.php" class="text-xl font-bold text-blue-600">LAREA - Admin Panel</a>
                </div>
                <div class="hidden sm:block sm:ml-6">
                    <div class="flex space-x-4">
                        <a href="/LAREA/users/admin_dashboard.php" class="bg-blue-100 text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="#" onclick="showUserManagement()" class="text-gray-900 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">User Management</a>
                        <a href="#" onclick="showParentChildLinks()" class="text-gray-900 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Parent-Child Links</a>
                        <a href="/LAREA/includes/logout.php" class="text-gray-900 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Welcome, <?= htmlspecialchars($admin_profile['first_name'] ?? 'Administrator') ?></span>
                <div class="h-8 w-8 bg-red-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-2 text-gray-600">Manage users, relationships, and system settings</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Teachers/Staff -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-chalkboard-teacher text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Teachers/Staff</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['users'][1] ?? 0 ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admins -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-user-shield text-red-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Administrators</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['users'][2] ?? 0 ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parents -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Parents</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['users'][3] ?? 0 ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-graduation-cap text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Students</dt>
                                <dd class="text-lg font-medium text-gray-900"><?= $stats['students'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div id="mainContent" class="bg-white shadow rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Quick Actions -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button onclick="showAddUserModal()" 
                                class="w-full flex items-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user-plus text-blue-500 mr-3"></i>
                            Add New User
                        </button>
                        <button onclick="showUserManagement()" 
                                class="w-full flex items-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-users-cog text-green-500 mr-3"></i>
                            Manage Users
                        </button>
                        <button onclick="showParentChildLinks()" 
                                class="w-full flex items-center px-4 py-3 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-link text-purple-500 mr-3"></i>
                            Manage Parent-Child Links
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">System Overview</h3>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Total Parent-Child Relationships:</span>
                            <span class="font-medium"><?= $stats['relationships'] ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Active User Sessions:</span>
                            <span class="font-medium">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span>System Status:</span>
                            <span class="font-medium text-green-600">Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add User Modal -->
        <div id="addUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Add New User</h3>
                        <button onclick="closeAddUserModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form id="addUserForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                                <input type="text" name="firstName" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                                <input type="text" name="lastName" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <input type="text" name="middleName" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">User ID *</label>
                                <input type="number" name="userID" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">User Role *</label>
                                <select name="userLevel" required 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Role</option>
                                    <option value="1">Teacher/Staff</option>
                                    <option value="2">Administrator</option>
                                    <option value="3">Parent</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closeAddUserModal()" 
                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showAddUserModal() {
        document.getElementById('addUserModal').classList.remove('hidden');
    }

    function closeAddUserModal() {
        document.getElementById('addUserModal').classList.add('hidden');
        document.getElementById('addUserForm').reset();
    }

    function showUserManagement() {
        // Load user management interface
        fetch('functions/admin_load_users.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to load user management', 'error');
            });
    }

    function showParentChildLinks() {
        // Load parent-child relationship management
        fetch('functions/admin_load_relationships.php')
            .then(response => response.text())
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to load relationship management', 'error');
            });
    }

    // Handle add user form submission
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'add_user');
        
        fetch('functions/admin_user_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                closeAddUserModal();
                // Refresh the page or update the content
                location.reload();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to add user', 'error');
        });
    });

    // Close modal when clicking outside
    document.getElementById('addUserModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddUserModal();
        }
    });
</script>

<?php include "../includes/footer.php" ?>