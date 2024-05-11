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


    $sql = "SELECT answers.*, users.fname, users.lname, COALESCE(sum(rates.rate)/count(rates.rate), 0) AS rate,
	(select rates.rate from rates where rates.user_id = 1 and rates.answer_id = answers.id) as prev_rate
	FROM answers
    join users on answers.user_id = users.id
	left join rates on rates.answer_id = answers.id
    where answers.question_id = :id
    GROUP BY answers.id;";
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
