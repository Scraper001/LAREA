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
        mysqli_stmt_bind_param($stmt, "iisssddsss", $student_id, $lrn, $subject, $assessment_type, $assessment_name, $grade_value, $max_points, $grading_period, $remarks, $teacher_notes);

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
        mysqli_stmt_bind_param($stmt, "iissssis", $student_id, $lrn, $record_type, $observation_title, $observation_details, $severity_level, $follow_up_required, $follow_up_notes);

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

// Function to get grade statistics for a student
function getStudentGradeStatistics($student_id)
{
    $conn = conn();

    $sql = "SELECT 
                COUNT(grade_id) as total_assessments,
                AVG(percentage) as average_percentage,
                MIN(percentage) as lowest_grade,
                MAX(percentage) as highest_grade,
                SUM(CASE WHEN pass_fail_status = 'Pass' THEN 1 ELSE 0 END) as passed_assessments,
                SUM(CASE WHEN pass_fail_status = 'Fail' THEN 1 ELSE 0 END) as failed_assessments,
                SUM(CASE WHEN grade_category = 'Excellent' THEN 1 ELSE 0 END) as excellent_grades,
                SUM(CASE WHEN grade_category = 'Very Good' THEN 1 ELSE 0 END) as very_good_grades,
                SUM(CASE WHEN grade_category = 'Good' THEN 1 ELSE 0 END) as good_grades,
                SUM(CASE WHEN grade_category = 'Satisfactory' THEN 1 ELSE 0 END) as satisfactory_grades,
                SUM(CASE WHEN grade_category = 'Needs Improvement' THEN 1 ELSE 0 END) as needs_improvement_grades,
                SUM(CASE WHEN grade_category = 'Failed' THEN 1 ELSE 0 END) as failed_grades
            FROM grades_tbl 
            WHERE student_id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $stats = [];
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $stats = $row;
        $stats['pass_rate'] = $stats['total_assessments'] > 0 ? 
            round(($stats['passed_assessments'] / $stats['total_assessments']) * 100, 2) : 0;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $stats;
}

// Function to get class performance statistics
function getClassPerformanceStatistics($subject = null, $grading_period = null)
{
    $conn = conn();

    $where_conditions = [];
    $params = [];
    $types = "";

    if ($subject) {
        $where_conditions[] = "subject = ?";
        $params[] = $subject;
        $types .= "s";
    }

    if ($grading_period) {
        $where_conditions[] = "grading_period = ?";
        $params[] = $grading_period;
        $types .= "s";
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $sql = "SELECT 
                subject,
                grading_period,
                COUNT(DISTINCT student_id) as total_students,
                AVG(percentage) as class_average,
                MIN(percentage) as lowest_grade,
                MAX(percentage) as highest_grade,
                COUNT(CASE WHEN pass_fail_status = 'Pass' THEN 1 END) as students_passed,
                COUNT(CASE WHEN pass_fail_status = 'Fail' THEN 1 END) as students_failed,
                (COUNT(CASE WHEN pass_fail_status = 'Pass' THEN 1 END) / COUNT(DISTINCT student_id)) * 100 as pass_rate
            FROM grades_tbl 
            $where_clause
            GROUP BY subject, grading_period
            ORDER BY subject, grading_period";

    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $stats = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $stats[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $stats;
}

// Function to get grade distribution data for charts
function getGradeDistribution($student_id = null, $subject = null, $grading_period = null)
{
    $conn = conn();

    $where_conditions = [];
    $params = [];
    $types = "";

    if ($student_id) {
        $where_conditions[] = "student_id = ?";
        $params[] = $student_id;
        $types .= "i";
    }

    if ($subject) {
        $where_conditions[] = "subject = ?";
        $params[] = $subject;
        $types .= "s";
    }

    if ($grading_period) {
        $where_conditions[] = "grading_period = ?";
        $params[] = $grading_period;
        $types .= "s";
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $sql = "SELECT 
                grade_category,
                COUNT(*) as count,
                ROUND(AVG(percentage), 2) as avg_percentage
            FROM grades_tbl 
            $where_clause
            GROUP BY grade_category
            ORDER BY 
                CASE grade_category
                    WHEN 'Excellent' THEN 1
                    WHEN 'Very Good' THEN 2
                    WHEN 'Good' THEN 3
                    WHEN 'Satisfactory' THEN 4
                    WHEN 'Needs Improvement' THEN 5
                    WHEN 'Failed' THEN 6
                END";

    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $distribution = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $distribution[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $distribution;
}

// Function to get grade improvement trends
function getGradeImprovementTrends($student_id)
{
    $conn = conn();

    $sql = "SELECT 
                subject,
                grading_period,
                new_percentage,
                improvement,
                date_changed
            FROM grade_history_tbl 
            WHERE student_id = ? AND action_type IN ('CREATED', 'UPDATED')
            ORDER BY subject, date_changed";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $trends = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $trends[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $trends;
}

// Function to add multiple grades at once (bulk entry)
function addBulkGrades($grades_data)
{
    $conn = conn();
    $success_count = 0;
    $errors = [];

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {
        $sql = "INSERT INTO grades_tbl (student_id, LRN, subject, assessment_type, assessment_name, grade_value, max_points, grading_period, remarks, teacher_notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);

        foreach ($grades_data as $index => $grade) {
            // Validate each grade entry
            if (empty($grade['student_id']) || empty($grade['lrn']) || empty($grade['subject']) || 
                empty($grade['assessment_name']) || empty($grade['grade_value']) || empty($grade['max_points'])) {
                $errors[] = "Row " . ($index + 1) . ": Missing required fields";
                continue;
            }

            if (!is_numeric($grade['grade_value']) || !is_numeric($grade['max_points'])) {
                $errors[] = "Row " . ($index + 1) . ": Grade value and max points must be numbers";
                continue;
            }

            mysqli_stmt_bind_param($stmt, "iisssddsss", 
                $grade['student_id'], $grade['lrn'], $grade['subject'], 
                $grade['assessment_type'], $grade['assessment_name'], 
                $grade['grade_value'], $grade['max_points'], 
                $grade['grading_period'], $grade['remarks'] ?? '', 
                $grade['teacher_notes'] ?? ''
            );

            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            } else {
                $errors[] = "Row " . ($index + 1) . ": Database error - " . mysqli_error($conn);
            }
        }

        mysqli_stmt_close($stmt);

        if (empty($errors)) {
            mysqli_commit($conn);
            mysqli_close($conn);
            return ['success' => true, 'message' => "$success_count grades added successfully!"];
        } else {
            mysqli_rollback($conn);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Some grades failed to add', 'errors' => $errors, 'success_count' => $success_count];
        }

    } catch (Exception $e) {
        mysqli_rollback($conn);
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()];
    }
}

// Function to generate grade report data
function generateGradeReport($student_id = null, $subject = null, $grading_period = null, $format = 'array')
{
    $conn = conn();

    $where_conditions = [];
    $params = [];
    $types = "";

    if ($student_id) {
        $where_conditions[] = "g.student_id = ?";
        $params[] = $student_id;
        $types .= "i";
    }

    if ($subject) {
        $where_conditions[] = "g.subject = ?";
        $params[] = $subject;
        $types .= "s";
    }

    if ($grading_period) {
        $where_conditions[] = "g.grading_period = ?";
        $params[] = $grading_period;
        $types .= "s";
    }

    $where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

    $sql = "SELECT 
                s.LRN,
                CONCAT(s.Fname, ' ', s.Lname) as student_name,
                s.GLevel,
                s.Course,
                g.subject,
                g.assessment_type,
                g.assessment_name,
                g.grade_value,
                g.max_points,
                g.percentage,
                g.grade_category,
                g.pass_fail_status,
                g.grading_period,
                g.remarks,
                g.date_recorded
            FROM grades_tbl g 
            JOIN students_tbl s ON g.student_id = s.id 
            $where_clause
            ORDER BY s.Lname, s.Fname, g.subject, g.grading_period, g.date_recorded";

    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $report_data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $report_data[] = $row;
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $report_data;
}

// Function to get grade settings
function getGradeSettings()
{
    $conn = conn();

    $sql = "SELECT setting_name, setting_value FROM grade_settings_tbl";
    $result = mysqli_query($conn, $sql);

    $settings = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
    }

    mysqli_close($conn);
    return $settings;
}

// Function to update grade settings
function updateGradeSetting($setting_name, $setting_value)
{
    $conn = conn();

    $sql = "UPDATE grade_settings_tbl SET setting_value = ? WHERE setting_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $setting_value, $setting_name);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => true, 'message' => 'Setting updated successfully!'];
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to update setting.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
    }
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

        case 'get_student_statistics':
            $result = getStudentGradeStatistics($_POST['student_id']);
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'get_class_performance':
            $result = getClassPerformanceStatistics(
                $_POST['subject'] ?? null,
                $_POST['grading_period'] ?? null
            );
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'get_grade_distribution':
            $result = getGradeDistribution(
                $_POST['student_id'] ?? null,
                $_POST['subject'] ?? null,
                $_POST['grading_period'] ?? null
            );
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'get_improvement_trends':
            $result = getGradeImprovementTrends($_POST['student_id']);
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'add_bulk_grades':
            $grades_data = json_decode($_POST['grades_data'], true);
            $result = addBulkGrades($grades_data);
            echo json_encode($result);
            break;

        case 'generate_report':
            $result = generateGradeReport(
                $_POST['student_id'] ?? null,
                $_POST['subject'] ?? null,
                $_POST['grading_period'] ?? null
            );
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'get_grade_settings':
            $result = getGradeSettings();
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'update_grade_setting':
            $result = updateGradeSetting($_POST['setting_name'], $_POST['setting_value']);
            echo json_encode($result);
            break;

        case 'export_grades_csv':
            $report_data = generateGradeReport(
                $_POST['student_id'] ?? null,
                $_POST['subject'] ?? null,
                $_POST['grading_period'] ?? null
            );
            
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="grade_report_' . date('Y-m-d') . '.csv"');
            
            $output = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($output, [
                'LRN', 'Student Name', 'Grade Level', 'Course', 'Subject', 
                'Assessment Type', 'Assessment Name', 'Score', 'Max Points', 
                'Percentage', 'Grade Category', 'Pass/Fail', 'Grading Period', 
                'Remarks', 'Date Recorded'
            ]);
            
            // CSV Data
            foreach ($report_data as $row) {
                fputcsv($output, [
                    $row['LRN'],
                    $row['student_name'],
                    $row['GLevel'],
                    $row['Course'],
                    $row['subject'],
                    $row['assessment_type'],
                    $row['assessment_name'],
                    $row['grade_value'],
                    $row['max_points'],
                    $row['percentage'] . '%',
                    $row['grade_category'],
                    $row['pass_fail_status'],
                    $row['grading_period'],
                    $row['remarks'],
                    $row['date_recorded']
                ]);
            }
            
            fclose($output);
            exit;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}
?>