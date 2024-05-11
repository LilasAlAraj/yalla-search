<?php
session_start();

require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Please logged in.'
    );
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['answer_id'])) {
        $response = array(
            'status' => 'error',
            'message' => 'answer field is required.'
        );
    } else if (!isset($_POST['star'])) {
        $response = array(
            'status' => 'error',
            'message' => 'star field is required.'
        );
    } else {
        try {
            $answer_id = $_POST['answer_id'];
            $star = $_POST['star'];
            $user_id = $_SESSION['user_id'];

            rate_answer($pdo, $star, $answer_id, $user_id);
            $response = array(
                'status' => 'success',
                'message' => 'You have rated the answer successfully.'
            );
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'An error has occured.',
                'e' => $e,
                '$answer_id' => $_POST['answer_id'],
                '$star' => $_POST['star'],
                '$user_id' => $_SESSION['user_id'],
            );
        }
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'USE POST METHOD ONLY'
    );
}

echo json_encode($response);
exit();



function rate_answer($PDO, $star, $answer_id, $user_id)
{
    $sql = "insert into rates (rate,answer_id ,user_id) 
          values(:rate,:answer_id,:user_id )";

    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(
        ':rate' => $star,
        ':answer_id' => $answer_id,
        ':user_id' => $user_id,

    ));
    // Get the ID of the last inserted row
    $lastId = $PDO->lastInsertId();

    // Return the last inserted ID
    return $lastId;
}
