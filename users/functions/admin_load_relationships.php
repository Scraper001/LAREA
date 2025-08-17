<?php
include "../../includes/session_manager.php";
requireAdmin(); // Ensure only admins can access this

include "../../connection/conn.php";
$conn = conn();

// Get all parent-child relationships
$relationships_sql = "SELECT pcr.*, 
                             up.first_name as parent_fname, up.last_name as parent_lname,
                             s.Fname as student_fname, s.Lname as student_lname, s.LRN,
                             tu.userID_col
                      FROM parent_child_relationships pcr
                      JOIN user_profiles up ON pcr.parent_user_id = up.user_id
                      JOIN students_tbl s ON pcr.student_id = s.id
                      JOIN tbl_user tu ON pcr.parent_user_id = tu.id
                      ORDER BY up.last_name, s.Lname";
$relationships_result = mysqli_query($conn, $relationships_sql);

// Get all parents (user level 3)
$parents_sql = "SELECT tu.id, tu.userID_col, up.first_name, up.last_name
                FROM tbl_user tu
                JOIN user_profiles up ON tu.id = up.user_id
                WHERE tu.userLevel_col = 3 AND up.status = 'active'
                ORDER BY up.last_name, up.first_name";
$parents_result = mysqli_query($conn, $parents_sql);

// Get all students
$students_sql = "SELECT id, Fname, Lname, LRN, GLevel FROM students_tbl ORDER BY Lname, Fname";
$students_result = mysqli_query($conn, $students_sql);
?>

<div class="mb-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Parent-Child Relationship Management</h3>
        <div class="flex space-x-3">
            <button onclick="showAddRelationshipModal()" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                <i class="fas fa-plus mr-2"></i>Add Relationship
            </button>
            <button onclick="window.location.reload()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </button>
        </div>
    </div>
</div>

<div class="bg-white shadow overflow-hidden sm:rounded-md">
    <div class="px-4 py-5 sm:p-6">
        <div class="mb-4">
            <input type="text" id="searchRelationships" placeholder="Search relationships..." 
                   class="px-3 py-2 border border-gray-300 rounded-md text-sm w-full max-w-md">
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="relationshipsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Relationship</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Primary Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($rel = mysqli_fetch_assoc($relationships_result)): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-green-600 text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($rel['parent_fname'] . ' ' . $rel['parent_lname']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">ID: <?= htmlspecialchars($rel['userID_col']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($rel['student_fname'] . ' ' . $rel['student_lname']) ?>
                                </div>
                                <div class="text-sm text-gray-500">LRN: <?= htmlspecialchars($rel['LRN']) ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?= htmlspecialchars($rel['relationship_type']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($rel['is_primary_contact']): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Yes
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        No
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?= date('M j, Y', strtotime($rel['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="editRelationship(<?= $rel['id'] ?>)" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteRelationship(<?= $rel['id'] ?>)" 
                                            class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <?php if (mysqli_num_rows($relationships_result) == 0): ?>
                <div class="text-center py-12">
                    <i class="fas fa-link text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Parent-Child Relationships</h3>
                    <p class="text-gray-500">Start by adding a relationship between a parent and student.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Relationship Modal -->
<div id="addRelationshipModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Add Parent-Child Relationship</h3>
                <button onclick="closeAddRelationshipModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="addRelationshipForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent *</label>
                    <select name="parent_user_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Parent</option>
                        <?php mysqli_data_seek($parents_result, 0); ?>
                        <?php while ($parent = mysqli_fetch_assoc($parents_result)): ?>
                            <option value="<?= $parent['id'] ?>">
                                <?= htmlspecialchars($parent['first_name'] . ' ' . $parent['last_name']) ?> 
                                (ID: <?= htmlspecialchars($parent['userID_col']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student *</label>
                    <select name="student_id" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Student</option>
                        <?php mysqli_data_seek($students_result, 0); ?>
                        <?php while ($student = mysqli_fetch_assoc($students_result)): ?>
                            <option value="<?= $student['id'] ?>">
                                <?= htmlspecialchars($student['Fname'] . ' ' . $student['Lname']) ?> 
                                (<?= htmlspecialchars($student['GLevel']) ?> - LRN: <?= htmlspecialchars($student['LRN']) ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship Type *</label>
                    <select name="relationship_type" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Relationship</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Grandparent">Grandparent</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_primary_contact" value="1" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Primary Contact</label>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddRelationshipModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Add Relationship
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('searchRelationships').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#relationshipsTable tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    function showAddRelationshipModal() {
        document.getElementById('addRelationshipModal').classList.remove('hidden');
    }

    function closeAddRelationshipModal() {
        document.getElementById('addRelationshipModal').classList.add('hidden');
        document.getElementById('addRelationshipForm').reset();
    }

    function editRelationship(relationshipId) {
        // Simplified edit functionality
        Swal.fire('Info', 'Edit functionality will be implemented in full version', 'info');
    }

    function deleteRelationship(relationshipId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will remove the parent-child relationship!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete relationship'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('action', 'delete_relationship');
                formData.append('relationship_id', relationshipId);
                
                fetch('admin_relationship_actions.php', {
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
                    Swal.fire('Error', 'Failed to delete relationship', 'error');
                });
            }
        });
    }

    // Handle add relationship form submission
    document.getElementById('addRelationshipForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('action', 'add_relationship');
        
        fetch('admin_relationship_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Success', data.message, 'success');
                closeAddRelationshipModal();
                location.reload();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to add relationship', 'error');
        });
    });

    // Close modal when clicking outside
    document.getElementById('addRelationshipModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddRelationshipModal();
        }
    });
</script>

<?php mysqli_close($conn); ?>