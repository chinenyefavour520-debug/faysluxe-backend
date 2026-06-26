<?php
function get_cart_data($conn, $user_id) {
    // Ensure cart exists
    $stmt = $conn->prepare("SELECT id FROM carts WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cart) {
        $insert = $conn->prepare("INSERT INTO carts (user_id) VALUES (:user_id)");
        $insert->bindParam(':user_id', $user_id);
        $insert->execute();
        $cart_id = $conn->lastInsertId();
    } else {
        $cart_id = $cart['id'];
    }

    // Fetch cart items
    $query = "
        SELECT ci.id as cart_item_id, ci.quantity, ci.variant_id, 
               p.id as product_id, p.name, p.price, p.slug,
               (SELECT image_url FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as primary_image
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_id = :cart_id
    ";
    
    $item_stmt = $conn->prepare($query);
    $item_stmt->bindParam(':cart_id', $cart_id);
    $item_stmt->execute();
    $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0;
    $item_count = 0;

    foreach ($items as &$item) {
        $item['price'] = (float)$item['price'];
        $item['quantity'] = (int)$item['quantity'];
        $item['cart_item_id'] = (int)$item['cart_item_id'];
        $item['product_id'] = (int)$item['product_id'];
        
        $subtotal += $item['price'] * $item['quantity'];
        $item_count += $item['quantity'];
        
        // Ensure image structure expected by frontend
        $item['image'] = $item['primary_image'];
    }

    return [
        'id' => $cart_id,
        'user_id' => $user_id,
        'items' => $items,
        'item_count' => $item_count,
        'subtotal' => $subtotal
    ];
}
?>
