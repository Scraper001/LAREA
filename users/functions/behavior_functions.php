<?php
// Comprehensive Behavior Management Functions
// Enhanced behavior management system for LAREA

if (!function_exists('conn')) {
    include dirname(__DIR__, 2) . '/connection/conn.php';
}

// Function to get all behavior records with filtering options
function getBehaviorRecords($limit = 50, $offset = 0, $filters = [])
{
    $conn = conn();
    
    $where_conditions = [];
    $params = [];
    $types = '';
    
    // Build WHERE clause based on filters
    if (!empty($filters['student_id'])) {
        $where_conditions[] = "b.student_id = ?";
        $params[] = $filters['student_id'];
        $types .= 'i';
    }
    
    if (!empty($filters['lrn'])) {
        $where_conditions[] = "b.LRN = ?";
        $params[] = $filters['lrn'];
        $types .= 's';
    }
    
    if (!empty($filters['behavior_type'])) {
        $where_conditions[] = "b.behavior_type = ?";
        $params[] = $filters['behavior_type'];
        $types .= 's';
    }
    
    if (!empty($filters['behavior_category'])) {
        $where_conditions[] = "b.behavior_category = ?";
        $params[] = $filters['behavior_category'];
        $types .= 's';
    }
    
    if (!empty($filters['severity_level'])) {
        $where_conditions[] = "b.severity_level = ?";
        $params[] = $filters['severity_level'];
        $types .= 's';
    }
    
    if (!empty($filters['status'])) {
        $where_conditions[] = "b.status = ?";
        $params[] = $filters['status'];
        $types .= 's';
    }
    
    if (!empty($filters['date_from'])) {
        $where_conditions[] = "DATE(b.date_entry) >= ?";
        $params[] = $filters['date_from'];
        $types .= 's';
    }
    
    if (!empty($filters['date_to'])) {
        $where_conditions[] = "DATE(b.date_entry) <= ?";
        $params[] = $filters['date_to'];
        $types .= 's';
    }
    
    if (!empty($filters['search'])) {
        $where_conditions[] = "(CONCAT(s.Fname, ' ', s.Lname, ' ', s.Mname) LIKE ? OR b.remarks LIKE ? OR b.LRN LIKE ?)";
        $search_term = '%' . $filters['search'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= 'sss';
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $sql = "SELECT 
                b.behavior_ID_PK as id,
                b.student_id,
                b.LRN,
                b.behavior_type,
                b.behavior_category,
                b.severity_level,
                b.status,
                b.follow_up_required,
                b.follow_up_notes,
                b.remarks,
                b.date_entry,
                b.updated_at,
                s.Fname,
                s.Lname,
                s.Mname,
                s.GLevel,
                s.Course,
                s.photo_path,
                bs.color_code as severity_color,
                bc.color_code as category_color,
                bc.category_type
            FROM behavior_tbl b
            LEFT JOIN students_tbl s ON b.student_id = s.id
            LEFT JOIN behavior_severity_tbl bs ON b.severity_level = bs.severity_name
            LEFT JOIN behavior_categories_tbl bc ON b.behavior_category = bc.category_name
            $where_clause
            ORDER BY b.date_entry DESC
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $records = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $records[] = $row;
            }
        }
        
        mysqli_stmt_close($stmt);
    } else {
        $result = mysqli_query($conn, $sql);
        $records = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $records[] = $row;
            }
        }
    }
    
    mysqli_close($conn);
    return $records;
}

// Function to add a new behavior record
function addBehaviorRecord($lrn, $behavior_type, $behavior_category, $severity_level, $remarks, $follow_up_required = 0, $follow_up_notes = '', $recorded_by = null)
{
    $conn = conn();
    
    // Get student information
    $student_sql = "SELECT id, Fname, Lname, Mname FROM students_tbl WHERE LRN = ?";
    $student_stmt = mysqli_prepare($conn, $student_sql);
    mysqli_stmt_bind_param($student_stmt, "s", $lrn);
    mysqli_stmt_execute($student_stmt);
    $student_result = mysqli_stmt_get_result($student_stmt);
    $student = mysqli_fetch_assoc($student_result);
    mysqli_stmt_close($student_stmt);
    
    if (!$student) {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Student with LRN ' . $lrn . ' not found.'];
    }
    
    // Validate inputs
    if (empty($lrn) || empty($behavior_type) || empty($behavior_category) || empty($severity_level)) {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Please fill in all required fields.'];
    }
    
    $sql = "INSERT INTO behavior_tbl (
                student_id, LRN, first_name, middle_name, last_name, 
                behavior_type, behavior_category, severity_level, 
                status, follow_up_required, follow_up_notes, 
                remarks, recorded_by, date_entry
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Active', ?, ?, ?, ?, NOW())";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssssssissi", 
            $student['id'], 
            $lrn, 
            $student['Fname'], 
            $student['Mname'], 
            $student['Lname'], 
            $behavior_type, 
            $behavior_category, 
            $severity_level, 
            $follow_up_required, 
            $follow_up_notes, 
            $remarks, 
            $recorded_by
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $behavior_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            
            // Check if parent notification is required
            checkAndSendNotifications($behavior_id, $severity_level);
            
            return ['success' => true, 'message' => 'Behavior record added successfully!', 'behavior_id' => $behavior_id];
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to add behavior record. Please try again.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
    }
}

// Function to update a behavior record
function updateBehaviorRecord($behavior_id, $lrn, $behavior_type, $behavior_category, $severity_level, $remarks, $follow_up_required = 0, $follow_up_notes = '', $status = 'Active')
{
    $conn = conn();
    
    // Get student information
    $student_sql = "SELECT id, Fname, Lname, Mname FROM students_tbl WHERE LRN = ?";
    $student_stmt = mysqli_prepare($conn, $student_sql);
    mysqli_stmt_bind_param($student_stmt, "s", $lrn);
    mysqli_stmt_execute($student_stmt);
    $student_result = mysqli_stmt_get_result($student_stmt);
    $student = mysqli_fetch_assoc($student_result);
    mysqli_stmt_close($student_stmt);
    
    if (!$student) {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Student with LRN ' . $lrn . ' not found.'];
    }
    
    // Validate inputs
    if (empty($behavior_id) || empty($lrn) || empty($behavior_type) || empty($behavior_category) || empty($severity_level)) {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Please fill in all required fields.'];
    }
    
    $sql = "UPDATE behavior_tbl SET 
                student_id = ?, LRN = ?, first_name = ?, middle_name = ?, last_name = ?,
                behavior_type = ?, behavior_category = ?, severity_level = ?, 
                status = ?, follow_up_required = ?, follow_up_notes = ?, 
                remarks = ?, updated_at = NOW()
            WHERE behavior_ID_PK = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issssssssissi", 
            $student['id'], 
            $lrn, 
            $student['Fname'], 
            $student['Mname'], 
            $student['Lname'], 
            $behavior_type, 
            $behavior_category, 
            $severity_level, 
            $status, 
            $follow_up_required, 
            $follow_up_notes, 
            $remarks, 
            $behavior_id
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            
            if ($affected_rows > 0) {
                return ['success' => true, 'message' => 'Behavior record updated successfully!'];
            } else {
                return ['success' => false, 'message' => 'No changes were made or record not found.'];
            }
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to update behavior record.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
    }
}

// Function to delete a behavior record
function deleteBehaviorRecord($behavior_id)
{
    $conn = conn();
    
    if (empty($behavior_id)) {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Behavior ID is required.'];
    }
    
    $sql = "DELETE FROM behavior_tbl WHERE behavior_ID_PK = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $behavior_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            
            if ($affected_rows > 0) {
                return ['success' => true, 'message' => 'Behavior record deleted successfully!'];
            } else {
                return ['success' => false, 'message' => 'Record not found or already deleted.'];
            }
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return ['success' => false, 'message' => 'Failed to delete behavior record.'];
        }
    } else {
        mysqli_close($conn);
        return ['success' => false, 'message' => 'Database preparation error.'];
    }
}

// Function to get behavior record by ID
function getBehaviorRecordById($behavior_id)
{
    $conn = conn();
    
    $sql = "SELECT 
                b.*,
                s.Fname,
                s.Lname,
                s.Mname,
                s.GLevel,
                s.Course,
                s.photo_path
            FROM behavior_tbl b
            LEFT JOIN students_tbl s ON b.student_id = s.id
            WHERE b.behavior_ID_PK = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $behavior_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $record = null;
    if ($result) {
        $record = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $record;
}

// Function to get behavior categories
function getBehaviorCategories()
{
    $conn = conn();
    
    $sql = "SELECT * FROM behavior_categories_tbl WHERE is_active = 1 ORDER BY category_type, category_name";
    $result = mysqli_query($conn, $sql);
    
    $categories = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $categories;
}

// Function to get severity levels
function getSeverityLevels()
{
    $conn = conn();
    
    $sql = "SELECT * FROM behavior_severity_tbl WHERE is_active = 1 ORDER BY severity_level";
    $result = mysqli_query($conn, $sql);
    
    $levels = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $levels[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $levels;
}

// Function to get behavior statistics for a student
function getStudentBehaviorStats($student_id)
{
    $conn = conn();
    
    $sql = "SELECT 
                COUNT(*) as total_records,
                SUM(CASE WHEN bc.category_type = 'Positive' THEN 1 ELSE 0 END) as positive_count,
                SUM(CASE WHEN bc.category_type = 'Negative' THEN 1 ELSE 0 END) as negative_count,
                SUM(CASE WHEN bc.category_type = 'Neutral' THEN 1 ELSE 0 END) as neutral_count,
                SUM(CASE WHEN b.severity_level = 'Critical' THEN 1 ELSE 0 END) as critical_count,
                SUM(CASE WHEN b.severity_level = 'High' THEN 1 ELSE 0 END) as high_count,
                SUM(CASE WHEN b.follow_up_required = 1 AND b.status = 'Active' THEN 1 ELSE 0 END) as pending_followups
            FROM behavior_tbl b
            LEFT JOIN behavior_categories_tbl bc ON b.behavior_category = bc.category_name
            WHERE b.student_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $stats = null;
    if ($result) {
        $stats = mysqli_fetch_assoc($result);
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $stats ?: [
        'total_records' => 0,
        'positive_count' => 0,
        'negative_count' => 0,
        'neutral_count' => 0,
        'critical_count' => 0,
        'high_count' => 0,
        'pending_followups' => 0
    ];
}

// Function to check and send notifications
function checkAndSendNotifications($behavior_id, $severity_level)
{
    $conn = conn();
    
    // Get severity settings
    $sql = "SELECT requires_parent_notification, requires_admin_notification 
            FROM behavior_severity_tbl 
            WHERE severity_name = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $severity_level);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $severity_settings = mysqli_fetch_assoc($result);
    
    if ($severity_settings) {
        // Here you would implement actual notification logic
        // For now, we'll just log the requirement
        if ($severity_settings['requires_parent_notification']) {
            // TODO: Implement parent notification
            error_log("Parent notification required for behavior record ID: $behavior_id");
        }
        
        if ($severity_settings['requires_admin_notification']) {
            // TODO: Implement admin notification
            error_log("Admin notification required for behavior record ID: $behavior_id");
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

// Function to get behavior summary report
function getBehaviorSummaryReport($filters = [])
{
    $conn = conn();
    
    $where_conditions = [];
    $params = [];
    $types = '';
    
    // Build WHERE clause based on filters
    if (!empty($filters['date_from'])) {
        $where_conditions[] = "DATE(b.date_entry) >= ?";
        $params[] = $filters['date_from'];
        $types .= 's';
    }
    
    if (!empty($filters['date_to'])) {
        $where_conditions[] = "DATE(b.date_entry) <= ?";
        $params[] = $filters['date_to'];
        $types .= 's';
    }
    
    if (!empty($filters['grade_level'])) {
        $where_conditions[] = "s.GLevel = ?";
        $params[] = $filters['grade_level'];
        $types .= 's';
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    $sql = "SELECT 
                bc.category_type,
                b.behavior_category,
                b.severity_level,
                COUNT(*) as record_count,
                COUNT(DISTINCT b.student_id) as student_count
            FROM behavior_tbl b
            LEFT JOIN students_tbl s ON b.student_id = s.id
            LEFT JOIN behavior_categories_tbl bc ON b.behavior_category = bc.category_name
            $where_clause
            GROUP BY bc.category_type, b.behavior_category, b.severity_level
            ORDER BY bc.category_type, b.behavior_category, b.severity_level";
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    } else {
        $result = mysqli_query($conn, $sql);
    }
    
    $summary = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $summary[] = $row;
        }
    }
    
    if ($stmt) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return $summary;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'add_behavior':
            $result = addBehaviorRecord(
                $_POST['behaviorLRN'],
                $_POST['behaviorType'], 
                $_POST['behaviorCategory'] ?? 'General Observation',
                $_POST['severityLevel'] ?? 'Low',
                $_POST['behaviorRemarks'] ?? '',
                $_POST['followUpRequired'] ?? 0,
                $_POST['followUpNotes'] ?? ''
            );
            echo json_encode($result);
            break;
            
        case 'update_behavior':
            $result = updateBehaviorRecord(
                $_POST['behaviorId'],
                $_POST['behaviorLRN'],
                $_POST['behaviorType'], 
                $_POST['behaviorCategory'] ?? 'General Observation',
                $_POST['severityLevel'] ?? 'Low',
                $_POST['behaviorRemarks'] ?? '',
                $_POST['followUpRequired'] ?? 0,
                $_POST['followUpNotes'] ?? '',
                $_POST['status'] ?? 'Active'
            );
            echo json_encode($result);
            break;
            
        case 'delete_behavior':
            $result = deleteBehaviorRecord($_POST['behaviorId']);
            echo json_encode($result);
            break;
            
        case 'get_behavior':
            $behavior = getBehaviorRecordById($_POST['behaviorId']);
            echo json_encode(['success' => true, 'data' => $behavior]);
            break;
            
        case 'get_categories':
            $categories = getBehaviorCategories();
            echo json_encode(['success' => true, 'data' => $categories]);
            break;
            
        case 'get_severity_levels':
            $levels = getSeverityLevels();
            echo json_encode(['success' => true, 'data' => $levels]);
            break;
            
        case 'get_student_stats':
            $stats = getStudentBehaviorStats($_POST['student_id']);
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'get_records':
            $filters = [
                'student_id' => $_GET['student_id'] ?? '',
                'lrn' => $_GET['lrn'] ?? '',
                'behavior_type' => $_GET['behavior_type'] ?? '',
                'behavior_category' => $_GET['behavior_category'] ?? '',
                'severity_level' => $_GET['severity_level'] ?? '',
                'status' => $_GET['status'] ?? '',
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'search' => $_GET['search'] ?? ''
            ];
            $limit = $_GET['limit'] ?? 50;
            $offset = $_GET['offset'] ?? 0;
            
            $records = getBehaviorRecords($limit, $offset, $filters);
            echo json_encode(['success' => true, 'data' => $records]);
            break;
            
        case 'summary_report':
            $filters = [
                'date_from' => $_GET['date_from'] ?? '',
                'date_to' => $_GET['date_to'] ?? '',
                'grade_level' => $_GET['grade_level'] ?? ''
            ];
            $summary = getBehaviorSummaryReport($filters);
            echo json_encode(['success' => true, 'data' => $summary]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}
?>