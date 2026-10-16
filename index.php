<?php
require_once 'lib/common.php';
require_once 'lib/twig-bootstrap.php';

session_start();

//Connect to the database, run a query, handle errors
$pdo = getPDO();
$posts = getAllPosts($pdo);

$notFound = isset($_GET['not-found']);

echo $twig->render('home.twig', [
    'isLoggedIn' => isLoggedIn(),
    'username' => getAuthUser(),
    'notFound' => isset($_GET['not-found']),
    'posts' => $posts,

]);
?>