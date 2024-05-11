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
    } else {
        try {
            $question_id = $_POST['question_id'];

            remove_question($pdo, $question_id);
            $response = array(
                'status' => 'success',
                'message' => 'You have removed an question successfully.'
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



function remove_question($PDO, $question_id)
{
    $sql = "delete from questions where id = :question_id";

    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(
        ':question_id' => $question_id,

    ));
    // Get the ID of the last inserted row
    $lastId = $PDO->lastInsertId();

    // Return the last inserted ID
    return $lastId;
}
