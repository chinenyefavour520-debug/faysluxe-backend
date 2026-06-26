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

$data = json_decode(file_get_contents("php://input"), true);
$cart_item_id = isset($data['cart_item_id']) ? (int)$data['cart_item_id'] : null;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : null;

if (!$cart_item_id || $quantity === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cart item ID and quantity are required']);
    exit();
}

try {
    $cart_data = get_cart_data($conn, $user_id);
    $cart_id = $cart_data['id'];

    if ($quantity > 0) {
        $update = $conn->prepare("UPDATE cart_items SET quantity = :qty WHERE id = :id AND cart_id = :cart_id");
        $update->bindParam(':qty', $quantity);
        $update->bindParam(':id', $cart_item_id);
        $update->bindParam(':cart_id', $cart_id);
        $update->execute();
    } else {
        $delete = $conn->prepare("DELETE FROM cart_items WHERE id = :id AND cart_id = :cart_id");
        $delete->bindParam(':id', $cart_item_id);
        $delete->bindParam(':cart_id', $cart_id);
        $delete->execute();
    }

    $updated_cart = get_cart_data($conn, $user_id);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $updated_cart]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
