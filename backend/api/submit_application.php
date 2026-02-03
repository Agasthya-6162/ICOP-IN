<?php
/**
 * Admission Application Submission Handler
 * Handles form data and file uploads from apply-online-enhanced.html
 */

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSON(['success' => false, 'message' => 'Invalid request method'], 405);
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Generate unique application number
    $applicationNo = generateReferenceNumber('ICOP');
    
    // Create applications table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS admission_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        application_no VARCHAR(50) UNIQUE NOT NULL,
        course VARCHAR(50),
        full_name VARCHAR(255),
        dob DATE,
        gender VARCHAR(20),
        nationality VARCHAR(50),
        religion VARCHAR(50),
        category VARCHAR(20),
        blood_group VARCHAR(10),
        aadhaar VARCHAR(20),
        abc_id VARCHAR(50),
        mobile VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        city VARCHAR(100),
        district VARCHAR(100),
        state VARCHAR(100),
        pincode VARCHAR(10),
        father_name VARCHAR(255),
        father_occupation VARCHAR(100),
        father_income VARCHAR(50),
        father_mobile VARCHAR(20),
        father_email VARCHAR(100),
        mother_name VARCHAR(255),
        mother_occupation VARCHAR(100),
        mother_mobile VARCHAR(20),
        guardian_name VARCHAR(255),
        ssc_board VARCHAR(255),
        ssc_year YEAR,
        ssc_percentage DECIMAL(5,2),
        ssc_seat_no VARCHAR(50),
        hsc_stream VARCHAR(50),
        hsc_year YEAR,
        hsc_percentage DECIMAL(5,2),
        pcb_marks DECIMAL(5,2),
        entrance_exam VARCHAR(50),
        entrance_score DECIMAL(10,2),
        entrance_roll_no VARCHAR(50),
        entrance_rank INT,
        photo_path VARCHAR(255),
        signature_path VARCHAR(255),
        aadhaar_doc_path VARCHAR(255),
        ssc_doc_path VARCHAR(255),
        hsc_doc_path VARCHAR(255),
        lc_doc_path VARCHAR(255),
        status VARCHAR(20) DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Sanitize all POST data
    $data = [];
    foreach ($_POST as $key => $value) {
        $data[$key] = sanitize($value);
    }
    
    // Handle file uploads
    $uploadedFiles = [];
    
    // Photo upload
    if (isset($_FILES['photo'])) {
        $result = uploadFile($_FILES['photo'], APPLICATION_DIR, ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
        if ($result['success']) {
            $uploadedFiles['photo'] = 'uploads/applications/' . $result['filename'];
        } else {
            sendJSON(['success' => false, 'message' => 'Photo upload failed: ' . $result['message']], 400);
        }
    }
    
    // Signature upload
    if (isset($_FILES['signature'])) {
        $result = uploadFile($_FILES['signature'], APPLICATION_DIR, ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
        if ($result['success']) {
            $uploadedFiles['signature'] = 'uploads/applications/' . $result['filename'];
        } else {
            sendJSON(['success' => false, 'message' => 'Signature upload failed: ' . $result['message']], 400);
        }
    }
    
    // Aadhaar document
    if (isset($_FILES['aadhaarDoc'])) {
        $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
        $result = uploadFile($_FILES['aadhaarDoc'], APPLICATION_DIR, $allowedTypes, MAX_PDF_SIZE);
        if ($result['success']) {
            $uploadedFiles['aadhaarDoc'] = 'uploads/applications/' . $result['filename'];
        }
    }
    
    // SSC document
    if (isset($_FILES['sscDoc'])) {
        $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
        $result = uploadFile($_FILES['sscDoc'], APPLICATION_DIR, $allowedTypes, MAX_PDF_SIZE);
        if ($result['success']) {
            $uploadedFiles['sscDoc'] = 'uploads/applications/' . $result['filename'];
        }
    }
    
    // HSC document
    if (isset($_FILES['hscDoc'])) {
        $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
        $result = uploadFile($_FILES['hscDoc'], APPLICATION_DIR, $allowedTypes, MAX_PDF_SIZE);
        if ($result['success']) {
            $uploadedFiles['hscDoc'] = 'uploads/applications/' . $result['filename'];
        }
    }
    
    // LC document
    if (isset($_FILES['lcDoc'])) {
        $allowedTypes = array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES);
        $result = uploadFile($_FILES['lcDoc'], APPLICATION_DIR, $allowedTypes, MAX_PDF_SIZE);
        if ($result['success']) {
            $uploadedFiles['lcDoc'] = 'uploads/applications/' . $result['filename'];
        }
    }
    
    // Insert into database
    $stmt = $db->prepare("
        INSERT INTO admission_applications (
            application_no, course, full_name, dob, gender, nationality, religion,
            category, blood_group, aadhaar, abc_id, mobile, email, address,
            city, district, state, pincode, father_name, father_occupation,
            father_income, father_mobile, father_email, mother_name,
            mother_occupation, mother_mobile, guardian_name, ssc_board,
            ssc_year, ssc_percentage, ssc_seat_no, hsc_stream, hsc_year,
            hsc_percentage, pcb_marks, entrance_exam, entrance_score,
            entrance_roll_no, entrance_rank, photo_path, signature_path,
            aadhaar_doc_path, ssc_doc_path, hsc_doc_path, lc_doc_path, status
        ) VALUES (
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, 'Pending'
        )
    ");
    
    $stmt->execute([
        $applicationNo,
        $data['course'] ?? '',
        $data['fullName'] ?? '',
        $data['dob'] ?? null,
        $data['gender'] ?? '',
        $data['nationality'] ?? 'Indian',
        $data['religion'] ?? '',
        $data['category'] ?? '',
        $data['bloodGroup'] ?? '',
        $data['aadhaar'] ?? '',
        $data['abcId'] ?? '',
        $data['mobile'] ?? '',
        $data['email'] ?? '',
        $data['address'] ?? '',
        $data['city'] ?? '',
        $data['district'] ?? '',
        $data['state'] ?? '',
        $data['pincode'] ?? '',
        $data['fatherName'] ?? '',
        $data['fatherOccupation'] ?? '',
        $data['fatherIncome'] ?? '',
        $data['fatherMobile'] ?? '',
        $data['fatherEmail'] ?? '',
        $data['motherName'] ?? '',
        $data['motherOccupation'] ?? '',
        $data['motherMobile'] ?? '',
        $data['guardianName'] ?? '',
        $data['sscBoard'] ?? '',
        $data['sscYear'] ?? null,
        $data['sscPercentage'] ?? null,
        $data['sscSeatNo'] ?? '',
        $data['hscStream'] ?? '',
        $data['hscYear'] ?? null,
        $data['hscPercentage'] ?? null,
        $data['pcbMarks'] ?? null,
        $data['entranceExam'] ?? '',
        $data['entranceScore'] ?? null,
        $data['entranceRollNo'] ?? '',
        $data['entranceRank'] ?? null,
        $uploadedFiles['photo'] ?? null,
        $uploadedFiles['signature'] ?? null,
        $uploadedFiles['aadhaarDoc'] ?? null,
        $uploadedFiles['sscDoc'] ?? null,
        $uploadedFiles['hscDoc'] ?? null,
        $uploadedFiles['lcDoc'] ?? null
    ]);
    
    // Send success response
    sendJSON([
        'success' => true,
        'message' => 'Application submitted successfully',
        'application_no' => $applicationNo,
        'data' => [
            'application_no' => $applicationNo,
            'name' => $data['fullName'] ?? '',
            'email' => $data['email'] ?? '',
            'course' => $data['course'] ?? ''
        ]
    ]);
    
} catch(PDOException $e) {
    sendJSON([
        'success' => false,
        'message' => DEBUG_MODE ? $e->getMessage() : 'Error submitting application'
    ], 500);
}
?>
