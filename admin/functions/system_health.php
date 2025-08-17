<?php
// Mock data functions for demonstration (no database connection needed)
function get_system_health() {
    // Mock data for demonstration (in production, this would check actual system status)
    $health = [];
    
    // Database connection status
    $health['database_status'] = 'Connected';
    
    // Check if critical tables exist
    $health['table_status'] = [
        'tbl_user' => 'OK',
        'students_tbl' => 'OK', 
        'attendance' => 'OK',
        'anecdotal_records_tbl' => 'OK'
    ];
    
    // Check uploads directory
    $health['uploads_directory'] = 'Writable';
    
    // Get database size
    $health['database_size'] = '15.3';
    
    // Recent backup status
    $health['last_backup'] = 'August 16, 2025';
    
    // System storage check
    $health['storage_status'] = 'OK';
    
    return $health;
}
?>