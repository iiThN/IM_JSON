<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

require 'config.php';

// Make API call to Supabase to fetch all records
$url = SUPABASE_URL . '/rest/v1/' . TABLE_NAME . '?order=created_at.desc';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'apikey: ' . SUPABASE_ANON_KEY,
  'Authorization: Bearer ' . SUPABASE_ANON_KEY
]);

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

if ($httpCode === 200) {
  $data = json_decode($response, true);
  echo json_encode([
    "success" => true,
    "data" => $data
  ]);
} else {
  echo json_encode([
    "success" => false,
    "message" => "Error fetching data",
    "http_code" => $httpCode,
    "error" => $response
  ]);
}
?>