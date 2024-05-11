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
            'message' => 'answer_id is required.'
        );
    } else if (!isset($_POST['answer'])) {
        $response = array(
            'status' => 'error',
            'message' => 'answer is required.'
        );
    } else {
        try {
            $answer_id = $_POST['answer_id'];
            $answer = $_POST['answer'];

            edit_answer($pdo, $answer_id, $answer);
            $response = array(
                'status' => 'success',
                'message' => 'You have edited the answer successfully.'
            );
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'An error has occured.',
                'e' => $e

            );
        }
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'USE POST METHOD ONLY',
    );
}

echo json_encode($response);
exit();



function edit_answer($PDO, $answer_id, $answer)
{
    $sql = "Update answers set answer = :answer where id = :answer_id";

    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(
        ':answer_id' => $answer_id,
        ':answer' => $answer,

    ));
    // Get the ID of the last inserted row
    $lastId = $PDO->lastInsertId();

    // Return the last inserted ID
    return $lastId;
}
