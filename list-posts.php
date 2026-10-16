<?php

require_once 'lib/common.php';
require_once 'lib/list-posts.php';
require_once 'lib/twig-bootstrap.php';

session_start();

if(!isLoggedIn()){
    redirectAndExit('index');
}

if($_POST){
    $deleteResponse = $_POST['delete-post'];
    if($deleteResponse){
        $keys = array_keys($deleteResponse);
        $deletePostId = $keys[0];
        if($deletePostId){
            deletePost(getPDO(), $deletePostId);
            redirectAndExit('list-posts');
        }
    }
}

//Connect to the database, run a query
$pdo = getPDO();
$posts = getAllPosts($pdo);

echo $twig->render('list-posts.twig', [
    'isLoggedIn' => isLoggedIn(),
    'username' => getAuthUser(),
    'posts' => $posts,

]);

?>