<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/select_behavior.php" ?>

<main class="min-h-screen main-font bg-gray-50">

    <?php include "../includes/navbar2.php" ?>

    <div class="px-4 pb-20">
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">STUDENT BEHAVIOR RECORDS</h1>
            <div class="flex flex-wrap gap-2 mb-4">
                <button id="addBehaviorButton"
                    class="flex-1 min-w-0 bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Behavior
                </button>
                <button id="filterButton"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
            <div class="relative mb-4">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-search text-gray-400"></i>
                </div>
                <input type="text" id="table-search-behavior"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Search for behavior records">
            </div>
        </div>

        <div class="space-y-3 h-[500px] overflow-y-auto">
            <?php if ($result->num_rows > 0) { ?>
                <?php do { ?>
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="flex-shrink-0">
                                <input type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <img class="w-12 h-12 rounded-full object-cover" src="../<?php echo $row['photo_path'] ?>"
                                alt="Profile picture"
                                onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGM0Y0RjYiLz4KPHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSIxMCIgeT0iMTAiPgo8cGF0aCBkPSJNMTAgMTBDMTIuNzYxNCAxMCAxNSA3Ljc2MTQyIDE1IDVDMTUgMi4yMzg1OCAxMi43NjE0IDAgMTAgMEM3LjIzODU4IDAgNSAyLjIzODU4IDUgNUM1IDcuNzYxNDIgNy4yMzg1OCAxMCAxMCAxMFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2ZyB4PSI1IiB5PSIxNCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjYiIHZpZXdCb3g9IjAgMCAxMCA2IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBkPSJNMCAwSDE0VjZIMFYwWiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo='">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">
                                    <?php echo $row['Fname'] . " " . $row['Lname'] . " " . $row['Mname'] ?>
                                </h3>
                                <p class="text-sm text-gray-500 truncate">
                                    LRN: <?php echo $row['LRN'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <?php echo $row['behavior_type'] ?>
                            </span>
                        </div>
                        <div class="mb-2">
                            <p class="text-sm text-gray-700">
                                <strong>Date:</strong> <?php echo $row['behavior_date'] ?>
                            </p>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Remarks:</strong> <?php echo $row['remarks'] ?>
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button
                                onclick="openEditBehaviorModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['Fname']); ?>', '<?php echo addslashes($row['Lname']); ?>', '<?php echo addslashes($row['Mname']); ?>', '<?php echo addslashes($row['LRN']); ?>', '<?php echo addslashes($row['behavior_type']); ?>', '<?php echo addslashes($row['behavior_date']); ?>', '<?php echo addslashes($row['remarks']); ?>', '<?php echo addslashes($row['photo_path']); ?>')"
                                class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-edit mr-1"></i>
                                Edit
                            </button>
                            <button
                                onclick="openDeleteBehaviorModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['Fname']); ?>', '<?php echo addslashes($row['Lname']); ?>', '<?php echo addslashes($row['Mname']); ?>', '<?php echo addslashes($row['LRN']); ?>')"
                                class="flex-1 bg-red-50 hover:bg-red-100 text-red-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-trash mr-1"></i>
                                Delete
                            </button>
                        </div>
                    </div>
                <?php } while ($row = $result->fetch_assoc()); ?>
            <?php } else { ?>
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <h1>No Result</h1>
                </div>
            <?php } ?>
        </div>

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

        <div class="mt-4 bg-white rounded-lg border border-gray-200 p-4">
            <label class="flex items-center space-x-3">
                <input type="checkbox" id="checkbox-all-search"
                    class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                <span class="text-sm font-medium text-gray-700">Select All Records</span>
            </label>
        </div>
    </div>

    <!-- Add Behavior Modal -->
    <div id="addBehaviorModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add Behavior Record</h3>
                <button id="closeAddBehaviorModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <form id="addBehaviorForm" class="space-y-4" enctype="multipart/form-data">
                <div>
                    <label for="addBehaviorLRN" class="block text-sm font-medium text-gray-700 mb-1">
                        LRN (Learner Reference Number) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="addBehaviorLRN" name="behaviorLRN" required maxlength="12"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter 12-digit LRN">
                </div>
                <div>
                    <label for="addBehaviorType" class="block text-sm font-medium text-gray-700 mb-1">
                        Behavior Type <span class="text-red-500">*</span>
                    </label>
                    <select id="addBehaviorType" name="behaviorType" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        <option value="Commendable">Commendable</option>
                        <option value="Needs Improvement">Needs Improvement</option>
                        <option value="Violation">Violation</option>
                    </select>
                </div>
                <div>
                    <label for="addBehaviorDate" class="block text-sm font-medium text-gray-700 mb-1">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="addBehaviorDate" name="behaviorDate" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="addBehaviorRemarks" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks
                    </label>
                    <textarea id="addBehaviorRemarks" name="behaviorRemarks"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter remarks"></textarea>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="cancelAddBehaviorModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-plus mr-1"></i>
                        Add Behavior
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Behavior Modal -->
    <div id="editBehaviorModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Behavior Record</h3>
                <button id="closeEditBehaviorModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <form id="editBehaviorForm" class="space-y-4" enctype="multipart/form-data">
                <input type="hidden" id="editBehaviorId" name="behaviorId">
                <div>
                    <label for="editBehaviorLRN" class="block text-sm font-medium text-gray-700 mb-1">
                        LRN (Learner Reference Number) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editBehaviorLRN" name="behaviorLRN" required maxlength="12"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter 12-digit LRN">
                </div>
                <div>
                    <label for="editBehaviorType" class="block text-sm font-medium text-gray-700 mb-1">
                        Behavior Type <span class="text-red-500">*</span>
                    </label>
                    <select id="editBehaviorType" name="behaviorType" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Type</option>
                        <option value="Commendable">Commendable</option>
                        <option value="Needs Improvement">Needs Improvement</option>
                        <option value="Violation">Violation</option>
                    </select>
                </div>
                <div>
                    <label for="editBehaviorDate" class="block text-sm font-medium text-gray-700 mb-1">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="editBehaviorDate" name="behaviorDate" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="editBehaviorRemarks" class="block text-sm font-medium text-gray-700 mb-1">
                        Remarks
                    </label>
                    <textarea id="editBehaviorRemarks" name="behaviorRemarks"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter remarks"></textarea>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="cancelEditBehaviorModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-save mr-1"></i>
                        Update Behavior
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes slide-in {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>

</main>

<script>
    // Modal elements
    const addBehaviorModal = document.getElementById('addBehaviorModal');
    const editBehaviorModal = document.getElementById('editBehaviorModal');
    const addBehaviorButton = document.getElementById('addBehaviorButton');
    const addBehaviorForm = document.getElementById('addBehaviorForm');
    const editBehaviorForm = document.getElementById('editBehaviorForm');
    const closeAddBehaviorModal = document.getElementById('closeAddBehaviorModal');
    const cancelAddBehaviorModal = document.getElementById('cancelAddBehaviorModal');
    const closeEditBehaviorModal = document.getElementById('closeEditBehaviorModal');
    const cancelEditBehaviorModal = document.getElementById('cancelEditBehaviorModal');

    // Open Add Modal
    if (addBehaviorButton) {
        addBehaviorButton.addEventListener('click', function () {
            addBehaviorModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    }

    // Open Edit Modal
    function openEditBehaviorModal(id, fname, lname, mname, lrn, type, date, remarks, photoPath) {
        document.getElementById('editBehaviorId').value = id;
        document.getElementById('editBehaviorLRN').value = lrn;
        document.getElementById('editBehaviorType').value = type;
        document.getElementById('editBehaviorDate').value = date;
        document.getElementById('editBehaviorRemarks').value = remarks || '';
        editBehaviorModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close Add Modal functions
    function closeAddBehaviorModalFunction() {
        if (addBehaviorModal) {
            addBehaviorModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            addBehaviorForm.reset();
        }
    }

    // Close Edit Modal functions
    function closeEditBehaviorModalFunction() {
        if (editBehaviorModal) {
            editBehaviorModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            editBehaviorForm.reset();
        }
    }

    // Add Modal close events
    if (closeAddBehaviorModal) {
        closeAddBehaviorModal.addEventListener('click', closeAddBehaviorModalFunction);
    }
    if (cancelAddBehaviorModal) {
        cancelAddBehaviorModal.addEventListener('click', closeAddBehaviorModalFunction);
    }

    // Edit Modal close events
    if (closeEditBehaviorModal) {
        closeEditBehaviorModal.addEventListener('click', closeEditBehaviorModalFunction);
    }
    if (cancelEditBehaviorModal) {
        cancelEditBehaviorModal.addEventListener('click', closeEditBehaviorModalFunction);
    }

    // Close modals when clicking outside
    if (addBehaviorModal) {
        addBehaviorModal.addEventListener('click', function (e) {
            if (e.target === addBehaviorModal) {
                closeAddBehaviorModalFunction();
            }
        });
    }
    if (editBehaviorModal) {
        editBehaviorModal.addEventListener('click', function (e) {
            if (e.target === editBehaviorModal) {
                closeEditBehaviorModalFunction();
            }
        });
    }

    // Escape key handler
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (addBehaviorModal && !addBehaviorModal.classList.contains('hidden')) {
                closeAddBehaviorModalFunction();
            }
            if (editBehaviorModal && !editBehaviorModal.classList.contains('hidden')) {
                closeEditBehaviorModalFunction();
            }
        }
    });

    // Custom Alert Function
    function showAlert(type, title, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'fixed top-4 right-4 z-50 max-w-sm w-full';
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
        const iconColor = type === 'success' ? 'text-green-500' : 'text-red-500';
        const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
        alertDiv.innerHTML = `
            <div class="${bgColor} border rounded-lg p-4 shadow-lg animate-slide-in">
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
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }

    // Add Behavior Form submission
    if (addBehaviorForm) {
        addBehaviorForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const lrn = formData.get('behaviorLRN');
            const type = formData.get('behaviorType');
            const date = formData.get('behaviorDate');
            if (!lrn || !type || !date) {
                showAlert('error', 'Validation Error', 'Please fill in all required fields.');
                return;
            }
            if (lrn.length !== 12) {
                showAlert('error', 'Invalid LRN', 'LRN must be exactly 12 digits.');
                return;
            }
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Adding...';
            submitButton.disabled = true;
            fetch('functions/add_behavior.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Success!', data.message);
                        closeAddBehaviorModalFunction();
                        setTimeout(() => { location.reload(); }, 1500);
                    } else {
                        showAlert('error', 'Error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Connection Error', 'Unable to connect to server. Please try again.');
                })
                .finally(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
        });
    }

    // Edit Behavior Form submission
    if (editBehaviorForm) {
        editBehaviorForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const lrn = formData.get('behaviorLRN');
            const type = formData.get('behaviorType');
            const date = formData.get('behaviorDate');
            if (!lrn || !type || !date) {
                showAlert('error', 'Validation Error', 'Please fill in all required fields.');
                return;
            }
            if (lrn.length !== 12) {
                showAlert('error', 'Invalid LRN', 'LRN must be exactly 12 digits.');
                return;
            }
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Updating...';
            submitButton.disabled = true;
            fetch('functions/edit_behavior.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Success!', data.message);
                        closeEditBehaviorModalFunction();
                        setTimeout(() => { location.reload(); }, 1500);
                    } else {
                        showAlert('error', 'Error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Connection Error', 'Unable to connect to server. Please try again.');
                })
                .finally(() => {
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
        });
    }

    // Filter button functionality
    const filterButton = document.getElementById('filterButton');
    if (filterButton) {
        filterButton.addEventListener('click', function () {
            console.log('Filter clicked');
        });
    }

    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('checkbox-all-search');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.bg-white input[type="checkbox"]:not(#checkbox-all-search)');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // Search functionality
    const searchInput = document.getElementById('table-search-behavior');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            console.log('Searching for:', searchTerm);
        });
    }

    // LRN input validation for Add Modal
    const addBehaviorLrnInput = document.getElementById('addBehaviorLRN');
    if (addBehaviorLrnInput) {
        addBehaviorLrnInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 12) {
                value = value.substring(0, 12);
            }
            e.target.value = value;
        });
    }

    // LRN input validation for Edit Modal
    const editBehaviorLrnInput = document.getElementById('editBehaviorLRN');
    if (editBehaviorLrnInput) {
        editBehaviorLrnInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 12) {
                value = value.substring(0, 12);
            }
            e.target.value = value;
        });
    }

    // Delete confirmation modal HTML
    const deleteBehaviorModalHTML = `
<div id="deleteBehaviorConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-red-600">Confirm Delete</h3>
            <button id="closeDeleteBehaviorModal" type="button" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-exclamation-triangle text-red-500 text-3xl"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-lg font-medium text-gray-900">Delete Behavior Record</h4>
                    <p class="text-sm text-gray-600 mt-1">Are you sure you want to delete this record?</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">
                    <strong>Student:</strong> <span id="deleteBehaviorStudentName"></span>
                </p>
                <p class="text-sm text-gray-700 mt-1">
                    <strong>LRN:</strong> <span id="deleteBehaviorStudentLRN"></span>
                </p>
                <p class="text-xs text-red-600 mt-2">
                    <i class="fa-solid fa-warning mr-1"></i>
                    This action cannot be undone.
                </p>
            </div>
        </div>
        <div class="flex space-x-3">
            <button type="button" id="cancelDeleteBehaviorModal"
                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="button" id="confirmDeleteBehaviorButton"
                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500">
                <i class="fa-solid fa-trash mr-1"></i>
                Delete Record
            </button>
        </div>
    </div>
</div>`;

    function initializeDeleteBehaviorModal() {
        document.body.insertAdjacentHTML('beforeend', deleteBehaviorModalHTML);
        const deleteModal = document.getElementById('deleteBehaviorConfirmModal');
        const closeDeleteModal = document.getElementById('closeDeleteBehaviorModal');
        const cancelDeleteModal = document.getElementById('cancelDeleteBehaviorModal');
        const confirmDeleteButton = document.getElementById('confirmDeleteBehaviorButton');
        let currentBehaviorId = null;

        function closeDeleteModalFunction() {
            if (deleteModal) {
                deleteModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentBehaviorId = null;
            }
        }

        if (closeDeleteModal) {
            closeDeleteModal.addEventListener('click', closeDeleteModalFunction);
        }
        if (cancelDeleteModal) {
            cancelDeleteModal.addEventListener('click', closeDeleteModalFunction);
        }
        if (deleteModal) {
            deleteModal.addEventListener('click', function (e) {
                if (e.target === deleteModal) {
                    closeDeleteModalFunction();
                }
            });
        }
        if (confirmDeleteButton) {
            confirmDeleteButton.addEventListener('click', function () {
                if (!currentBehaviorId) return;
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...';
                this.disabled = true;
                const formData = new FormData();
                formData.append('behaviorId', currentBehaviorId);
                fetch('functions/delete_behavior.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', 'Success!', data.message);
                            closeDeleteModalFunction();
                            setTimeout(() => { location.reload(); }, 1500);
                        } else {
                            showAlert('error', 'Error', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showAlert('error', 'Connection Error', 'Unable to connect to server. Please try again.');
                    })
                    .finally(() => {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    });
            });
        }

        window.openDeleteBehaviorModal = function (id, fname, lname, mname, lrn) {
            currentBehaviorId = id;
            const fullName = `${fname} ${lname} ${mname}`;
            document.getElementById('deleteBehaviorStudentName').textContent = fullName;
            document.getElementById('deleteBehaviorStudentLRN').textContent = lrn;
            deleteModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        initializeDeleteBehaviorModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const deleteModal = document.getElementById('deleteBehaviorConfirmModal');
            if (deleteModal && !deleteModal.classList.contains('hidden')) {
                deleteModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
    });
</script>

<?php include "../includes/footer.php" ?>