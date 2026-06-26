<?php
require_once '../../config/auth.php';
$auth_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (function_exists('apache_request_headers') ? apache_request_headers()['Authorization'] : 'none');
$user_id = get_authenticated_user();

echo json_encode([
    'header_received' => $auth_header,
    'user_id' => $user_id
]);
?>
