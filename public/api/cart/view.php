<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';
require_once '../../config/auth.php';
require_once '../../config/cart_helper.php';

$user_id = get_authenticated_user();
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $cart_data = get_cart_data($conn, $user_id);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $cart_data]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
