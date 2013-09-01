<?

//ADD EVENT LOGS
// add forgot password

function get_playlist($user)
{
    $returnData = array();
    $user_q = mysql_query("SELECT playlist FROM user WHERE id=".$user['id']);
    if (mysql_num_rows($user_q) <= 0)
    {
        $returnData['status'] = "error";
        $returnData['error'] = "No playlist found.";
        return json_encode($returnData);
    }
    $returnData['status'] = "success";
    $u = mysql_fetch_assoc($user_q);
    $returnData['playlist'] = explode('|', $u['playlist']);
    $returnData['playlist_data'] = array();
    $pld = mysql_query("SELECT * FROM audio WHERE id IN (".implode(',', $returnData['playlist']).")");
    while($p = mysql_fetch_assoc($pld))
        $returnData['playlist_data'][$p['id']] = $p;
    
    return json_encode($returnData);
}

function save_playlist($user, $playlist)
{
    $returnData = array();
    
    $pieces = explode('|', $playlist);
    for ($i=0; $i<count($pieces); $i++)
        $pieces[$i] = abs(intval($pieces[$i]));
    $playlist = implode('|', $pieces);

    mysql_query("UPDATE user SET playlist='".$playlist."' WHERE id=".$user['id']);

    $returnData['status'] = "success";

    return json_encode($returnData);
}

function new_current($user, $audio_id)
{
    $returnData = array();
    $audio_id = abs(intval($audio_id));
        
    mysql_query("INSERT INTO plays (user_id, audio_id, start_timestamp) VALUES(".$user['id'].", ".$audio_id.", unix_timestamp())");
    mysql_query("UPDATE audio SET plays=plays+1 WHERE id=".$audio_id);

    $returnData['audio_id'] = $audio_id;
    $returnData['status'] = "success";
    $returnData['user'] = $user;
        
    return json_encode($returnData);
}

function update_current($user, $audio_id, $audio_time)
{
    $returnData = array();
    $audio_id = abs(intval($audio_id));
    $audio_time = abs(intval($audio_time));

    $shrz = mysql_query("SELECT * FROM plays WHERE user_id=".$user['id']." AND audio_id=".$audio_id." ORDER BY start_timestamp DESC LIMIT 1");
    if (mysql_num_rows($shrz) > 0)
    {
        mysql_query("UPDATE plays SET audio_timestamp=".$audio_time." WHERE user_id=".$user['id']." AND audio_id=".$audio_id." ORDER BY start_timestamp DESC LIMIT 1");
    }
    else
    {
        mysql_query("INSERT INTO plays (user_id, audio_id, start_timestamp, audio_timestamp) VALUES(".$user['id'].", ".$audio_id.", unix_timestamp(), ".$audio_time.")");
        mysql_query("UPDATE audio SET plays=plays+1 WHERE id=".$audio_id);
    }

    $returnData['audio_id'] = $audio_id;
    $returnData['audio_time'] = $audio_time;
    $returnData['status'] = "success";
        
    return json_encode($returnData);
}

function end_current($user, $audio_id, $duration)
{
    $returnData = array();
    $audio_id = abs(intval($audio_id));
    $duration = abs(intval($duration));
    $end_time = time();
    
    mysql_query("UPDATE plays SET end_timestamp=".$end_time." WHERE user_id=".$user['id']." AND audio_id=".$audio_id." ORDER BY start_timestamp DESC LIMIT 1");
    mysql_query("UPDATE audio SET duration=".$duration." WHERE id=".$audio_id);
        
    $returnData['audio_id'] = $audio_id;
    $returnData['end_time'] = $end_time;
    $returnData['status'] = "success";
        
    return json_encode($returnData);
}
        
function view_available_tracks()
{
    $returnData = array();
    $returnData['status'] = "success";
    $returnData['tracks'] = array();
    $exists = mysql_query("SELECT * FROM audio");
    while($d = mysql_fetch_assoc($exists))
    {
        $returnData['tracks'][] = $d;
    }
    return json_encode($returnData);
}

function add_track($file_loc, $file_loc_s3, $title, $author_name, $owner_name, $owner_website, $description, $tags, $track_timestamp, $download_me)
{
    $file_loc = mysql_real_escape_string($file_loc);
    $file_loc_s3 = mysql_real_escape_string($file_loc_s3);
    $title = mysql_real_escape_string($title);
    $author_name = mysql_real_escape_string($author_name);
    $owner_name = mysql_real_escape_string($owner_name);
    $owner_website = mysql_real_escape_string($owner_website);
    $description = mysql_real_escape_string($description);
    $tags = mysql_real_escape_string($tags);
    $track_timestamp = mysql_real_escape_string($track_timestamp);
    $download_me = intval($download_me);
    $exists = mysql_query("SELECT * FROM audio WHERE file_loc='$file_loc'");
    if(mysql_num_rows($exists) > 0)
    {
        mysql_query("UPDATE audio SET file_loc_s3='$file_loc_s3', title='$title', author_name='$author_name', owner_name='$owner_name', owner_website='$owner_website', description='$description', tags='$tags', track_timestamp='$track_timestamp', download_me=$download_me WHERE file_loc='$file_loc'");
    }
    else
    {
        mysql_query("INSERT INTO audio (file_loc, file_loc_s3, title, author_name, owner_name, owner_website, description, tags, track_timestamp, download_me) VALUES('$file_loc', '$file_loc_s3', '$title', '$author_name', '$owner_name', '$owner_website', '$description', '$tags', '$track_timestamp', $download_me)");
    }
}

function user_login($email, $password)
{
    $returnData = array();
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $returnData['status'] = "error";
        $returnData['error'] = "Invalid email.";
        return json_encode($returnData);
    }
    else
    {
        $email = mysql_real_escape_string($email);
        $encPassword = sha1($email.$password);
        return json_encode(helper_login_user($email, $encPassword));
    }
}

function user_register($email, $pass, $pass2)
{
    $returnData = array();
    
    if ($pass == '')
    {
        $returnData['status'] = "error";
        $returnData['error'] = "Password cannot be blank";
        return json_encode($returnData);;
    }
    if($pass != $pass2)
    {
        $returnData['status'] = "error";
        $returnData['error'] = "Passwords don't match.";
        return json_encode($returnData);;
    }
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $returnData['status'] = "error";
        $returnData['error'] = "Invalid email.";
        return json_encode($returnData);;
    }

    $email = mysql_real_escape_string($email);

    $existsQ2 = mysql_query("SELECT COUNT(*) as cnt FROM user WHERE email='$email'");
    $exists2 = mysql_fetch_assoc($existsQ2);
    if($exists2['cnt'] > 0)
    {
        $returnData['error'] = "Email unavailable.";
        return json_encode($returnData);;
    }

    $encPassword = sha1($email.$pass);
    $existsQ = mysql_query("INSERT INTO user (password, email) VALUES('$encPassword', '$email')");
    
    return json_encode(helper_login_user($email, $encPassword));
}

function get_logged_in_user()
{
    global $user;
    if ($user != -1)
    {
        return json_encode(helper_login_user('', '', $_SESSION['userid']));
    }
    else
    {
        return json_encode(array("status"=>"error", "error"=>"Not logged in."));
    }
}

function user_logout()
{
    $returnData = array();
    session_destroy();
    $returnData['status'] = 'success';
    return json_encode($returnData);
}

/*
 *Used by both login and register (which logs in a user after they finish creating an account).
 */
function helper_login_user($email, $encPassword, $user_id=-1)
{
    $returnData = array();
    if ($user_id == -1)
        $exists = mysql_query("SELECT * FROM user WHERE (email='$email' AND password='$encPassword')");
    else
        $exists = mysql_query("SELECT * FROM user WHERE id=$user_id");
    
    if(mysql_num_rows($exists) > 0)
    {
        $returnData['status'] = "success";
        $user = mysql_fetch_array($exists);
        $_SESSION['userid'] = $user['id'];
        $returnData['email'] = $user['email'];
        $returnData['curr_playing'] = -1;
        $shrz = mysql_query("SELECT * FROM plays WHERE user_id=".$user['id']." AND end_timestamp=0 AND downloaded=0");
        if (mysql_num_rows($shrz) > 0)
        {
            $returnData['curr_playing'] = array();
            $shr = mysql_fetch_assoc($shrz);
            $returnData['curr_playing']['audio_id'] = $shr['audio_id'];
            $returnData['curr_playing']['audio_time'] = $shr['audio_timestamp'];
            $returnData['curr_playing']['audio_data'] = mysql_fetch_assoc(mysql_query("SELECT * FROM audio WHERE id=".$shr['audio_id']));
        }
    }
    else
    {
        $returnData['status'] = "error";
        $returnData['error'] = "User not found.";
    }
    return $returnData;
}

?>