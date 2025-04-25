<?php
require_once __DIR__ . "/../../../config/config.php";
require_once BASE_PATH . "./server/database/db_utils/db_connect.php";

$conn = db_connect();
// get all the codes of the airports in the database
$stmt = $conn->prepare("SELECT name, code FROM airports");
$stmt->execute();
$result = $stmt->fetchall(PDO::FETCH_ASSOC);
// make sure that the return object is treated as json and not text
header('Content-Type: application/json');
echo json_encode($result);
?>