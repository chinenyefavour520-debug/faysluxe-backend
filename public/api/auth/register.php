<?php
require_once '../../config/cors.php';
require_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$name_parts = explode(' ', trim($data['name']), 2);
$first_name = $name_parts[0];
$last_name = isset($name_parts[1]) ? $name_parts[1] : '';
$email = $data['email'];
$password = $data['password'];
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if email exists
    $check_query = "SELECT id FROM users WHERE email = :email";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        exit();
    }

    $query = "INSERT INTO users (first_name, last_name, email, password_hash) VALUES (:first_name, :last_name, :email, :password_hash)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);

    if ($stmt->execute()) {
        $user_id = $conn->lastInsertId();
        $token = base64_encode(json_encode(['id' => $user_id, 'email' => $email, 'time' => time()]));
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user_id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'role' => 'customer'
                ]
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to register user']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
