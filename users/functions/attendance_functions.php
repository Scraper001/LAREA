<?php
// Include database connection
require_once('../connection/conn.php');

/**
 * Get today's attendance statistics
 */
function getAttendanceStats($date = null)
{
    $conn = conn();
    $date = $date ?? date('Y-m-d');

    $query = "SELECT 
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
        SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
        SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count,
        COUNT(*) as total_records
        FROM attendance 
        WHERE date = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();

    // If no records for today, initialize with zeros
    if (!$stats || !isset($stats['total_records']) || $stats['total_records'] == 0) {
        $stats = [
            'present_count' => 0,
            'absent_count' => 0,
            'late_count' => 0,
            'excused_count' => 0,
            'total_records' => 0
        ];
    }

    return $stats;
}

/**
 * Simple attendance recording function
 */
function recordAttendance($student_id, $date, $status, $time_in = null, $time_out = null, $remarks = null, $recorded_by = null)
{
    $conn = conn();

    // Check if attendance already exists for this student on this date
    $check_query = "SELECT id FROM attendance WHERE student_id = ? AND date = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("is", $student_id, $date);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $row = $result->fetch_assoc();
        $id = $row['id'];

        $update_query = "UPDATE attendance SET status = ?, time_in = ?, time_out = ?, remarks = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssi", $status, $time_in, $time_out, $remarks, $id);

        if ($update_stmt->execute()) {
            // Update hours worked if both time_in and time_out are set
            if ($time_in && $time_out) {
                $hours_query = "UPDATE attendance SET hours_worked = TIMESTAMPDIFF(MINUTE, time_in, time_out)/60 WHERE id = ?";
                $hours_stmt = $conn->prepare($hours_query);
                $hours_stmt->bind_param("i", $id);
                $hours_stmt->execute();
            }

            return ['success' => true, 'message' => 'Attendance updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Error updating attendance: ' . $conn->error];
        }
    } else {
        // Insert new record
        $insert_query = "INSERT INTO attendance (student_id, date, status, time_in, time_out, remarks, recorded_by) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("isssssi", $student_id, $date, $status, $time_in, $time_out, $remarks, $recorded_by);

        if ($insert_stmt->execute()) {
            $last_id = $conn->insert_id;

            // Update hours worked if both time_in and time_out are set
            if ($time_in && $time_out) {
                $hours_query = "UPDATE attendance SET hours_worked = TIMESTAMPDIFF(MINUTE, time_in, time_out)/60 WHERE id = ?";
                $hours_stmt = $conn->prepare($hours_query);
                $hours_stmt->bind_param("i", $last_id);
                $hours_stmt->execute();
            }

            return ['success' => true, 'message' => 'Attendance recorded successfully'];
        } else {
            return ['success' => false, 'message' => 'Error recording attendance: ' . $conn->error];
        }
    }
}

/**
 * Get students without attendance records for a specific date
 */
function getStudentsWithoutAttendance($date = null)
{
    $conn = conn();
    $date = $date ?? date('Y-m-d');

    $query = "SELECT s.* FROM students_tbl s
              LEFT JOIN attendance a ON s.id = a.student_id AND a.date = ?
              WHERE a.id IS NULL
              ORDER BY s.Lname, s.Fname";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;
}

/**
 * Get students with attendance records for a specific date
 */
function getStudentsWithAttendance($date = null)
{
    $conn = conn();
    $date = $date ?? date('Y-m-d');

    $query = "SELECT s.*, a.id as attendance_id, a.status, a.time_in, a.time_out, 
              a.hours_worked, a.remarks
              FROM students_tbl s
              JOIN attendance a ON s.id = a.student_id 
              WHERE a.date = ?
              ORDER BY s.Lname, s.Fname";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;
}

/**
 * Get attendance settings
 */
function getAttendanceSettings()
{
    $conn = conn();

    $query = "SELECT * FROM attendance_settings ORDER BY id LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    // Return default settings if none found
    return [
        'school_start_time' => '08:00:00',
        'school_end_time' => '17:00:00',
        'late_threshold_minutes' => 15,
        'grace_period_minutes' => 5,
        'lunch_start_time' => '12:00:00',
        'lunch_end_time' => '13:00:00'
    ];
}

/**
 * Search students by name or LRN
 */
function searchStudents($search_term)
{
    $conn = conn();

    $search_term = "%$search_term%";

    $query = "SELECT * FROM students_tbl 
              WHERE Fname LIKE ? OR Lname LIKE ? OR Mname LIKE ? OR LRN LIKE ?
              ORDER BY Lname, Fname";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $search_term, $search_term, $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;
}

/**
 * Get a specific attendance record
 */
function getAttendanceById($id)
{
    $conn = conn();

    $query = "SELECT a.*, s.Fname, s.Lname, s.Mname, s.GLevel, s.LRN, s.photo_path
              FROM attendance a
              JOIN students_tbl s ON a.student_id = s.id
              WHERE a.id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

/**
 * Delete an attendance record
 */
function deleteAttendance($id)
{
    $conn = conn();

    $query = "DELETE FROM attendance WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Attendance record deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Error deleting attendance record: ' . $conn->error];
    }
}
?>