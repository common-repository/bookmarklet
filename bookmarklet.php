<?php
/*
Plugin Name: Bookmarklet
Plugin URI:
Description: bookmarklet adds a (X)HTML compliant list of social bookmarking icons to each of your posts. See <a href="options-general.php?page=bookmarklet.php">configuration panel</a> for more settings.
Version: 1.1.1
Author: Mohammad Shabani
Author URI: http://caspisky.blogspot.com/
Author MAIL: caspisky@ymail.com


*/

define('PLUGINNAME','Bookmarklet');
define('OPTIONS','Bookmarklet');
define('vNum','1.1.1');
define('PLUGPATH',get_option('siteurl').'/wp-content/plugins/'.plugin_basename(dirname(__FILE__)).'/');

require_once('bookmarks-data.php');

//add defaults to an array
$plugopts = array(
	'position' => 'below', // below, above, or manual
	'reloption' => 'nofollow', // 'nofollow', or ''
	'targetopt' => 'blank', // 'blank' or 'self'
	'bgimg' => 'top', // 'bookmarklet' or 'caring'
	'shorty' => 'rims',
	'pageorpost' => '',
	'bookmark' => array_keys($bookmarks),
	'xtrastyle' => '',
	'feed' => '1', // 1 or 0
);


//add 2 db
add_option(OPTIONS, $plugopts);

//reload
$plugopts = get_option(OPTIONS);

//add sidebar link to settings page
function menu_link() {
	if (function_exists('add_options_page')) {
		add_options_page('Bookmarklet', 'Bookmarklet', 8, basename(__FILE__), 'settings_page');
	}
}

function network_input_select($name, $hint) {
	global $plugopts;
	return sprintf('<label class="%s" title="%s"><input %sname="bookmark[]" type="checkbox" value="%s" /></label>',
		$name,
		$hint,
		@in_array($name, $plugopts['bookmark'])?'checked="checked" ':"",
		$name
	);
}

//write settings page
function settings_page() {
	global $plugopts, $bookmarks, $_POST;
	$status_message = "";
	$error_message = "";
	if(isset($_POST['save_changes'])) {
		$status_message = 'Settings saved!';
		
		//was there an error?
		if($_POST['position'] == '') {		
			$error_message = 'Please choose where you would like the menu to be displayed.';
		}
		elseif($_POST['bookmark'] == '') {
			$error_message = 'You can\'t display the menu if you don\'t choose a few sites to add to it!';
		}
		elseif($_POST['pageorpost'] == '') {
			$error_message = 'Please choose where you want the menu displayed.';
		}
		else {
			$plugopts['position'] = $_POST['position'];
			$plugopts['xtrastyle'] = $_POST['xtrastyle'];
			$plugopts['reloption'] = $_POST['reloption'];
			$plugopts['targetopt'] = $_POST['targetopt'];
			$plugopts['bookmark'] = $_POST['bookmark'];
			$plugopts['shorty'] = $_POST['shorty'];
			$plugopts['pageorpost'] = $_POST['pageorpost'];
			$plugopts['twittid'] = $_POST['twittid'];
			$plugopts['addthisid'] = $_POST['addthisid'];
			$plugopts['bgimg'] = $_POST['bgimg'];
			$plugopts['feed'] = $_POST['feed'];
			update_option(OPTIONS, $plugopts);
		}
	}

	//if there was an error,
	//display it in that fancy fading div
	if($error_message != '') {
		echo '<div id="message" class="error" style="margin-top:25px;"><p>'.$error_message.'</p></div>';
	}
	elseif($status_message != '') {
		echo '<div id="message" class="updated fade" style="margin-top:25px;"><p>'.$status_message.'</p></div>';
	}
?>



<h2 class="bookmarklet">Bookmarklet</h2>
<div class="wrapit">

	 <form name="bookmarklet-bookmarks" id="bookmarklet-bookmarks" action="" method="post">
		<div class="in1">
			<label for="xtrastyle" class="title">You can style the DIV that holds the menu here:</label>
			<textarea id="xtrastyle" name="xtrastyle" cols="52" rows="8" style="font-family:monospace;font-weight:bold;font-size:15px;padding:5px;"><?php 
$default_bookmarklet = "margin:20px 0 0 0 !important;\npadding:23px 0 0 10px !important;\nheight:29px;/*the height of the icons (29px)*/\ndisplay:block !important;\nclear:both !important;";
if (!empty($plugopts['xtrastyle'])) {
	echo $plugopts['xtrastyle']; 
} 
elseif ( empty($plugopts['xtrastyle']) || $plugopts['xtrastyle'] == 'Array' )
	echo $default_bookmarklet;
else {
	echo "If you see this message, please delete the contents of this textarea and click \"Save Changes\".";
}
?></textarea>
		</div>
		<div class="in1"><span class="title"><label for="twittid">Twitter ID:</label></span>
			<div class="in2">
				<input type="text" id="twittid" name="twittid" value="<?php echo $plugopts['twittid']; ?>" />
			</div>

		</div>
		<div class="in1"><span class="title"><label for="addthisid">AddThis ID:</label></span>
			<div class="in2">
				<input type="text" id="addthisid" name="addthisid" value="<?php echo $plugopts['addthisid']; ?>" />
			</div>

		</div>
		<div class="in1"><span class="title">Menu Location:</span>
			<div class="in2">			
				<label><input <?php echo (($plugopts['position'] == "above")? 'checked="checked"' : ""); ?> name="position" id="position-above" type="radio" value="above" />
				Above each post</label>
				<br />
				<label><input <?php echo (($plugopts['position'] == "below")? 'checked="checked"' : ""); ?> name="position" id="position-below" type="radio" value="below" />
				Below each post</label>
				<br />
			    <label><input <?php echo (($plugopts['position'] == "manual")? 'checked="checked"' : ""); ?> name="position" id="position-manual" type="radio" value="manual" />
				Manually insert</label>
			</div>
		</div>
		<div class="in1"><span class="title">Add nofollow to the links?</span>
			<div class="in2">
				<label><input <?php echo (($plugopts['reloption'] == "nofollow")? 'checked="checked"' : ""); ?> name="reloption" id="reloption" type="radio" value="nofollow" /> Yes</label>
				<label><input <?php echo (($plugopts['reloption'] == "")? 'checked="checked"' : ""); ?> name="reloption" id="reloption" type="radio" value="" /> No</label>
			</div>
		</div>
		<div class="in1"><span class="title">Which URL Shortening Service?</span>
			<div class="in2">
				<select name="shorty" id="shorty">
				 <option <?php echo (($plugopts['shorty'] == "rims")? 'selected="selected"' : ""); ?> value="rims">http://ri.ms</option>
				 <option <?php echo (($plugopts['shorty'] == "tinyarrow")? 'selected="selected"' : ""); ?> value="tinyarrow">http://tinyarro.ws</option>
				 <option <?php echo (($plugopts['shorty'] == "cligs")? 'selected="selected"' : ""); ?> value="cligs">http://cli.gs</option>
				 <option <?php echo (($plugopts['shorty'] == "tiny")? 'selected="selected"' : ""); ?> value="tiny">http://tinyurl.com</option>
				 <option <?php echo (($plugopts['shorty'] == "snip")? 'selected="selected"' : ""); ?> value="snip">http://snipr.com</option>
				</select>
			</div>
		</div>
		<div class="in1" title="Now the plugin supports insertion on your site's main page for those of you who use themes that post the entire content of posts on the homepage."><span class="title">Posts, pages, or the whole shebang?</span>
			<div class="in2">

				<label><input <?php echo (($plugopts['pageorpost'] == "post")? 'checked="checked"' : ""); ?> name="pageorpost" id="pageorpost-post" type="radio" value="post" /> Posts</label><br />
				<label><input <?php echo (($plugopts['pageorpost'] == "page")? 'checked="checked"' : ""); ?> name="pageorpost" id="pageorpost-page" type="radio" value="page" /> Pages</label><br />
				<label><input <?php echo (($plugopts['pageorpost'] == "index")? 'checked="checked"' : ""); ?> name="pageorpost" id="pageorpost-index" type="radio" value="index" /> Index</label><br />
				<label><input <?php echo (( $plugopts['pageorpost'] == "pagepost")? 'checked="checked"' : ""); ?> name="pageorpost" id="pageorpost-pagepost" type="radio" value="pagepost" /> Posts &amp; Pages</label><br />
				<label><input <?php echo (( $plugopts['pageorpost'] == "postindex")? 'checked="checked"' : ""); ?> name="pageorpost" id="pageorpost-postindex" type="radio" value="postindex" /> Posts &amp; Index</label><br />
				<label><input <?php echo (( $plugopts['pageorpost'] == "pageindex")? 'checked="checked"' : ""); ?> name="pageorpost" id="pageorpost-pageindex" type="radio" value="pageindex" /> Pages &amp; Index</label><br />
				<label><input <?php echo (( $plugopts['pageorpost'] == "postpageindex")? 'checked="checked"' : ""); ?> name="pageorpost" id="pageorpost-all" type="radio" value="postpageindex" /> All</label>
			</div>
		</div>

		<div class="in1"><span class="title">Open links in new window?</span>
			<div class="in2">
				<label><input <?php echo (($plugopts['targetopt'] == "_blank")? 'checked="checked"' : ""); ?> name="targetopt" id="targetopt-blank" type="radio" value="_blank" /> Yes</label>
				<label><input <?php echo (($plugopts['targetopt'] == "_self")? 'checked="checked"' : ""); ?> name="targetopt" id="targetopt-self" type="radio" value="_self" /> No</label>
			</div>
		</div>

		<div class="iconator" id="iconator">
			<h2>Preferred Networks: (drag to reorder)</h2><?
foreach ($plugopts['bookmark'] as $name) print network_input_select($name, $bookmarks[$name]['check']);
$unused_networks=array_diff(array_keys($bookmarks), $plugopts['bookmark']);
foreach ($unused_networks as $name) print network_input_select($name, $bookmarks[$name]['check']);
?>
		</div>
		<div class="clear"></div>
		<div class="in1"><span class="title">Which background image would you like to use?</span>
			<div class="in2">
				<label class="bgimg"><input <?php echo (($plugopts['bgimg'] == "bottom")? 'checked="checked"' : ""); ?> id="bgimg-bookmarklet" name="bgimg" type="radio" value="bottom" />
				<span class="share-bookmarklet"> </span></label>
				<br />
				<label class="bgimg"><input <?php echo (($plugopts['bgimg'] == "top")? 'checked="checked"' : ""); ?> id="bgimg-caring" name="bgimg" type="radio" value="top" />
				<span class="share-care"> </span></label>
				<br />
				<label class="bgimg"><input <?php echo (($plugopts['bgimg'] == "none")? 'checked="checked"' : ""); ?> id="bgimg-none" name="bgimg" type="radio" value="none" /></label>
			</div>
		</div>
		<div class="in1"><span class="title">Feed Settings</span>
			<div class="in2">			
				<label><input <?php echo (($plugopts['feed'] == "1")? 'checked="checked"' : ""); ?> name="feed" id="feed-show" type="radio" value="1" />
				Show in feed</label>
				<br />
				<label><input <?php echo (($plugopts['feed'] == "0" || empty($plugopts['feed']))? 'checked="checked"' : ""); ?> name="feed" id="feed-hide" type="radio" value="0" />
				Hide in feed</label>
			</div>
		</div>
		<div class="in1">
			<input type="hidden" name="save_changes" value="1" />
			<div class="submit"><input type="submit" value="Save Changes" /></div>
		</div>
	</form>
</div>

<?php

}//closing brace for function "settings_page"


function get_fetch_url() {
	global $post, $plugopts;
	$perms=get_permalink();
	
	// which short url service should be used?
	if($plugopts['shorty'] == "rims") {
		$first_url = "http://ri.ms/api-create.php?url=".$perms;
	} elseif($plugopts['shorty'] == "tinyarrow") {
		$first_url = "http://tinyarro.ws/api-create.php?url=".$perms;
	} elseif($plugopts['shorty'] == "cligs") {
		$first_url = "http://cli.gs/api/v1/cligs/create?url=".$perms;
	} elseif($plugopts['shorty'] == "tiny") {
		$first_url = "http://tinyurl.com/api-create.php?url=".$perms;
	} elseif($plugopts['shorty'] == "snip") {
		$first_url = "http://snipr.com/site/snip?&r=simple&link=".$perms;
	} else {
		$first_url = "http://e7t.us/create.php?url=".$perms;
	}
	
	$fetch_url=get_post_meta($post->ID, '_bookmarklet_shortUrl', true);
	if (!empty($fetch_url) && md5($perms)==get_post_meta($post->ID, '_bookmarklet_permaHash', true)) {
		// no curl fetch neccessary.
	} else { //fetch and store
		// retrieve the shortened URL
		if (function_exists('curl_init')) {
			// Use cURL
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_URL, $first_url);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
			curl_setopt($ch, CURLOPT_TIMEOUT, 3);
			$fetch_url = curl_exec($ch);
			curl_close($ch);
			
		} elseif (function_exists('file_get_contents')) { // use file_get_contents()
			$fetch_url = file_get_contents($first_url);
		} else {
			$fetch_url='';
		}

		if ($fetch_url) { // remote call made and was successful
			$fetch_url=trim($fetch_url);
			// add/update values
			// tries updates first, then add if field does not already exist
			if (!update_post_meta($post->ID, '_bookmarklet_shortUrl', $fetch_url)) {
				add_post_meta($post->ID, '_bookmarklet_shortUrl', $fetch_url);
			}
			if (!update_post_meta($post->ID, '_bookmarklet_permaHash', md5($perms))) {
				add_post_meta($post->ID, '_bookmarklet_permaHash', md5($perms));
			}
		} else { // failed. use permalink.
			$fetch_url=$perms;
		}
	}
	return $fetch_url;
}


//create an auto-insertion function
function position_menu($post_content) {
	global $plugopts;
	
	// if user selected manual positioning, get out.
	if ($plugopts['position']=='manual') return $post_content;
	
	// decide whether or not to generate the bookmarks.
	if ((is_single() && false!==strpos($plugopts['pageorpost'],"post")) ||
		(is_page() && false!==strpos($plugopts['pageorpost'],"page")) ||
		(is_home() && false!==strpos($plugopts['pageorpost'],"index")) ||
		(is_feed() && !empty($plugopts['feed']))
	) { // socials should be generated and added
		$socials=get_bookmarklet();
	}
	
	// place of bookmarks and return w/ post content.
	if (empty($socials)) {
		return $post_content;
	} elseif ($plugopts['position']=='above') {
		return $socials.$post_content;
	} elseif ($plugopts['position']=='below') {
		return $post_content.$socials;
	} else { // some other unexpected error, don't do anything. return.
		error_log("an error occurred in Bookmarklet");
		return $post_content;
	}
}
//end position_menu...

function bookmark_list_item($name, $opts=array()) {
	global $plugopts, $bookmarks;
	
	$url=$bookmarks[$name]['baseUrl'];
	$share=$bookmarks[$name]['share'];
	foreach ($opts as $key=>$value) {
		$url=str_replace(strtoupper($key), $value, $url);
	}
	
	return sprintf(
		'<li class="%s"><a href="%s" target="%s" rel="%s" title="%s"></a></li>',
		$name,
		$url,
		$plugopts['targetopt'],
		$plugopts['reloption'],
		$share,
		$bookmarks[$name],
		$bookmarks[$name]
	);
}

function get_bookmarklet() {
	global $plugopts;
	
	$title = urlencode(get_the_title());
	$title = str_replace('%3A',':',$title);
	$title = str_replace('%3F','?',$title);
	$perms = get_permalink();
    $short_title = substr($title, 0, 60)."...";
	$bookmarklet_content = urlencode(strip_tags(substr(get_the_content(), 0, 220)."[..]"));
	$post_summary = stripslashes($bookmarklet_content);
	$site_name = get_bloginfo('name');
	$bookmarklet_teaser = strip_tags(substr(get_the_content(), 0, 250)."[..]");
	$strip_teaser = strip_tags(stripslashes($bookmarklet_teaser));
	$mail_subject = urldecode(substr($title, 0, 60)."...");

	// select the background
	if($plugopts['bgimg'] == 'top') {
		$bgchosen = "background:url(".PLUGPATH."images/share-love-hearts.png) no-repeat left top;";
	} elseif($plugopts['bgimg'] == 'bottom') {
		$bgchosen = "background:url(".PLUGPATH."images/share-enjoy.png) no-repeat left top;";
	} elseif($plugopts['bgimg'] == 'none') {
		$bgchosen = "background:none;";
	}
	
	// do not add inline styles to the feed.
	$style=is_feed()?'':' style="'.__($bgchosen).__($plugopts['xtrastyle']).'"';
	//write the menu
	$socials = '<div class="bookmarklet-bookmarks"'.$style.'><ul class="socials">';
	foreach ($plugopts['bookmark'] as $name) {
		if ($name=='bookmarklet-twitter') {
			$socials.=bookmark_list_item($name, array(
				'post_by'=>(!empty($plugopts['twittid']))?"RT+@".$plugopts['twittid'].":+":'',
				'short_title'=>$title,
				'fetch_url'=>get_fetch_url(),
			));
		} elseif ($name=='bookmarklet-addthis') {
			$socials.=bookmark_list_item($name, array(
				'addthis_id'=>$plugopts['addthisid'],
				'permalink'=>$perms,
				'title'=>$title,
			));
		} elseif ($name=='bookmarklet-mail') {
			$socials.=bookmark_list_item($name, array(
				'mail_subject'=>$mail_subject,
				'strip_teaser'=>$strip_teaser,
				'permalink'=>$perms,
			));
		} elseif ($name=='bookmarklet-linkedin') {
			$socials.=bookmark_list_item($name, array(
				'post_summary'=>$post_summary,
				'site_name'=>$site_name,
				'permalink'=>$perms,
				'title'=>$title,
			));
		} elseif ($name=='bookmarklet-devmarks') {
			$socials.=bookmark_list_item($name, array(
				'post_summary'=>$post_summary,
				'permalink'=>$perms,
				'title'=>$title,
			));
		}elseif ($name=='bookmarklet-orkut') {
			$socials.=bookmark_list_item($name, array(
				'post_summary'=>$post_summary,
				'permalink'=>$perms,
				'title'=>$title,
			));
		} else {
			$socials.=bookmark_list_item($name, array(
				'permalink'=>$perms,
				'title'=>$title,
			));
		}
	}
	$socials.='</ul></div>';

	return $socials;
}

// This function is what allows people to insert the menu wherever they please rather than above/below a post...
function selfserv_bookmarklet() {
	echo get_bookmarklet();
}

//write the <head> code
add_action('wp_head', 'add_bookmarklet_public');
function add_bookmarklet_public() {
	echo "\n\n".'<!-- Start Of Code Generated By bookmarklet '.vNum.' -->'."\n";
	wp_register_style('bookmarklet-bookmarks', PLUGPATH.'css/style.css', false, vNum, 'all');
	wp_print_styles('bookmarklet-bookmarks');
	echo '<!-- End Of Code Generated By bookmarklet '.vNum.' -->'."\n\n";
}


//styles for admin area
add_action('admin_head', 'bookmarklet_admin');
function bookmarklet_admin() {
	wp_register_style('bookmarklet-bookmarks', PLUGPATH.'css/admin-style.css', false, vNum, 'all');
	wp_register_script('bookmarklet-bookmarks-js', PLUGPATH.'bookmarklet.js', array('jquery-ui-sortable'), vNum);
	wp_print_styles('bookmarklet-bookmarks');
	wp_print_scripts('bookmarklet-bookmarks-js');
}



//add a sidebar menu link
//hook the menu to "the_content"
add_action('admin_menu', 'menu_link');
add_filter('the_content', 'position_menu');
?>
