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
$product_id = isset($data['product_id']) ? (int)$data['product_id'] : null;
$variant_id = isset($data['variant_id']) ? (int)$data['variant_id'] : null;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

if (!$product_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Product ID is required']);
    exit();
}

try {
    $cart_data = get_cart_data($conn, $user_id);
    $cart_id = $cart_data['id'];

    $check = $conn->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id AND (variant_id = :variant_id OR (variant_id IS NULL AND :variant_id2 IS NULL))");
    $check->bindParam(':cart_id', $cart_id);
    $check->bindParam(':product_id', $product_id);
    $check->bindParam(':variant_id', $variant_id);
    $check->bindParam(':variant_id2', $variant_id);
    $check->execute();
    $existing = $check->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $new_qty = $existing['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart_items SET quantity = :qty WHERE id = :id");
        $update->bindParam(':qty', $new_qty);
        $update->bindParam(':id', $existing['id']);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO cart_items (cart_id, product_id, variant_id, quantity) VALUES (:cart_id, :product_id, :variant_id, :quantity)");
        $insert->bindParam(':cart_id', $cart_id);
        $insert->bindParam(':product_id', $product_id);
        $insert->bindParam(':variant_id', $variant_id);
        $insert->bindParam(':quantity', $quantity);
        $insert->execute();
    }

    $updated_cart = get_cart_data($conn, $user_id);
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $updated_cart]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
