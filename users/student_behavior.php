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
                <button id="reportButton"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-chart-bar mr-1"></i>
                    Report
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
                            <!-- Behavior Type and Category Badges -->
                            <div class="flex flex-wrap gap-2 mb-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    <?php 
                                    echo match($row['behavior_type']) {
                                        'Commendable' => 'bg-green-100 text-green-800 border border-green-200',
                                        'Needs Improvement' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
                                        'Violation' => 'bg-red-100 text-red-800 border border-red-200',
                                        default => 'bg-gray-100 text-gray-800 border border-gray-200'
                                    };
                                    ?>">
                                    <?php echo $row['behavior_type'] ?>
                                </span>
                                
                                <?php if (!empty($row['behavior_category'])): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                    <?php echo $row['behavior_category'] ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($row['severity_level'])): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    <?php 
                                    echo match($row['severity_level']) {
                                        'Low' => 'bg-green-50 text-green-700 border border-green-200',
                                        'Medium' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
                                        'High' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                        'Critical' => 'bg-red-50 text-red-700 border border-red-200',
                                        default => 'bg-gray-50 text-gray-700 border border-gray-200'
                                    };
                                    ?>">
                                    <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                                    <?php echo $row['severity_level'] ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($row['status']) && $row['status'] !== 'Active'): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium
                                    <?php 
                                    echo match($row['status']) {
                                        'Resolved' => 'bg-green-50 text-green-700 border border-green-200',
                                        'Follow-up Required' => 'bg-orange-50 text-orange-700 border border-orange-200',
                                        'Archived' => 'bg-gray-50 text-gray-700 border border-gray-200',
                                        default => 'bg-blue-50 text-blue-700 border border-blue-200'
                                    };
                                    ?>">
                                    <i class="fa-solid fa-info-circle mr-1"></i>
                                    <?php echo $row['status'] ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($row['follow_up_required']) && $row['follow_up_required'] == 1): ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-50 text-purple-700 border border-purple-200">
                                    <i class="fa-solid fa-bell mr-1"></i>
                                    Follow-up Required
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mb-2">
                            <p class="text-sm text-gray-700">
                                <strong>Date:</strong> <?php echo date('M d, Y', strtotime($row['date_entry'])) ?>
                                <?php if (!empty($row['updated_at']) && $row['updated_at'] !== $row['date_entry']): ?>
                                    <span class="text-xs text-gray-500 ml-2">
                                        (Updated: <?php echo date('M d, Y', strtotime($row['updated_at'])) ?>)
                                    </span>
                                <?php endif; ?>
                            </p>
                            
                            <?php if (!empty($row['remarks'])): ?>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Remarks:</strong> <?php echo htmlspecialchars($row['remarks']) ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($row['follow_up_notes'])): ?>
                            <p class="text-sm text-gray-700 mt-1">
                                <strong>Follow-up Notes:</strong> <?php echo htmlspecialchars($row['follow_up_notes']) ?>
                            </p>
                            <?php endif; ?>
                            
                            <?php if (!empty($row['GLevel']) || !empty($row['Course'])): ?>
                            <p class="text-xs text-gray-500 mt-2">
                                <?php if (!empty($row['GLevel'])): ?>
                                    <span class="mr-3">Grade: <?php echo $row['GLevel'] ?></span>
                                <?php endif; ?>
                                <?php if (!empty($row['Course']) && $row['Course'] !== 'N/A'): ?>
                                    <span>Course: <?php echo $row['Course'] ?></span>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
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
                    <label for="addBehaviorCategory" class="block text-sm font-medium text-gray-700 mb-1">
                        Behavior Category <span class="text-red-500">*</span>
                    </label>
                    <select id="addBehaviorCategory" name="behaviorCategory" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        <option value="Academic Excellence">Academic Excellence</option>
                        <option value="Leadership">Leadership</option>
                        <option value="Participation">Participation</option>
                        <option value="Respect">Respect</option>
                        <option value="Punctuality">Punctuality</option>
                        <option value="Tardiness">Tardiness</option>
                        <option value="Disruptive Behavior">Disruptive Behavior</option>
                        <option value="Academic Concerns">Academic Concerns</option>
                        <option value="Violation of Rules">Violation of Rules</option>
                        <option value="Attendance Issues">Attendance Issues</option>
                        <option value="General Observation">General Observation</option>
                        <option value="Follow-up Required">Follow-up Required</option>
                    </select>
                </div>
                <div>
                    <label for="addSeverityLevel" class="block text-sm font-medium text-gray-700 mb-1">
                        Severity Level <span class="text-red-500">*</span>
                    </label>
                    <select id="addSeverityLevel" name="severityLevel" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Severity</option>
                        <option value="Low">Low - Minor observation</option>
                        <option value="Medium">Medium - Requires attention</option>
                        <option value="High">High - Serious concern</option>
                        <option value="Critical">Critical - Immediate action required</option>
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
                    <textarea id="addBehaviorRemarks" name="behaviorRemarks" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter detailed behavior observation..."></textarea>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="addFollowUpRequired" name="followUpRequired"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Requires Follow-up</span>
                    </label>
                </div>
                <div id="addFollowUpNotes" class="hidden">
                    <label for="addFollowUpNotesText" class="block text-sm font-medium text-gray-700 mb-1">
                        Follow-up Notes
                    </label>
                    <textarea id="addFollowUpNotesText" name="followUpNotes" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter follow-up instructions or notes..."></textarea>
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
                    <label for="editBehaviorCategory" class="block text-sm font-medium text-gray-700 mb-1">
                        Behavior Category <span class="text-red-500">*</span>
                    </label>
                    <select id="editBehaviorCategory" name="behaviorCategory" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Category</option>
                        <option value="Academic Excellence">Academic Excellence</option>
                        <option value="Leadership">Leadership</option>
                        <option value="Participation">Participation</option>
                        <option value="Respect">Respect</option>
                        <option value="Punctuality">Punctuality</option>
                        <option value="Tardiness">Tardiness</option>
                        <option value="Disruptive Behavior">Disruptive Behavior</option>
                        <option value="Academic Concerns">Academic Concerns</option>
                        <option value="Violation of Rules">Violation of Rules</option>
                        <option value="Attendance Issues">Attendance Issues</option>
                        <option value="General Observation">General Observation</option>
                        <option value="Follow-up Required">Follow-up Required</option>
                    </select>
                </div>
                <div>
                    <label for="editSeverityLevel" class="block text-sm font-medium text-gray-700 mb-1">
                        Severity Level <span class="text-red-500">*</span>
                    </label>
                    <select id="editSeverityLevel" name="severityLevel" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Severity</option>
                        <option value="Low">Low - Minor observation</option>
                        <option value="Medium">Medium - Requires attention</option>
                        <option value="High">High - Serious concern</option>
                        <option value="Critical">Critical - Immediate action required</option>
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
                    <textarea id="editBehaviorRemarks" name="behaviorRemarks" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter detailed behavior observation..."></textarea>
                </div>
                <div>
                    <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select id="editStatus" name="status"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="Active">Active</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Follow-up Required">Follow-up Required</option>
                        <option value="Archived">Archived</option>
                    </select>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" id="editFollowUpRequired" name="followUpRequired"
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Requires Follow-up</span>
                    </label>
                </div>
                <div id="editFollowUpNotes" class="hidden">
                    <label for="editFollowUpNotesText" class="block text-sm font-medium text-gray-700 mb-1">
                        Follow-up Notes
                    </label>
                    <textarea id="editFollowUpNotesText" name="followUpNotes" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter follow-up instructions or notes..."></textarea>
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

    <!-- Filter Modal -->
    <div id="filterModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Filter Behavior Records</h3>
                <button id="closeFilterModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <form id="filterForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="filterBehaviorType" class="block text-sm font-medium text-gray-700 mb-1">
                            Behavior Type
                        </label>
                        <select id="filterBehaviorType" name="behaviorType"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="Commendable">Commendable</option>
                            <option value="Needs Improvement">Needs Improvement</option>
                            <option value="Violation">Violation</option>
                        </select>
                    </div>
                    <div>
                        <label for="filterCategory" class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>
                        <select id="filterCategory" name="category"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Categories</option>
                            <option value="Academic Excellence">Academic Excellence</option>
                            <option value="Leadership">Leadership</option>
                            <option value="Participation">Participation</option>
                            <option value="Respect">Respect</option>
                            <option value="Punctuality">Punctuality</option>
                            <option value="Tardiness">Tardiness</option>
                            <option value="Disruptive Behavior">Disruptive Behavior</option>
                            <option value="Academic Concerns">Academic Concerns</option>
                            <option value="Violation of Rules">Violation of Rules</option>
                            <option value="Attendance Issues">Attendance Issues</option>
                            <option value="General Observation">General Observation</option>
                        </select>
                    </div>
                    <div>
                        <label for="filterSeverity" class="block text-sm font-medium text-gray-700 mb-1">
                            Severity Level
                        </label>
                        <select id="filterSeverity" name="severity"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Severities</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label for="filterStatus" class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>
                        <select id="filterStatus" name="status"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="Active">Active</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Follow-up Required">Follow-up Required</option>
                            <option value="Archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label for="filterDateFrom" class="block text-sm font-medium text-gray-700 mb-1">
                            Date From
                        </label>
                        <input type="date" id="filterDateFrom" name="dateFrom"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="filterDateTo" class="block text-sm font-medium text-gray-700 mb-1">
                            Date To
                        </label>
                        <input type="date" id="filterDateTo" name="dateTo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" id="clearFilters"
                        class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-2 focus:ring-blue-500">
                        Clear Filters
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fa-solid fa-filter mr-1"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Modal -->
    <div id="reportModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Behavior Summary Report</h3>
                <button id="closeReportModal" type="button" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times text-xl"></i>
                </button>
            </div>
            <div class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="reportDateFrom" class="block text-sm font-medium text-gray-700 mb-1">
                            Report Period From
                        </label>
                        <input type="date" id="reportDateFrom" name="reportDateFrom"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="reportDateTo" class="block text-sm font-medium text-gray-700 mb-1">
                            Report Period To
                        </label>
                        <input type="date" id="reportDateTo" name="reportDateTo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-end">
                        <button id="generateReport" type="button"
                            class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                            <i class="fa-solid fa-chart-bar mr-1"></i>
                            Generate Report
                        </button>
                    </div>
                </div>
            </div>
            <div id="reportContent" class="min-h-64 border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="text-center text-gray-500 py-8">
                    <i class="fa-solid fa-chart-bar text-4xl mb-4"></i>
                    <p>Click "Generate Report" to view behavior statistics</p>
                </div>
            </div>
        </div>
    </div>

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
            const category = formData.get('behaviorCategory');
            const severity = formData.get('severityLevel');
            const date = formData.get('behaviorDate');
            
            if (!lrn || !type || !category || !severity || !date) {
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
            const category = formData.get('behaviorCategory');
            const severity = formData.get('severityLevel');
            const date = formData.get('behaviorDate');
            
            if (!lrn || !type || !category || !severity || !date) {
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
    const filterModal = document.getElementById('filterModal');
    const closeFilterModal = document.getElementById('closeFilterModal');
    const filterForm = document.getElementById('filterForm');
    const clearFilters = document.getElementById('clearFilters');
    
    if (filterButton) {
        filterButton.addEventListener('click', function () {
            if (filterModal) {
                filterModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        });
    }
    
    if (closeFilterModal) {
        closeFilterModal.addEventListener('click', function() {
            filterModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            filterForm.reset();
            // Apply empty filters (show all records)
            console.log('Filters cleared');
        });
    }
    
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // Apply filters logic here
            console.log('Filters applied');
            filterModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }
    
    // Report button functionality
    const reportButton = document.getElementById('reportButton');
    const reportModal = document.getElementById('reportModal');
    const closeReportModal = document.getElementById('closeReportModal');
    const generateReport = document.getElementById('generateReport');
    const reportContent = document.getElementById('reportContent');
    
    if (reportButton) {
        reportButton.addEventListener('click', function () {
            if (reportModal) {
                reportModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Set default dates (last 30 days)
                const today = new Date();
                const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
                
                document.getElementById('reportDateTo').value = today.toISOString().split('T')[0];
                document.getElementById('reportDateFrom').value = thirtyDaysAgo.toISOString().split('T')[0];
            }
        });
    }
    
    if (closeReportModal) {
        closeReportModal.addEventListener('click', function() {
            reportModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        });
    }
    
    if (generateReport) {
        generateReport.addEventListener('click', function() {
            const dateFrom = document.getElementById('reportDateFrom').value;
            const dateTo = document.getElementById('reportDateTo').value;
            
            if (!dateFrom || !dateTo) {
                showAlert('error', 'Date Required', 'Please select both start and end dates for the report.');
                return;
            }
            
            // Generate sample report
            const sampleReportData = [
                {category: 'Positive Behaviors', count: 45, percentage: 60},
                {category: 'Needs Improvement', count: 20, percentage: 27},
                {category: 'Violations', count: 10, percentage: 13}
            ];
            
            let reportHTML = `
                <h4 class="text-lg font-semibold mb-4">Behavior Report: ${dateFrom} to ${dateTo}</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            `;
            
            sampleReportData.forEach(item => {
                const colorClass = item.category === 'Positive Behaviors' ? 'bg-green-100 text-green-800' :
                                 item.category === 'Needs Improvement' ? 'bg-yellow-100 text-yellow-800' :
                                 'bg-red-100 text-red-800';
                
                reportHTML += `
                    <div class="bg-white rounded-lg border p-4 text-center">
                        <div class="text-3xl font-bold ${colorClass.split(' ')[1]}">${item.count}</div>
                        <div class="text-sm font-medium text-gray-600 mt-1">${item.category}</div>
                        <div class="text-xs text-gray-500">${item.percentage}% of total</div>
                    </div>
                `;
            });
            
            reportHTML += `
                </div>
                <div class="bg-white rounded-lg border p-4">
                    <h5 class="font-semibold mb-3">Summary</h5>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li>• Total behavior records: 75</li>
                        <li>• Students with positive behavior: 35</li>
                        <li>• Students requiring follow-up: 8</li>
                        <li>• Critical incidents: 2</li>
                    </ul>
                </div>
            `;
            
            reportContent.innerHTML = reportHTML;
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
    
    // Follow-up required toggle for Add Modal
    const addFollowUpCheckbox = document.getElementById('addFollowUpRequired');
    const addFollowUpNotesDiv = document.getElementById('addFollowUpNotes');
    if (addFollowUpCheckbox && addFollowUpNotesDiv) {
        addFollowUpCheckbox.addEventListener('change', function() {
            if (this.checked) {
                addFollowUpNotesDiv.classList.remove('hidden');
            } else {
                addFollowUpNotesDiv.classList.add('hidden');
                document.getElementById('addFollowUpNotesText').value = '';
            }
        });
    }
    
    // Follow-up required toggle for Edit Modal
    const editFollowUpCheckbox = document.getElementById('editFollowUpRequired');
    const editFollowUpNotesDiv = document.getElementById('editFollowUpNotes');
    if (editFollowUpCheckbox && editFollowUpNotesDiv) {
        editFollowUpCheckbox.addEventListener('change', function() {
            if (this.checked) {
                editFollowUpNotesDiv.classList.remove('hidden');
            } else {
                editFollowUpNotesDiv.classList.add('hidden');
                document.getElementById('editFollowUpNotesText').value = '';
            }
        });
    }
    
    // Set today's date as default for add form
    const addDateInput = document.getElementById('addBehaviorDate');
    if (addDateInput && !addDateInput.value) {
        const today = new Date().toISOString().split('T')[0];
        addDateInput.value = today;
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