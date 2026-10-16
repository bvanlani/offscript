<?php 
require_once 'lib/common.php';
require_once 'lib/edit-post.php';
require_once 'lib/view-post.php';
require_once 'lib/twig-bootstrap.php';

session_start();

//Don't let non-auth users see this screen
if(!isLoggedIn()){
    redirectAndExit('index');
}

//Empty defaults
$title = $body = '';

//Init the databse and get handle
$pdo = getPDO();

$postId = null;
if(isset($_GET['post_id'])){
    $post = getPostRow($pdo, $_GET['post_id']);
    if($post){
        $postId = $_GET['post_id'];
        $title = $post['title'];
        $body = $post['body'];
    }
}

//Handle the post operation here
$errors = array();
if($_POST){
    //Validate these first
    $title = $_POST['post-title'];
    if(!$title){
        $errors[] = 'The post must have a title';
    }
    if(strlen($title) > 60){
        $errors[] = 'The title cannot be longer than 60 characters';
    }
    $body = $_POST['post-body'];
    if(!$body){
        $errors[] = 'The post must have a body';
    }

    if(!$errors){
        $pdo = getPDO();

        //Decide if we are editing or adding
        if($postId){
            editPost($pdo, $title, $body, $postId);
        }else{
            $userId = getAuthUserId($pdo);
            $postId = addPost($pdo, $title, $body, $userId);

            if($postId === false){
                $errors[] = 'Post operation failed';
            }
        }

        if($postId === false){
            $errors[] = 'Post operation failed';
        }
    }

    if(!$errors){
        redirectAndExit('view-post?post_id=' . $postId);
    }
}

echo $twig->render('edit-post.twig', [
    'isLoggedIn' => isLoggedIn(),
    'username' => getAuthUser(),
    'isEdit' => isset($_GET['post_id']),
    'errors' => $errors,
    'title' => $title,
    'body' => $body
]);

?>