<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : null;

if (!$slug) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Slug is required']);
    exit();
}

$query = "SELECT * FROM categories WHERE slug = :slug";
$stmt = $conn->prepare($query);
$stmt->bindParam(':slug', $slug);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if ($category) {
    echo json_encode([
        'success' => true,
        'data' => [
            'category' => $category,
            'subcategories' => [] // Mocking empty subcategories
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Category not found']);
}
?>
