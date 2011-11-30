<div class="wrap">
	
	<div class="icon32" id="icon-options-general"><br></div><h2>Control Freak Settings</h2>
	
	<?php if (isset($_GET["settings-updated"]) && $_GET["settings-updated"] === "true") : ?>
	<div class="updated settings-error" id="setting-error-settings_updated"> 
	<p><strong>Settings saved.</strong></p></div>
	<?php endif; ?>
	
	<form action="" method="post" accept-charset="utf-8">
		<div class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div class="stuffbox">
					<h3>Settings</h3>
					<div class="submitbox">
						<div id="major-publishing-actions">
							<div id="delete-action">
								<input name="controlfreak[revert]" type="submit" class="submitdelete deletion" value="Revert to defaults">
							</div>
							<div id="publishing-action">
								<input name="controlfreak[save]" type="submit" class="button-primary" value="Save Changes">
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="stuffbox import">
					<h3>Import</h3>
					<div class="submitbox">
						<div class="inside">
							<textarea class="textarea" name="controlfreak[import][data]"></textarea>
						</div>
						<div id="minor-publishing-actions">
							<div id="importing-action">
								<input name="controlfreak[import][save]" type="submit" class="button" value="Import">
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
				<div class="stuffbox export">
					<h3>Export</h3>
					<div class="inside">
						<textarea class="textarea" readonly><?php echo $options_json; ?></textarea>
					</div>
				</div>
			</div>
			<div id="post-body">
				<div id="post-body-content">
				
					<div class="stuffbox">
						<h3><label><?php cf_checkbox(array("posts","enabled"),$options["posts"]["enabled"]); ?><span>Posts</span></label></h3>
						<div class="inside">
														
							<p><b>Supports</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("posts","supports","title"),$options["posts"]["supports"]["title"]); ?> Title</label></li>
								<li><label><?php cf_checkbox(array("posts","supports","editor"),$options["posts"]["supports"]["editor"]); ?> Editor</label></li>
								<li><label><?php cf_checkbox(array("posts","supports","author"),$options["posts"]["supports"]["author"]); ?> Author</label></li>
								<li><label><?php cf_checkbox(array("posts","supports","excerpt"),$options["posts"]["supports"]["excerpt"]); ?> Excerpt</label></li>
								<li><label><?php cf_checkbox(array("posts","supports","trackbacks"),$options["posts"]["supports"]["trackbacks"]); ?> Trackbacks</label></li>
								<li><label><?php cf_checkbox(array("posts","supports","custom-fields"),$options["posts"]["supports"]["custom-fields"]); ?> Custom Fields</label></li>
								<li><label><?php cf_checkbox(array("posts","supports","comments"),$options["posts"]["supports"]["comments"],($options["comments"]["enabled"]!="on")); ?> Comments</label></li>
								<li><label><?php cf_checkbox(array("posts","supports","revisions"),$options["posts"]["supports"]["revisions"]); ?> Revisions</label></li>
							</ul>
							<p><b>Taxonomies</b></p>
							
							<ul class="fields">
								<li><label><?php cf_checkbox(array("posts","taxonomies","category"),$options["posts"]["taxonomies"]["category"]); ?> Categories</label></li>
								<li><label><?php cf_checkbox(array("posts","taxonomies","post_tag"),$options["posts"]["taxonomies"]["post_tag"]); ?> Post Tags</label></li>
							</ul>
						</div>
					</div>
				
					<div class="stuffbox">
						<h3><label><?php cf_checkbox(array("links","enabled"),$options["links"]["enabled"]); ?><span>Links</span></label></h3>
						<div class="inside">
						</div>
					</div>
				
					<div class="stuffbox">
						<h3><label><?php cf_checkbox(array("pages","enabled"),$options["pages"]["enabled"]); ?><span>Pages</span></label></h3>
						<div class="inside">
						
							<p><b>Supports</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("pages","supports","title"),$options["pages"]["supports"]["title"]); ?> Title</label></li>
								<li><label><?php cf_checkbox(array("pages","supports","editor"),$options["pages"]["supports"]["editor"]); ?> Editor</label></li>
								<li><label><?php cf_checkbox(array("pages","supports","author"),$options["pages"]["supports"]["author"]); ?> Author</label></li>
								<li><label><?php cf_checkbox(array("pages","supports","excerpt"),$options["pages"]["supports"]["excerpt"]); ?> Excerpt</label></li>
								<li><label><?php cf_checkbox(array("pages","supports","trackbacks"),$options["pages"]["supports"]["trackbacks"]); ?> Trackbacks</label></li>
								<li><label><?php cf_checkbox(array("pages","supports","custom-fields"),$options["pages"]["supports"]["custom-fields"]); ?> Custom Fields</label></li>
								<li><label><?php cf_checkbox(array("pages","supports","comments"),$options["pages"]["supports"]["comments"],($options["comments"]["enabled"]!="on")); ?> Comments</label></li>
								<li><label><?php cf_checkbox(array("pages","supports","revisions"),$options["pages"]["supports"]["revisions"]); ?> Revisions</label></li>
							</ul>
						
						</div>
					</div>
				
					<div class="stuffbox">
						<h3><label><?php cf_checkbox(array("comments","enabled"),$options["comments"]["enabled"]); ?><span>Comments</span></label></h3>
						<div class="inside">
						</div>
					</div>
				
					<div class="stuffbox">
						<h3><span>Front End</span></h3>
						<div class="inside">
						
							<p><b>Remove Meta Tags</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("frontend","remove","remotepub"),$options["frontend"]["remove"]["remotepub"]); ?> Remote Publishing</label></li>
								<li><label><?php cf_checkbox(array("frontend","remove","rssfeeds"),$options["frontend"]["remove"]["rssfeeds"]); ?> RSS Feeds</label></li>
								<li><label><?php cf_checkbox(array("frontend","remove","postrel"),$options["frontend"]["remove"]["postrel"]); ?> Posts Rel Links</label></li>
								<li><label><?php cf_checkbox(array("frontend","remove","generator"),$options["frontend"]["remove"]["generator"]); ?> Generator</label></li>
							</ul>
							
							<p><b>Remove Scripts &amp; Styles</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("frontend","remove","l10n"),$options["frontend"]["remove"]["l10n"]); ?> Localization (l10n)</label></li>
								<li><label><?php cf_checkbox(array("frontend","remove","adminbar_margin"),$options["frontend"]["remove"]["adminbar_margin"]); ?> Admin Bar Margin</label></li>
							</ul>
							
						</div>
					</div>
					
					<div class="stuffbox">
						<h3><span>Media</span></h3>
						<div class="inside">
						
							<p><b>Image Cropping</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("media","crop","medium"),$options["media"]["crop"]["medium"]); ?> Medium</label></li>
								<li><label><?php cf_checkbox(array("media","crop","large"),$options["media"]["crop"]["large"]); ?> Large</label></li>
							</ul>
														
						</div>
					</div>
									
					<div class="stuffbox">
						<h3><span>Administraton</span></h3>
						<div class="inside">
						
							<p><b>Dashboard</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("admin","dashboard","right_now"),$options["admin"]["dashboard"]["right_now"]); ?> Right Now</label></li>
								<li><label><?php cf_checkbox(array("admin","dashboard","recent_comments"),$options["admin"]["dashboard"]["recent_comments"],($options["comments"]["enabled"]!="on")); ?> Recent Comments</label></li>
								<li><label><?php cf_checkbox(array("admin","dashboard","incoming_links"),$options["admin"]["dashboard"]["incoming_links"]); ?> Incoming Links</label></li>
								<li><label><?php cf_checkbox(array("admin","dashboard","plugins"),$options["admin"]["dashboard"]["plugins"]); ?> Plugins</label></li>
								<li><label><?php cf_checkbox(array("admin","dashboard","quickpress"),$options["admin"]["dashboard"]["quickpress"],($options["posts"]["enabled"]!="on")); ?> QuickPress</label></li>
								<li><label><?php cf_checkbox(array("admin","dashboard","recent_drafts"),$options["admin"]["dashboard"]["recent_drafts"]); ?> Recent Drafts</label></li>
								<li><label><?php cf_checkbox(array("admin","dashboard","primary"),$options["admin"]["dashboard"]["primary"]); ?> WordPress Blog</label></li>
								<li><label><?php cf_checkbox(array("admin","dashboard","secondary"),$options["admin"]["dashboard"]["secondary"]); ?> Other WordPress News</label></li>
							</ul>
												
							<p><b>Menu</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("admin","menu","pages_before"),$options["admin"]["menu"]["pages_before"],($options["pages"]["enabled"]!="on")); ?> Pages First</label></li>
								<li><label><?php cf_checkbox(array("admin","menu","hide_plugins"),$options["admin"]["menu"]["hide_plugins"]); ?> Hide Plugins</label></li>
								<li><label><?php cf_checkbox(array("admin","menu","hide_tools"),$options["admin"]["menu"]["hide_tools"]); ?> Hide Tools</label></li>
							</ul>
																			
							<p><b>Advanced</b></p>
						
							<ul class="fields">
								<li><label><?php cf_checkbox(array("admin","advanced","disable_adminbar"),$options["admin"]["advanced"]["disable_adminbar"]); ?> Disable Admin Bar</label></li>
								<li><label><?php cf_checkbox(array("admin","advanced","disable_updates"),$options["admin"]["advanced"]["disable_updates"]); ?> Disable Update Checks</label></li>
								<li><label><?php cf_checkbox(array("admin","advanced","parent_dropdown_all"),$options["admin"]["advanced"]["parent_dropdown_all"]); ?> Parent Select All Posts</label></li>	
								<li><label><?php cf_checkbox(array("admin","advanced","tinymce_strictpasting"),$options["admin"]["advanced"]["tinymce_strictpasting"]); ?> Strict TinyMCE Pasting</label></li>							
							</ul>
												
						</div>
					</div>
				
				</div>
			</div>
		</div>
	</form>
		
</div>
<script>
jQuery(function($){
	$("h3 .checkbox:first", ".stuffbox").each(function () {
		var $checkbox = $(this),
			$stuffbox = $checkbox.closest(".stuffbox"),
			update_stuffbox = (function () {
				if ($checkbox.prop("checked")) {
					$stuffbox.removeClass("disabled");
				} else {
					$stuffbox.addClass("disabled");
				}
			});
		update_stuffbox();
		$checkbox.change(update_stuffbox);
	});
	$(".export .textarea:first",".stuffbox").click(function () {
		$(this).select();
	});
});
</script>
<?php

function cf_checkbox ($name,$value="",$disabled=false) {
	echo "<input type=\"checkbox\" name=\"controlfreak[".implode("][",$name)."]\" class=\"checkbox\"".(($value=="on")?" checked":"").(($disabled)?" disabled":"").">";
}
function cf_textbox ($name,$value="",$placehoder="",$disabled=false) {
	echo "<input type=\"text\" name=\"controlfreak[".implode("][",$name)."]\" class=\"textbox\" value=\"$value\" placeholder=\"$placehoder\" ".(($disabled)?" disabled":"").">";
}

?>