<?php
$opts = ['http' => ['method' => 'GET', 'header' => "Authorization: Bearer test_token\r\n"]];
$context = stream_context_create($opts);
echo file_get_contents('https://faysluxe-backend.onrender.com/api/test_headers.php', false, $context);
?>
