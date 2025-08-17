<?php
include "../includes/session_manager.php";
requireParent(); // Ensure only parents can access this page

include "../connection/conn.php";
$conn = conn();

// Get parent's children
$parent_user_id = getCurrentUserId();
$children_sql = "SELECT s.*, pcr.relationship_type, pcr.is_primary_contact 
                 FROM students_tbl s 
                 JOIN parent_child_relationships pcr ON s.id = pcr.student_id 
                 WHERE pcr.parent_user_id = ?
                 ORDER BY s.Lname, s.Fname";
$stmt = mysqli_prepare($conn, $children_sql);
mysqli_stmt_bind_param($stmt, "i", $parent_user_id);
mysqli_stmt_execute($stmt);
$children_result = mysqli_stmt_get_result($stmt);

$parent_profile = getUserProfile();
?>

<?php include "../includes/header.php" ?>

<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <div class="relative flex items-center justify-between h-16">
            <div class="flex-1 flex items-center justify-center sm:items-stretch sm:justify-start">
                <div class="flex-shrink-0">
                    <a href="/LAREA/users/parent_dashboard.php" class="text-xl font-bold text-blue-600">LAREA - Parent Portal</a>
                </div>
                <div class="hidden sm:block sm:ml-6">
                    <div class="flex space-x-4">
                        <a href="/LAREA/users/parent_dashboard.php" class="bg-blue-100 text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="/LAREA/includes/logout.php" class="text-gray-900 hover:bg-gray-100 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">Logout</a>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Welcome, <?= htmlspecialchars($parent_profile['first_name'] ?? 'Parent') ?></span>
                <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-sm"></i>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Parent Dashboard</h1>
            <p class="mt-2 text-gray-600">Track your children's academic progress and school activities</p>
        </div>

        <!-- Children Overview Cards -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">My Children</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (mysqli_num_rows($children_result) > 0): ?>
                    <?php while ($child = mysqli_fetch_assoc($children_result)): ?>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <?php if ($child['photo_path'] && file_exists("../" . $child['photo_path'])): ?>
                                        <img src="../<?= htmlspecialchars($child['photo_path']) ?>" 
                                             alt="Student Photo" 
                                             class="h-16 w-16 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="h-16 w-16 bg-gray-300 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600 text-xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <?= htmlspecialchars($child['Fname'] . ' ' . $child['Lname']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars($child['GLevel']) ?> • LRN: <?= htmlspecialchars($child['LRN']) ?>
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        <?= htmlspecialchars($child['relationship_type']) ?>
                                        <?= $child['is_primary_contact'] ? ' (Primary Contact)' : '' ?>
                                    </p>
                                    <div class="mt-3">
                                        <button onclick="viewChildDetails(<?= $child['id'] ?>)" 
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            View Details →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Children Assigned</h3>
                            <p class="text-gray-500">Please contact the school administrator to link your children to your account.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Child Details Modal -->
        <div id="childDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900" id="modalChildName">Child Details</h3>
                        <button onclick="closeChildDetails()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="childDetailsContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script>
    let currentChildId = null;

    function viewChildDetails(childId) {
        currentChildId = childId;
        
        // Show modal
        document.getElementById('childDetailsModal').classList.remove('hidden');
        
        // Load child details via AJAX
        fetch(`functions/get_child_details.php?child_id=${childId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalChildName').textContent = data.child.name;
                    document.getElementById('childDetailsContent').innerHTML = data.html;
                } else {
                    document.getElementById('childDetailsContent').innerHTML = 
                        '<div class="text-red-600">Error loading child details: ' + data.message + '</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('childDetailsContent').innerHTML = 
                    '<div class="text-red-600">Error loading child details. Please try again.</div>';
            });
    }

    function closeChildDetails() {
        document.getElementById('childDetailsModal').classList.add('hidden');
        currentChildId = null;
    }

    // Close modal when clicking outside
    document.getElementById('childDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeChildDetails();
        }
    });
</script>

<?php
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<?php include "../includes/footer.php" ?>