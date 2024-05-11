<?php
session_start();

require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Please logged in.'
    );
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['question_id'])) {
        $response = array(
            'status' => 'error',
            'message' => 'question_id is required.'
        );
    } else if (!isset($_POST['title'])) {
        $response = array(
            'status' => 'error',
            'message' => 'title is required.'
        );
    } else if (!isset($_POST['question'])) {
        $response = array(
            'status' => 'error',
            'message' => 'question is required.'
        );
    } else {
        try {
            $question_id = $_POST['question_id'];
            $question = $_POST['question'];
            $title = $_POST['title'];

            edit_question($pdo, $question_id, $question, $title);
            $response = array(
                'status' => 'success',
                'message' => 'You have edited the question successfully.'
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



function edit_question($PDO, $question_id, $question, $title)
{
    $sql = "update questions set title =:title , question =:question  where id = :question_id";

    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(
        ':question_id' => $question_id,
        ':question' => $question,
        ':title' => $title,

    ));
    // Get the ID of the last inserted row
    $lastId = $PDO->lastInsertId();

    // Return the last inserted ID
    return $lastId;
}
