<?php
// Mock data functions for demonstration (no database connection needed)
function get_admin_stats() {
    // Mock data for demonstration (in production, this would connect to database)
    $stats = [];
    
    // Total users count
    $stats['total_users'] = 15;
    
    // Total students count  
    $stats['total_students'] = 120;
    
    // Today's attendance - Present
    $stats['present_today'] = 85;
    
    // Today's attendance - Absent  
    $stats['absent_today'] = 12;
    
    // Total behavior records
    $stats['total_behavior_records'] = 45;
    
    // Active behavior issues (follow-up required)
    $stats['active_issues'] = 8;
    
    // Users by role
    $stats['users_by_role'] = [
        'Admin' => 2,
        'Teacher' => 8,
        'Student' => 5
    ];
    
    // Recent activity (mock data)
    $stats['recent_activity'] = [
        [
            'observation_title' => 'Excellent participation in Math class',
            'date_recorded' => '2025-08-17 09:30:00',
            'fname' => 'Juan',
            'lname' => 'Dela Cruz'
        ],
        [
            'observation_title' => 'Late arrival to Science class',
            'date_recorded' => '2025-08-17 08:15:00',
            'fname' => 'Maria',
            'lname' => 'Santos'
        ],
        [
            'observation_title' => 'Outstanding leadership in group project',
            'date_recorded' => '2025-08-16 14:45:00',
            'fname' => 'Jose',
            'lname' => 'Rizal'
        ]
    ];
    
    // Weekly attendance trend (mock data)
    $stats['weekly_attendance'] = [
        ['day_name' => 'Monday', 'present' => 95, 'absent' => 8],
        ['day_name' => 'Tuesday', 'present' => 88, 'absent' => 15],
        ['day_name' => 'Wednesday', 'present' => 92, 'absent' => 11],
        ['day_name' => 'Thursday', 'present' => 85, 'absent' => 18],
        ['day_name' => 'Friday', 'present' => 98, 'absent' => 5]
    ];
    
    return $stats;
}
?>