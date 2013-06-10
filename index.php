<? session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <link href="css/jplayer.morning.light.css" rel="stylesheet" type="text/css" />
        <link href="css/jquery-ui-1.10.3.custom.min.css" rel="stylesheet" type="text/css" />
        
	<link rel="stylesheet" type="text/css" href="css/style.css">
        <!--<link href="http://jplayer.org/latest/skin/pink.flag/jplayer.pink.flag.css" rel="stylesheet" type="text/css" />--> 
        
        <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-migrate-1.1.1.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.3.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.jplayer.min.js"></script>
        <script type="text/javascript" src="js/jquery.jplayer.inspector.js"></script>
        <script type="text/javascript" src="js/jplayer.playlist.min.js"></script>
        <script type="text/javascript" src="js/json2.js"></script>
        <!--<script type="text/javascript" src="js/jquery.mockjax.js"></script>-->
        <!--<script type="text/javascript" src="js/emethplayer_mockjax.js"></script>-->
        <script type="text/javascript" src="js/icanhaz.js"></script>
        <script type="text/javascript" src="js/emethplayer.js"></script>
        
<script type="text/javascript" id="js_css">
function calc_content_height() {
        var window_height = $(window).height();
        var foot_height = $('div.footer').outerHeight();
        var head_height = $('div.header').outerHeight();
        var content_height = window_height - foot_height - head_height;

        $('div.content').css({height: content_height});
        $('div.right_slider').css({height: window_height});
}
$(document).ready(function () {
	var window_height = $(window).height();
	var foot_height = $('div.footer').outerHeight();
	var head_height = $('div.header').outerHeight();
	var content_height = window_height - foot_height - head_height;

	$('div.content').css({height: content_height});
	$('div.right_slider').css({height: window_height});
	$('.right_slider').click(function () {
		$(this).parent().parent().toggleClass('wide');
	})
	$(window).resize(function () {
		calc_content_height();
	});
})
</script>
        
<script type="text/javascript">
    
$(document).ready(function() {
    get_logged_in_user();
    // http://www.jplayer.org/latest/developer-guide/
    // http://www.jplayer.org/latest/quick-start-guide/
    
    $("#jquery_jplayer_1").jPlayer({
        ready: function(event) {
            
        },
        playing: function(event) {
            //console.log(event.jPlayer.status)
        },
        timeupdate: function(event) {
            curr_playing['current_playing_time'] = parseInt(event.jPlayer.status.currentTime);
            curr_playing['total_playing_time'] = parseInt(event.jPlayer.status.duration); //curr_playing['total_playing_time'] = parseInt(jQuery(this).data().jPlayer.status.duration);
            update_current();
        },
        swfPath: "js",
        supplied: "mp3"
    });
    $("#jplayer_inspector").jPlayerInspector({jPlayer:$("#jquery_jplayer_1")});
    
    get_available_tracks();
});

</script>
<style>
#bottom_bar {
    position: fixed;
    z-index: 100; 
    bottom: 0; 
    left: 0;
    width: 100%;
}
        </style>
    </head>
    <body class="wide">
        
        
        
        
        
        
        
	<div class="left floats">
		<div class="header">
			links
			<span class="rightbutton">
                            <span class='logged_in' style='display:none;'>You are logged in as <span class='username_display'></span>.</span>
                            <span class='logged_out' style='display:block;'>You are not logged in.</span>
			</span>
		</div>
		<div class="content">
			<div class="inner">
				<div id='available_tracks'></div>
                                <div id="jplayer_inspector"></div>
			</div>
		</div>
		<div class="footer">
                    <table>
                        <tr>
                            <td> 
                                <div id="jquery_jplayer_1" class="jp-jplayer"></div>
                        
                                <div id="jp_container_1" class="jp-audio">
                                    <div class="jp-type-playlist">
                                        <div class="jp-gui jp-interface">
                                            <ul class="jp-controls">
                                                <li><a href="javascript:;" class="jp-previous" tabindex="1">previous</a></li>
                                                <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                                                <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                                                <li><a href="javascript:;" onclick="next_track();" class="jp-next" tabindex="1">next</a></li>
                                                <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
                                                <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
                                                <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
                                                <li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
                                            </ul>
                                            <div class="jp-progress">
                                                <div class="jp-seek-bar">
                                                    <div class="jp-play-bar"></div>
                        
                                                </div>
                                            </div>
                                            <div class="jp-volume-bar">
                                                <div class="jp-volume-bar-value"></div>
                                            </div>
                                            <div class="jp-current-time"></div>
                                            <div class="jp-duration"></div>
                                        </div>
                                        <div class="jp-no-solution">
                                            <span>Update Required</span>
                                            To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span id='curr_title' style="font-weight:bold;">None</span><br />
                                <span id='curr_description'></span><br />
                                by <span id='curr_speaker'></span> on <span id='curr_date'></span><br />
                                <a id='curr_church_website' href=""><span id='curr_church'></span></a>
                                <span id='curr_total_plays'></span> plays.<br />
                            </td>
                        </tr>
                    </table>    
		</div>
	</div>
	<div class="right floats">
		<div class="right_slider">
                    <table id='playlist'>
                        
                    </table>
                </div>
	</div>
        
        
        
        
        
<!--


                   


<br /><br />

<span class='logged_out' style='display:block;'>
    <hr />
    <h2>Login</h2>
    Email: <input type='text' id='login_email' onkeypress="if(event.keyCode==13) {login();}"><br />
    Password: <input type='password' id='login_pass' onkeypress="if(event.keyCode==13) {login();}"><br />
    <button onclick="login()">Submit</button><br /><br />
    
    <hr />
    <h2>Register</h2>
    Email: <input type='text' id='register_email' onkeypress="if(event.keyCode==13) {register();}"><br />
    Password: <input type='password' id='register_pass1' onkeypress="if(event.keyCode==13) {register();}"><br />
    Password Again: <input type='password' id='register_pass2' onkeypress="if(event.keyCode==13) {register();}"><br />
    <button onclick="register()">Submit</button><br /><br />
</span>

<span class='logged_in' style='display:none;'>
    <hr />
    <button onclick="logout()">Logout</button><br /><br />
</span>
-->

<script type="text/html" id="available_track_template">
<table data-url="{{ file_loc }}">
    <tr>
        <td><h4 onclick="add_to_playlist('{{ title }}', '{{ file_loc }}', '{{ id }}', '{{ author_name }}', '{{ sermon_timestamp }}')">[Add to Playlist] {{ author_name }}: {{ title }}</h4></td>
        <td>{{ scripture }}</td>
    </tr>
    <tr>
        <td colspan=2>{{ description }}<br />Date: {{ sermon_timestamp }}</td>
    </tr>
    <tr>
        <td><a target="_blank" href='{{ church_website }}'>{{ church }}</a></td><td>Plays: {{ plays }}</td><td><a href='{{ file_loc }}'>Download</a></td>
    </tr>
</table>
</script>
<script type="text/html" id="playlist_track">
    <tr class="playlist_{{ id }}">
        <td class="playlist_td" data-url="{{ file_loc }}" data-id="{{ id }}"><h4>[<a href="javascript:void" onclick="remove_from_playlist('{{ id }}')">X</a>] {{ author_name }}: {{ title }}</h4></td> <td colspan=2>Date: {{ sermon_timestamp }}</td><td></td>
    </tr>
</script>

</body>
</html>