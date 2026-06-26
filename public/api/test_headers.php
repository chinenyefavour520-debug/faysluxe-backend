<?php
header('Content-Type: application/json');
$apache_headers = function_exists('apache_request_headers') ? apache_request_headers() : 'function does not exist';
echo json_encode([
    'server' => $_SERVER,
    'apache_headers' => $apache_headers
]);
?>
