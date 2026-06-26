<?php
function get_authenticated_user() {
    $headers = null;
    if (function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
    } else {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
    }
    
    $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : null);
    
    if (!$auth_header || !preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
        return null;
    }
    
    $token = $matches[1];
    
    try {
        $decoded = json_decode(base64_decode($token), true);
        if ($decoded && isset($decoded['id'])) {
            return (int)$decoded['id'];
        }
    } catch (Exception $e) {
        return null;
    }
    
    return null;
}
?>
