<?php
if(!isset($_SESSION)) {
    session_start(); 
}

ob_start();

define('PRINC',__DIR__ . '/');
define('SYSTEM',__DIR__ . '/system/');
define('APPLICATION',__DIR__ . '/application/');
define('CONTENT',__DIR__. '/views/');
define('STYLE',CONTENT . '/general/');

include PRINC . 'system/autoload.php';
include_once APPLICATION . 'this.auto.php';
include_once APPLICATION . 'connect.auto.php';
include_once APPLICATION . 'redirect.auto.php';
include_once APPLICATION . 'auth.auto.php';
include_once APPLICATION . 'user.auto.php';
include_once APPLICATION . 'PHPMailerAutoload.php';
include_once APPLICATION . 'gameq/GameQ.php';

$db = array(
    "mysql" => array(
        'host'      =>  'localhost',
        'username'  =>  'username-here',
        'dbname'    =>  'database-here',
        'password'  =>  'parola-here'
    )
);

this::$_PAGE_URL = "https://yoururl.com/";
this::$_FORUM_URL = "";
this::$_FACEBOOK_URL = "";
this::$_DISCORD_URL = "";
this::$_SERVER_IP = ""; // SERVER IP without PORT!

this::init()->getContent();
?>