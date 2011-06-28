<?php
/**
 * @package SimpleCMSHacks
 * @author Jacob Buck
 */

/*
Plugin Name: Simple CMS Hacks
Description: This handy little plugin hacks, cracks and phreaks some of the core features and settings in Wordpress to make it a more suitable content management system. User's discretion is advised.
Author: Jacob Buck
Author URI: http://jacobbuck.co.nz/
Version: 2.4.1
*/

/** 
 * Start Plugin Configuration
 */

class SimpleCMS {
	
	// Public Variables
	
	public $options;
	
	// Private Variables
	
	private $default_options = array(
		'disable_posts' => false,
		'disable_links' => true,
		'disable_comments' => false,
		'dash_drafts' => false,
		'dash_quickpress' => false,
		'dash_feeds' => true,
		'dash_comments' => false,
		'dash_incoming' => false,
		'dash_rightnow' => false,
		'menu_pagesabove' => true,
		'menu_hidetools' => false,
		'menu_renameposts' => false,
		'menu_renameposts_name' => 'Posts',
		'menu_renameposts_singular' => 'Post',
		'posts_hideauthor' => false,
		'posts_hidecategories' => false,
		'posts_hidetags' => false,
		'editor_hidecustomfields' => false,
		'editor_hideexcerpt' => false,
		'editor_hidetrackbacks' => false,
		'core_adminbar' => false,
		'core_updates' => true,
		'core_flashupload' => false,
	);
	
	// Public Funtions
	
	public function __construct () {
		// get latest settings
		$this->options = json_decode(get_option('simple_cms'),true);
	}
	
	public function init () {
		// get latest settings
		$this->options = json_decode(get_option('simple_cms'),true);
		// settings page submit
		if (isset($_GET['page']) && $_GET['page'] == 'simple-cms' &&
			(isset($_POST['sch_submit']) || isset($_POST['sch_reset']) || isset($_POST['sch_maintenance'])) ) {
			// save new settings
			if (isset($_POST['sch_submit'])) {
				$new_options = array(
					'disable_posts' => ($_POST['disable_posts']) ? true : false,
					'disable_links' => ($_POST['disable_links']) ? true : false,
					'disable_comments' => ($_POST['disable_comments']) ? true : false,
					'dash_drafts' => ($_POST['dash_drafts']) ? true : false,
					'dash_quickpress' => ($_POST['dash_quickpress']) ? true : false,
					'dash_feeds' => ($_POST['dash_feeds']) ? true : false,
					'dash_comments' => ($_POST['dash_comments']) ? true : false,
					'dash_incoming' => ($_POST['dash_incoming']) ? true : false,
					'dash_rightnow' => ($_POST['dash_rightnow']) ? true : false,
					'menu_pagesabove' => ($_POST['menu_pagesabove']) ? true : false,
					'menu_hidetools' => ($_POST['menu_hidetools']) ? true : false,
					'menu_renameposts' => ($_POST['menu_renameposts']) ? true : false,
					'menu_renameposts_name' => ($_POST['menu_renameposts_name']) ? $_POST['menu_renameposts_name'] : 'Posts',
					'menu_renameposts_singular' => ($_POST['menu_renameposts_singular']) ? $_POST['menu_renameposts_singular'] : 'Post',
					'posts_hideauthor' => ($_POST['posts_hideauthor']) ? true : false,
					'posts_hidecategories' => ($_POST['posts_hidecategories']) ? true : false,
					'posts_hidetags' => ($_POST['posts_hidetags']) ? true : false,
					'editor_hidecustomfields' => ($_POST['editor_hidecustomfields']) ? true : false,
					'editor_hideexcerpt' => ($_POST['editor_hideexcerpt']) ? true : false,
					'editor_hidetrackbacks' => ($_POST['editor_hidetrackbacks']) ? true : false,
					'core_adminbar' => ($_POST['core_adminbar']) ? true : false,
					'core_updates' => ($_POST['core_updates']) ? true : false,
					'core_flashupload' => ($_POST['core_flashupload']) ? true : false,
				);			
				update_option('simple_cms',json_encode($new_options));
				// redirect
				header("Location: ".get_bloginfo('url')."/wp-admin/options-general.php?page=simple-cms&settings-updated=true#settings");
			}
			// reset to default settings
			if (isset($_POST['sch_reset'])) {
				update_option('simple_cms',json_encode($this->default_options));
				// redirect
				header("Location: ".get_bloginfo('url')."/wp-admin/options-general.php?page=simple-cms&settings-updated=true#settings");
			}
			// run maintenance
			if (isset($_POST['sch_maintenance'])) {
				// delete post revisions
				if (isset($_POST['delete_revisions']) && $_POST['delete_revisions']) {
					self::delete_revisions();
				}
				// empty post trash
				if (isset($_POST['delete_trash']) && $_POST['delete_trash']) {
					self::delete_trash();
				}
				// buld edit posts
				if (
					(isset($_POST['bulk_comments']) && $_POST['bulk_comments']) ||
					(isset($_POST['bulk_pingbacks']) && $_POST['bulk_pingbacks'])
				) {
					if ($_POST['bulk_comments'] == 'closed') $new_values['comment_status'] = 'closed';
					if ($_POST['bulk_comments'] == 'open') $new_values['comment_status'] = 'open';
					if ($_POST['bulk_pingbacks'] == 'closed') $new_values['ping_status'] = 'closed';
					if ($_POST['bulk_pingbacks'] == 'open') $new_values['ping_status'] = 'open';
					self::bulk_edit($new_values);
				}
				// redirect
				header("Location: ".get_bloginfo('url')."/wp-admin/options-general.php?page=simple-cms&settings-updated=maintenance#maintenance");
			}
		}
		
	}
	
	public function install () {
		if (! get_option('simple_cms')) {
			add_option('simple_cms',json_encode($ths->default_options),'','yes');
		}
	}
	
	public function add_settings_link ($links, $file) {
		if ($file == 'simple-cms/index.php') {
			$links[] = '<a href="options-general.php?page=simple-cms">'.__('Settings').'</a>';
		}
		return $links;
	}
	
	public function add_settings_submenu () {
		add_submenu_page('options-general.php', 'Simple CMS', 'Simple CMS', 'manage_options', 'simple-cms', array($this,'settings_page'));
	}
	
	public function settings_page () {
		// get settings
		$options = $this->options;
		// display page
		include('settings.php');
	}
	
	// Private Functions
	
	private function delete_revisions () {
		$all_posts = new WP_Query('post_status=any&post_type=revision&posts_per_page=-1');
		foreach ($all_posts->posts as $post) {
			 wp_delete_post($post->ID,true);
		}
	}
	
	private function delete_trash () {
		$all_posts = new WP_Query('post_status=trash&post_type=any&posts_per_page=-1');
		foreach ($all_posts->posts as $post) {
			 wp_delete_post($post->ID,true);
		}
	}
	
	private function bulk_edit ($new_values) {
		$all_posts = new WP_Query('post_status=any&post_type=any&posts_per_page=-1');
		foreach ($all_posts->posts as $post) {
			$update_post = $new_values;
			$update_post['ID'] = $post->ID;
			wp_update_post($update_post);
		}
	}
	
}

$simple_cms = new SimpleCMS;

add_action('admin_menu', array($simple_cms,'add_settings_submenu'));
add_action('init', array($simple_cms,'init'));
add_filter('plugin_action_links', array($simple_cms,'add_settings_link'), 10, 2 );

register_activation_hook(__FILE__,array($simple_cms,'install'));

/* 
 * End Plugin Configuration
 **/

/** 
 * Start Plugin Actions
 */
 
/**
 * Always Use Flash Uploader 
 */

if ($simple_cms->options['core_flashupload']) {
	add_action("init","dfu_check");
	add_filter("image_upload_iframe_src","dfu_new");
	add_filter("video_upload_iframe_src","dfu_new");
	add_filter("audio_upload_iframe_src","dfu_new");
	function dfu_new($result) {
	    $result.="&flash=1";
	    return $result;
	}
	function dfu_check (){
	    $file=$_SERVER['REQUEST_URI'];
	    if ($file == '/wp-admin/media-new.php') {
	        header("Location: " . $file . "?flash=1");
	    }
	}
}

/** 
 * Disable Core and Plugin Updates
 */

if ($simple_cms->options['core_updates']) {
	add_filter('pre_site_transient_update_core',create_function('$a',"return null;"));
	remove_action('load-update-core.php','wp_update_plugins');
	add_filter('pre_site_transient_update_plugins',create_function('$a',"return null;"));
}

/** 
 * Remove Dashboard Widgets
 */

add_action('wp_dashboard_setup','remove_dashboard_widgets');
function remove_dashboard_widgets () {
	global $wp_meta_boxes, $simple_cms;
	// recent drafts
	if ($simple_cms->options['dash_drafts'] || $simple_cms->options['disable_posts']) {
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	}
	// quickpress
	if ($simple_cms->options['dash_quickpress']) {
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	}
	// feeds
	if ($simple_cms->options['dash_feeds']) {
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	}
	// recent comments
	if ($simple_cms->options['dash_comments'] || $simple_cms->options['disable_comments']) {
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	}
	// incoming links
	if ($simple_cms->options['dash_incoming']) {
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	}
	// right now
	if ($simple_cms->options['dash_rightnow']) {
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	}
}

/** 
 * Remove Admin Bar
 */

if ($simple_cms->options['core_adminbar']) {
	add_filter('show_admin_bar','__return_false');
	add_action('admin_print_styles','hide_admin_bar_prefs');
	function hide_admin_bar_prefs () {
		echo "<style type=\"text/css\">.show-admin-bar { display: none; }</style>\n";
	}
}

/** 
 * Rename Posts
 */

if ($simple_cms->options['menu_renameposts'] && !$simple_cms->options['disable_posts']) {

	add_action('init','rename_post_object_label');
	function rename_post_object_label() {
		global $wp_post_types, $simple_cms;
		$posts_name = $simple_cms->options['menu_renameposts_name'];
		$posts_singular = $simple_cms->options['menu_renameposts_singular'];
		
		$labels = &$wp_post_types['post']->labels;
		$labels->name = $posts_name;
		$labels->singular_name = $posts_singular;
		$labels->add_new = "Add New";
		$labels->add_new_item = "Add New $posts_singular";
		$labels->edit_item = "Edit $posts_singular";
		$labels->new_item = "New $posts_singular";
		$labels->view_item = "View $posts_singular";
		$labels->search_items = "Search $posts_name";
		$labels->not_found = "No ".strtolower($posts_name)." found";
		$labels->not_found_in_trash = "No ".strtolower($posts_name)." found in Trash";
		$labels->menu_name = $posts_name;
	}

}

/** 
 * Modify items in the Admin Navigation Menu.
 */

add_action('admin_menu','simple_admin_menu');
function simple_admin_menu () {
	global $menu, $submenu, $simple_cms;
	
	// config
	
	$hide = array();
	$move = array();
	
	// hide
	
	$hide[] = 'plugin-install.php';
	
	if ($simple_cms->options['disable_posts']) {
		$hide[] = 'edit.php';
	}
	if ($simple_cms->options['disable_links']) {
		$hide[] = 'link-manager.php';
	}
	if ($simple_cms->options['disable_comments']) {
		$hide[] = 'edit-comments.php';
		$hide[] = 'options-discussion.php';
	}
	if ($simple_cms->options['core_updates']) {
		$hide[] = 'update-core.php';
	}
	if ($simple_cms->options['menu_hidetools']) {
		$hide[] = 'tools.php';
	}
	if ($simple_cms->options['posts_hidecategories']) {
		$hide[] = 'edit-tags.php?taxonomy=category';
	}
	if ($simple_cms->options['posts_hidetags']) {
		$hide[] = 'edit-tags.php?taxonomy=post_tag';
	}	
	
	// move
	
	if ($simple_cms->options['menu_pagesabove']) {
		$move[5] = 6;
		$move[20] = 5;
	}
	
	// renaming
	
	if ($simple_cms->options['menu_renameposts'] && !$simple_cms->options['disable_posts']) {
		$posts_name = $simple_cms->options['menu_renameposts_name'];
		$posts_singular = $simple_cms->options['menu_renameposts_singular'];
		$menu[5][0] = $posts_name;
		$submenu['edit.php'][5][0] = $posts_name;
		$submenu['edit.php'][10][0] = "Add $posts_singular";
		$submenu['edit.php'][16][0] = "$posts_singular Tags";
	}
		
	// hiding
	
	foreach ($menu as $key => $item){
		if (in_array($item[2],$hide)) {
			unset($menu[$key]);
			unset($submenu[$item[2]]);
		}
		if (count($submenu[$item[2]])) {
			foreach($submenu[$item[2]] as $key => $value){
				if (in_array($value[2],$hide)) {
					unset($submenu[$item[2]][$key]);
				}
			}
		}	
	}
	
	// moving
	
	foreach ($move as $key => $item) {
		if (isset($menu[$key])) {
			$menu[$item] = $menu[$key];
			unset($menu[$key]);
		}
	}
	
}

/** 
 * Modify items in the Admin Bar.
 */

add_action('wp_before_admin_bar_render','simple_admin_bar');
function simple_admin_bar () {
	global $wp_admin_bar, $simple_cms;
	if ($simple_cms->options['disable_posts']) {
		unset($wp_admin_bar->menu->{'new-content'}['children']->{'new-post'});
	}
	if ($simple_cms->options['disable_comments']) {
		unset($wp_admin_bar->menu->comments);
	}
	// remove advanced custom fields
	if (isset($wp_admin_bar->menu->{'new-content'}['children']->{'new-acf'})) {
		unset($wp_admin_bar->menu->{'new-content'}['children']->{'new-acf'});
	}
}

/** 
 * Remove Items from Edit and Manage Screen Options
 */

add_filter("manage_posts_columns", "remove_columns");
add_filter("manage_pages_columns", "remove_columns");
add_filter("manage_upload_columns", "remove_columns");

function remove_columns ($columns) {
	global $simple_cms;
	if ($simple_cms->options['disable_comments']) {
		unset($columns['comments']);
	}
	if ($simple_cms->options['posts_hideauthor']) {
		unset($columns['author']);
	}
	if ($simple_cms->options['posts_hidecategories']) {
		unset($columns['categories']);
	}
	if ($simple_cms->options['posts_hidetags']) {
		unset($columns['tags']);
	}
	return $columns;
}

add_action('admin_menu','remove_postboxes');

function remove_postboxes () {
	global $simple_cms;
	if ($simple_cms->options['disable_comments']) {
		remove_meta_box('commentsdiv', 'post', 'main');
		remove_meta_box('commentsdiv', 'page', 'main');
		remove_meta_box('commentstatusdiv', 'post', 'main'); 
		remove_meta_box('commentstatusdiv', 'page', 'main'); 
	}
	if ($simple_cms->options['posts_hideauthor']) {
		remove_meta_box('authordiv', 'post', 'side'); 
		remove_meta_box('authordiv', 'page', 'side'); 
	}
	if ($simple_cms->options['posts_hidecategories']) {
		remove_meta_box('categorydiv', 'post', 'side'); 
	}
	if ($simple_cms->options['posts_hidetags']) {
		remove_meta_box('tagsdiv-post_tag', 'post', 'side'); 
	}
	if ($simple_cms->options['editor_hidecustomfields']) {
		remove_meta_box('postcustom', 'post', 'normal'); 
		remove_meta_box('postcustom', 'page', 'normal'); 
	}
	if ($simple_cms->options['editor_hideexcerpt']) {
		remove_meta_box('postexcerpt', 'post', 'normal'); 
		remove_meta_box('postexcerpt', 'page', 'normal'); 
	}
	if ($simple_cms->options['editor_hidetrackbacks']) {
		remove_meta_box('trackbacksdiv', 'post', 'normal'); 
		remove_meta_box('trackbacksdiv', 'page', 'normal'); 
	}
}

/** 
 * Favorite Actions Menu (2.7 - 3.1 only)
 * Modify items in the Favorite Actions Menu.
 */

add_action('favorite_actions','simple_favorite_actions');

function simple_favorite_actions ($actions) {
	global $simple_cms;
	if ($simple_cms->options['disable_posts']) {
		unset($actions['post-new.php']);
		unset($actions['edit.php?post_status=draft']);
	}
	if ($simple_cms->options['disable_comments']) {
		unset($actions['edit-comments.php']);
	}
	return $actions;
}

/* 
 * End Plugin Actions
 **/

/** 
 * Start Plugin Extra Actions
 * These are non configurable functions which run as a part of the plugin.
 */

/** 
 * Additional TinyMCE Configurations
 */

function change_mce_options ($init) {
	// Remove crap when pasted in
	$init['paste_auto_cleanup_on_paste'] = true;
	$init['paste_remove_spans'] = true;
	$init['paste_remove_styles'] = true;
	$init['paste_remove_styles_if_webkit'] = true;
	return $init;
}
add_filter('tiny_mce_before_init','change_mce_options');

/** 
 * Remove Header Generator 
 */

remove_action('wp_head','wp_generator');

/** 
 * Replace Domain In Content
 * Works in conjunction with the Domain Switcher in wp-config.php.
 */

if (defined('DOMAIN_LIST')) {
	add_filter("the_content", "replace_domain_content");
	function replace_domain_content ($content) {
		$domains = explode("|",DOMAIN_LIST);
		$new_domain = $_SERVER['HTTP_HOST'];
		$content = str_replace($domains,$new_domain,$content);
		return $content;
	}	
}

/* 
 * End Plugin Extra Actions
 **/

/* :) */