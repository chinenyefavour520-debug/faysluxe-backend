<?php
require_once '../../../config/cors.php';
require_once '../../../config/database.php';

try {
    // 1. REVENUE
    // Total revenue
    $rev_query = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'";
    $rev_stmt = $conn->query($rev_query);
    $total_revenue = (float)($rev_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    
    // Revenue this month
    $rev_month_query = "SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
    $rev_month_stmt = $conn->query($rev_month_query);
    $revenue_this_month = (float)($rev_month_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // 2. ORDERS
    // Total orders
    $ord_query = "SELECT COUNT(*) as total FROM orders";
    $ord_stmt = $conn->query($ord_query);
    $total_orders = (int)($ord_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
    
    // Pending orders
    $pend_query = "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'";
    $pend_stmt = $conn->query($pend_query);
    $pending_orders = (int)($pend_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Orders by status
    $status_query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
    $status_stmt = $conn->query($status_query);
    $by_status = $status_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. PRODUCTS
    $prod_query = "SELECT COUNT(*) as total FROM products";
    $prod_stmt = $conn->query($prod_query);
    $total_products = (int)($prod_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // 4. USERS
    $users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
    $users_stmt = $conn->query($users_query);
    $total_users = (int)($users_stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Active users (mocked as total users for now)
    $active_users = $total_users;

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => [
            'revenue' => [
                'total' => $total_revenue,
                'this_month' => $revenue_this_month
            ],
            'orders' => [
                'total' => $total_orders,
                'pending' => $pending_orders,
                'by_status' => $by_status
            ],
            'products' => [
                'total' => $total_products
            ],
            'users' => [
                'total' => $total_users,
                'active' => $active_users
            ]
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
