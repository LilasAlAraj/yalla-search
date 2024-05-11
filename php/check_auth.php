<?php
session_start();

// Check if the user is authenticated
if (isset($_SESSION['user_id'])) {
    // User is authenticated
    $response = array(
        'authenticated' => true,
        'user_id'=>$_SESSION['user_id']
    );
} else {
    // User is not authenticated
    $response = array(
        'authenticated' => false
    );
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
