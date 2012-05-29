<?php
/*
Plugin Name: Control Freak 
Plugin URI: https://github.com/jacobbuck/wp-control-freak
Description: A handy little plugin which tweaks some of the core features and settings in WordPress to make it more suitable for your needs.
Author: Jacob Buck
Author URI: http://jacobbuck.co.nz/
Version: 3.1 alpha 3
*/

class ControlFreak {
		
	private $backup_vars;       // Keep a backup of pre-modified global vars
	private $options;
	private $default_supports;  // Default post supports
	private $default_dashboard; // Default dashboard widgets
	private $default_widgets;   // Default widgets
	public $version = 3.1;
	
	/* Construct */
	
	public function __construct () {
		// Activate
		$this->activate();
		// Store defaults
		$this->default_supports = array(
			"title" => "Title",
			"editor" => "Editor",
			"author" => "Author",
			"excerpt" => "Excerpt",
			"trackbacks" => "Trackbacks",
			"custom-fields" => "Custom Fields",
			"comments" => "Comments",
			"revisions" => "Revisions",
			"page-attributes" => "Page Attributes"
		);
		$this->default_dashboard = array(
			"right_now" => "Right Now", 
			"recent_comments" => "Recent Comments", 
			"incoming_links" => "Incoming Links", 
			"plugins" => "Plugins", 
			"quick_press" => "QuickPress", 
			"recent_drafts" => "Recent Drafts", 
			"primary" => "WordPress Blog", 
			"secondary" => "Other WordPress News"
		);
		$this->default_widgets = array(
			"WP_Widget_Pages" => "Pages", 
			"WP_Widget_Calendar" => "Calendar", 
			"WP_Widget_Archives" => "Archives", 
			"WP_Widget_Links" => "Links", 
			"WP_Widget_Meta" => "Meta", 
			"WP_Widget_Search" => "Search", 
			"WP_Widget_Text" => "Text", 
			"WP_Widget_Categories" => "Categories", 
			"WP_Widget_Recent_Posts" => "Recent Posts", 
			"WP_Widget_Recent_Comments" => "Recent Comments", 
			"WP_Widget_RSS" => "RSS", 
			"WP_Widget_Tag_Cloud" => "Tag Cloud", 
			"WP_Nav_Menu_Widget" => "Custom Menu"
		);		
		// Actions
		add_action("init", array($this, "init"));
		add_action("admin_menu", array($this, "admin_menu"));
		add_action("wp_dashboard_setup", array($this, "wp_dashboard_setup"));
		add_filter("tiny_mce_before_init", array($this, "tiny_mce_before_init"));
		add_action("wp_before_admin_bar_render", array($this, "wp_before_admin_bar_render"));
		add_action("widgets_init", array($this, "widgets_init"));
		// Filters
		add_filter("intermediate_image_sizes_advanced", array($this, "intermediate_image_sizes_advanced"));
		add_filter("plugin_action_links", array($this, "add_settings_link"), 10, 2);
		add_filter("user_has_cap", array($this, "user_has_cap"), 10, 3);
		add_filter("quick_edit_dropdown_pages_args", array($this, "show_all_in_parent_dropdown"));
		add_filter("page_attributes_dropdown_pages_args", array($this, "show_all_in_parent_dropdown"));
	}
	
	/* Init */
	
	public function init () {
		global $wp_post_types, $wp_taxonomies, $pagenow;
		// Get The Latest Options
		$this->options = get_option("controlfreak");
		// Backup $wp_post_types and $wp_taxonomies
		$this->backup_vars["wp_post_types"] = $wp_post_types;
		$this->backup_vars["wp_taxonomies"] = $wp_taxonomies;
		// Add theme enabeled post supprts
		if (current_theme_supports("post-formats")) {
			$this->default_supports["post-formats"] = "Post Formats";
		}
		if (current_theme_supports("post-thumbnails")) {
			$this->default_supports["thumbnail"] = "Thumbnail";
		}
		// Remove defaults on posts disable
		if ($this->options["post_types"]["post"]["enabled"] == "off") {
			unset($this->default_dashboard["quick_press"]);
			unset($this->default_widgets["WP_Widget_Archives"]);
			unset($this->default_widgets["WP_Widget_Recent_Posts"]);
		}
		// Remove defaults on pages disable
		if ($this->options["post_types"]["page"]["enabled"] == "off") {
			unset($this->default_widgets["WP_Widget_Pages"]);
		}
		// Remove defualts on post tags disable
		if ($this->options["taxonomies"]["post_tag"]["enabled"] == "off") {
			unset($this->default_widgets["WP_Widget_Tag_Cloud"]);
		}
		// Remove defaults on links disable
		if ($this->options["links"]["enabled"] == "off") {
			unset($this->default_widgets["WP_Widget_Links"]);
		}
		// Remove defaults on comments disable
		if ($this->options["comments"]["enabled"] == "off") {
			unset($this->default_supports["comments"]);
			unset($this->default_dashboard["recent_comments"]);
			unset($this->default_widgets["WP_Widget_Recent_Comments"]);
		}
		// Posts Types
		foreach (array("post", "page") as $post_type) {
			if ($this->options["post_types"][$post_type]["enabled"] == "on") {
				if ($this->options["post_types"][$post_type]["supports"] != "off") {
					$supports = $this->options["post_types"][$post_type]["supports"];
					foreach ($this->default_supports as $name => $title) {
						if (! post_type_supports($post_type, $name)) {
							if (in_array($name, $supports))
								add_post_type_support($post_type, $name);
						} else {
							if (! in_array($name, $supports))
								remove_post_type_support($post_type, $name);
						}					
					}
				}
				if ($this->options["comments"]["enabled"] == "off") {
					remove_post_type_support($post_type, "comments");
				}
				$wp_post_types[$post_type]->public = $this->options["post_types"][$post_type]["public"] == "on";
				$wp_post_types[$post_type]->show_ui = true;
				$wp_post_types[$post_type]->show_in_nav_menus = $this->options["post_types"][$post_type]["show_in_nav_menus"] == "on";
				$wp_post_types[$post_type]->exclude_from_search = $this->options["post_types"][$post_type]["show_in_search"] != "on";
			} else {
				$wp_post_types[$post_type]->public = false;
				$wp_post_types[$post_type]->publicly_queryable = false;
				$wp_post_types[$post_type]->show_ui = false;
				$wp_post_types[$post_type]->show_in_nav_menus = false;
				$wp_post_types[$post_type]->show_in_menu = false;
				$wp_post_types[$post_type]->show_in_admin_bar = false;
				$wp_post_types[$post_type]->exclude_from_search = true;
			}
		}	
		// Taxonomies
		foreach (array("category", "post_tag") as $taxonomy) {
			if ($this->options["taxonomies"][$taxonomy]["enabled"] == "on") {
				$wp_taxonomies[$taxonomy]->public = $this->options["taxonomies"][$taxonomy]["public"] == "on";
				$wp_taxonomies[$taxonomy]->show_ui = true;
				$wp_taxonomies[$taxonomy]->show_in_nav_menus = $this->options["taxonomies"][$taxonomy]["show_in_nav_menus"] == "on";
				foreach ($wp_post_types as $type_key => $type_obj) {
					if ($type_key == "post" || $type_key == "page" || ! $type_obj->_builtin) { 
						if (in_array($type_key, $wp_taxonomies[$taxonomy]->object_type) && ! in_array($type_key, $this->options["taxonomies"][$taxonomy]["post_types"])) {
							foreach ($wp_taxonomies[$taxonomy]->object_type as $key => $value) {
								if ($value == $type_key)
									unset($wp_taxonomies[$taxonomy]->object_type[$key]);
							}
						} else if (! in_array($type_key, $wp_taxonomies[$taxonomy]->object_type) && in_array($type_key, $this->options["taxonomies"][$taxonomy]["post_types"])) {
							register_taxonomy_for_object_type($taxonomy, $type_key);
						}
					}
				}
			} else {
				$wp_taxonomies[$taxonomy]->public = false;
				$wp_taxonomies[$taxonomy]->show_in_nav_menus = false;
				$wp_taxonomies[$taxonomy]->show_ui = false;
				$wp_taxonomies[$taxonomy]->object_type = array();
			}
		}
		// Comments
		if ($this->options["comments"]["enabled"] == "off") {
			wp_deregister_script("comment-reply");
			if ($pagenow == "edit-comments.php") {
				wp_die(__("You do not have sufficient permissions to access this page."));
			}
		}
		// Media
		if ($this->options["comments"]["enabled"] == "off" || (post_type_supports("attachment", "comments") && ! in_array("comments", $this->options["media"]["supports"]))) {
			remove_post_type_support("attachment", "comments");
		}
		// Front End
		if ($this->options["frontend"]["head_remove"]["admin_bar_bump"] == "on") {
			remove_action("wp_head", "_admin_bar_bump_cb");
		}
		if ($this->options["frontend"]["head_remove"]["remotepub"] == "on") {
			remove_action("wp_head", "rsd_link");
			remove_action("wp_head", "wlwmanifest_link");
		}
		if ($this->options["frontend"]["head_remove"]["rssfeeds"] == "on") {
			remove_action("wp_head", "feed_links_extra", 3);
			remove_action("wp_head", "feed_links", 2);
			add_action("do_feed", array($this, "disable_our_feeds"), 1);
			add_action("do_feed_rdf", array($this, "disable_our_feeds"), 1);
			add_action("do_feed_rss", array($this, "disable_our_feeds"), 1);
			add_action("do_feed_rss2", array($this, "disable_our_feeds"), 1);
			add_action("do_feed_atom", array($this, "disable_our_feeds"), 1);
		}
		if ($this->options["frontend"]["head_remove"]["postrel"] == "on") {
			remove_action("wp_head", "index_rel_link");
			remove_action("wp_head", "parent_post_rel_link", 10, 0);
			remove_action("wp_head", "start_post_rel_link", 10, 0);
			remove_action("wp_head", "adjacent_posts_rel_link", 10, 0);
			remove_action("wp_head", "adjacent_posts_rel_link_wp_head", 10, 0);
		}
		if ($this->options["frontend"]["head_remove"]["generator"] == "on") {
			remove_action("wp_head", "wp_generator");
		}
		// Administraton		
		if ($this->options["admin"]["advanced"]["disable_updates"] == "on") {
			add_filter("pre_site_transient_update_core", create_function('$a', "return null;"));
			remove_action("load-update-core.php", "wp_update_plugins");
			add_filter("pre_site_transient_update_plugins", create_function('$a', "return null;"));
		}
		// Settings Page Init
		$this->settings_init();
	}
	
	/* Disable DSS Feeds */
	
	function disable_our_feeds () {
		wp_die( __("<strong>Error:</strong> No RSS Feed Available, Please visit our <a href=\"". site_url("/") ."\">home page</a>."));
	}
	
	/* Admin Menu */
	
	public function admin_menu () {
		global $menu, $submenu;
		// Backup $menu and $submenu
		$this->backup_vars["menu"] = $menu;
		$this->backup_vars["submenu"] = $submenu;
		// For Settings Page
		$this->add_settings_submenu();
		// Posts
		if ($this->options["post_types"]["post"]["enabled"] == "off")
			remove_menu_page("edit.php");
		// Pages
		if ($this->options["post_types"]["page"]["enabled"] == "off")
			remove_menu_page("edit.php?post_type=page");
		// Links
		if ($this->options["links"]["enabled"] == "off")
			remove_menu_page("link-manager.php");
		// Comments
		if ($this->options["comments"]["enabled"] == "off")
			remove_menu_page("edit-comments.php");
	}
	
	/* Dashboard Setup */
	
	public function wp_dashboard_setup () {
		global $wp_meta_boxes;
		// Backup $wp_meta_boxes
		$this->backup_vars["wp_meta_boxes"] = $wp_meta_boxes;
		// Administraton
		foreach (array("right_now", "recent_comments", "incoming_links", "plugins") as $name) {
			if (! in_array($name, $this->options["admin"]["dashboard_remove"]))
				unset($wp_meta_boxes["dashboard"]["normal"]["core"]["dashboard_$name"]);
		}
		foreach (array("quick_press", "recent_drafts", "primary", "secondary") as $name) {
			if (! in_array($name, $this->options["admin"]["dashboard_remove"]))
				unset($wp_meta_boxes["dashboard"]["side"]["core"]["dashboard_$name"]);
		}
		if ($this->options["post_types"]["post"]["enabled"] == "off") {
			unset($wp_meta_boxes["dashboard"]["side"]["core"]["dashboard_quick_press"]);
		}
		if ($this->options["comments"]["enabled"] == "off") { 
			unset($wp_meta_boxes["dashboard"]["normal"]["core"]["dashboard_recent_comments"]);
		}
	}
	
	/* Tiny MCE Init */
	
	public function tiny_mce_before_init ($init) {
		if ($this->options["admin"]["advanced"]["tinymce_strictpasting"] == "on") {
			$init["paste_auto_cleanup_on_paste"] = true;
			$init["paste_remove_spans"] = true;
			$init["paste_remove_styles"] = true;
			$init["paste_remove_styles_if_webkit"] = true;
		}
		return $init;
	}
	
	/* Admin Bar */	
	
	public function wp_before_admin_bar_render () {
		global $wp_admin_bar;
		// Backup $wp_admin_bar
		$this->backup_vars["wp_admin_bar"] = $wp_admin_bar;
		// remove comments link
		if ($this->options["comments"]["enabled"] == "off") {
			$wp_admin_bar->remove_menu("comments");
		}
	}
	
	/* Widgets */
	
	public function widgets_init () {
		foreach ($this->default_widgets as $widget_name => $widget_title) {
			if (! in_array($widget_name, $this->options["admin"]["widgets_remove"]))
				unregister_widget($widget_name);
		}
		if ($this->options["post_types"]["post"]["enabled"] == "off") {
			unregister_widget("WP_Widget_Archives");
			unregister_widget("WP_Widget_Recent_Posts");
		}
		if ($this->options["post_types"]["page"]["enabled"] == "off") {
			unregister_widget("WP_Widget_Pages");
		}
		if ($this->options["taxonomies"]["post_tag"]["enabled"] == "off") {
			unregister_widget("WP_Widget_Tag_Cloud");
		}
		if ($this->options["links"]["enabled"] == "off") {
			unregister_widget("WP_Widget_Links");
		}
		if ($this->options["comments"]["enabled"] == "off") {
			unregister_widget("WP_Widget_Recent_Comments");
		}
	}
	
	/* Image Sizes */
	
	public function intermediate_image_sizes_advanced ($sizes) {
		foreach ($this->options["media"]["crop"] as $size) {
			if (isset($sizes[$size]))
				$sizes[$size]["crop"] = true;
		}
		return $sizes;
	}
	
	/* Parent Dropdown List */
	
	public function show_all_in_parent_dropdown ($args) {
		if ($this->options["admin"]["advanced"]["parent_dropdown_fix"]) {
			$args["post_status"] = array("publish", "pending", "draft", "private");
		}
		return $args;
	}
	
	/* Plugin Activation */
	
	public function activate () {
		$this->options = get_option("controlfreak");
		if (empty($this->options) || ! is_array($this->options)) {
			// Get default options
			$this->options = $this->filter_options(true);
			// Set defualt options
			delete_option("controlfreak");
			add_option("controlfreak", $this->options);
		}
	}
	
	/* Start Settings Page */
	
	private function settings_init () {
		// register plugin styles
		wp_register_style("control_freak_settings", plugins_url("settings.css", __FILE__));
		// save settings 
		if (isset($_POST["controlfreak"]["save"]) || isset($_POST["controlfreak"]["revert"]) || isset($_POST["controlfreak"]["import"]["save"])) {
			// get new options
			if (isset($_POST["controlfreak"]["save"])) { 
				// save
				$new_options = $this->filter_options(false, $_POST["controlfreak"]["options"]);
			} else if (isset($_POST["controlfreak"]["revert"])) {
				// default
				$new_options = $this->filter_options(true);
			} else if (isset($_POST["controlfreak"]["import"]["save"]) && isset($_POST["controlfreak"]["import"]["data"])) {
				// import
				$new_options = $this->filter_options(false, json_decode(stripslashes($_POST["controlfreak"]["import"]["data"]), true));
			}
			if (isset($new_options)) {
				// store new options
				update_option("controlfreak", $new_options);
				// update wp options
				if ($new_options["comments"]["enabled"] == "off") {
					update_option("default_comment_status", "closed");	
				}
			}
			wp_redirect(admin_url("/options-general.php?page=control-freak&settings-updated=true"));
		}
		if (! empty($_GET["settings-updated"]) && $_GET["settings-updated"] == "true") {
			add_action("all_admin_notices", array($this, "notice_settings_changed"));
		}
	}
	
	public function add_settings_link ($links, $file) {
		if (strstr(__FILE__, $file)) {
			$links[] = "<a href=\"".admin_url("/options-general.php?page=control-freak")."\">".__("Settings")."</a>";
		}
		return $links;
	}
	
	public function add_settings_submenu () {
		$page = add_submenu_page("options-general.php", "Control Freak", "Control Freak", "manage_options", "control-freak", array($this, "settings_page"));
		add_action("admin_print_styles-$page", array($this, "settings_page_styles"));
	}
	
	public function settings_page () {
		global $wp_post_types; 
		$options = $this->options;
		// display page
		include("settings.php");
	}
	
	public function settings_page_styles () {
		wp_enqueue_style("control_freak_settings");
	}
	
	/* Disallow Capabilities to Disabled */
	
	public function user_has_cap ($allcaps, $cap, $args) {
		if ($this->options["links"]["enabled"] == "off") {
			$allcaps["manage_links"] = false;
		}
		if ($this->options["comments"]["enabled"] == "off") {
			$allcaps["edit_comment"] = false;
			$allcaps["moderate_comments"] = false;
		}
		return $allcaps;
	}
	
	private function filter_options ($default = false, $input = false) {
		$filtered = array(
			"version" => $this->version,
			"post_types" => array(
				"post" => array(
					"enabled" => $default ? "on" : (! empty($input["post_types"]["post"]["enabled"]) ? "on" : "off"),
					"supports" => $default ? "off" : (! empty($input["post_types"]["post"]["supports"]) && is_array($input["post_types"]["post"]["supports"]) ? $input["post_types"]["post"]["supports"] : array()),
					"public" => $default ? "on" : (! empty($input["post_types"]["post"]["public"]) ? "on" : "off"),
					"show_in_search" => $default ? "on" : (! empty($input["post_types"]["post"]["show_in_search"]) ? "on" : "off"),
					"show_in_nav_menus" => $default ? "on" : (! empty($input["post_types"]["post"]["show_in_nav_menus"]) ? "on" : "off")
				),
				"page" => array(
					"enabled" => $default ? "on" : (! empty($input["post_types"]["page"]["enabled"]) ? "on" : "off"),
					"supports" => $default ? "off" : (! empty($input["post_types"]["page"]["supports"]) && is_array($input["post_types"]["page"]["supports"]) ? $input["post_types"]["page"]["supports"] : array()),
					"public" => $default ? "on" : (! empty($input["post_types"]["page"]["public"]) ? "on" : "off"),
					"show_in_search" => $default ? "on" : (! empty($input["post_types"]["page"]["show_in_search"]) ? "on" : "off"),
					"show_in_nav_menus" => $default ? "on" : (! empty($input["post_types"]["page"]["show_in_nav_menus"]) ? "on" : "off")
				)
			),
			"taxonomies" => array(
				"category" => array(
					"enabled" => $default ? "on" : (! empty($input["taxonomies"]["category"]["enabled"]) ? "on" : "off"),
					"post_types" => $default ? array("post") : (! empty($input["taxonomies"]["category"]["post_types"]) && is_array($input["taxonomies"]["category"]["post_types"]) ? $input["taxonomies"]["category"]["post_types"] : array()),
					"public" => $default ? "on" : (! empty($input["taxonomies"]["category"]["public"]) ? "on" : "off"),
					"show_in_nav_menus" => $default ? "on" : (! empty($input["taxonomies"]["category"]["show_in_nav_menus"]) ? "on" : "off")
				),
				"post_tag" => array(
					"enabled" => $default ? "on" : (! empty($input["taxonomies"]["post_tag"]["enabled"]) ? "on" : "off"),
					"post_types" => $default ? array("post") : (! empty($input["taxonomies"]["post_tag"]["post_types"]) && is_array($input["taxonomies"]["post_tag"]["post_types"]) ? $input["taxonomies"]["post_tag"]["post_types"] : array()),
					"public" => $default ? "on" : (! empty($input["taxonomies"]["post_tag"]["public"]) ? "on" : "off"),
					"show_in_nav_menus" => $default ? "on" : (! empty($input["taxonomies"]["post_tag"]["show_in_nav_menus"]) ? "on" : "off")
				)
			),
			"comments" => array(
				"enabled" => $default ? "on" : (! empty($input["comments"]["enabled"]) ? "on" : "off")
			),
			"links" => array(
				"enabled" => $default ? "on" : (! empty($input["links"]["enabled"]) ? "on" : "off")
			),
			"frontend" => array(
				"head_remove" => array(
					"admin_bar_bump" => $default ? "off" : (! empty($input["frontend"]["head_remove"]["admin_bar_bump"]) ? "on" : "off"),
					"remotepub" => $default ? "off" : (! empty($input["frontend"]["head_remove"]["remotepub"]) ? "on" : "off"),
					"rssfeeds" => $default ? "off" : (! empty($input["frontend"]["head_remove"]["rssfeeds"]) ? "on" : "off"),
					"postrel" => $default ? "off" : (! empty($input["frontend"]["head_remove"]["postrel"]) ? "on" : "off"),
					"generator" => $default ? "off" : (! empty($input["frontend"]["head_remove"]["generator"]) ? "on" : "off")
				)
			),
			"media" => array(
				"crop" => $default ? array() : (! empty($input["media"]["crop"]) && is_array($input["media"]["crop"]) ? $input["media"]["crop"] : array()),
				"supports" => $default ? array("comments") : (! empty($input["media"]["supports"]) && is_array($input["media"]["supports"]) ? $input["media"]["supports"] : array())
			),
			"admin" => array(
				"dashboard_remove" => $default ? array_keys($this->default_dashboard) : (! empty($input["admin"]["dashboard_remove"]) && is_array($input["admin"]["dashboard_remove"]) ? $input["admin"]["dashboard_remove"] : array()),
				"widgets_remove" => $default ? array_keys($this->default_widgets) : (! empty($input["admin"]["widgets_remove"]) && is_array($input["admin"]["widgets_remove"]) ? $input["admin"]["widgets_remove"] : array()),
				"advanced" => array(
					"disable_updates" => $default ? "off" : (! empty($input["admin"]["advanced"]["disable_updates"]) ? "on" : "off"),
					"parent_dropdown_fix" => $default ? "off" : (! empty($input["admin"]["advanced"]["parent_dropdown_fix"]) ? "on" : "off"),
					"tinymce_strictpasting" => $default ? "off" : (! empty($input["admin"]["advanced"]["tinymce_strictpasting"]) ? "on" : "off")
				)
			),
		);
		// return filtered
		return $filtered;
	}
	
	public function notice_settings_changed () {
		echo "<div class=\"updated settings-error\" id=\setting-error-settings_updated\"><p><strong>".__("Settings Changed")."</strong></p></div>";
	}
	
	/* End Settings Page */
		
}

$controlfreak = new ControlFreak;

register_activation_hook(__FILE__, array($controlfreak, "activate"));
