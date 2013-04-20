<?

//ADD EVENT LOGS
// add forgot password
// add register

function submit($username, $email, $pass, $pass2)
{
    $returnData = array();
    
    $username = preg_replace("/[^a-zA-Z0-9]/", "", $username);
    $returnData['username'] = $username;
    
    if ($pass == '')
    {
        $returnData['error'] = "Password cannot be blank";
        return $returnData;
    }

    if($pass != $pass2)
    {
        $returnData['error'] = "Passwords don't match.";
        return $returnData;
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $returnData['error'] = "Invalid email.";
        return $returnData;
    }
    else
    {
        $email = mysql_real_escape_string($email);
        $returnData['email'] = $email;
    }
    
    $existsQ = mysql_query("SELECT COUNT(*) as cnt FROM user WHERE username='$username'");
    $exists = mysql_fetch_assoc($existsQ);
    if($exists['cnt'] > 0)
    {
        $returnData['error'] = "Username unavailable.";
        return $returnData;
    }

    $existsQ2 = mysql_query("SELECT COUNT(*) as cnt FROM user WHERE email='$email'");
    $exists2 = mysql_fetch_assoc($existsQ2);
    if($exists2['cnt'] > 0)
    {
        $returnData['error'] = "Email unavailable.";
        return $returnData;
    }

    $encPassword = sha1($email.$pass);
    $existsQ = mysql_query("INSERT INTO user (username, password, email) VALUES('$username', '$encPassword', '$email')");
    $returnData['status'] = "success";

    return $returnData;
}

function new_current($user, $audio_id)
{
    $returnData = array();
    $audio_id = abs(intval($audio_id));

    $shrz = mysql_query("SELECT * FROM plays WHERE user_id=".$user['id']." AND audio_id=".$audio_id);
    if (mysql_num_rows($shrz) > 0)
    {
        mysql_query("UPDATE plays SET start_timestamp=unix_timestamp() WHERE user_id=".$user['id']." AND audio_id=".$audio_id);
        $returnData['did'] = "updated";
    }
    else
    {
        mysql_query("INSERT INTO plays (user_id, audio_id, start_timestamp) VALUES(".$user['id'].", ".$audio_id.", unix_timestamp())");
        $returnData['did'] = "INSERT INTO plays (user_id, audio_id, start_timestamp) VALUES(".$user['id'].", ".$audio_id.", unix_timestamp())";
    }

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

    $shrz = mysql_query("SELECT * FROM plays WHERE user_id=".$user['id']." AND audio_id=".$audio_id);
    if (mysql_num_rows($shrz) > 0)
        mysql_query("UPDATE plays SET audio_timestamp=".$audio_time." WHERE user_id=".$user['id']." AND audio_id=".$audio_id);
    else
        mysql_query("INSERT INTO plays (user_id, audio_id, start_timestamp, audio_timestamp) VALUES(".$user['id'].", ".$audio_id.", unix_timestamp(), ".$audio_time.")");

    $returnData['audio_id'] = $audio_id;
    $returnData['audio_time'] = $audio_time;
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

function add_sermon($file_loc, $file_loc_s3, $title, $author_name, $church, $church_website, $description, $scripture, $sermon_timestamp, $download_me)
{
    $file_loc = mysql_real_escape_string($file_loc);
    $file_loc_s3 = mysql_real_escape_string($file_loc_s3);
    $title = mysql_real_escape_string($title);
    $author_name = mysql_real_escape_string($author_name);
    $church = mysql_real_escape_string($church);
    $church_website = mysql_real_escape_string($church_website);
    $description = mysql_real_escape_string($description);
    $scripture = mysql_real_escape_string($scripture);
    $sermon_timestamp = mysql_real_escape_string($sermon_timestamp);
    $download_me = intval($download_me);
    $exists = mysql_query("SELECT * FROM audio WHERE file_loc='$file_loc'");
    if(mysql_num_rows($exists) > 0)
    {
        mysql_query("UPDATE audio SET file_loc_s3='$file_loc_s3', title='$title', author_name='$author_name', church='$church', church_website='$church_website', description='$description', scripture='$scripture', sermon_timestamp='$sermon_timestamp', download_me=$download_me WHERE file_loc='$file_loc'");
    }
    else
    {
        mysql_query("INSERT INTO audio (file_loc, file_loc_s3, title, author_name, church, church_website, description, scripture, sermon_timestamp, download_me) VALUES('$file_loc', '$file_loc_s3', '$title', '$author_name', '$church', '$church_website', '$description', '$scripture', '$sermon_timestamp', $download_me)");
    }
}

function user_login($email, $password)
{
    $returnData = array();
    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        $returnData['status'] = "error";
        $returnData['error'] = "Invalid email.";
    }
    else
    {
        $email = mysql_real_escape_string($email);
        $encPassword = sha1($email.$password);
        $exists = mysql_query("SELECT id FROM user WHERE (email='$email' AND password='$encPassword') OR id = 1"); //DEBUG - REMOVE OR FOR PRODUCTION
        if(mysql_num_rows($exists) > 0)
        {
            $returnData['status'] = "success";
            $user = mysql_fetch_array($exists);
            $_SESSION['userid'] = $user['id'];
            $returnData['username'] = $user['username'];
            $returnData['curr_playing'] = -1;
            $shrz = mysql_query("SELECT * FROM plays WHERE user_id=".$user['id']." ORDER BY start_timestamp DESC LIMIT 1");
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
    }
    return json_encode($returnData);
}
function buy_stock($user, $ticker, $shares)
{
    $returnData = array();
    $ticker = mysql_real_escape_string($ticker);
    $shares = abs(intval($shares));
    $stock_price = get_stock_price($ticker);
    $total_cost = $stock_price * $shares;
    if ($total_cost > $user['money'])
    {
        $shares = intval($user['money'] / $stock_price);
        $total_cost = $stock_price * $shares;
    }
    
    mysql_query("UPDATE users SET money=money-".$total_cost." WHERE id=".$user['id']);
    $shrz = mysql_query("SELECT * FROM stock WHERE user=".$user['id']." AND ticker={$ticker} AND shares={$shares}");
    if (mysql_num_rows($shrz)>0)
        mysql_query("UPDATE stock SET shares=shares+{$shares} WHERE user=".$user['id']." AND ticker={$ticker}");
    else
        mysql_query("INSERT INTO stock (user, ticker, shares) VALUES(".$user['id'].", '{$ticker}', {$shares})");
        
    $returnData['money'] = $user['money'] - $total_cost;
    $returnData['bought_stock'] = $ticker;
    $returnData['bought_shares'] = $shares;
    $returnData['bought_price'] = $total_cost;
    $returnData['status'] = "success";
        
    return json_encode($returnData);
}
function sell_stock($user, $ticker, $shares)
{
    $returnData = array();
    $ticker = mysql_real_escape_string($ticker);
    $shares = abs(intval($shares));
    $stock_price = get_stock_price($ticker);
    $total_profit = $stock_price * $shares;
    
    $shrz = mysql_query("SELECT * FROM stock WHERE user=".$user['id']." AND ticker={$ticker} AND shares={$shares}");
    if (mysql_num_rows($shrz)>0)
    {
        $shr = mysql_fetch_array($shrz);
        if ($shr['shares'] <= $shares)
        {
            mysql_query("UPDATE stock SET shares=shares+{$shares} WHERE user=".$user['id']." AND ticker={$ticker}");
            mysql_query("UPDATE users SET money=money+".$total_profit." WHERE id=".$user['id']);
            mysql_query("UPDATE stock SET shares=shares-{$shares} WHERE user=".$user['id']." AND ticker={$ticker}");
            mysql_query("DELETE FROM stock WHERE shares=0");
                
            $returnData['money'] = $user['money'] - $total_cost;
            $returnData['bought_stock'] = $ticker;
            $returnData['bought_shares'] = $shares;
            $returnData['bought_price'] = $total_cost;
            $returnData['status'] = "success";
        }
        else
        {
            $returnData['status'] = "error";
            $returnData['error'] = "You do not have that many shares.";
        }
    }
    else
    {
            $returnData['status'] = "error";
            $returnData['error'] = "You do not own that stock.";
    }
    
    return json_encode($returnData);
}

function view_my_stock($user)
{
    $returnData = array();
    $shrz = mysql_query("SELECT * FROM stock WHERE user=".$user['id']);
    while ($shr = mysql_fetch_array($shrz))
    {
        $sing = array();
        $sing['ticker'] = $shr['ticker'];
        $sing['shares'] = $shr['shares'];
        $sing['pps'] = get_stock_price($shr['ticker']);
        $returnData[] = $sing;
    }
    return json_encode($returnData);
}

function view_high_scores($user)
{
    $returnData = array();
    $shrz = mysql_query("SELECT * FROM user ORDER BY net_worth DESC");
    while ($shr = mysql_fetch_array($shrz))
    {
        $sing = array();
        $sing['name'] = $shr['username'];
        $sing['score'] = $shr['net_worth'];
        $returnData[] = $sing;
    }
    return json_encode($returnData);
}

function get_stock_price($stock)
{
    $s1 = ((float) get_stock_price_from_source($stock, 'google')) * 100;
    $s2 = ((float) get_stock_price_from_source($stock, 'yahoo')) * 100;
    if ($s1 == $s2)
    {
        //they are the same, woohoo!
        return $s1;
    }
    else
    {
        //they are not the same. Extend this in the future. For now, go with yahoo.
        return $s2;
    }
}

function get_stock_price_from_source($stock, $source)
{
    if ($source == 'google')
    {
        /*
            http://finance.google.com/finance/info?client=ig&q=NASDAQ%3aGOOG
            http://finance.google.com/finance/info?client=ig&q=GOOG
        */
        $url = 'http://finance.google.com/finance/info?client=ig&q='.$stock;
        $content = file_get_contents($url);
        $res = json_decode(str_replace('// ', '', $content), true);
        return $res[0]['l_cur'];
    }
    elseif ($source == 'yahoo')
    {
        /*
            http://download.finance.yahoo.com/d/quotes.csv?s=%40%5EDJI,GOOG&f=nsl1op&e=.csv
            http://download.finance.yahoo.com/d/quotes.csv?s=GOOG&f=nsl1op&e=.csv
        */
        $url = 'http://download.finance.yahoo.com/d/quotes.csv?s='.$stock.'&f=nsl1op&e=.csv';
        $content = file_get_contents($url);
        $data = explode(',', $content);
        return $data[2];
    }
    elseif ($source == 'yql_yahoo')
    {
        /* http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quote%20where%20symbol%20in%20(%22YHOO%22%2C%22AAPL%22%2C%22GOOG%22%2C%22MSFT%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback= http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quote%20where%20symbol%20in%20(%22GOOG%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=
        */
        $url = 'http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.quote%20where%20symbol%20in%20(%22'.$stock.'%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';
        $content = file_get_contents($url);
        $res = json_decode($content, true);
        return $res['query']['results']['quote']['LastTradePriceOnly'];
    }
    elseif ($source == 'yql_google')
    {
        /* http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20google.igoogle.stock%20where%20stock%20in%20('GOOG')%3B&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=
        */
        $url = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20google.igoogle.stock%20where%20stock%20in%20('".$stock."')%3B&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=";
        $content = file_get_contents($url);
        $res = json_decode($content, true);
        return $res['query']['results']['xml_api_reply']['finance']['last']['data'];
    }
    
    // http://finance.yahoo.com/d/quotes.csv?s=STOCK_SYMBOLS separated by Ò+Ó &f= special_tags
}

?>