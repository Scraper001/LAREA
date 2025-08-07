<form action="functions/add_user.php" method="POST" id="addStudentForm" class="space-y-4">
    <!-- Student Name -->
    <div>
        <label for="studentName" class="block text-sm font-medium text-gray-700 mb-1">
            Student Name <span class="text-red-500">*</span>
        </label>
        <input type="text" id="studentName" name="studentName" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter student's full name">
    </div>

    <!-- LRN (Learner Reference Number) -->
    <div>
        <label for="studentLRN" class="block text-sm font-medium text-gray-700 mb-1">
            LRN (Learner Reference Number) <span class="text-red-500">*</span>
        </label>
        <input type="text" id="studentLRN" name="studentLRN" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter 12-digit LRN">
    </div>

    <!-- Student Grade -->
    <div>
        <label for="studentGrade" class="block text-sm font-medium text-gray-700 mb-1">
            Grade Level <span class="text-red-500">*</span>
        </label>
        <select id="studentGrade" name="studentGrade" required
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">Select Grade Level</option>
            <option value="7">Grade 7</option>
            <option value="8">Grade 8</option>
            <option value="9">Grade 9</option>
            <option value="10">Grade 10</option>
            <option value="11">Grade 11</option>
            <option value="12">Grade 12</option>
        </select>
    </div>

    <!-- Student Course (Optional) -->
    <div>
        <label for="studentCourse" class="block text-sm font-medium text-gray-700 mb-1">
            Course/Strand <span class="text-gray-400">(if applicable)</span>
        </label>
        <select id="studentCourse" name="studentCourse"
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
        </select>
    </div>

    <!-- Modal Footer -->
    <div class="flex space-x-3 pt-4">
        <button type="button" id="cancelModal"
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