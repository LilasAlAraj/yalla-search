<?php
session_start();

require_once "pdo.php";

if (!isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Please logged in.'
    );
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['title'])) {
        $response = array(
            'status' => 'error',
            'message' => 'title field is required.'
        );
    } else  if (!isset($_POST['question'])) {
        $response = array(
            'status' => 'error',
            'message' => 'question field is required.'
        );
    } else {
        try {
            $question = $_POST['question'];
            $title = $_POST['title'];
            $user_id = $_SESSION['user_id'];

            add_question($pdo, $question, $title, $user_id);
            $response = array(
                'status' => 'success',
                'message' => 'You have added a question successfully.'
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



function add_question($PDO, $question, $title, $user_id)
{
    $sql = "insert into questions (title,question ,user_id) 
          values(:title,:question,  :user_id )";

    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(
        ':title' => $title,
        ':question' => $question,
        ':user_id' => $user_id,

    ));
    // Get the ID of the last inserted row
    $lastId = $PDO->lastInsertId();

    // Return the last inserted ID
    return $lastId;
}
