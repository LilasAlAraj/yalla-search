<?php
$host='localhost';
$port='3306';
$user='root';
$password='';
$dbname='YallaSearch';
$pdo = new PDO("mysql:host=$host; port=$port; dbname=$dbname;",$user,$password);

$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

?>