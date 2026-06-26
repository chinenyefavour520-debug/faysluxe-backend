<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : null;

if (!$slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Slug is required']);
    exit();
}

$query = "SELECT p.*, c.name as category_name, 
          (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as image_url,
          (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as primary_image
          FROM products p 
          JOIN categories c ON p.category_id = c.id 
          WHERE c.slug = :slug";

$stmt = $conn->prepare($query);
$stmt->bindParam(':slug', $slug);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => [
        'products' => $products,
        'pagination' => ['current_page' => 1, 'total_pages' => 1]
    ]
]);
?>
