<div class="wrap">
	
	<div class="icon32" id="icon-options-general"><br></div><h2><?php _e("Control Freak Settings"); ?></h2>
		
	<form action="" method="post" accept-charset="utf-8">
		<div class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div class="stuffbox">
					<h3><?php _e("Settings"); ?></h3>
					<div class="submitbox">
						<div id="major-publishing-actions">
							<div id="delete-action">
								<input name="controlfreak[revert]" type="submit" class="submitdelete deletion" value="<?php _e("Revert to defaults"); ?>">
							</div>
							<div id="publishing-action">
								<input name="controlfreak[save]" type="submit" class="button-primary" value="<?php _e("Save Changes"); ?>">
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="stuffbox import">
					<h3><?php _e("Import"); ?></h3>
					<div class="submitbox">
						<div class="inside">
							<textarea class="textarea" name="controlfreak[import][data]"></textarea>
						</div>
						<div id="minor-publishing-actions">
							<div id="importing-action">
								<input name="controlfreak[import][save]" type="submit" class="button" value="<?php _e("Import"); ?>">
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="stuffbox export">
					<h3><?php _e("Export"); ?></h3>
					<div class="inside">
						<textarea class="textarea" onclick="this.focus();this.select();return false" readonly="readonly"><?php echo json_encode($options); ?></textarea>
					</div>
				</div>
			</div>
			<div id="post-body">
				<div id="post-body-content">					
					
					<div class="stuffbox <?php echo $options["post_types"]["post"]["enabled"] == "on" ? "" : "disabled"; ?>">
						<h3><div class="icon16 icon-post"><br></div><?php _e("Posts"); ?></h3>
						<div class="inside">
							
							<p class="field"><label><?php cf_checkbox("post_types|post|enabled", $options["post_types"]["post"]["enabled"] == "on"); _e("Enabled"); ?></label></p>
							
							<div class="hide-on-disabled">
							
								<p><b><?php _e("Privacy"); ?></b></p>
								
								<ul class="fields">
									<li><label><?php cf_checkbox("post_types|post|public", $options["post_types"]["post"]["public"] == "on"); _e("Public"); ?></label></li>
									<li><label><?php cf_checkbox("post_types|post|show_in_nav_menus", $options["post_types"]["post"]["show_in_nav_menus"] == "on"); _e("Show in Menu Editor"); ?></label></li>
									<li><label><?php cf_checkbox("post_types|post|show_in_search", $options["post_types"]["post"]["show_in_search"] == "on"); _e("Show in Search Results"); ?></label></li>
								</ul>
								
								<p><b><?php _e("Supports"); ?></b></p>
								
								<ul class="fields">
									<?php foreach ($this->default_supports as $name => $title) { ?>
										<li><label><?php cf_checkbox("post_types|post|supports|", post_type_supports("post", $name), $name); ?> <?php echo $title; ?></label></li>
									<?php } ?>
								</ul>
							
							</div>
							
						</div>
					</div>
									
					<div class="stuffbox <?php echo $options["post_types"]["page"]["enabled"] == "on" ? "" : "disabled"; ?>">
						<h3><div class="icon16 icon-page"><br></div><?php _e("Pages"); ?></h3>
						<div class="inside">
							
							<p class="field"><label><?php cf_checkbox("post_types|page|enabled", $options["post_types"]["page"]["enabled"] == "on"); _e("Enabled"); ?></label></p>
							
							<div class="hide-on-disabled">
							
								<p><b><?php _e("Privacy"); ?></b></p>
								
								<ul class="fields">
									<li><label><?php cf_checkbox("post_types|page|public", $options["post_types"]["page"]["public"] == "on"); _e("Public"); ?></label></li>
									<li><label><?php cf_checkbox("post_types|page|show_in_nav_menus", $options["post_types"]["page"]["show_in_nav_menus"] == "on"); _e("Show in Menu Editor"); ?></label></li>
									<li><label><?php cf_checkbox("post_types|page|show_in_search", $options["post_types"]["page"]["show_in_search"] == "on"); _e("Show in Search Results"); ?></label></li>
								</ul>
								
								<p><b><?php _e("Supports"); ?></b></p>
								
								<ul class="fields">
									<?php foreach ($this->default_supports as $name => $title) { ?>
										<li><label><?php cf_checkbox("post_types|page|supports|$name", post_type_supports("page", $name), $name); ?> <?php _e($title); ?></label></li>
									<?php } ?>
								</ul>
							
							</div>
							
						</div>
					</div>
					
					<div class="stuffbox <?php echo $options["taxonomies"]["category"]["enabled"] == "on" ? "" : "disabled"; ?>">
						<h3><div class="icon16 icon-post"><br></div><?php _e("Categories"); ?></h3>
						<div class="inside">
							
							<p class="field"><label><?php cf_checkbox("taxonomies|category|enabled", $options["taxonomies"]["category"]["enabled"] == "on"); _e("Enabled"); ?></label></p>
							
							<div class="hide-on-disabled">
							
								<p><b><?php _e("Post Types"); ?></b></p>
								
								<ul class="fields">
									<?php foreach ($wp_post_types as $type_key => $type_obj) { if ($type_key == "post" || $type_key == "page" || ! $type_obj->_builtin) { ?>
									<li><label><?php cf_checkbox("taxonomies|category|post_types|", in_array($type_key, $options["taxonomies"]["category"]["post_types"]), $type_key); _e($type_obj->label); ?></label></li>
									<?php } } ?>
								</ul>
								
								<p><b><?php _e("Privacy"); ?></b></p>
								
								<ul class="fields">
									<li><label><?php cf_checkbox("taxonomies|category|public", $options["taxonomies"]["category"]["public"] == "on"); _e("Public"); ?></label></li>
									<li><label><?php cf_checkbox("taxonomies|category|show_in_nav_menus", $options["taxonomies"]["category"]["show_in_nav_menus"] == "on"); _e("Show in Menu Editor"); ?></label></li>
								</ul>
							
							</div>
							
						</div>
					</div>
					
					<div class="stuffbox <?php echo $options["taxonomies"]["post_tag"]["enabled"] == "on" ? "" : "disabled"; ?>">
						<h3><div class="icon16 icon-post"><br></div><?php _e("Post Tags"); ?></h3>
						<div class="inside">
							
							<p class="field"><label><?php cf_checkbox("taxonomies|post_tag|enabled", $options["taxonomies"]["post_tag"]["enabled"] == "on"); _e("Enabled"); ?></label></p>
							
							<div class="hide-on-disabled">
							
								<p><b><?php _e("Post Types"); ?></b></p>
								
								<ul class="fields">
									<?php foreach ($wp_post_types as $type_key => $type_obj) { if ($type_key == "post" || $type_key == "page" || ! $type_obj->_builtin) { ?>
									<li><label><?php cf_checkbox("taxonomies|post_tag|post_types|", in_array($type_key, $options["taxonomies"]["post_tag"]["post_types"]), $type_key); _e($type_obj->label); ?></label></li>
									<?php } } ?>
								</ul>
								
								<p><b><?php _e("Privacy"); ?></b></p>
								
								<ul class="fields">
									<li><label><?php cf_checkbox("taxonomies|post_tag|public", $options["taxonomies"]["post_tag"]["public"] == "on"); _e("Public"); ?></label></li>
									<li><label><?php cf_checkbox("taxonomies|post_tag|show_in_nav_menus", $options["taxonomies"]["post_tag"]["show_in_nav_menus"] == "on"); _e("Show in Menu Editor"); ?></label></li>
								</ul>
							
							</div>
							
						</div>
					</div>
					
					<div class="stuffbox">
						<h3><div class="icon16 icon-media"><br></div><?php _e("Media"); ?></h3>
						<div class="inside">
						
							<p><b><?php _e("Force Image Cropping"); ?></b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox("media|crop|", in_array("medium", $options["media"]["crop"]), "medium"); _e("Medium"); ?></label></li>
								<li><label><?php cf_checkbox("media|crop|", in_array("large", $options["media"]["crop"]), "large"); _e("Large"); ?></label></li>
							</ul>
							
							<?php if ($this->options["comments"]["enabled"] == "on") { ?>
							
								<p><b><?php _e("Supports"); ?></b></p>
								
								<ul class="fields">
									<li><label><?php cf_checkbox("media|supports|", $options["media"]["supports"], "comments"); _e("Comments"); ?></label></li>
								</ul>
								
							<?php } ?>
						</div>
					</div>
					
					<div class="stuffbox <?php echo $options["links"]["enabled"] == "on" ? "" : "disabled"; ?>">
						<h3><div class="icon16 icon-links"><br></div><?php _e("Links"); ?></h3>
						<div class="inside">
							<p class="field"><label><?php cf_checkbox("links|enabled", $options["links"]["enabled"] == "on"); _e("Enabled"); ?></label></p>
						</div>
					</div>
					
					<div class="stuffbox <?php echo $options["comments"]["enabled"] == "on" ? "" : "disabled"; ?>">
						<h3><div class="icon16 icon-comments"><br></div><?php _e("Comments"); ?></h3>
						<div class="inside">
							<p class="field"><label><?php cf_checkbox("comments|enabled", $options["comments"]["enabled"] == "on"); _e("Enabled"); ?></label></p>
						</div>
					</div>
				
					<div class="stuffbox">
						<h3><div class="icon16 icon-appearance"><br></div><?php _e("Front End"); ?></h3>
						<div class="inside">
						
							<p><b><?php _e("Remove from Header"); ?></b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox("frontend|head_remove|admin_bar_bump", $options["frontend"]["head_remove"]["admin_bar_bump"]  == "on"); _e("Admin Bar Bump"); ?></label></li>
								<li><label><?php cf_checkbox("frontend|head_remove|generator", $options["frontend"]["head_remove"]["generator"] == "on"); _e("Generator"); ?></label></li>
								<li><label><?php cf_checkbox("frontend|head_remove|postrel", $options["frontend"]["head_remove"]["postrel"] == "on"); _e("Posts Rel Links"); ?></label></li>
								<li><label><?php cf_checkbox("frontend|head_remove|remotepub", $options["frontend"]["head_remove"]["remotepub"] == "on"); _e("Remote Publishing"); ?></label></li>
								<li><label><?php cf_checkbox("frontend|head_remove|rssfeeds", $options["frontend"]["head_remove"]["rssfeeds"] == "on"); _e("RSS Feeds"); ?></label></li>
							</ul>
							
						</div>
					</div>
									
					<div class="stuffbox">
						<h3><div class="icon16 icon-tools"><br></div><?php _e("Administration"); ?></h3>
						<div class="inside">
						
							<p><b><?php _e("Dashboard"); ?></b></p>
							
							<ul class="fields">
								<?php foreach ($this->default_dashboard as $name => $title) { ?>
									<li><label><?php cf_checkbox("admin|dashboard_remove|", in_array($name, $options["admin"]["dashboard_remove"]), $name); _e($title); ?></label></li>
								<?php } ?>
							</ul>
							
							<p><b><?php _e("Widgets"); ?></b></p>
							
							<ul class="fields">
								<?php foreach ($this->default_widgets as $name => $title) { ?>
									<li><label><?php cf_checkbox("admin|widgets_remove|", in_array($name, $options["admin"]["widgets_remove"]), $name); _e($title); ?></label></li>
								<?php } ?>
							</ul>
							
							<p><b><?php _e("Advanced"); ?></b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox("admin|advanced|disable_updates", $options["admin"]["advanced"]["disable_updates"] == "on"); _e("Disable Update Checks"); ?></label></li>
								<li><label><?php cf_checkbox("admin|advanced|parent_dropdown_fix", $options["admin"]["advanced"]["parent_dropdown_fix"] == "on"); _e("Parent Select All Posts"); ?></label></li>	
								<li><label><?php cf_checkbox("admin|advanced|tinymce_strictpasting", $options["admin"]["advanced"]["tinymce_strictpasting"] == "on"); _e("Strict TinyMCE Pasting"); ?></label></li>
							</ul>
												
						</div>
					</div>
				
				</div>
			</div>
		</div>
	</form>
		
</div>
<?php
function cf_checkbox ($name, $checked=false, $value="", $disabled=false) {
	echo "<input type=\"checkbox\" name=\"controlfreak[options][".implode("][", explode("|", $name))."]\" class=\"checkbox\"".($checked?" checked":"").($disabled?" disabled":"").($value?" value=\"$value\"":"")."> ";
}
?>