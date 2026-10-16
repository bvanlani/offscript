<?php
require_once 'lib/common.php';
require_once 'lib/twig-bootstrap.php';

//We need to test for a minimum version of PHP, because earlier versions have bugs that affect security
if(version_compare(PHP_VERSION, '5.3.7') < 0){
    throw new Exception(
        'This system needs PHP 5.3.7 or later'
    );
}

session_start();

//If we're already logged in, go back home
if(isLoggedIn()){
    redirectAndExit('index');
}

//Handle the form posting
$username = '';
if($_POST){
    
    //Init the database
    $pdo = getPDO();

    //We redirect only if the password is correct
    $username = $_POST['username'];
    $ok = tryLogin($pdo, $username, $_POST['password']);
    if($ok){
        login($username);
        redirectAndExit('index');
    }
}

echo $twig->render('login.twig', [
    'isLoggedIn' => isLoggedIn(),
    'username' => $username
]);

?>