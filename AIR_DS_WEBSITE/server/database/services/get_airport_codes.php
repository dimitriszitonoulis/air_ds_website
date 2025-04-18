<?php
require_once 'C:\xampp\htdocs\WEB_ZITONOULIS_DIMITRIOS_E22054\AIR_DS_WEBSITE\server\database\db_utils\db_connect.php';


$conn = db_connect();
// get all the codes of the airports in the database
$stmt = $conn->prepare("SELECT code FROM airports");
$stmt->execute();
//Only one row is return so it does not matter if FETCH_NUM is used
$result = $stmt->fetchall(PDO::FETCH_NUM);

echo json_encode($result);
?>