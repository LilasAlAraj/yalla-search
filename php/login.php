<?php
session_start();

require_once "pdo.php";

if (isset($_SESSION['user_id'])) {
    $response = array(
        'status' => 'error',
        'message' => 'User already logged in.'
    );
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_POST['email'])) {
        $response = array(
            'status' => 'error',
            'message' => 'email field is required.'
        );
    } else  if (!isset($_POST['password'])) {
        $response = array(
            'status' => 'error',
            'message' => 'password field is required.'
        );
    } else {

        try {
            $email = $_POST['email'];
            $password = $_POST['password'];


            $salt = 'XyZzy12*_';
            $hashedPassword = hash('md5', $salt . $password);
            $row = checking_user($pdo, htmlentities($email), $hashedPassword);
            if ($row == false) {
                $response = array(
                    'status' => 'error',
                    'message' => 'email or password are incorrect.'
                );
            } else {

                $_SESSION['user_id'] = $row['id'];
                $response = array(
                    'status' => 'success',
                    'message' => 'You have loggedin successfully.'
                );
            }
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


function checking_user($pdo, $em, $pw)
{
    $sql = "select * from users where email = :email and password =:password";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':email' => $em,
        ':password' => $pw
    ));
    return ($stmt->fetch());
}


function add_user($PDO, $fname, $lname, $email, $password)
{
    $sql = "insert into users (fname,lname,email,password) 
          values(:fname,:lname, :email,:password )";

    $stmt = $PDO->prepare($sql);
    $stmt->execute(array(
        ':fname' => $fname,
        ':lname' => $lname,
        ':email' => $email,
        ':password' => $password,

    ));
    // Get the ID of the last inserted row
    $lastId = $PDO->lastInsertId();

    // Return the last inserted ID
    return $lastId;
}
