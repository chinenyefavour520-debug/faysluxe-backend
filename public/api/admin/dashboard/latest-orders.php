<?php
require_once '../../../config/cors.php';
require_once '../../../config/database.php';

try {
    $query = "
        SELECT o.id, o.order_number, o.status, o.total_amount, o.created_at, 
               CONCAT(u.first_name, ' ', u.last_name) as customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        ORDER BY o.created_at DESC
        LIMIT 5
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ensure numeric types
    $formatted_orders = array_map(function($order) {
        $order['total_amount'] = (float)$order['total_amount'];
        return $order;
    }, $orders);

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => [
            'orders' => $formatted_orders
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
