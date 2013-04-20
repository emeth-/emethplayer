<?
$requireLogin = 1;
require_once('config.php');
require_once('logic.php');

switch ($_GET['act'])
{
    case "get_available_tracks":
        die(view_available_tracks());
        break;
    
    case "login":
        die(user_login($_POST['email'], $_POST['password']));
        break;
    
    case "register":
        die(user_login($_POST['register_username'], $_POST['register_email'], $_POST['register_pass1'], $_POST['register_pass2']));
        break;
    
    case "update_current":
        die(update_current($user, $_POST['audio_id'], $_POST['audio_time']));
        break;
    
    case "new_current":
        die(new_current($user, $_POST['audio_id']));
        break;
    
    case "add_sermon":
        if ($_POST['password'] != "royale")
            die("bad password");
        else
            die(add_sermon($_POST['file_loc'], $_POST['file_loc_s3'], $_POST['title'], $_POST['author_name'], $_POST['church'], $_POST['church_website'], $_POST['description'], $_POST['scripture'], $_POST['sermon_timestamp'], $_POST['download_me']));
        break;
}

?>