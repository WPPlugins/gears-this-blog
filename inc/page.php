<?php


$option = get_option( 'gearsthisblog' );
$config = $option['config'];
add_action('wp_head', 'gearsthisblog_head');

/**
 * The widget
 */
function widget_gearsthisblog($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;
	$option = get_option('gearsthisblog');
	$config = $option['config'];
	echo $config['title'];
	echo $after_title;
	echo gearsthisblog_the_widget($config);
	echo $after_widget;
}

/**
 * Output the widget as configured
 */
function gearsthisblog_the_widget($config) {
	$message = $config['message'];

        $retour = "
        <div id=\"gearsThisBlog\">

        <p>".nl2br($message)."</p>

          <p id=\"gearsThisBlogButtons\">
               <button class=\"gearsThisBlogButton\" onclick=\"createStore()\" > Save  </button>
               <button class=\"gearsThisBlogButton\" onclick=\"removeStore()\" > Erase </button>
          </p>

        <p id=\"gearsThisBlogOut\"></p>
        </div>";

        return $retour;
}


/**
 * Print the default CSS styles in the <head>
 */
function gearsthisblog_head() { ?>
<link rel="stylesheet" type="text/css" href="<?php echo bloginfo('url');  ?>/wp-content/plugins/gears-this-blog/css/style.css" />
<script type="text/javascript" src="<?php echo bloginfo('url');  ?>/wp-content/plugins/gears-this-blog/js/gears_init.js"></script>
<script type="text/javascript" src="<?php echo bloginfo('url');  ?>/wp-content/plugins/gears-this-blog/js/vars.js"></script>
<script type="text/javascript" src="<?php echo bloginfo('url');  ?>/wp-content/plugins/gears-this-blog/js/go_offline.js"></script>
<?php
}

/**
 * Print the link to the plugin page
*/
function gearsthisblog_footer() {
	_e('<a href="http://www.yriase.fr/">Gears This Blog</a>', 'gearsthisblog' );
	echo '<br />';
}

?>
