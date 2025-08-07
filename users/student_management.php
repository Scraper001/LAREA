<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<?php include "../includes/header.php" ?>
<?php include "../includes/navbar.php" ?>
<?php include "functions/select_users.php" ?>


<main class="min-h-screen main-font bg-gray-50">

    <!-- Navigation Bar -->

    <?php include "../includes/navbar2.php" ?>


    <!-- Main Content -->
    <div class="px-4 pb-20">
        <!-- Header Section -->
        <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">STUDENT INFORMATION</h1>

            <!-- Action Buttons Row -->
            <div class="flex flex-wrap gap-2 mb-4">
                <!-- Add Button -->
                <button id="addStudentButton"
                    class="flex-1 min-w-0 bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Student
                </button>

                <!-- Filter Button -->
                <button id="filterButton"
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
            <?php if ($result->num_rows > 0) { ?>

                <?php do { ?>
                    <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                        <!-- User Info -->
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

                        <!-- Position Badge -->
                        <div class="mb-3">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?php echo $row['GLevel'] . " " . $row['Course'] ?>
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button
                                onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['Fname']); ?>', '<?php echo addslashes($row['Lname']); ?>', '<?php echo addslashes($row['Mname']); ?>', '<?php echo addslashes($row['LRN']); ?>', '<?php echo addslashes($row['GLevel']); ?>', '<?php echo addslashes($row['Course']); ?>', '<?php echo addslashes($row['photo_path']); ?>')"
                                class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-3 rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fa-solid fa-edit mr-1"></i>
                                Edit
                            </button>

                            <button
                                onclick="openDeleteModal(<?php echo $row['id']; ?>, '<?php echo addslashes($row['Fname']); ?>', '<?php echo addslashes($row['Lname']); ?>', '<?php echo addslashes($row['Mname']); ?>', '<?php echo addslashes($row['LRN']); ?>')"
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

            <!-- Additional user cards will be populated dynamically -->
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

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Add New Student</h3>
                <button id="closeAddModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="addStudentForm" class="space-y-4" enctype="multipart/form-data">
                <!-- Student Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Student Photo
                    </label>
                    <div class="flex flex-col items-center">
                        <!-- Image Preview -->
                        <div id="addImagePreview" class="hidden mb-4">
                            <img id="addPreviewImg" class="w-32 h-32 object-cover rounded-full border-4 border-gray-300"
                                src="" alt="Preview">
                            <button type="button" id="addRemoveImage"
                                class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                <i class="fa-solid fa-trash mr-1"></i>Remove Photo
                            </button>
                        </div>

                        <!-- Upload Area -->
                        <div id="addUploadArea"
                            class="w-full max-w-md border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer">
                            <input type="file" id="addStudentPhoto" name="studentPhoto" accept="image/*" class="hidden">
                            <i class="fa-solid fa-cloud-upload text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                            <p class="text-sm text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                        </div>
                    </div>
                </div>

                <!-- First Name -->
                <div>
                    <label for="addStudentFname" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="addStudentFname" name="studentFname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter first name">
                </div>

                <!-- Last Name -->
                <div>
                    <label for="addStudentLname" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="addStudentLname" name="studentLname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter last name">
                </div>

                <!-- Middle Name -->
                <div>
                    <label for="addStudentMname" class="block text-sm font-medium text-gray-700 mb-1">
                        Middle Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="addStudentMname" name="studentMname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter middle name">
                </div>

                <!-- LRN (Learner Reference Number) -->
                <div>
                    <label for="addStudentLRN" class="block text-sm font-medium text-gray-700 mb-1">
                        LRN (Learner Reference Number) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="addStudentLRN" name="studentLRN" required maxlength="12"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter 12-digit LRN">
                </div>

                <!-- Grade Level -->
                <div>
                    <label for="addStudentGLevel" class="block text-sm font-medium text-gray-700 mb-1">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select id="addStudentGLevel" name="studentGLevel" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Grade Level</option>
                        <option value="Grade 7">Grade 7</option>
                        <option value="Grade 8">Grade 8</option>
                        <option value="Grade 9">Grade 9</option>
                        <option value="Grade 10">Grade 10</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12</option>
                    </select>
                </div>

                <!-- Course -->
                <div>
                    <label for="addStudentCourse" class="block text-sm font-medium text-gray-700 mb-1">
                        Course/Strand
                    </label>
                    <select id="addStudentCourse" name="studentCourse"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Course/Strand</option>
                        <option value="STEM">STEM (Science, Technology, Engineering & Mathematics)</option>
                        <option value="ABM">ABM (Accountancy, Business & Management)</option>
                        <option value="HUMSS">HUMSS (Humanities & Social Sciences)</option>
                        <option value="GAS">GAS (General Academic Strand)</option>
                        <option value="TVL-ICT">TVL-ICT (Information & Communication Technology)</option>
                        <option value="TVL-HE">TVL-HE (Home Economics)</option>
                        <option value="TVL-IA">TVL-IA (Industrial Arts)</option>
                        <option value="TVL-AGRI">TVL-AGRI (Agriculture)</option>
                        <option value="ARTS">Arts & Design Track</option>
                        <option value="SPORTS">Sports Track</option>
                        <option value="N/A">Not Applicable</option>
                    </select>
                </div>

                <!-- Modal Footer -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="cancelAddModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-plus mr-1"></i>
                        Add Student
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div id="editStudentModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Edit Student</h3>
                <button id="closeEditModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="editStudentForm" class="space-y-4" enctype="multipart/form-data">
                <input type="hidden" id="editStudentId" name="studentId">

                <!-- Student Photo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Student Photo
                    </label>
                    <div class="flex flex-col items-center">
                        <!-- Image Preview -->
                        <div id="editImagePreview" class="mb-4">
                            <img id="editPreviewImg"
                                class="w-32 h-32 object-cover rounded-full border-4 border-gray-300" src=""
                                alt="Preview">
                            <button type="button" id="editRemoveImage"
                                class="mt-2 text-red-500 hover:text-red-700 text-sm">
                                <i class="fa-solid fa-trash mr-1"></i>Remove Photo
                            </button>
                        </div>

                        <!-- Upload Area -->
                        <div id="editUploadArea"
                            class="w-full max-w-md border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors cursor-pointer hidden">
                            <input type="file" id="editStudentPhoto" name="studentPhoto" accept="image/*"
                                class="hidden">
                            <i class="fa-solid fa-cloud-upload text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                            <p class="text-sm text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                        </div>
                    </div>
                </div>

                <!-- First Name -->
                <div>
                    <label for="editStudentFname" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editStudentFname" name="studentFname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter first name">
                </div>

                <!-- Last Name -->
                <div>
                    <label for="editStudentLname" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editStudentLname" name="studentLname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter last name">
                </div>

                <!-- Middle Name -->
                <div>
                    <label for="editStudentMname" class="block text-sm font-medium text-gray-700 mb-1">
                        Middle Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editStudentMname" name="studentMname" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter middle name">
                </div>

                <!-- LRN (Learner Reference Number) -->
                <div>
                    <label for="editStudentLRN" class="block text-sm font-medium text-gray-700 mb-1">
                        LRN (Learner Reference Number) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="editStudentLRN" name="studentLRN" required maxlength="12"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter 12-digit LRN">
                </div>

                <!-- Grade Level -->
                <div>
                    <label for="editStudentGLevel" class="block text-sm font-medium text-gray-700 mb-1">
                        Grade Level <span class="text-red-500">*</span>
                    </label>
                    <select id="editStudentGLevel" name="studentGLevel" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Grade Level</option>
                        <option value="Grade 7">Grade 7</option>
                        <option value="Grade 8">Grade 8</option>
                        <option value="Grade 9">Grade 9</option>
                        <option value="Grade 10">Grade 10</option>
                        <option value="Grade 11">Grade 11</option>
                        <option value="Grade 12">Grade 12</option>
                    </select>
                </div>

                <!-- Course -->
                <div>
                    <label for="editStudentCourse" class="block text-sm font-medium text-gray-700 mb-1">
                        Course/Strand
                    </label>
                    <select id="editStudentCourse" name="studentCourse"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Course/Strand</option>
                        <option value="STEM">STEM (Science, Technology, Engineering & Mathematics)</option>
                        <option value="ABM">ABM (Accountancy, Business & Management)</option>
                        <option value="HUMSS">HUMSS (Humanities & Social Sciences)</option>
                        <option value="GAS">GAS (General Academic Strand)</option>
                        <option value="TVL-ICT">TVL-ICT (Information & Communication Technology)</option>
                        <option value="TVL-HE">TVL-HE (Home Economics)</option>
                        <option value="TVL-IA">TVL-IA (Industrial Arts)</option>
                        <option value="TVL-AGRI">TVL-AGRI (Agriculture)</option>
                        <option value="ARTS">Arts & Design Track</option>
                        <option value="SPORTS">Sports Track</option>
                        <option value="N/A">Not Applicable</option>
                    </select>
                </div>

                <!-- Modal Footer -->
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="cancelEditModal"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-save mr-1"></i>
                        Update Student
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Modal elements
    const addModal = document.getElementById('addStudentModal');
    const editModal = document.getElementById('editStudentModal');
    const addStudentButton = document.getElementById('addStudentButton');
    const addStudentForm = document.getElementById('addStudentForm');
    const editStudentForm = document.getElementById('editStudentForm');

    // Add Modal controls
    const closeAddModal = document.getElementById('closeAddModal');
    const cancelAddModal = document.getElementById('cancelAddModal');

    // Edit Modal controls
    const closeEditModal = document.getElementById('closeEditModal');
    const cancelEditModal = document.getElementById('cancelEditModal');

    // Add Modal Image upload functionality
    const addUploadArea = document.getElementById('addUploadArea');
    const addFileInput = document.getElementById('addStudentPhoto');
    const addImagePreview = document.getElementById('addImagePreview');
    const addPreviewImg = document.getElementById('addPreviewImg');
    const addRemoveImageBtn = document.getElementById('addRemoveImage');

    // Edit Modal Image upload functionality
    const editUploadArea = document.getElementById('editUploadArea');
    const editFileInput = document.getElementById('editStudentPhoto');
    const editImagePreview = document.getElementById('editImagePreview');
    const editPreviewImg = document.getElementById('editPreviewImg');
    const editRemoveImageBtn = document.getElementById('editRemoveImage');

    // Initialize Add Modal image upload functionality
    if (addUploadArea && addFileInput && addImagePreview && addPreviewImg && addRemoveImageBtn) {
        addUploadArea.addEventListener('click', () => {
            addFileInput.click();
        });

        addUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            addUploadArea.classList.add('drag-over');
        });

        addUploadArea.addEventListener('dragleave', () => {
            addUploadArea.classList.remove('drag-over');
        });

        addUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            addUploadArea.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleAddFileSelection(files[0]);
            }
        });

        addFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleAddFileSelection(e.target.files[0]);
            }
        });

        addRemoveImageBtn.addEventListener('click', () => {
            addFileInput.value = '';
            addImagePreview.classList.add('hidden');
            addUploadArea.classList.remove('hidden');
        });
    }

    // Initialize Edit Modal image upload functionality
    if (editUploadArea && editFileInput && editImagePreview && editPreviewImg && editRemoveImageBtn) {
        editUploadArea.addEventListener('click', () => {
            editFileInput.click();
        });

        editUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            editUploadArea.classList.add('drag-over');
        });

        editUploadArea.addEventListener('dragleave', () => {
            editUploadArea.classList.remove('drag-over');
        });

        editUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            editUploadArea.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleEditFileSelection(files[0]);
            }
        });

        editFileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleEditFileSelection(e.target.files[0]);
            }
        });

        editRemoveImageBtn.addEventListener('click', () => {
            editFileInput.value = '';
            editImagePreview.classList.add('hidden');
            editUploadArea.classList.remove('hidden');
        });
    }

    // Handle file selection for Add Modal
    function handleAddFileSelection(file) {
        if (!file.type.startsWith('image/')) {
            showAlert('error', 'Invalid File', 'Please select a valid image file.');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showAlert('error', 'File Too Large', 'Image size must be less than 5MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            if (addPreviewImg) {
                addPreviewImg.src = e.target.result;
                addImagePreview.classList.remove('hidden');
                addUploadArea.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }

    // Handle file selection for Edit Modal
    function handleEditFileSelection(file) {
        if (!file.type.startsWith('image/')) {
            showAlert('error', 'Invalid File', 'Please select a valid image file.');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            showAlert('error', 'File Too Large', 'Image size must be less than 5MB.');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            if (editPreviewImg) {
                editPreviewImg.src = e.target.result;
                editImagePreview.classList.remove('hidden');
                editUploadArea.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }

    // Open Add Modal
    if (addStudentButton) {
        addStudentButton.addEventListener('click', function () {
            addModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        });
    }

    // Open Edit Modal
    function openEditModal(id, fname, lname, mname, lrn, glevel, course, photoPath) {
        document.getElementById('editStudentId').value = id;
        document.getElementById('editStudentFname').value = fname;
        document.getElementById('editStudentLname').value = lname;
        document.getElementById('editStudentMname').value = mname;
        document.getElementById('editStudentLRN').value = lrn;
        document.getElementById('editStudentGLevel').value = glevel;
        document.getElementById('editStudentCourse').value = course || '';

        // Handle photo display
        if (photoPath && photoPath !== 'null') {
            editPreviewImg.src = '../' + photoPath;
            editImagePreview.classList.remove('hidden');
            editUploadArea.classList.add('hidden');
        } else {
            editPreviewImg.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiNGM0Y0RjYiLz4KPHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4PSIxMCIgeT0iMTAiPgo8cGF0aCBkPSJNMTAgMTBDMTIuNzYxNCAxMCAxNSA3Ljc2MTQyIDE1IDVDMTUgMi4yMzg1OCAxMi43NjE0IDAgMTAgMEM3LjIzODU4IDAgNSAyLjIzODU4IDUgNUM1IDcuNzYxNDIgNy4yMzg1OCAxMCAxMCAxMFoiIGZpbGw9IiM5Q0EzQUYiLz4KPHN2ZyB4PSI1IiB5PSIxNCIgd2lkdGg9IjEwIiBoZWlnaHQ9IjYiIHZpZXdCb3g9IjAgMCAxMCA2IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cGF0aCBkPSJNMCAwSDE0VjZIMFYwWiIgZmlsbD0iIzlDQTNBRiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo=';
            editImagePreview.classList.remove('hidden');
            editUploadArea.classList.add('hidden');
        }

        editModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close Add Modal functions
    function closeAddModalFunction() {
        if (addModal) {
            addModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            addStudentForm.reset();

            if (addImagePreview && addUploadArea) {
                addImagePreview.classList.add('hidden');
                addUploadArea.classList.remove('hidden');
            }
        }
    }

    // Close Edit Modal functions
    function closeEditModalFunction() {
        if (editModal) {
            editModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            editStudentForm.reset();

            if (editImagePreview && editUploadArea) {
                editImagePreview.classList.add('hidden');
                editUploadArea.classList.remove('hidden');
            }
        }
    }

    // Add Modal close events
    if (closeAddModal) {
        closeAddModal.addEventListener('click', closeAddModalFunction);
    }

    if (cancelAddModal) {
        cancelAddModal.addEventListener('click', closeAddModalFunction);
    }

    // Edit Modal close events
    if (closeEditModal) {
        closeEditModal.addEventListener('click', closeEditModalFunction);
    }

    if (cancelEditModal) {
        cancelEditModal.addEventListener('click', closeEditModalFunction);
    }

    // Close modals when clicking outside
    if (addModal) {
        addModal.addEventListener('click', function (e) {
            if (e.target === addModal) {
                closeAddModalFunction();
            }
        });
    }

    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) {
                closeEditModalFunction();
            }
        });
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            if (addModal && !addModal.classList.contains('hidden')) {
                closeAddModalFunction();
            }
            if (editModal && !editModal.classList.contains('hidden')) {
                closeEditModalFunction();
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

    // Add Student Form submission
    if (addStudentForm) {
        addStudentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            const fname = formData.get('studentFname');
            const lname = formData.get('studentLname');
            const mname = formData.get('studentMname');
            const lrn = formData.get('studentLRN');
            const glevel = formData.get('studentGLevel');

            if (!fname || !lname || !mname || !lrn || !glevel) {
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

            fetch('functions/add_users.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Success!', data.message);
                        closeAddModalFunction();
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
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

    // Edit Student Form submission
    if (editStudentForm) {
        editStudentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            const fname = formData.get('studentFname');
            const lname = formData.get('studentLname');
            const mname = formData.get('studentMname');
            const lrn = formData.get('studentLRN');
            const glevel = formData.get('studentGLevel');

            if (!fname || !lname || !mname || !lrn || !glevel) {
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

            fetch('functions/edit_users.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Success!', data.message);
                        closeEditModalFunction();
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
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
    const searchInput = document.getElementById('table-search-users');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            console.log('Searching for:', searchTerm);
        });
    }

    // LRN input validation for Add Modal
    const addLrnInput = document.getElementById('addStudentLRN');
    if (addLrnInput) {
        addLrnInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 12) {
                value = value.substring(0, 12);
            }
            e.target.value = value;
        });
    }

    // LRN input validation for Edit Modal
    const editLrnInput = document.getElementById('editStudentLRN');
    if (editLrnInput) {
        editLrnInput.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 12) {
                value = value.substring(0, 12);
            }
            e.target.value = value;
        });
    }

    // Delete confirmation modal HTML (add this to your main HTML)
    const deleteModalHTML = `
<div id="deleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-md shadow-lg rounded-md bg-white">
        <!-- Modal Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-red-600">Confirm Delete</h3>
            <button id="closeDeleteModal" type="button" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Content -->
        <div class="mb-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-exclamation-triangle text-red-500 text-3xl"></i>
                </div>
                <div class="ml-3">
                    <h4 class="text-lg font-medium text-gray-900">Delete Student</h4>
                    <p class="text-sm text-gray-600 mt-1">Are you sure you want to delete this student?</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-3">
                <p class="text-sm text-gray-700">
                    <strong>Student:</strong> <span id="deleteStudentName"></span>
                </p>
                <p class="text-sm text-gray-700 mt-1">
                    <strong>LRN:</strong> <span id="deleteStudentLRN"></span>
                </p>
                <p class="text-xs text-red-600 mt-2">
                    <i class="fa-solid fa-warning mr-1"></i>
                    This action cannot be undone.
                </p>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex space-x-3">
            <button type="button" id="cancelDeleteModal" 
                class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                Cancel
            </button>
            <button type="button" id="confirmDeleteButton"
                class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500">
                <i class="fa-solid fa-trash mr-1"></i>
                Delete Student
            </button>
        </div>
    </div>
</div>`;

    // Add delete modal to body (call this when DOM is loaded)
    function initializeDeleteModal() {
        document.body.insertAdjacentHTML('beforeend', deleteModalHTML);

        // Get modal elements
        const deleteModal = document.getElementById('deleteConfirmModal');
        const closeDeleteModal = document.getElementById('closeDeleteModal');
        const cancelDeleteModal = document.getElementById('cancelDeleteModal');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');

        let currentStudentId = null;

        // Close modal functions
        function closeDeleteModalFunction() {
            if (deleteModal) {
                deleteModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
                currentStudentId = null;
            }
        }

        // Close modal events
        if (closeDeleteModal) {
            closeDeleteModal.addEventListener('click', closeDeleteModalFunction);
        }

        if (cancelDeleteModal) {
            cancelDeleteModal.addEventListener('click', closeDeleteModalFunction);
        }

        // Close modal when clicking outside
        if (deleteModal) {
            deleteModal.addEventListener('click', function (e) {
                if (e.target === deleteModal) {
                    closeDeleteModalFunction();
                }
            });
        }

        // Confirm delete functionality
        if (confirmDeleteButton) {
            confirmDeleteButton.addEventListener('click', function () {
                if (!currentStudentId) return;

                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Deleting...';
                this.disabled = true;

                const formData = new FormData();
                formData.append('studentId', currentStudentId);

                fetch('functions/delete_users.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showAlert('success', 'Success!', data.message);
                            closeDeleteModalFunction();
                            setTimeout(() => {
                                location.reload();
                            }, 1500);
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

        // Global function to open delete modal
        window.openDeleteModal = function (id, fname, lname, mname, lrn) {
            currentStudentId = id;
            const fullName = `${fname} ${lname} ${mname}`;

            document.getElementById('deleteStudentName').textContent = fullName;
            document.getElementById('deleteStudentLRN').textContent = lrn;

            deleteModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };
    }

    // Call this function when the page loads
    document.addEventListener('DOMContentLoaded', function () {
        initializeDeleteModal();
    });

    // Add to existing Escape key handler
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const deleteModal = document.getElementById('deleteConfirmModal');
            if (deleteModal && !deleteModal.classList.contains('hidden')) {
                deleteModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
            // ... your existing escape key handlers
        }
    });
</script>

<?php include "../includes/footer.php" ?>