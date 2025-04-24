<?php
require_once 'C:\xampp\htdocs\WEB_ZITONOULIS_DIMITRIOS_E22054\AIR_DS_WEBSITE\server\database\db_utils\db_connect.php';

$conn = db_connect();
// get all the codes of the airports in the database
$stmt = $conn->prepare("SELECT name, code FROM airports");
$stmt->execute();
$result = $stmt->fetchall(PDO::FETCH_ASSOC);
// make sure that the return object is treated as json and not text
header('Content-Type: application/json');
echo json_encode($result);
?>