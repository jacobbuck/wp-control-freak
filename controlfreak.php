<?php
/*
Plugin Name: Control Freak 
Description: This handy little plugin hacks some of the core features and settings in WordPress to make it more suitable for your needs. User's discretion is advised.
Author: Jacob Buck
Author URI: http://jacobbuck.co.nz/
Version: 3.0a7
*/

class ControlFreak {
		
	private $options;
	
	/* Construct */
	
	public function __construct () {
		// Set Defualt Options if none available
		if (! get_option("controlfreak")) {
			add_option("controlfreak",json_encode($this->default_options()),"","yes");
		}
		// Get The Latest Options
		$this->options = json_decode(get_option("controlfreak"),true);
		// Actions
		add_action("init", array($this,"init"));
		add_action("admin_menu", array($this,"admin_menu"));
		add_action("wp_dashboard_setup", array($this,"wp_dashboard_setup"));
		add_filter("tiny_mce_before_init",array($this,"tiny_mce_before_init"));
		add_action("wp_before_admin_bar_render",array($this,"wp_before_admin_bar_render"));
		// Filters
		add_filter("plugin_action_links", array($this,"add_settings_link"), 10, 2 );
	}

	/* Init */

	public function init () {
		global $wp_post_types, $wp_taxonomies, $wp_roles;
		// For Settings Page
		$this->settings_init();
		// Get The Latest Options
		$this->options = json_decode(get_option("controlfreak"),true);	
		// Posts
		if ($this->options["posts"]["enabled"] == "on") {
			if ($this->options["posts"]["supports"] && count($this->options["posts"]["supports"])) {
				foreach ($this->options["posts"]["supports"] as $support => $value) {
					if ($value == "off") remove_post_type_support("post",$support);
				}
			}
			if ($this->options["posts"]["rename"]["name"] && $this->options["posts"]["rename"]["singular_name"]) {
				$posts_name = $this->options["posts"]["rename"]["name"];
				$posts_singular_name = $this->options["posts"]["rename"]["singular_name"];
				$wp_post_types["post"]->labels->name = $posts_name;
				$wp_post_types["post"]->labels->singular_name = $posts_singular_name;
				$wp_post_types["post"]->labels->add_new = "Add New";
				$wp_post_types["post"]->labels->add_new_item = "Add New $posts_singular_name";
				$wp_post_types["post"]->labels->edit_item = "Edit $posts_singular_name";
				$wp_post_types["post"]->labels->new_item = "New $posts_singular_name";
				$wp_post_types["post"]->labels->view_item = "View $posts_singular_name";
				$wp_post_types["post"]->labels->search_items = "Search $posts_name";
				$wp_post_types["post"]->labels->not_found = "No ".strtolower($posts_name)." found";
				$wp_post_types["post"]->labels->not_found_in_trash = "No ".strtolower($posts_name)." found in Trash";
				$wp_post_types["post"]->labels->menu_name = $posts_name;
			}
			/*
			if ($this->options["posts"]["taxonomies"]["category"] == "off") {
				unset($wp_taxonomies["category"]);
			}
			if ($this->options["posts"]["taxonomies"]["post_tag"] == "off") {
				unset($wp_taxonomies["post_tag"]);
			}
			*/
		} else if ($this->options["posts"]["enabled"] == "off") {
			unset($wp_post_types["post"]);
			/*
			unset($wp_taxonomies["category"]);
			unset($wp_taxonomies["post_tag"]);
			*/
		}
		
		// Links
		
		
		// Pages
		if ($this->options["pages"]["enabled"] == "on") {
			if ($this->options["pages"]["supports"] && count($this->options["posts"]["supports"])) {
				foreach ($this->options["pages"]["supports"] as $support => $value) {
					if ($value == "off") remove_post_type_support("page",$support);
				}
			}
		} else if ($this->options["pages"]["enabled"] == "off") {
			unset($wp_post_types["page"]);
		}
		
		// Comments
		if ($this->options["comments"]["enabled"] == "off") {
			wp_deregister_script("comment-reply");
		}
		
		// Front End
		if ($this->options["frontend"]["remove"]["remotepub"] == "on") {
			remove_action("wp_head", "rsd_link");
			remove_action("wp_head", "wlwmanifest_link");
		}
		if ($this->options["frontend"]["remove"]["rssfeeds"] == "on") {
			remove_action("wp_head", "feed_links_extra", 3);
			remove_action("wp_head", "feed_links", 2);
			add_action("do_feed", "disable_our_feeds", 1);
			add_action("do_feed_rdf", "disable_our_feeds", 1);
			add_action("do_feed_rss", "disable_our_feeds", 1);
			add_action("do_feed_rss2", "disable_our_feeds", 1);
			add_action("do_feed_atom", "disable_our_feeds", 1);
			function disable_our_feeds() {
				wp_die( __("<strong>Error:</strong> No RSS Feed Available, Please visit our <a href=\"". get_bloginfo("url") ."\">homepage</a>.") );
			}
		}
		if ($this->options["frontend"]["remove"]["postrel"] == "on") {
			remove_action("wp_head", "index_rel_link");
			remove_action("wp_head", "parent_post_rel_link", 10, 0);
			remove_action("wp_head", "start_post_rel_link", 10, 0);
			remove_action("wp_head", "adjacent_posts_rel_link", 10, 0);
			remove_action("wp_head", "adjacent_posts_rel_link_wp_head", 10, 0);
		}
		if ($this->options["frontend"]["remove"]["generator"] == "on") {
			remove_action("wp_head", "wp_generator");
		}
		if ($this->options["frontend"]["remove"]["l10n"] == "on") {
			wp_deregister_script("l10n");
		}
		if ($this->options["frontend"]["remove"]["adminbar_margin"] == "on") {
			remove_action("wp_head", "_admin_bar_bump_cb");
		}
		
		// Administraton
		if ($this->options["admin"]["roles"] && count($this->options["admin"]["roles"])) {
			foreach ($this->options["admin"]["roles"] as $role => $value) {
				if ($value == "off") remove_role($role);
			}
		}
		if ($this->options["admin"]["advanced"]["disable_adminbar"] == "on") {
			add_filter("show_admin_bar","__return_false");
			wp_deregister_script("admin-bar");
			wp_deregister_style("admin-bar");
			remove_action("init", "wp_admin_bar_init");
			remove_action("admin_head", "wp_admin_bar_header");
			remove_action("wp_footer", "wp_admin_bar_render");
			remove_action("wp_head", "_admin_bar_bump_cb");
			remove_action("wp_head", "wp_admin_bar_header");
			add_action("admin_print_styles","hide_admin_bar_prefs");
			function hide_admin_bar_prefs () {
				echo "<style type=\"text/css\">.show-admin-bar { display: none; }</style>\n";
			}
		}
		if ($this->options["admin"]["advanced"]["disable_updates"] == "on") {
			add_filter('pre_site_transient_update_core',create_function('$a',"return null;"));
			remove_action('load-update-core.php','wp_update_plugins');
			add_filter('pre_site_transient_update_plugins',create_function('$a',"return null;"));
		}
		
	}
	
	/* Admin Menu */
	
	public function admin_menu () {
		global $menu, $submenu;
		// For Settings Page
		$this->add_settings_submenu();
		// Posts
		if ($this->options["posts"]["enabled"] == "on") {
			if ($this->options["posts"]["rename"]["name"] && $this->options["posts"]["rename"]["singular_name"]) {
				$posts_name = $this->options["posts"]["rename"]["name"];
				$posts_singular_name = $this->options["posts"]["rename"]["singular_name"];
				$menu[5][0] = $posts_name;
				$submenu['edit.php'][5][0] = $posts_name;
				$submenu['edit.php'][10][0] = "Add $posts_singular_name";
				if (! current_user_can("level_1")) unset($menu[5]);
			}
		} else if ($this->options["posts"]["enabled"] == "off") {
			remove_menu_page("edit.php");
		}
		// Links
		if ($this->options["links"]["enabled"] == "off") {
			remove_menu_page("link-manager.php");
		}
		// Pages
		if ($this->options["pages"]["enabled"] == "off") {
			remove_menu_page("edit.php?post_type=page");
		}
		// Comments
		if ($this->options["comments"]["enabled"] == "off") {
			remove_menu_page("edit-comments.php");
			remove_submenu_page("options-general.php","options-discussion.php");
		}
		// Administraton
		if ($this->options["admin"]["menu"]["pages_before"] == "on" && $this->options["pages"]["enabled"] == "on") {
			if ($this->options["posts"]["enabled"] == "on") {
				$menu[6] = $menu[5];  unset($menu[5]);
			}
			$menu[5] = $menu[20]; unset($menu[20]);
		}
		if ($this->options["admin"]["menu"]["hide_plugins"] == "on") {
			remove_menu_page("plugins.php");
		}
		if ($this->options["admin"]["menu"]["hide_tools"] == "on") {
			remove_menu_page("tools.php");
		}
	}
	
	/* Dashboard Setup */
	
	public function wp_dashboard_setup () {
		global $wp_meta_boxes;
		// Administraton
		if ($this->options["admin"]["dashboard"]["right_now"] == "off") unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		if ($this->options["admin"]["dashboard"]["recent_comments"] == "off") unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		if ($this->options["admin"]["dashboard"]["incoming_links"] == "off") unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		if ($this->options["admin"]["dashboard"]["plugins"] == "off") unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		if ($this->options["admin"]["dashboard"]["quickpress"] == "off") unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		if ($this->options["admin"]["dashboard"]["recent_drafts"] == "off") unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
		if ($this->options["admin"]["dashboard"]["primary"] == "off") unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		if ($this->options["admin"]["dashboard"]["secondary"] == "off") unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
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
	
	function wp_before_admin_bar_render () {
		global $wp_admin_bar;
		// check if admin bar not disabled
		if ($this->options["admin"]["advanced"]["disable_adminbar"] != "on") {
			// remove comments link
			if ($this->options["comments"]["enabled"] == "off") {
				$wp_admin_bar->remove_menu('comments');
			}
		}
	}
	
	/* Start Settings Page */
	
	private function settings_init () {
		// register plugin styles
		wp_register_style("control_freak_settings", WP_PLUGIN_URL . "/wp-control-freak/settings.css");
		// save settings
		if (isset($_POST["controlfreak"]["save"]) || isset($_POST["controlfreak"]["revert"])) {
			// get new options
			if (isset($_POST["controlfreak"]["save"])) { 
				$new_options = $this->filter_post_options($_POST["controlfreak"]);
			} else if (isset($_POST["controlfreak"]["revert"])) {
				$new_options = $this->default_options();
			}
			// save new options
			if (! get_option("controlfreak")) {
				add_option("controlfreak",json_encode($new_options),"","yes");
			} else {
				update_option("controlfreak",json_encode($new_options));
			}
			// update wp options
			if ($new_options["comments"]["enabled"] == "off") {
				update_option("default_comment_status","closed");	
			}
			
			header("Location: ".get_bloginfo("url")."/wp-admin/options-general.php?page=control-freak&settings-updated=true");
		}
	}
	
	public function add_settings_link ($links, $file) {
		if ($file == "wp-control-freak/controlfreak.php") {
			$links[] = "<a href=\"options-general.php?page=control-freak\">".__("Settings")."</a>";
		}
		return $links;
	}
	
	public function add_settings_submenu () {
		$page = add_submenu_page("options-general.php", "Control Freak", "Control Freak", "manage_options", "control-freak", array($this,"settings_page"));
		add_action( "admin_print_styles-" . $page, array($this,"settings_page_styles") );
	}
	
	public function settings_page () {
		// get settings
		$options = json_decode(get_option("controlfreak"),true);
		// display page
		include("settings.php");
	}
	
	public function settings_page_styles () {
		wp_enqueue_style("control_freak_settings");
	}
	
	private function filter_post_options ($posted) {
		$filtered = array();
		// posts
		$filtered["posts"]["enabled"] = ($posted["posts"]["enabled"]) ? "on" : "off";	
		$filtered["posts"]["rename"]["name"] = ($posted["posts"]["rename"]["name"]) ? $posted["posts"]["rename"]["name"] : "";
		$filtered["posts"]["rename"]["singular_name"] = ($posted["posts"]["rename"]["singular_name"]) ? $posted["posts"]["rename"]["singular_name"] : "";
		$filtered["posts"]["supports"]["title"] = ($posted["posts"]["supports"]["title"]) ? "on" : "off";
		$filtered["posts"]["supports"]["editor"] = ($posted["posts"]["supports"]["editor"]) ? "on" : "off";
		$filtered["posts"]["supports"]["author"] = ($posted["posts"]["supports"]["author"]) ? "on" : "off";
		$filtered["posts"]["supports"]["excerpt"] = ($posted["posts"]["supports"]["excerpt"]) ? "on" : "off";
		$filtered["posts"]["supports"]["trackbacks"] = ($posted["posts"]["supports"]["trackbacks"]) ? "on" : "off";
		$filtered["posts"]["supports"]["custom-fields"] = ($posted["posts"]["supports"]["custom-fields"]) ? "on" : "off";
		$filtered["posts"]["supports"]["comments"] = ($posted["posts"]["supports"]["comments"] && $posted["comments"]["enabled"]) ? "on" : "off";
		$filtered["posts"]["supports"]["revisions"] = ($posted["posts"]["supports"]["revisions"]) ? "on" : "off";
		$filtered["posts"]["taxonomies"]["category"] = ($posted["posts"]["taxonomies"]["category"]) ? "on" : "off";
		$filtered["posts"]["taxonomies"]["post_tag"] = ($posted["posts"]["taxonomies"]["post_tag"]) ? "on" : "off";
		// links
		$filtered["links"]["enabled"] = ($posted["links"]["enabled"]) ? "on" : "off";
		// pages
		$filtered["pages"]["enabled"] = ($posted["pages"]["enabled"]) ? "on" : "off";	
		$filtered["pages"]["supports"]["title"] = ($posted["pages"]["supports"]["title"]) ? "on" : "off";
		$filtered["pages"]["supports"]["editor"] = ($posted["pages"]["supports"]["editor"]) ? "on" : "off";
		$filtered["pages"]["supports"]["author"] = ($posted["pages"]["supports"]["author"]) ? "on" : "off";
		$filtered["pages"]["supports"]["excerpt"] = ($posted["pages"]["supports"]["excerpt"]) ? "on" : "off";
		$filtered["pages"]["supports"]["trackbacks"] = ($posted["pages"]["supports"]["trackbacks"]) ? "on" : "off";
		$filtered["pages"]["supports"]["custom-fields"] = ($posted["pages"]["supports"]["custom-fields"]) ? "on" : "off";
		$filtered["pages"]["supports"]["comments"] = ($posted["pages"]["supports"]["comments"] && $posted["comments"]["enabled"]) ? "on" : "off";
		$filtered["pages"]["supports"]["revisions"] = ($posted["pages"]["supports"]["revisions"]) ? "on" : "off";
		// comments
		$filtered["comments"]["enabled"] = ($posted["comments"]["enabled"]) ? "on" : "off";
		// frontend
		$filtered["frontend"]["remove"]["remotepub"] = ($posted["frontend"]["remove"]["remotepub"]) ? "on" : "off";
		$filtered["frontend"]["remove"]["rssfeeds"] = ($posted["frontend"]["remove"]["rssfeeds"]) ? "on" : "off";
		$filtered["frontend"]["remove"]["postrel"] = ($posted["frontend"]["remove"]["postrel"]) ? "on" : "off";
		$filtered["frontend"]["remove"]["generator"] = ($posted["frontend"]["remove"]["generator"]) ? "on" : "off";
		$filtered["frontend"]["remove"]["l10n"] = ($posted["frontend"]["remove"]["l10n"]) ? "on" : "off";
		$filtered["frontend"]["remove"]["adminbar_margin"] = ($posted["frontend"]["remove"]["adminbar_margin"]) ? "on" : "off";
		// admin
		$filtered["admin"]["dashboard"]["right_now"] = ($posted["admin"]["dashboard"]["right_now"]) ? "on" : "off";
		$filtered["admin"]["dashboard"]["recent_comments"] = ($posted["admin"]["dashboard"]["recent_comments"] && $posted["comments"]["enabled"]) ? "on" : "off";
		$filtered["admin"]["dashboard"]["incoming_links"] = ($posted["admin"]["dashboard"]["incoming_links"]) ? "on" : "off";
		$filtered["admin"]["dashboard"]["plugins"] = ($posted["admin"]["dashboard"]["plugins"]) ? "on" : "off";
		$filtered["admin"]["dashboard"]["quickpress"] = ($posted["admin"]["dashboard"]["quickpress"] && $posted["posts"]["enabled"]) ? "on" : "off";
		$filtered["admin"]["dashboard"]["recent_drafts"] = ($posted["admin"]["dashboard"]["recent_drafts"]) ? "on" : "off";
		$filtered["admin"]["dashboard"]["primary"] = ($posted["admin"]["dashboard"]["primary"]) ? "on" : "off";
		$filtered["admin"]["dashboard"]["secondary"] = ($posted["admin"]["dashboard"]["secondary"]) ? "on" : "off";
		$filtered["admin"]["menu"]["pages_before"] = ($posted["admin"]["menu"]["pages_before"]) ? "on" : "off";
		$filtered["admin"]["menu"]["hide_plugins"] = ($posted["admin"]["menu"]["hide_plugins"]) ? "on" : "off";
		$filtered["admin"]["menu"]["hide_tools"] = ($posted["admin"]["menu"]["hide_tools"]) ? "on" : "off";
		$filtered["admin"]["roles"]["subscriber"] = ($posted["admin"]["roles"]["subscriber"]) ? "on" : "off";
		$filtered["admin"]["roles"]["contributor"] = ($posted["admin"]["roles"]["contributor"]) ? "on" : "off";
		$filtered["admin"]["roles"]["author"] = ($posted["admin"]["roles"]["author"]) ? "on" : "off";
		$filtered["admin"]["roles"]["editor"] = ($posted["admin"]["roles"]["editor"]) ? "on" : "off";
		$filtered["admin"]["advanced"]["disable_adminbar"] = ($posted["admin"]["advanced"]["disable_adminbar"]) ? "on" : "off";
		$filtered["admin"]["advanced"]["disable_updates"] = ($posted["admin"]["advanced"]["disable_updates"]) ? "on" : "off";
		$filtered["admin"]["advanced"]["tinymce_strictpasting"] = ($posted["admin"]["advanced"]["tinymce_strictpasting"]) ? "on" : "off";
		// return filtered
		return $filtered;
	}
	
	private function default_options () {
		$filtered = array();
		// posts
		$filtered["posts"]["enabled"] = "on";	
		$filtered["posts"]["rename"]["name"] = "";
		$filtered["posts"]["rename"]["singular_name"] = "";
		$filtered["posts"]["supports"]["title"] = "on";
		$filtered["posts"]["supports"]["editor"] = "on";
		$filtered["posts"]["supports"]["author"] = "on";
		$filtered["posts"]["supports"]["excerpt"] = "on";
		$filtered["posts"]["supports"]["trackbacks"] = "on";
		$filtered["posts"]["supports"]["custom-fields"] = "on";
		$filtered["posts"]["supports"]["comments"] = "on";
		$filtered["posts"]["supports"]["revisions"] = "on";
		$filtered["posts"]["taxonomies"]["category"] = "on";
		$filtered["posts"]["taxonomies"]["post_tag"] = "on";
		// links
		$filtered["links"]["enabled"] = "on";
		// pages
		$filtered["pages"]["enabled"] = "on";	
		$filtered["pages"]["supports"]["title"] = "on";
		$filtered["pages"]["supports"]["editor"] = "on";
		$filtered["pages"]["supports"]["author"] = "on";
		$filtered["pages"]["supports"]["excerpt"] = "on";
		$filtered["pages"]["supports"]["trackbacks"] = "on";
		$filtered["pages"]["supports"]["custom-fields"] = "on";
		$filtered["pages"]["supports"]["comments"] = "on";
		$filtered["pages"]["supports"]["revisions"] = "on";
		// comments
		$filtered["comments"]["enabled"] = "on";
		// frontend
		$filtered["frontend"]["remove"]["remotepub"] = "off";
		$filtered["frontend"]["remove"]["rssfeeds"] = "off";
		$filtered["frontend"]["remove"]["postrel"] = "off";
		$filtered["frontend"]["remove"]["generator"] = "off";
		$filtered["frontend"]["remove"]["l10n"] = "off";
		$filtered["frontend"]["remove"]["adminbar_margin"] = "off";
		// admin
		$filtered["admin"]["dashboard"]["right_now"] = "on";
		$filtered["admin"]["dashboard"]["recent_comments"] = "on";
		$filtered["admin"]["dashboard"]["incoming_links"] = "on";
		$filtered["admin"]["dashboard"]["plugins"] = "on";
		$filtered["admin"]["dashboard"]["quickpress"] = "on";
		$filtered["admin"]["dashboard"]["recent_drafts"] = "on";
		$filtered["admin"]["dashboard"]["primary"] = "on";
		$filtered["admin"]["dashboard"]["secondary"] = "on";
		$filtered["admin"]["menu"]["pages_before"] = "off";
		$filtered["admin"]["menu"]["hide_plugins"] = "off";
		$filtered["admin"]["menu"]["hide_tools"] = "off";
		$filtered["admin"]["roles"]["subscriber"] = "on";
		$filtered["admin"]["roles"]["contributor"] = "on";
		$filtered["admin"]["roles"]["author"] = "on";
		$filtered["admin"]["roles"]["editor"] = "on";
		$filtered["admin"]["advanced"]["disable_adminbar"] = "off";
		$filtered["admin"]["advanced"]["disable_updates"] = "off";
		$filtered["admin"]["advanced"]["tinymce_strictpasting"] = "off";
		// return filtered
		return $filtered;
	}
	
	/* End Settings Page */
			
}

$controlfreak = new ControlFreak;