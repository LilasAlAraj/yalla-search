<?php
require_once "pdo.php";

try {

    // Example query to fetch questions from the database
    $sql = "SELECT questions.*, users.fname, users.lname FROM questions
join users on questions.user_id = users.id
where questions.id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $question = $stmt->fetch(PDO::FETCH_ASSOC);


    $sql = "SELECT answers.*, users.fname, users.lname FROM answers
    join users on answers.user_id = users.id
    where answers.question_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $answers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response with total pages
    $response = array(
        'status' => 'success',
        'question' => $question,
        'answers' => $answers,
        // 'totalPages' => $totalPages,
        // 'currentPage'=>$page
    );
} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'message' => 'An error has occured.',
        'e' => $e
    );
}
// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
