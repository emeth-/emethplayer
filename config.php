<?
session_start();
    
$settings = array();
$settings['forgotPasswordEmail'] = "admin@site.com";
$settings['forgotPasswordEmailSubject'] = "Forgot Password Request from site.com";
$settings['forgotPasswordEmailMessage'] = "To reset your password for your account {username}, go to the url below:\n\n<a href='{url}'>{url}</a>";
$settings['forgotPasswordURL'] = "http://".$_SERVER['SERVER_NAME']."/reset_password.php"; //location of resetpassword.php

$mysqlConfig = array(
"mysql_user" => "root", 
"mysql_password" => "root", 
"mysql_db" => "emethplayer", 
"mysql_server" => "localhost"
);

$con = mysql_connect($mysqlConfig['mysql_server'],$mysqlConfig['mysql_user'],$mysqlConfig['mysql_password']);
if (!$con)
   die('Error connecting to DB');

mysql_select_db($mysqlConfig['mysql_db'], $con);

$user = -1;
if($_SESSION && $_SESSION['userid'])
{
    $exists = mysql_query("SELECT * FROM user WHERE id={$_SESSION['userid']}");
    if(mysql_num_rows($exists) > 0)
        $user = mysql_fetch_array($exists);
    else
        session_destroy(); 
}

function rand_string( $length ) 
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";	
    $size = strlen( $chars );
    for( $i = 0; $i < $length; $i++ ) 
    {
        $str .= $chars[ rand( 0, $size - 1 ) ];
    }
    return $str;
}

?>