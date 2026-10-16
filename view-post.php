<?php
require_once 'lib/common.php';
require_once 'lib/view-post.php';
require_once 'lib/twig-bootstrap.php';

session_start();

//Get the post ID
if(isset($_GET['post_id'])){
    $postId = $_GET['post_id'];
}
else{
    //So we always have a post ID var defined
    $postId = 0;
}

//Connect to the database, run a query, handle errors
$pdo = getPDO();
$row = getPostRow($pdo, $postId);

//If the post does not exist, let's deal with that here
if(!$row){
    redirectAndExit('index?not-found=1');
}

$errors = null;
if($_POST){
    switch ($_GET['action']){
        case 'add-comment':
            $commentData = array(
                'text' => $_POST['comment-text']
            );
            $errors = handleAddComment($pdo, $postId, $commentData);
            break;
        case 'delete-comment':
            $deleteResponse = $_POST['delete-comment'];
            handleDeleteComment($pdo, $postId, $deleteResponse);
            break;
    }
}
else{
    $commentData = array(
        'text' => '',
    );
}

$comments = getCommentsForPost($pdo, $postId);

echo $twig->render('view-post.twig', [
    'isLoggedIn' => isLoggedIn(),
    'username' => getAuthUser(),
    'row' => $row,
    'comments' => $comments,
    'postId' => $postId,
]);
?>