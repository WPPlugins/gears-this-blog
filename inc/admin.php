<?php

add_action('publish_post', 'gearsthisblog_post_added');
add_action('deleted_post', 'gearsthisblog_post_added');
add_action('publish_page', 'gearsthisblog_post_added');

/**
 * Set default options on installation
 */
function gearsthisblog_install() {
      gearsthisblog_reset();
}

/**
 * Reset to default options values
 */
function gearsthisblog_reset() {

	$default = array(
		'version'	=> time(),
		'config'	=> array(
			'title'					=> 'Gears This Blog',
			'message'				=> __("Save this blog's content on your computer with Google Gears' help.
Simply download and install it and click Save. 
You will be able to see the blog when being offline !", 'gearsthisblog'),
			'message_allow'				=> __('Stock this blog content on your computer for offline navigation.', 'gearsthisblog'),
			'message_allow_icon'			=> '',
			'files'					=> '',
			'auto_add_css'				=> 'Yes',
			'auto_add_img'				=> 'Yes',	
			'auto_add_js'				=> 'Yes',
			'add_feed'				=> 'Yes',
			'add_cat'				=> 'Yes',
			'add_tags'				=> 'Yes',
			'posts_nb'				=> '-1'
		),
	);

	gearsthisblog_manifest_reset();
	save_js_vars($default);

	update_option( 'gearsthisblog', $default );
}


/**
 * Reset manifest file only
 */
function gearsthisblog_manifest_reset() {

        $f = fopen('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json', 'w');
        $defaultManifest = '{
  "betaManifestVersion": 1,
  "version": "'.time().'",
  "entries": [
          { "url": "'.WP_CONTENT_URL.'/plugins/gears-this-blog/js/go_offline.js"},
          { "url": "'.WP_CONTENT_URL.'/plugins/gears-this-blog/css/style.css"},
          { "url": "'.WP_CONTENT_URL.'/plugins/gears-this-blog/images/valid.png"},
          { "url": "'.WP_CONTENT_URL.'/plugins/gears-this-blog/images/error.png"},
          { "url": "'.WP_CONTENT_URL.'/plugins/gears-this-blog/js/gears_init.js"},
          { "url": "'.get_bloginfo('url').'", "redirect": "'.get_bloginfo('url').'/"},
          { "url": "'.get_bloginfo('url').'/"}
    ]
}';
        file_put_contents('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json', $defaultManifest);
        fclose($f);

}


/**
 * The widget controls
 */
function gearsthisblog_control() {
	printf( __( "To configure the widget please go to the <a href=\"%s\" >settings page</a>.", 'gearsthisblog' ), get_bloginfo( 'url' ) . '/wp-admin/options-general.php?page=gearsthisblog' );
}


/**
 * Add item menu
 */
function gearsthisblog_add_pages() {
        $page = add_options_page(__( 'Gears This Blog', 'gearsthisblog' ), __( 'Gears This Blog', 'gearsthisblog' ), 10, 'gearsthisblog', 'gearsthisblog_options_page');

	add_action( 'admin_head-' . $page, 'gearsthisblog_css_admin' );

	// Add icon
	add_filter( 'ozh_adminmenu_icon_gearsthisblog', 'gearsthisblog_icon' );
}

/**
 * Return admin menu icon
 */
function gearsthisblog_icon() {
	static $icon;
	if(!$icon) {
		$icon = WP_CONTENT_URL . '/plugins/gears-this-blog/images/admin_icon.png';
	}
	return $icon;
}

/**
 * Load admin CSS style
 */
function gearsthisblog_css_admin() { ?>
	<link rel="stylesheet" href="<?php echo get_bloginfo( 'home' ) . '/' . PLUGINDIR . '/gears-this-blog/css/admin.css' ?>" type="text/css" media="all" /> <?php
}


/**
 * The configuration page
 */
function gearsthisblog_options_page() {
	gearsthisblog_load_translation_file();
	#echo '<pre>'; var_dump( $_POST ); echo '</pre>'; echo '<hr>';
	if ( current_user_can( 'manage_options' ) ) { 
		if ( $_POST['config'] ) {
			#function_exists( 'check_admin_referer ') ? check_admin_referer( 'gearsthisblog' ) : null;
			$nonce = $_POST['_wpnonce'];
			if ( !wp_verify_nonce( $nonce, 'gearsthisblog-config') ) die( 'Security check' );
			$option = get_option( 'gearsthisblog' );
			$option['config'] = $_POST['config'];
			$option['config']['message'] = stripslashes($option['config']['message']);
			update_option( 'gearsthisblog', $option );
			$config = $option['config'];
			build_manifest($config);
			save_js_vars($config);
		}
		elseif ( $_POST['reset'] ) {
			function_exists( 'check_admin_referer ') ? check_admin_referer( 'gearsthisblog' ) : null;
			$nonce = $_POST['_wpnonce'];
			if ( !wp_verify_nonce( $nonce, 'gearsthisblog-reset') ) die( 'Security check' );
			gearsthisblog_reset();
			$option = get_option( 'gearsthisblog' );
			$config = $option['config'];
			save_js_vars($config);
		}
		$option = get_option( 'gearsthisblog' );
		$config = $option['config']; ?>

		<div id="gearsthisblog" class="wrap" >
			<div id="icon-options-general" class="icon32"><br/></div>
			<h2><?php _e( 'Gears This Blog Configuration', 'gearsthisblog' ) ?></h2>
			<?php //require_once( 'nkuttler.php' ); ?>
			<?php //nkuttler0_2_1_links( 'gearsthisblog' ) ?>
			<form action="" method="post">
				<?php function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'gearsthisblog-config' ) : null; ?>
				<?php gearsthisblog_input( __( 'Title', 'gearsthisblog' ), 'title', 20, $config['title'], __('Widget title', 'gearsthisblog')) ?> 
				<br />
                                <?php gearsthisblog_textarea(__( 'Message', 'gearsthisblog' ), 'message', $config['message'], __('Message that will be displayed to the user' , 'gearsthisblog')) ?>
				<br />
				<?php gearsthisblog_textarea( __( 'Authorization message', 'gearsthisblog' ), 'message_allow', $config['message_allow'], __('Message that will be displayed for Gears authorization', 'gearsthisblog')) ?>
				<br />
				<?php gearsthisblog_input( __( 'Icon', 'gearsthisblog' ), 'message_allow_icon', 50, $config['message_allow_icon'], __('Authorization message icon', 'gearsthisblog')) ?>				
				<br />

				<p>				
                                <?php gearsthisblog_select( __( 'Auto add CSS files ?', 'gearsthisblog' ), 'auto_add_css', array('No', 'Yes')) ?>                                
				<?php gearsthisblog_select( __( 'Auto add JS files ?', 'gearsthisblog' ), 'auto_add_js', array('No', 'Yes')) ?>
				<br />
                                <?php gearsthisblog_select( __( 'Auto add images files ?', 'gearsthisblog' ), 'auto_add_img', array('No', 'Yes')) ?>
				<?php gearsthisblog_select( __( 'Add feed URLs ?', 'gearsthisblog' ), 'add_feed', array('No', 'Yes')) ?>
				<br />
                                <?php gearsthisblog_select( __( 'Add categories ?', 'gearsthisblog' ), 'add_cat', array('No', 'Yes')) ?>
                                <?php gearsthisblog_select( __( 'Add tags ?', 'gearsthisblog' ), 'add_tags', array('No', 'Yes')) ?>
				<br />
				<?php gearsthisblog_textarea( __( 'Files', 'gearsthisblog' ), 'files', $config['files'], __('Files or url you want to add to the manifest file / one by line', 'gearsthisblog')) ?>
				</p>

				<p>
                                <?php gearsthisblog_input( __( 'Number of posts to add ', 'gearsthisblog' ), 'posts_nb', 4, $config['posts_nb'], __('latest posts (-1 will add all posts)', 'gearsthisblog')) ?>
				</p>

				<br />
				<?php gearsthisblog_submit( __( 'Save changes', 'gearsthisblog' ) ) ?>
				</form>

	                        <form action="" method="post" >
                       	        <?php function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'gearsthisblog-reset' ) : null; ?>
               	                <input type="hidden" name="reset" value="ihatephp" />
                                <?php gearsthisblog_submit( __( 'Reset settings', 'gearsthisblog' ), 'button-secondary', __('/!\\ Will also reset manifest file /!\\' , 'gearsthisblog')) ?>
	                        </form>

				<br /><br />
				<?php gearsthisblog_textarea(__( 'Manifest Content', 'gearsthisblog' ), 'files',  get_manifest_content(), __('Content of the manifest file used to list files.', 'gearsthisblog'), true) ?>
				<br />
				gearsThisBlogManifest.json
				<br /><?php echo __('permissions', 'gearsthisblog') ?> : <?php echo file_permissions('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json'); ?>
				<br /><?php echo __('chmod', 'gearsthisblog')?> :  <?php echo substr(sprintf('%o', fileperms('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json')), -4); ?>
		</div> <?php
	}
}

/**
 * Prints a <select> and <option>s
 */
function gearsthisblog_select( $title, $name, $choices, $legend = '') {
	print $title; ?>
	<select name="config[<?php echo $name; ?>]"  ><?php
		// FIXME $option or selected should be passed as a parameter
		$option = get_option( 'gearsthisblog' );
		$select = $option['config'][$name];
		foreach ( $choices as $choice ) {
			if ( $choice == $select ) {
				echo "<option value=\"$choice\" selected>" . __( $choice, 'gearsthisblog' ) . "</option>\n";
			}
			else {
				echo "<option value=\"$choice\" >" . __( $choice, 'gearsthisblog' ) . "</option>\n";
			}
		} ?>
	</select> <?php
	if ($br) {
		echo '<br />';
	} 
	if($legend != '') {
		echo '<small>'.$legend.'</small><br />';
	}
}

/**
 * Prints an input field
 */
function gearsthisblog_input( $title, $name, $size, $value, $legend = '' ) { ?>
	<?php echo $title ?>
	<input name="config[<?php echo $name ?>]" type="text" size="<?php echo $size ?>" value="<?php echo $value ?>" /> <?php
	if ($legend != '') echo '<small>'.$legend.'</small>';
	if ($br) echo '<br />';
}

/**
 * Prints a textarea field
 */
function gearsthisblog_textarea($title, $name, $value, $legend = '', $disabled = false) { ?>
	<?php echo $title ?>
	<br />
	<textarea name="config[<?php echo $name ?>]" rows="8" cols="70" <?php if($disabled) echo "disabled"; ?> ><?php echo $value; ?></textarea>
	<br />
	<small><?php echo $legend; ?></small>
	<br />
	<?php
}

/**
 * Prints a submit button
 */
function gearsthisblog_submit( $value, $class = 'button-primary', $legend = '' ) { ?>
	<input type="submit" class="<?php echo $class ?>" value="<?php echo $value ?>" /> <?php if($legend != '') echo '<small>'.$legend.'</small>';
}


/**
 * Rebuild manifest file
 */
function build_manifest($config) {
	gearsthisblog_manifest_reset();
	$json = json_decode(get_manifest_content());
	$posts = get_posts_array($config['posts_nb']);
	$files = array(); 
	$filters = array();
	if($config['auto_add_css'] == 'Yes') {
		$filters[] = 'css';
	}
	if($config['auto_add_img'] == 'Yes') {
		$filters = array_merge($filters, array('png', 'gif', 'jpeg', 'jpg', 'bmp'));
        }
	if($config['auto_add_js'] == 'Yes') {
		$filters[] = 'js';
        }
	if($config['add_feed'] == 'Yes') {
		if(!in_array((object)get_bloginfo('rss_url'), $json->entries))
			$files = array_merge($files, array(get_bloginfo('rss_url'), get_bloginfo('rss2_url'), get_bloginfo('atom_url') ));
	}	
	if($config['add_cat'] == 'Yes') {
		$files = array_merge($files, get_cats_array());
	}	
	if($config['add_tags'] == 'Yes') {
		$files = array_merge($files, get_tags_array());
	}

	if($config['files'] != '') {
		$files = array_merge($files, explode('<br />', nl2br($config['files'])));
	}

	$files = array_merge($files, scan_wp_content($files, $filters));
	$files = array_unique($files);
	//echo '<pre>';   var_dump($files); echo '</pre><hr />';
	foreach($files as $file) {
                $entry = array('url' => $file);
                $json->entries[] = (object)$entry;
        }

        foreach($posts as $post_id => $permalink) {
                $entry = array('url' => $permalink);
                if(!in_array((object)$entry, $json->entries)) $json->entries[] = (object)$entry;
        }
	
	$json->version = (string)time();

	$f = fopen('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json', 'w+');
	file_put_contents('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json', json_encode($json));
	fclose($f);
	//echo '<pre>';	var_dump($json); echo '</pre><hr />';
}

/**
 * Scan wp-content folder to retrieve files
 */ 
function scan_wp_content(&$files, $filters) {
	if(!empty($filters)) browseDir('../wp-content', $files, $filters);
	return $files;
}
	
function browseDir($path, &$files, $filters)
{
	if($f = opendir($path)) {
		while($file = readdir($f)) {
			if($file != '.' && $file != '..') {
				if(is_dir($path.'/'.$file)) browseDir($path.'/'.$file, $files, $filters);
				else {
					$extension=substr(strrchr($path.'/'.$file,'.'),1) ; 
					if(in_array(strtolower($extension), $filters)) 
						$files[] = str_replace('../wp-content', WP_CONTENT_URL, $path.'/'.$file);
				}				
			}
		}
	}
}

/**
 * Write JS vars
 */
function save_js_vars($config) {
	$content = 'var STORE_BLOG_NAME = \''.get_bloginfo('name').'\';
var STORE_MESSAGE = \''.$config['message_allow'].'\';
var STORE_MESSAGE_ICON = \''.$config['message_allow_icon'].'\';';

	$f = fopen('../wp-content/plugins/gears-this-blog/js/vars.js', 'w');
	file_put_contents('../wp-content/plugins/gears-this-blog/js/vars.js',$content);
	fclose($f);
}

/**
 * Retrieve all posts as an array
 */
function get_posts_array($nb = 0) {
	$posts = array();
	if($nb == 0) return $posts;
        
        $myposts = ($nb == -1) ? get_posts() : get_posts($nb);

	foreach($myposts as $post) {
	   $posts[$post->ID] = get_permalink($post->ID); 
	}

	foreach(get_pages() as $page) {
	   $posts[$page->ID] = get_page_link($page->ID);
	}

	return $posts;
}

/**
 * Retrive all categories as an array
 */
function get_cats_array() {
	$cats = array();
	$args=array(
	  'orderby' => 'name',
	  'order' => 'ASC'
  	);
	foreach(get_categories($args) as $category) {
		$cats[] = get_category_link( $category->term_id );
	}
	return $cats;
}

/**
 * Retrieve all tags as an array
 */
function get_tags_array() {
	$tags = array();
	$posttags = get_the_tags();
	if($posttags)
	foreach ($posttags as $tag)
		$tags[] = get_tag_link($tag->term_id);
	return $tags;
}

/**
 * Retrive manifest file content
 */
function get_manifest_content() {
	try {
		$f = fopen('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json', 'r');
		$content = file_get_contents('../wp-content/plugins/gears-this-blog/gearsThisBlogManifest.json');
		fclose($f);
		return $content;
	} catch(Exception $e) {
		return 'Error while opening file : '.$e->getMessage();
	}
}


/**
 * On post added re-build manifest
 */
function gearsthisblog_post_added($id) {
	$option = get_option( 'gearsthisblog' );
	build_manifest($option['config']);
}


/**
 * Load Translations
 */
function gearsthisblog_load_translation_file() {
	$plugin_path = plugin_basename( dirname( __FILE__ ) .'/../translations' );
	load_plugin_textdomain( 'gearsthisblog', '', $plugin_path );
}

/**
 * File permissions
 */
function file_permissions($file) {

$perms = fileperms($file);

if (($perms & 0xC000) == 0xC000) {
    // Socket
    $info = 's';
} elseif (($perms & 0xA000) == 0xA000) {
    // Lien symbolique
    $info = 'l';
} elseif (($perms & 0x8000) == 0x8000) {
    // Régulier
    $info = '-';
} elseif (($perms & 0x6000) == 0x6000) {
    // Block special
    $info = 'b';
} elseif (($perms & 0x4000) == 0x4000) {
    // Dossier
    $info = 'd';
} elseif (($perms & 0x2000) == 0x2000) {
    // Caractère spécial
    $info = 'c';
} elseif (($perms & 0x1000) == 0x1000) {
    // pipe FIFO
    $info = 'p';
} else {
    // Inconnu
    $info = 'u';
}

// Autres
$info .= (($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

// Groupe
$info .= (($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

// Tout le monde
$info .= (($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

return $info;
}
?>
