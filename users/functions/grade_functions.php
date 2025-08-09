<?php
// Grade Functions - Database operations for grade management
include "../../connection/conn.php";

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

    $sql = "INSERT INTO grades_tbl (student_id, LRN, subject, assessment_type, assessment_name, grade_value, max_points, grading_period, remarks, teacher_notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iissssdss", $student_id, $lrn, $subject, $assessment_type, $assessment_name, $grade_value, $max_points, $grading_period, $remarks, $teacher_notes);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => true, 'message' => 'Grade added successfully!'];
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to add grade. Database error.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
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

// Function to calculate student average for a subject and grading period
function getStudentSubjectAverage($student_id, $subject, $grading_period)
{
    $conn = conn();

    $sql = "SELECT AVG((grade_value / max_points) * 100) as average 
            FROM grades_tbl 
            WHERE student_id = ? AND subject = ? AND grading_period = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $student_id, $subject, $grading_period);
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

    $sql = "UPDATE grades_tbl SET subject = ?, assessment_type = ?, assessment_name = ?, grade_value = ?, max_points = ?, grading_period = ?, remarks = ?, teacher_notes = ? WHERE grade_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssddsssi", $subject, $assessment_type, $assessment_name, $grade_value, $max_points, $grading_period, $remarks, $teacher_notes, $grade_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => true, 'message' => 'Grade updated successfully!'];
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

// Function to get all anecdotal records with student information
function getAllAnecdotalRecords($limit = 50, $offset = 0)
{
    $conn = conn();

    $sql = "SELECT ar.*, s.Fname, s.Lname, s.Mname, s.GLevel, s.Course 
            FROM anecdotal_records_tbl ar 
            JOIN students_tbl s ON ar.student_id = s.id 
            ORDER BY ar.date_recorded DESC 
            LIMIT ? OFFSET ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
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

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $action = $_POST['action'] ?? '';

    switch ($action) {
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

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}
?>