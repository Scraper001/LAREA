<?php
include "../../includes/session_manager.php";
requireAdmin(); // Ensure only admins can access this

include "../../connection/conn.php";
$conn = conn();

// Get all users with their profiles
$users_sql = "SELECT tu.id, tu.userID_col, tu.userLevel_col, 
                     up.first_name, up.last_name, up.middle_name, 
                     up.email, up.phone, up.status
              FROM tbl_user tu
              LEFT JOIN user_profiles up ON tu.id = up.user_id
              ORDER BY tu.userLevel_col, up.last_name, up.first_name";
$users_result = mysqli_query($conn, $users_sql);
?>

<div class="mb-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">User Management</h3>
        <button onclick="window.location.reload()" 
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </button>
    </div>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:p-6">
        <div class="mb-4">
            <div class="flex justify-between items-center">
                <h4 class="font-medium text-gray-900">All Users</h4>
                <div class="flex space-x-2">
                    <input type="text" id="searchUsers" placeholder="Search users..." 
                           class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <select id="filterRole" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All Roles</option>
                        <option value="1">Teacher/Staff</option>
                        <option value="2">Administrator</option>
                        <option value="3">Parent</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="usersTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                        <tr data-role="<?= $user['userLevel_col'] ?>" data-user-id="<?= $user['id'] ?>">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center
                                            <?php 
                                            switch($user['userLevel_col']) {
                                                case 1: echo 'bg-blue-100 text-blue-600'; break;
                                                case 2: echo 'bg-red-100 text-red-600'; break;
                                                case 3: echo 'bg-green-100 text-green-600'; break;
                                                default: echo 'bg-gray-100 text-gray-600';
                                            }
                                            ?>">
                                            <i class="fas fa-user text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'No Name') ?>
                                        </div>
                                        <?php if ($user['middle_name']): ?>
                                            <div class="text-sm text-gray-500"><?= htmlspecialchars($user['middle_name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= htmlspecialchars($user['userID_col']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?php 
                                    switch($user['userLevel_col']) {
                                        case 1: echo 'bg-blue-100 text-blue-800'; break;
                                        case 2: echo 'bg-red-100 text-red-800'; break;
                                        case 3: echo 'bg-green-100 text-green-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?= getRoleName($user['userLevel_col']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php if ($user['email']): ?>
                                    <div><?= htmlspecialchars($user['email']) ?></div>
                                <?php endif; ?>
                                <?php if ($user['phone']): ?>
                                    <div><?= htmlspecialchars($user['phone']) ?></div>
                                <?php endif; ?>
                                <?php if (!$user['email'] && !$user['phone']): ?>
                                    <span class="text-gray-400">No contact info</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    <?= ($user['status'] ?? 'active') === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                    <?= ucfirst($user['status'] ?? 'active') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editUser(<?= $user['id'] ?>)" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="toggleUserStatus(<?= $user['id'] ?>, '<?= $user['status'] ?? 'active' ?>')" 
                                            class="<?= ($user['status'] ?? 'active') === 'active' ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' ?>">
                                        <i class="fas fa-<?= ($user['status'] ?? 'active') === 'active' ? 'ban' : 'check' ?>"></i>
                                    </button>
                                    <?php if ($user['id'] != getCurrentUserId()): ?>
                                        <button onclick="deleteUser(<?= $user['id'] ?>)" 
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit User</h3>
                <button onclick="closeEditUserModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="editUserForm" class="space-y-4">
                <input type="hidden" name="user_id" id="editUserId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" name="firstName" id="editFirstName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" name="lastName" id="editLastName" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                    <input type="text" name="middleName" id="editMiddleName" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="editEmail" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="tel" name="phone" id="editPhone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditUserModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Search and filter functionality
    document.getElementById('searchUsers').addEventListener('input', filterUsers);
    document.getElementById('filterRole').addEventListener('change', filterUsers);

    function filterUsers() {
        const searchTerm = document.getElementById('searchUsers').value.toLowerCase();
        const roleFilter = document.getElementById('filterRole').value;
        const rows = document.querySelectorAll('#usersTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const role = row.getAttribute('data-role');
            
            const matchesSearch = text.includes(searchTerm);
            const matchesRole = !roleFilter || role === roleFilter;
            
            row.style.display = matchesSearch && matchesRole ? '' : 'none';
        });
    }

    function editUser(userId) {
        // Get user data from the row
        const row = document.querySelector(`tr[data-user-id="${userId}"]`);
        
        // This is a simplified version - in a real app, you'd fetch the full user data
        document.getElementById('editUserId').value = userId;
        
        // Show modal
        document.getElementById('editUserModal').classList.remove('hidden');
        
        // In a real implementation, you'd fetch the current user data here
        // For now, this is a placeholder
    }

    function closeEditUserModal() {
        document.getElementById('editUserModal').classList.add('hidden');
    }

    function toggleUserStatus(userId, currentStatus) {
        const action = currentStatus === 'active' ? 'deactivate' : 'activate';
        
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to ${action} this user?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Yes, ${action} user`
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'toggle_user_status');
                formData.append('user_id', userId);
                formData.append('status', currentStatus);
                
                fetch('functions/admin_user_actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to update user status', 'error');
                });
            }
        });
    }

    function deleteUser(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete user'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_user');
                formData.append('user_id', userId);
                
                fetch('functions/admin_user_actions.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Deleted!', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Failed to delete user', 'error');
                });
            }
        });
    }

    // Handle edit user form submission
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'edit_user');
        
        fetch('functions/admin_user_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                closeEditUserModal();
                location.reload();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to update user', 'error');
        });
    });
</script>

<?php mysqli_close($conn); ?>