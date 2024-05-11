<?php
require_once "pdo.php";
try {
    // Example query to count total number of questions
    $countSql =
        isset($_GET['search']) ?
        'SELECT COUNT(*) AS total FROM questions 
         WHERE questions.title LIKE "%' . $_GET["search"] . '%" OR  questions.question LIKE "%' . $_GET["search"] . '%" 
    ' :
        "SELECT COUNT(*) AS total FROM questions";
    $countStmt = $pdo->query($countSql);
    $totalRows = $countStmt->fetchColumn();

    // Example pagination logic
    $perPage = 10;
    $totalPages = ceil($totalRows / $perPage); // Calculate total number of pages

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $perPage;

    // Example query to fetch questions from the database
    $sql =     isset($_GET['search']) ?
        'SELECT questions.*, users.fname, users.lname FROM questions
    join users on questions.user_id = users.id
    WHERE questions.title LIKE "%' . $_GET["search"] . '%" OR  questions.question LIKE "%' . $_GET["search"] . '%" 
    ORDER BY questions.created_at DESC
    LIMIT :perPage OFFSET :offset' :
        "SELECT questions.*, users.fname, users.lname FROM questions
            join users on questions.user_id = users.id
            ORDER BY questions.created_at DESC
            LIMIT :perPage OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response with total pages
    $response = array(
        'status' => 'success',

        'questions' => $questions,
        'totalPages' => $totalPages,
        'currentPage' => $page
    );
} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'message' => 'An error has occured.'
    );
}
// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
