<?php
// Test database connection and run migration
include 'connection/conn.php';

try {
    $conn = conn();
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    echo "Connected to database successfully\n";
    
    // Read the migration script
    $sql_file = 'database_updates/behavior_table_enhancement.sql';
    if (!file_exists($sql_file)) {
        die("Migration file not found: $sql_file\n");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split into individual statements
    $statements = array_filter(
        array_map('trim', explode(';', $sql_content)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^\s*--/', $stmt);
        }
    );
    
    echo "Found " . count($statements) . " SQL statements to execute\n";
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (trim($statement)) {
            if (mysqli_query($conn, $statement)) {
                $success_count++;
                echo "✓ Executed successfully\n";
            } else {
                $error_count++;
                echo "✗ Error: " . mysqli_error($conn) . "\n";
                echo "Statement: " . substr($statement, 0, 100) . "...\n";
            }
        }
    }
    
    echo "\nMigration complete:\n";
    echo "- Success: $success_count statements\n";
    echo "- Errors: $error_count statements\n";
    
    mysqli_close($conn);
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>