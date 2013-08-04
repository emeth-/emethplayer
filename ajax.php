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
        die(user_login($_POST['login_email'], $_POST['login_pass']));
        break;
    
    case "logout":
        die(user_logout());
        break;
    
    case "get_logged_in_user":
        die(get_logged_in_user());
        break;
    
    case "register":
        die(user_register($_POST['register_email'], $_POST['register_pass1'], $_POST['register_pass2']));
        break;
    
    case "get_playlist":
        die(get_playlist($user));
        break;
    
    case "save_playlist":
        die(save_playlist($user, $_POST['playlist']));
        break;
    
    case "new_current":
        die(new_current($user, $_POST['audio_id']));
        break;
    
    case "update_current":
        die(update_current($user, $_POST['audio_id'], $_POST['audio_time']));
        break;
    
    case "end_current":
        die(end_current($user, $_POST['audio_id'], $_POST['duration']));
        break;
    
    case "add_track":
        if ($_POST['password'] != "royale")
            die("bad password");
        else
            die(add_track($_POST['file_loc'], $_POST['file_loc_s3'], $_POST['title'], $_POST['author_name'], $_POST['owner_name'], $_POST['owner_website'], $_POST['description'], $_POST['tags'], $_POST['track_timestamp'], $_POST['download_me']));
        break;
}

?>