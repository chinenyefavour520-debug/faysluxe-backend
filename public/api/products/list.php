<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$featured = isset($_GET['featured']) ? filter_var($_GET['featured'], FILTER_VALIDATE_BOOLEAN) : null;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;

$query = "SELECT p.*, c.name as category_name, 
          (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as image_url 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id";

if ($featured !== null) {
    $query .= $featured ? " WHERE p.featured = TRUE" : " WHERE p.featured = FALSE";
}

$query .= " ORDER BY p.created_at DESC";

if ($limit !== null && $limit > 0) {
    $query .= " LIMIT " . $limit;
}

$stmt = $conn->prepare($query);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode(['success' => true, 'data' => ['products' => $products]]);
?>
