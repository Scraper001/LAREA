<?php
// Complete Enhanced Grade Functions - All functionality included
if (!function_exists('conn')) {
    include dirname(__DIR__, 2) . '/connection/conn.php';
}

// Function to calculate grade status and category (60% passing)
function calculateGradeDetails($percentage)
{
    $conn = conn();

    // Get grade thresholds from settings
    $settings = [];
    $sql = "SELECT setting_name, setting_value FROM grade_settings_tbl";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $settings[$row['setting_name']] = (float) $row['setting_value'];
        }
    }
    mysqli_close($conn);

    // Updated thresholds for 60% passing
    $passing_grade = $settings['passing_grade'] ?? 60;
    $outstanding_grade = $settings['outstanding_grade'] ?? 95;
    $very_satisfactory_grade = $settings['very_satisfactory_grade'] ?? 90;
    $satisfactory_grade = $settings['satisfactory_grade'] ?? 85;
    $fairly_satisfactory_grade = $settings['fairly_satisfactory_grade'] ?? 80;
    $did_not_meet_expectations_grade = $settings['did_not_meet_expectations_grade'] ?? 75;

    // Determine status
    $status = $percentage >= $passing_grade ? 'Passed' : 'Failed';

    // Determine category with new grading scale
    $category = 'Failed';
    if ($percentage >= $outstanding_grade) {
        $category = 'Outstanding';
    } elseif ($percentage >= $very_satisfactory_grade) {
        $category = 'Very Satisfactory';
    } elseif ($percentage >= $satisfactory_grade) {
        $category = 'Satisfactory';
    } elseif ($percentage >= $fairly_satisfactory_grade) {
        $category = 'Fairly Satisfactory';
    } elseif ($percentage >= $did_not_meet_expectations_grade) {
        $category = 'Did Not Meet Expectations';
    } elseif ($percentage >= $passing_grade) {
        $category = 'Beginning';
    }

    return ['status' => $status, 'category' => $category];
}

// Function to get all students for dropdown selection
function getAllStudents()
{
    $conn = conn();
    $sql = "SELECT id, LRN, Fname, Lname, Mname, GLevel, Course FROM students_tbl ORDER BY Lname, Fname";
    $result = mysqli_query($conn, $sql);

    $students = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }
    }

    mysqli_close($conn);
    return $students;
}

// Function to get student grade statistics
function getStudentGradeStatistics($student_id)
{
    $conn = conn();

    $stats = [];

    // Overall average
    $sql = "SELECT AVG(percentage) as avg_percentage FROM grades_tbl WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $stats['overall_average'] = round($row['avg_percentage'] ?? 0, 2);

    // Pass/Fail ratio
    $sql = "SELECT grade_status, COUNT(*) as count FROM grades_tbl WHERE student_id = ? GROUP BY grade_status";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['status_' . strtolower($row['grade_status'])] = $row['count'];
    }

    // Subject averages
    $sql = "SELECT subject, AVG(percentage) as avg_percentage FROM grades_tbl WHERE student_id = ? GROUP BY subject";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $stats['subject_averages'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['subject_averages'][$row['subject']] = round($row['avg_percentage'], 2);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $stats;
}

// Function to get student summary with grade statistics
function getStudentSummary($student_id)
{
    $conn = conn();

    // Get student info
    $sql = "SELECT * FROM students_tbl WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    if (!$student) {
        mysqli_close($conn);
        return null;
    }

    // Get grade statistics
    $stats = getStudentGradeStatistics($student_id);

    // Get total grades count
    $sql = "SELECT COUNT(*) as total_grades FROM grades_tbl WHERE student_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $stats['total_grades'] = $row['total_grades'];

    // Get recent grade
    $sql = "SELECT assessment_name, percentage, grade_status FROM grades_tbl WHERE student_id = ? ORDER BY date_recorded DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $recent_grade = mysqli_fetch_assoc($result);
    $stats['recent_grade'] = $recent_grade;

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return array_merge($student, $stats);
}

// Function to get all students with their grade summaries
function getAllStudentsWithSummaries()
{
    $conn = conn();
    $sql = "SELECT id, LRN, Fname, Lname, Mname, GLevel, Course FROM students_tbl ORDER BY Lname, Fname";
    $result = mysqli_query($conn, $sql);

    $students = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $summary = getStudentSummary($row['id']);
            if ($summary) {
                $students[] = $summary;
            }
        }
    }

    mysqli_close($conn);
    return $students;
}

// Function to get structured grading sheet for a student
function getStudentGradingSheet($student_id)
{
    $conn = conn();

    $sql = "SELECT g.*, s.Fname, s.Lname 
            FROM grades_tbl g 
            JOIN students_tbl s ON g.student_id = s.id 
            WHERE g.student_id = ? 
            ORDER BY g.grading_period, g.subject, g.date_recorded";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $gradingSheet = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $period = $row['grading_period'];
            $subject = $row['subject'];

            if (!isset($gradingSheet[$period])) {
                $gradingSheet[$period] = [];
            }
            if (!isset($gradingSheet[$period][$subject])) {
                $gradingSheet[$period][$subject] = [];
            }

            $gradingSheet[$period][$subject][] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $gradingSheet;
}

// Function to add a new grade
function addGrade($student_id, $lrn, $subject, $assessment_type, $assessment_name, $grade_value, $max_points, $grading_period, $remarks = '', $teacher_notes = '')
{
    $conn = conn();

    // Validate inputs
    if (empty($student_id) || empty($lrn) || empty($subject) || empty($assessment_name) || empty($grade_value) || empty($max_points)) {
        return ['success' => false, 'message' => 'Please fill in all required fields.'];
    }

    if (!is_numeric($grade_value) || !is_numeric($max_points)) {
        return ['success' => false, 'message' => 'Grade value and max points must be numbers.'];
    }

    if ($grade_value < 0 || $max_points <= 0) {
        return ['success' => false, 'message' => 'Invalid grade or max points values.'];
    }

    // Calculate percentage and grade details
    $percentage = ($grade_value / $max_points) * 100;
    $gradeDetails = calculateGradeDetails($percentage);

    $sql = "INSERT INTO grades_tbl (student_id, LRN, subject, assessment_type, assessment_name, grade_value, max_points, percentage, grade_status, grade_category, grading_period, remarks, teacher_notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "iisssdddsssss",
            $student_id,
            $lrn,
            $subject,
            $assessment_type,
            $assessment_name,
            $grade_value,
            $max_points,
            $percentage,
            $gradeDetails['status'],
            $gradeDetails['category'],
            $grading_period,
            $remarks,
            $teacher_notes
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => true, 'message' => 'Grade added successfully!', 'percentage' => round($percentage, 2), 'status' => $gradeDetails['status'], 'category' => $gradeDetails['category']];
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to add grade. Database error: ' . mysqli_error($conn)];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error: ' . mysqli_error($conn)];
    }
}

// Function to get all grades with student information
function getAllGrades($limit = 50, $offset = 0)
{
    $conn = conn();

    $sql = "SELECT g.*, s.Fname, s.Lname, s.Mname, s.GLevel, s.Course 
            FROM grades_tbl g 
            JOIN students_tbl s ON g.student_id = s.id 
            ORDER BY g.date_recorded DESC 
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $grades = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $grades[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $grades;
}

// Function to get grade statistics
function getGradeStatistics()
{
    $conn = conn();

    $stats = [];

    // Total grades
    $sql = "SELECT COUNT(*) as total_grades FROM grades_tbl";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['total_grades'] = $row['total_grades'];

    // Pass/Fail counts
    $sql = "SELECT grade_status, COUNT(*) as count FROM grades_tbl GROUP BY grade_status";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['status_' . strtolower($row['grade_status'])] = $row['count'];
    }

    // Grade category distribution
    $sql = "SELECT grade_category, COUNT(*) as count FROM grades_tbl GROUP BY grade_category";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $stats['category_' . str_replace(' ', '_', strtolower($row['grade_category']))] = $row['count'];
    }

    // Average percentage
    $sql = "SELECT AVG(percentage) as avg_percentage FROM grades_tbl";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['avg_percentage'] = round($row['avg_percentage'], 2);

    mysqli_close($conn);
    return $stats;
}

// Function to get grades for a specific student
function getStudentGrades($student_id)
{
    $conn = conn();

    $sql = "SELECT * FROM grades_tbl WHERE student_id = ? ORDER BY grading_period, subject, date_recorded DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $grades = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $grades[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $grades;
}

// Function to update a grade
function updateGrade($grade_id, $subject, $assessment_type, $assessment_name, $grade_value, $max_points, $grading_period, $remarks = '', $teacher_notes = '')
{
    $conn = conn();

    // Validate inputs
    if (empty($grade_id) || empty($subject) || empty($assessment_name) || empty($grade_value) || empty($max_points)) {
        return ['success' => false, 'message' => 'Please fill in all required fields.'];
    }

    if (!is_numeric($grade_value) || !is_numeric($max_points)) {
        return ['success' => false, 'message' => 'Grade value and max points must be numbers.'];
    }

    // Calculate new percentage and grade details
    $percentage = ($grade_value / $max_points) * 100;
    $gradeDetails = calculateGradeDetails($percentage);

    $sql = "UPDATE grades_tbl SET subject = ?, assessment_type = ?, assessment_name = ?, grade_value = ?, max_points = ?, percentage = ?, grade_status = ?, grade_category = ?, grading_period = ?, remarks = ?, teacher_notes = ? WHERE grade_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "sssdddsssssi",
            $subject,
            $assessment_type,
            $assessment_name,
            $grade_value,
            $max_points,
            $percentage,
            $gradeDetails['status'],
            $gradeDetails['category'],
            $grading_period,
            $remarks,
            $teacher_notes,
            $grade_id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => true, 'message' => 'Grade updated successfully!', 'percentage' => round($percentage, 2), 'status' => $gradeDetails['status'], 'category' => $gradeDetails['category']];
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to update grade.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
    }
}

// Function to delete a grade
function deleteGrade($grade_id)
{
    $conn = conn();

    $sql = "DELETE FROM grades_tbl WHERE grade_id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $grade_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => true, 'message' => 'Grade deleted successfully!'];
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to delete grade.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
    }
}

// Function to get a single grade by ID
function getGradeById($grade_id)
{
    $conn = conn();

    $sql = "SELECT g.*, s.Fname, s.Lname, s.LRN FROM grades_tbl g JOIN students_tbl s ON g.student_id = s.id WHERE g.grade_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $grade_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $grade = null;
    if ($result) {
        $grade = mysqli_fetch_assoc($result);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $grade;
}

// Function to add anecdotal record
function addAnecdotalRecord($student_id, $lrn, $record_type, $observation_title, $observation_details, $severity_level = 'Low', $follow_up_required = 0, $follow_up_notes = '')
{
    $conn = conn();

    // Validate inputs
    if (empty($student_id) || empty($lrn) || empty($observation_title) || empty($observation_details)) {
        return ['success' => false, 'message' => 'Please fill in all required fields.'];
    }

    $sql = "INSERT INTO anecdotal_records_tbl (student_id, LRN, record_type, observation_title, observation_details, severity_level, follow_up_required, follow_up_notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissssss", $student_id, $lrn, $record_type, $observation_title, $observation_details, $severity_level, $follow_up_required, $follow_up_notes);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => true, 'message' => 'Anecdotal record added successfully!'];
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to add anecdotal record.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
    }
}

// Function to get anecdotal records for a student
function getStudentAnecdotalRecords($student_id)
{
    $conn = conn();

    $sql = "SELECT * FROM anecdotal_records_tbl WHERE student_id = ? ORDER BY date_recorded DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $records = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $records[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $records;
}

// Function to search grades by student name or subject
function searchGrades($search_term)
{
    $conn = conn();

    $search_term = '%' . $search_term . '%';
    $sql = "SELECT g.*, s.Fname, s.Lname, s.Mname, s.GLevel, s.Course 
            FROM grades_tbl g 
            JOIN students_tbl s ON g.student_id = s.id 
            WHERE CONCAT(s.Fname, ' ', s.Lname, ' ', s.Mname) LIKE ? 
               OR g.subject LIKE ? 
               OR g.assessment_name LIKE ?
            ORDER BY g.date_recorded DESC";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $grades = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $grades[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $grades;
}

// Function to bulk add grades for multiple students
function bulkAddGrades($grades_data)
{
    $conn = conn();
    $results = [];

    foreach ($grades_data as $grade_data) {
        $result = addGrade(
            $grade_data['student_id'],
            $grade_data['lrn'],
            $grade_data['subject'],
            $grade_data['assessment_type'],
            $grade_data['assessment_name'],
            $grade_data['grade_value'],
            $grade_data['max_points'],
            $grade_data['grading_period'],
            $grade_data['remarks'] ?? '',
            $grade_data['teacher_notes'] ?? ''
        );

        $results[] = $result;
    }

    return $results;
}

// Function to calculate subject average for a student
function getStudentSubjectAverage($student_id, $subject, $grading_period = null)
{
    $conn = conn();

    $sql = "SELECT AVG(percentage) as average FROM grades_tbl WHERE student_id = ? AND subject = ?";
    $params = [$student_id, $subject];
    $types = "is";

    if ($grading_period) {
        $sql .= " AND grading_period = ?";
        $params[] = $grading_period;
        $types .= "s";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $average = 0;
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $average = round($row['average'], 2);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $average;
}

// Function to export student grades as CSV
function exportStudentGradesCSV($student_id)
{
    $conn = conn();

    // Get student info
    $sql = "SELECT Fname, Lname, LRN FROM students_tbl WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($result);

    if (!$student) {
        return false;
    }

    // Get all grades
    $grades = getStudentGrades($student_id);

    $filename = "grades_" . $student['Fname'] . "_" . $student['Lname'] . "_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // CSV Headers
    fputcsv($output, [
        'Student Name',
        'LRN',
        'Subject',
        'Assessment Type',
        'Assessment Name',
        'Score',
        'Max Points',
        'Percentage',
        'Status',
        'Category',
        'Grading Period',
        'Date Recorded',
        'Remarks'
    ]);

    // CSV Data
    foreach ($grades as $grade) {
        fputcsv($output, [
            $student['Fname'] . ' ' . $student['Lname'],
            $student['LRN'],
            $grade['subject'],
            $grade['assessment_type'],
            $grade['assessment_name'],
            $grade['grade_value'],
            $grade['max_points'],
            $grade['percentage'] . '%',
            $grade['grade_status'],
            $grade['grade_category'],
            $grade['grading_period'],
            $grade['date_recorded'],
            $grade['remarks']
        ]);
    }

    fclose($output);
    mysqli_close($conn);
    return true;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'get_student_summary':
            $summary = getStudentSummary($_POST['student_id']);
            echo json_encode(['success' => true, 'data' => $summary]);
            break;

        case 'get_grading_sheet':
            $sheet = getStudentGradingSheet($_POST['student_id']);
            echo json_encode(['success' => true, 'data' => $sheet]);
            break;

        case 'get_grade_by_id':
            $grade = getGradeById($_POST['grade_id']);
            echo json_encode(['success' => true, 'data' => $grade]);
            break;

        case 'add_grade':
            $result = addGrade(
                $_POST['student_id'],
                $_POST['lrn'],
                $_POST['subject'],
                $_POST['assessment_type'],
                $_POST['assessment_name'],
                $_POST['grade_value'],
                $_POST['max_points'],
                $_POST['grading_period'],
                $_POST['remarks'] ?? '',
                $_POST['teacher_notes'] ?? ''
            );
            echo json_encode($result);
            break;

        case 'update_grade':
            $result = updateGrade(
                $_POST['grade_id'],
                $_POST['subject'],
                $_POST['assessment_type'],
                $_POST['assessment_name'],
                $_POST['grade_value'],
                $_POST['max_points'],
                $_POST['grading_period'],
                $_POST['remarks'] ?? '',
                $_POST['teacher_notes'] ?? ''
            );
            echo json_encode($result);
            break;

        case 'delete_grade':
            $result = deleteGrade($_POST['grade_id']);
            echo json_encode($result);
            break;

        case 'add_anecdotal':
            $result = addAnecdotalRecord(
                $_POST['student_id'],
                $_POST['lrn'],
                $_POST['record_type'],
                $_POST['observation_title'],
                $_POST['observation_details'],
                $_POST['severity_level'] ?? 'Low',
                $_POST['follow_up_required'] ?? 0,
                $_POST['follow_up_notes'] ?? ''
            );
            echo json_encode($result);
            break;

        case 'get_student_anecdotal':
            $records = getStudentAnecdotalRecords($_POST['student_id']);
            echo json_encode(['success' => true, 'data' => $records]);
            break;

        case 'get_statistics':
            $stats = getGradeStatistics();
            echo json_encode(['success' => true, 'data' => $stats]);
            break;

        case 'search_grades':
            $grades = searchGrades($_POST['search_term']);
            echo json_encode(['success' => true, 'data' => $grades]);
            break;

        case 'bulk_add_grades':
            $grades_data = json_decode($_POST['grades_data'], true);
            $results = bulkAddGrades($grades_data);
            echo json_encode(['success' => true, 'data' => $results]);
            break;

        case 'get_subject_average':
            $average = getStudentSubjectAverage(
                $_POST['student_id'],
                $_POST['subject'],
                $_POST['grading_period'] ?? null
            );
            echo json_encode(['success' => true, 'data' => $average]);
            break;

        case 'export_csv':
            if (exportStudentGradesCSV($_POST['student_id'])) {
                // File download handled in function
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Export failed']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}

// Handle GET requests for exports
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'export_csv' && isset($_GET['student_id'])) {
        exportStudentGradesCSV($_GET['student_id']);
        exit;
    }
}
?>