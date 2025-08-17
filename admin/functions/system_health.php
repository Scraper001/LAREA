<?php
include "../../connection/conn.php";

function get_system_health() {
    $conn = conn();
    $health = [];
    
    // Database connection status
    $health['database_status'] = mysqli_ping($conn) ? 'Connected' : 'Disconnected';
    
    // Check if critical tables exist
    $critical_tables = ['tbl_user', 'students_tbl', 'attendance', 'anecdotal_records_tbl'];
    $health['table_status'] = [];
    
    foreach ($critical_tables as $table) {
        $sql = "SHOW TABLES LIKE '$table'";
        $result = mysqli_query($conn, $sql);
        $health['table_status'][$table] = mysqli_num_rows($result) > 0 ? 'OK' : 'Missing';
    }
    
    // Check uploads directory
    $uploads_dir = '../../uploads';
    $health['uploads_directory'] = is_dir($uploads_dir) && is_writable($uploads_dir) ? 'Writable' : 'Not Accessible';
    
    // Get database size
    $sql = "SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'db_size_mb'
            FROM information_schema.tables 
            WHERE table_schema = DATABASE()";
    $result = mysqli_query($conn, $sql);
    $health['database_size'] = mysqli_fetch_assoc($result)['db_size_mb'] ?? 0;
    
    // Recent backup status (simulated - would need actual backup implementation)
    $health['last_backup'] = 'Not Configured';
    
    // System storage check (simplified)
    $health['storage_status'] = disk_free_bytes('.') > 100000000 ? 'OK' : 'Low Space';
    
    mysqli_close($conn);
    return $health;
}
?>