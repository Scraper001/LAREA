<?php
// Mock data functions for demonstration (no database connection needed)
function get_teacher_stats($teacher_id = null) {
    // Mock data for demonstration (in production, this would connect to database)
    $stats = [];
    
    // Today's attendance summary
    $stats['present_today'] = 28;
    $stats['absent_today'] = 4;
    $stats['total_students_today'] = 32;
    
    // Total assigned students 
    $stats['total_assigned_students'] = 95;
    
    // Recent behavior reports (last 7 days)
    $stats['recent_behavior_reports'] = 12;
    
    // Pending follow-ups
    $stats['pending_followups'] = 3;
    
    // Weekly attendance for chart
    $stats['weekly_attendance'] = [
        ['day_name' => 'Monday', 'present' => 30, 'absent' => 2],
        ['day_name' => 'Tuesday', 'present' => 28, 'absent' => 4],
        ['day_name' => 'Wednesday', 'present' => 31, 'absent' => 1],
        ['day_name' => 'Thursday', 'present' => 29, 'absent' => 3],
        ['day_name' => 'Friday', 'present' => 32, 'absent' => 0]
    ];
    
    // Recent student activity
    $stats['recent_activity'] = [
        [
            'observation_title' => 'Excellent Math performance',
            'date_recorded' => '2025-08-17 10:30:00',
            'severity_level' => 'Low',
            'fname' => 'Ana',
            'lname' => 'Garcia',
            'grade_level' => '10'
        ],
        [
            'observation_title' => 'Disruptive behavior in class',
            'date_recorded' => '2025-08-17 09:15:00',
            'severity_level' => 'Medium',
            'fname' => 'Mark',
            'lname' => 'Johnson',
            'grade_level' => '9'
        ],
        [
            'observation_title' => 'Outstanding group work leadership',
            'date_recorded' => '2025-08-16 14:00:00',
            'severity_level' => 'Low',
            'fname' => 'Sarah',
            'lname' => 'Williams',
            'grade_level' => '11'
        ]
    ];
    
    // Class performance overview (behavior severity distribution)
    $stats['behavior_severity'] = [
        'Low' => 15,
        'Medium' => 8,
        'High' => 2
    ];
    
    // Students needing attention 
    $stats['students_needing_attention'] = [
        [
            'id' => 1,
            'fname' => 'Michael',
            'lname' => 'Brown',
            'grade_level' => '10',
            'incident_count' => 3,
            'last_incident' => '2025-08-15 11:30:00'
        ],
        [
            'id' => 2,
            'fname' => 'Emma',
            'lname' => 'Davis',
            'grade_level' => '9',
            'incident_count' => 2,
            'last_incident' => '2025-08-14 13:45:00'
        ]
    ];
    
    return $stats;
}
?>