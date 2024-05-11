<?php
require_once "pdo.php";
session_start();
if (!isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'Please logged in.'
    );
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['answer_id'])) {
        $response = array(
            'status' => 'error',
            'message' => 'answer_id is required.'
        );
    } else {
        try {
            $answer_id = $_GET['answer_id'];

            // Example query to fetch questions from the database
            $sql =
                "SELECT comments.*, users.fname, users.lname FROM comments
            join users on comments.user_id = users.id
            where comments.answer_id = :answer_id
            ORDER BY comments.created_at DESC
            ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':answer_id', $answer_id, PDO::PARAM_INT);
            $stmt->execute();

            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return JSON response with total pages
            $response = array(
                'status' => 'success',
                'comments' => $comments,
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
        'message' => 'USE POST METHOD ONLY',
    );
}
// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
