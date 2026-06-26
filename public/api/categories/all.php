<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$query = "SELECT * FROM categories ORDER BY name ASC";
$stmt = $conn->prepare($query);
$stmt->execute();

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => ['categories' => $categories]]);
?>
