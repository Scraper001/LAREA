<?php
// Enhanced time calculation utilities for attendance system

include_once "../connection/conn.php";
include_once "attendance_functions.php";

/**
 * Calculate total working hours for a student on a specific date
 */
function calculateDailyHours($student_id, $date)
{
    global $conn;
    
    $query = "SELECT time_in, time_out, break_start, break_end, total_hours FROM attendance 
              WHERE student_id = ? AND date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $student_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();
    
    if (!$attendance || !$attendance['time_in'] || !$attendance['time_out']) {
        return ['total_hours' => 0, 'overtime_hours' => 0, 'break_hours' => 0];
    }
    
    return calculateWorkingHours(
        $attendance['time_in'], 
        $attendance['time_out'], 
        $attendance['break_start'], 
        $attendance['break_end']
    );
}

/**
 * Calculate weekly hours for a student
 */
function calculateWeeklyHours($student_id, $start_date)
{
    $end_date = date('Y-m-d', strtotime($start_date . ' +6 days'));
    return calculatePeriodHours($student_id, $start_date, $end_date);
}

/**
 * Calculate monthly hours for a student
 */
function calculateMonthlyHours($student_id, $year, $month)
{
    $start_date = sprintf('%04d-%02d-01', $year, $month);
    $end_date = date('Y-m-t', strtotime($start_date));
    return calculatePeriodHours($student_id, $start_date, $end_date);
}

/**
 * Calculate hours for a specific period
 */
function calculatePeriodHours($student_id, $start_date, $end_date)
{
    global $conn;
    
    $query = "SELECT 
                SUM(total_hours) as total_hours,
                SUM(overtime_hours) as overtime_hours,
                COUNT(CASE WHEN status IN ('present', 'late') THEN 1 END) as days_present,
                COUNT(CASE WHEN status = 'absent' THEN 1 END) as days_absent
              FROM attendance 
              WHERE student_id = ? AND date BETWEEN ? AND ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $student_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Calculate attendance rate for a student
 */
function calculateAttendanceRate($student_id, $start_date, $end_date)
{
    global $conn;
    
    // Get school days (excluding weekends and holidays)
    $school_days = getSchoolDaysCount($start_date, $end_date);
    
    $query = "SELECT 
                COUNT(CASE WHEN status IN ('present', 'late') THEN 1 END) as days_present
              FROM attendance 
              WHERE student_id = ? AND date BETWEEN ? AND ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $student_id, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    
    if ($school_days == 0) return 0;
    
    return round(($data['days_present'] / $school_days) * 100, 2);
}

/**
 * Get count of school days (excluding weekends and holidays)
 */
function getSchoolDaysCount($start_date, $end_date)
{
    global $conn;
    
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $end->modify('+1 day'); // Include end date
    
    $school_days = 0;
    $current = clone $start;
    
    // Get holidays in the period
    $holiday_query = "SELECT date FROM holidays WHERE date BETWEEN ? AND ?";
    $stmt = $conn->prepare($holiday_query);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $holiday_result = $stmt->get_result();
    
    $holidays = [];
    while ($row = $holiday_result->fetch_assoc()) {
        $holidays[] = $row['date'];
    }
    
    while ($current < $end) {
        $day_of_week = $current->format('N'); // 1 = Monday, 7 = Sunday
        $current_date = $current->format('Y-m-d');
        
        // Count if it's a weekday and not a holiday
        if ($day_of_week <= 5 && !in_array($current_date, $holidays)) {
            $school_days++;
        }
        
        $current->modify('+1 day');
    }
    
    return $school_days;
}

/**
 * Auto-calculate break time if not manually entered
 */
function autoCalculateBreakTime($time_in, $time_out)
{
    $settings = getAttendanceSettings();
    
    if (!$time_in || !$time_out) {
        return ['break_start' => null, 'break_end' => null];
    }
    
    $start = new DateTime($time_in);
    $end = new DateTime($time_out);
    $break_start = new DateTime($settings['break_start_time']);
    $break_end = new DateTime($settings['break_end_time']);
    
    // Check if the working period overlaps with break time
    if ($start <= $break_start && $end >= $break_end) {
        return [
            'break_start' => $settings['break_start_time'],
            'break_end' => $settings['break_end_time']
        ];
    }
    
    return ['break_start' => null, 'break_end' => null];
}

/**
 * Check if a time entry qualifies as half-day
 */
function isHalfDay($time_in, $time_out, $break_start = null, $break_end = null)
{
    $hours = calculateWorkingHours($time_in, $time_out, $break_start, $break_end);
    $settings = getAttendanceSettings();
    
    return $hours['total_hours'] <= $settings['half_day_hours'];
}

/**
 * Generate time suggestions based on common patterns
 */
function getTimeSuggestions()
{
    $settings = getAttendanceSettings();
    
    return [
        'regular_day' => [
            'time_in' => $settings['school_start_time'],
            'time_out' => $settings['school_end_time'],
            'break_start' => $settings['break_start_time'],
            'break_end' => $settings['break_end_time']
        ],
        'half_day' => [
            'time_in' => $settings['school_start_time'],
            'time_out' => date('H:i:s', strtotime($settings['school_start_time'] . ' +4 hours')),
            'break_start' => null,
            'break_end' => null
        ],
        'late_arrival' => [
            'time_in' => date('H:i:s', strtotime($settings['school_start_time'] . ' +30 minutes')),
            'time_out' => $settings['school_end_time'],
            'break_start' => $settings['break_start_time'],
            'break_end' => $settings['break_end_time']
        ]
    ];
}

/**
 * Validate time entries for logical consistency
 */
function validateTimeEntries($time_in, $time_out, $break_start = null, $break_end = null)
{
    $errors = [];
    
    if ($time_in && $time_out) {
        if (strtotime($time_in) >= strtotime($time_out)) {
            $errors[] = "Time out must be after time in";
        }
    }
    
    if ($break_start && $break_end) {
        if (strtotime($break_start) >= strtotime($break_end)) {
            $errors[] = "Break end must be after break start";
        }
        
        if ($time_in && strtotime($break_start) < strtotime($time_in)) {
            $errors[] = "Break start cannot be before time in";
        }
        
        if ($time_out && strtotime($break_end) > strtotime($time_out)) {
            $errors[] = "Break end cannot be after time out";
        }
    }
    
    return $errors;
}

/**
 * Format hours for display (e.g., 8.5 hours, 30 minutes)
 */
function formatHoursDisplay($hours)
{
    if ($hours < 1) {
        $minutes = round($hours * 60);
        return $minutes . " minute" . ($minutes != 1 ? "s" : "");
    } else {
        $full_hours = floor($hours);
        $minutes = round(($hours - $full_hours) * 60);
        
        $display = $full_hours . " hour" . ($full_hours != 1 ? "s" : "");
        if ($minutes > 0) {
            $display .= ", " . $minutes . " minute" . ($minutes != 1 ? "s" : "");
        }
        
        return $display;
    }
}

/**
 * Get attendance analytics for dashboard
 */
function getAttendanceAnalytics($date_from, $date_to)
{
    global $conn;
    
    $query = "SELECT 
                COUNT(DISTINCT a.student_id) as active_students,
                COUNT(DISTINCT a.date) as school_days,
                AVG(CASE WHEN a.status IN ('present', 'late') THEN 1 ELSE 0 END) * 100 as avg_attendance_rate,
                SUM(a.total_hours) as total_hours_logged,
                SUM(a.overtime_hours) as total_overtime,
                COUNT(CASE WHEN a.status = 'late' THEN 1 END) as total_late_arrivals
              FROM attendance a
              WHERE a.date BETWEEN ? AND ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>