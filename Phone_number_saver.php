<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

//Imported the To convert the number in +91XXXXXXXXXX format
require_once __DIR__ . '/normalizePhoneNumber.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

//Database Connection starts from here
$db_conn = new mysqli('localhost', 'root', '', 'databasename');

if ($db_conn->connect_error) {
    http_response_code(500);
    echo json_encode(["Error" => "Database connection failed: " . $db_conn->connect_error]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        http_response_code(400);
        echo json_encode(["Error" => "Invalid input data"]);
        exit();
    }

    $Name = trim($input['Name'] ?? '');
    $Phone = trim($input['Phone'] ?? '');
    $Company = trim($input['Company'] ?? '');
    $Email = filter_var($input['Email'] ?? '', FILTER_SANITIZE_EMAIL);
    $Address = trim($input['Address'] ?? '');
    $Purpose = trim($input['Purpose'] ?? '');
    $Category = trim($input['Category'] ?? '');

//Normalizing the contact number into +91XXXXXXXXXX format
    $normalized_contact_no = normalizePhoneNumber($Phone);
    if ($normalized_contact_no === null) {
        http_response_code(400);
        echo json_encode(["Error" => "Invalid phone number format"]);
        exit();
    }

    $stmt = $db_conn->prepare("INSERT INTO contactDairy (Name, Phone, Company, Email, Address, Purpose, Category) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["Error" => "Prepare failed: " . $db_conn->error]);
        exit();
    }

    $stmt->bind_param("sssssss", $Name, $normalized_contact_no, $Company, $Email, $Address, $Purpose, $Category);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["Success" => "Data Added Successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["Error" => "Failed to add data: " . $stmt->error]);
    }

    $stmt->close();
}

$db_conn->close();
?>
