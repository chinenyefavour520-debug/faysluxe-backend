<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : null;

if (!$slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product slug is required']);
    exit();
}

$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.slug = :slug";

$stmt = $conn->prepare($query);
$stmt->bindParam(':slug', $slug);
$stmt->execute();

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    // Fetch images
    $img_query = "SELECT image_url, is_primary FROM product_images WHERE product_id = :id";
    $img_stmt = $conn->prepare($img_query);
    $img_stmt->bindParam(':id', $product['id']);
    $img_stmt->execute();
    $product['images'] = $img_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => ['product' => $product]]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found']);
}
?>
