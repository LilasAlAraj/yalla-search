<?php
session_start();

require_once "pdo.php";
if (!isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'please login first.',
    );
} else {
    try {
        // Example query to count total number of questions
        $countSql =
            isset($_GET['search']) ?
            '
            select count(*) from (SELECT questions.id id 
            FROM questions
            WHERE questions.user_id = :user_id
            UNION all 
            SELECT answers.id id
            FROM answers 
            WHERE answers.user_id = :user_id ) result
            
            ' :
            "select count(*) from (SELECT questions.id id, questions.title title, questions.question body, questions.created_at created_at, 'question' as 'type' 
            FROM questions
            WHERE questions.user_id = :user_id
            UNION all 
            SELECT answers.id id,'' title , answers.answer body, answers.created_at created_at, 'answer' as 'type' 
            FROM answers 
            WHERE answers.user_id = :user_id ) result
            ";
        $stmt = $pdo->prepare($countSql);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        $totalRows = $stmt->fetchColumn();


        // Example pagination logic
        $perPage = 10;
        $totalPages = ceil($totalRows / $perPage); // Calculate total number of pages

        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($page - 1) * $perPage;

        // Example query to fetch questions from the database
        $sql =     isset($_GET['search']) ?
            'SELECT questions.id id, questions.title title, questions.question body, questions.created_at created_at, "question" as "type"
            FROM questions
            WHERE questions.user_id = :user_id
            UNION all 
            SELECT answers.id id,CONCAT(SUBSTRING(answers.answer,1,100), " ...") title , answers.answer body, answers.created_at created_at, "answer" as "type" 
            FROM answers 
            WHERE answers.user_id = :user_id ORDER by created_at
            LIMIT :perPage OFFSET :offset' :
            "SELECT questions.id id, questions.title title, questions.question body, questions.created_at created_at, 'question' as 'type' 
            FROM questions
            WHERE questions.user_id = :user_id
            UNION all 
            SELECT answers.id id,CONCAT(SUBSTRING(answers.answer,1,100), ' ...') title , answers.answer body, answers.created_at created_at, 'answer' as 'type' 
            FROM answers 
            WHERE answers.user_id = :user_id ORDER by created_at
            LIMIT :perPage OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        $log = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return JSON response with total pages
        $response = array(
            'status' => 'success',
            'log' => $log,
            'totalPages' => $totalPages,
            'currentPage' => $page
        );
    } catch (Exception $e) {
        $response = array(
            'status' => 'error',
            'message' => 'An error has occured.',
            'e'=>$e
        );
    }
}
// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
