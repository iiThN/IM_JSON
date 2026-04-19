<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require 'config.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
$requiredFields = ['city_name', 'department_name', 'id', 'first_name', 'last_name', 'hire_date', 'job_title', 'salary'];
foreach ($requiredFields as $field) {
  if (empty($data[$field])) {
    echo json_encode([
      "success" => false,
      "message" => ucfirst(str_replace('_', ' ', $field)) . " is required"
    ]);
    exit();
  }
}

// pagcreate sa employee details nga JSON object
$employeeDetails = [
  "id" => (int)$data['id'],
  "first_name" => $data['first_name'],
  "last_name" => $data['last_name'],
  "hire_date" => $data['hire_date'],
  "job_title" => $data['job_title'],
  "salary" => (float)$data['salary']
];

// Prepare the data to send to Supabase
$insertData = [
  "city_name" => $data['city_name'],
  "department_name" => $data['department_name'],
  "employee_details" => $employeeDetails
];

// Make API call to Supabase
$url = SUPABASE_URL . '/rest/v1/' . TABLE_NAME;

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'apikey: ' . SUPABASE_ANON_KEY,
  'Authorization: Bearer ' . SUPABASE_ANON_KEY,
  'Prefer: return=representation'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([$insertData]));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
  echo json_encode([
    "success" => false,
    "message" => "CURL Error: " . $error
  ]);
  exit;
}

if ($httpCode === 201) {
  echo json_encode([
    "success" => true,
    "message" => "Data saved successfully"
  ]);
} else {
  echo json_encode([
    "success" => false,
    "message" => "Error saving data",
    "http_code" => $httpCode,
    "error" => $response
  ]);
}
?>