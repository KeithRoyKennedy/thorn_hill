<?php
/**
 * Staff Data Web Service
 * 
 * This service receives staff data in tab-separated format and updates the database.
 */
require_once 'config.php';

// Set content type to JSON for response
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Only POST method is allowed']);
    exit;
}

// Check if data is provided
if (!isset($_POST['data']) || empty($_POST['data'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'No data provided']);
    exit;
}

// Get the raw data
$rawData = $_POST['data'];

// Process the data
try {
    $result = processStaffData($rawData);
    echo json_encode(['status' => 'success', 'message' => 'Data successfully processed', 'details' => $result]);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Error processing data: ' . $e->getMessage()]);
}

/**
 * Process the staff data
 * 
 * @param string $rawData Tab-separated data with newlines between records
 * @return array Processing results
 */
function processStaffData($rawData) {
    // Connect to database
    $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Start transaction
    $conn->beginTransaction();
    
    try {
        // Create a new batch
        $batchId = createNewBatch($conn);
        
        // Archive current data
        archiveCurrentData($conn, $batchId);
        
        // Parse and insert the new data
        $lines = explode("\n", trim($rawData));
        $staffCount = 0;
        $departmentCount = 0;
        
        foreach ($lines as $line) {
            $fields = explode("\t", trim($line));
            
            // Check if we have the correct number of fields
            if (count($fields) != 7) {
                continue; // Skip invalid lines
            }
            
            // Extract fields
            $firstName = trim($fields[0]);
            $surname = trim($fields[1]);
            $email = trim($fields[2]);
            $gender = strtolower(trim($fields[3]));
            $departmentName = trim($fields[4]);
            $contactName = trim($fields[5]);
            $contactEmail = trim($fields[6]);
            
            // Basic validation
            if (empty($firstName) || empty($surname) || empty($email) || 
                empty($gender) || empty($departmentName) || 
                empty($contactName) || empty($contactEmail)) {
                continue; // Skip invalid lines
            }
            
            if ($gender != 'm' && $gender != 'f') {
                $gender = 'm'; // Default to 'm' if invalid
            }
            
            // Get or create department
            $departmentId = getOrCreateDepartment($conn, $departmentName, $contactName, $contactEmail);
            if ($departmentId === 'created') {
                $departmentCount++;
                $departmentId = getOrCreateDepartment($conn, $departmentName, $contactName, $contactEmail);
            }
            
            // Insert staff member
            insertStaffMember($conn, $firstName, $surname, $email, $gender, $departmentId, $batchId);
            $staffCount++;
        }
        
        // Commit transaction
        $conn->commit();
        
        return [
            'batch_id' => $batchId,
            'staff_processed' => $staffCount,
            'departments_created' => $departmentCount
        ];
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        throw $e;
    }
}

/**
 * Create a new batch and mark previous batches as not current
 * 
 * @param PDO $conn Database connection
 * @return int New batch ID
 */
function createNewBatch($conn) {
    // Mark all existing batches as not current
    $stmt = $conn->prepare("UPDATE batches SET is_current = FALSE");
    $stmt->execute();
    
    // Create new batch
    $stmt = $conn->prepare("INSERT INTO batches (is_current) VALUES (TRUE)");
    $stmt->execute();
    
    return $conn->lastInsertId();
}

/**
 * Archive current staff data
 * 
 * @param PDO $conn Database connection
 * @param int $batchId Current batch ID
 */
function archiveCurrentData($conn, $batchId) {
    // First, archive all current staff data
    $stmt = $conn->prepare("
        INSERT INTO staff_archive (staff_id, first_name, surname, email, gender, department_id, batch_id)
        SELECT staff_id, first_name, surname, email, gender, department_id, batch_id
        FROM staff
        WHERE is_current = TRUE
    ");
    $stmt->execute();
    
    // Then delete all current staff records from the staff table
    // This completely removes old records instead of just marking them as not current
    $stmt = $conn->prepare("DELETE FROM staff WHERE is_current = TRUE");
    $stmt->execute();
}

/**
 * Get or create a department
 * 
 * @param PDO $conn Database connection
 * @param string $name Department name
 * @param string $contactName Contact person name
 * @param string $contactEmail Contact person email
 * @return mixed Department ID or 'created' if new
 */
function getOrCreateDepartment($conn, $name, $contactName, $contactEmail) {
    // Check if department exists
    $stmt = $conn->prepare("SELECT department_id FROM departments WHERE name = :name");
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Update contact details if needed
        $stmt = $conn->prepare("
            UPDATE departments 
            SET contact_person_name = :contact_name, contact_person_email = :contact_email 
            WHERE department_id = :department_id
        ");
        $stmt->bindParam(':contact_name', $contactName);
        $stmt->bindParam(':contact_email', $contactEmail);
        $stmt->bindParam(':department_id', $row['department_id']);
        $stmt->execute();
        
        return $row['department_id'];
    } else {
        // Create new department
        $stmt = $conn->prepare("
            INSERT INTO departments (name, contact_person_name, contact_person_email)
            VALUES (:name, :contact_name, :contact_email)
        ");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':contact_name', $contactName);
        $stmt->bindParam(':contact_email', $contactEmail);
        $stmt->execute();
        
        return 'created';
    }
}

/**
 * Insert a staff member
 * 
 * @param PDO $conn Database connection
 * @param string $firstName First name
 * @param string $surname Surname
 * @param string $email Email
 * @param string $gender Gender ('m' or 'f')
 * @param int $departmentId Department ID
 * @param int $batchId Batch ID
 */
function insertStaffMember($conn, $firstName, $surname, $email, $gender, $departmentId, $batchId) {
    $stmt = $conn->prepare("
        INSERT INTO staff (first_name, surname, email, gender, department_id, batch_id, is_current)
        VALUES (:first_name, :surname, :email, :gender, :department_id, :batch_id, TRUE)
    ");
    
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':surname', $surname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':department_id', $departmentId);
    $stmt->bindParam(':batch_id', $batchId);
    
    $stmt->execute();
}
