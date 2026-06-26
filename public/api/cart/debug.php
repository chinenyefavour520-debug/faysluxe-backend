<?php
require_once '../../config/cors.php';
$auth = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : (function_exists('apache_request_headers') ? apache_request_headers()['Authorization'] : 'Missing');
file_put_contents('debug.txt', date('Y-m-d H:i:s') . " - Auth: " . $auth . "\n", FILE_APPEND);
echo json_encode(['success' => true, 'auth' => $auth]);
?>
