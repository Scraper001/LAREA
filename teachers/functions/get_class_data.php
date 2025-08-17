<?php
// Mock data functions for demonstration (no database connection needed)
function get_class_data($teacher_id = null) {
    // Mock data for demonstration (in production, this would connect to database)
    $data = [];
    
    // Get all students (mock data)
    $data['students'] = [
        [
            'id' => 1,
            'fname' => 'John',
            'lname' => 'Smith',
            'mname' => 'A',
            'grade_level' => '10',
            'course' => 'General Academic Strand',
            'LRN' => '123456789012',
            'last_attendance_status' => 'Present',
            'last_attendance_date' => '2025-08-17',
            'behavior_records_count' => 2
        ],
        [
            'id' => 2,
            'fname' => 'Jane',
            'lname' => 'Doe',
            'mname' => 'B',
            'grade_level' => '9',
            'course' => 'N/A',
            'LRN' => '123456789013',
            'last_attendance_status' => 'Absent',
            'last_attendance_date' => '2025-08-17',
            'behavior_records_count' => 0
        ],
        [
            'id' => 3,
            'fname' => 'Mike',
            'lname' => 'Johnson',
            'mname' => 'C',
            'grade_level' => '11',
            'course' => 'STEM',
            'LRN' => '123456789014',
            'last_attendance_status' => 'Present',
            'last_attendance_date' => '2025-08-17',
            'behavior_records_count' => 1
        ]
    ];
    
    // Get upcoming deadlines (mock data)
    $data['upcoming_deadlines'] = [
        [
            'title' => 'Math Quiz 3',
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'class' => 'Grade 10 Mathematics',
            'status' => 'pending'
        ],
        [
            'title' => 'Science Project Submission',
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'class' => 'Grade 9 Science',
            'status' => 'pending'
        ],
        [
            'title' => 'English Essay',
            'due_date' => date('Y-m-d', strtotime('+10 days')),
            'class' => 'Grade 11 English',
            'status' => 'pending'
        ]
    ];
    
    // Get class attendance summary by grade level (mock data)
    $data['class_attendance'] = [
        [
            'grade_level' => '9',
            'total_students' => 25,
            'present_today' => 22,
            'absent_today' => 3
        ],
        [
            'grade_level' => '10',
            'total_students' => 28,
            'present_today' => 26,
            'absent_today' => 2
        ],
        [
            'grade_level' => '11',
            'total_students' => 30,
            'present_today' => 28,
            'absent_today' => 2
        ]
    ];
    
    return $data;
}
?>