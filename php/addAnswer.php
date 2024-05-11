<?php
session_start();

require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Please logged in.'
    );
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['answer'])) {
        $response = array(
            'status' => 'error',
            'message' => 'answer field is required.'
        );
    } else  if (!isset($_POST['question_id'])) {
        $response = array(
            'status' => 'error',
            'message' => 'question_id field is required.'
        );
    } else {
        try {
            $question_id = $_POST['question_id'];
            $answer = $_POST['answer'];
            $user_id = $_SESSION['user_id'];

            add_answer($pdo, $answer, $question_id, $user_id);
            $response = array(
                'status' => 'success',
                'message' => 'You have added an answer successfully.'
            );
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'An error has occured.'
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



function add_answer($PDO, $answer, $question_id, $user_id)
{
    $sql = "insert into answers (answer,question_id ,user_id) 
          values(:answer,:question_id,  :user_id )";

    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(
        ':answer' => $answer,
        ':question_id' => $question_id,
        ':user_id' => $user_id,

    ));
    // Get the ID of the last inserted row
    $lastId = $PDO->lastInsertId();

    // Return the last inserted ID
    return $lastId;
}
