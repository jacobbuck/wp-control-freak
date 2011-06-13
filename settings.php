<?php
/**
 * @package SimpleCMS
 * @author Jacob Buck
 */

?>
<div class="wrap">
	
	<div class="icon32" id="icon-options-general"><br></div><h2>Simple CMS Settings</h2>
	
	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : ?>
	<div class="updated settings-error" id="setting-error-settings_updated"> 
	<p><strong>Settings saved.</strong></p></div>
	<?php endif; ?>
	
	<form action="" method="post" accept-charset="utf-8">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Basics</th>
					<td>
						<p><?php sch_checkbox("disable_posts", "Disable Posts", $options['disable_posts']); ?></p>
						<p><?php sch_checkbox("disable_links", "Disable Links", $options['disable_links']); ?></p>
						<p><?php sch_checkbox("disable_comments", "Disable Comments", $options['disable_comments']); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Dashboard</th>
					<td>
						<p><?php sch_checkbox("dash_comments", "Remove Recent Comments", $options['dash_comments']); ?></p>
						<p><?php sch_checkbox("dash_drafts", "Remove Recent Drafts", $options['dash_drafts']); ?></p>
						<p><?php sch_checkbox("dash_incoming", "Remove Incoming Links", $options['dash_incoming']); ?></p>
						<p><?php sch_checkbox("dash_quickpress", "Remove QuickPress", $options['dash_quickpress']); ?></p>
						<p><?php sch_checkbox("dash_rightnow", "Remove Right Now", $options['dash_rightnow']); ?></p>
						<p><?php sch_checkbox("dash_feeds", "Remove WordPress News and Plugins", $options['dash_feeds']); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Menu</th>
					<td>
						<p><?php sch_checkbox("menu_pagesabove", "Move Pages above Posts", $options['menu_pagesabove']); ?></p>
						<p><?php sch_checkbox("menu_hidetools", "Hide Tools", $options['menu_hidetools']); ?></p>
					</td>
				</tr>		
				<tr valign="top">
					<th scope="row">Posts</th>
					<td>
						<p><?php sch_checkbox("menu_renameposts", "Rename Posts to", $options['menu_renameposts']); ?>&nbsp;
							<input type="text" name="menu_renameposts_name" value="<?php echo $options['menu_renameposts_name']; ?>" id="menu_renameposts_name" title="Name">
							<input type="text" name="menu_renameposts_singular" value="<?php echo $options['menu_renameposts_singular']; ?>" id="menu_renameposts_singular" title="Singular Name">
						</p>
						<p><?php sch_checkbox("posts_hideauthor", "Hide Author", $options['posts_hideauthor']); ?></p>
						<p><?php sch_checkbox("posts_hidecategories", "Hide Categories", $options['posts_hidecategories']); ?></p>
						<p><?php sch_checkbox("posts_hidetags", "Hide Post Tags", $options['posts_hidetags']); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Editor</th>
					<td>
						<p><?php sch_checkbox("editor_hidecustomfields", "Hide Custom Fields", $options['editor_hidecustomfields']); ?></p>
						<p><?php sch_checkbox("editor_hideexcerpt", "Hide Excerpt", $options['editor_hideexcerpt']); ?></p>
						<p><?php sch_checkbox("editor_hidetrackbacks", "Hide Trackbacks", $options['editor_hidetrackbacks']); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Advanced</th>
					<td>
						<p><?php sch_checkbox("core_adminbar", "Disable Admin Bar", $options['core_adminbar']); ?></p>
						<p><?php sch_checkbox("core_updates", "Disable Core &amp; Plugin Update Checking", $options['core_updates']); ?></p>
						<p><?php sch_checkbox("core_flashupload", "Force Flash Uploader", $options['core_flashupload']); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p>
			<input type="submit" value="Save Changes" class="button-primary" id="sch_submit" name="sch_submit">&nbsp;
			<input type="submit" value="Reset to Defaults" class="button-secondary" id="sch_reset" name="sch_reset">
		</p>
	</form>
	
	<br>
	
	<a name="maintenance"></a>
	
	<div class="icon32" id="icon-tools"><br></div><h2>Maintenance</h2>
	
	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'maintenance') : ?>
	<div class="updated settings-error" id="setting-error-settings_updated"> 
	<p><strong>Maintenance finished.</strong></p></div>
	<?php endif; ?>
	
	<form action="" method="post" accept-charset="utf-8">		
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">Clean Posts</th>
					<td>
						<p><?php sch_checkbox("delete_revisions", "Delete Revisions"); ?></p>
						<p><?php sch_checkbox("delete_trash", "Empty Trash"); ?></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Comments</th>
					<td>
						<p><select name="bulk_comments">
							<option value="">&mdash; Select &mdash;</option>
							<option value="closed">Close All Discussions</option>
							<option value="open">Open All Discussions</option>
						</select></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Pingbacks</th>
					<td>
						<p><select name="bulk_pingbacks">
							<option value="">&mdash; Select &mdash;</option>
							<option value="closed">Close All Pingbacks</option>
							<option value="open">Open All Pingbacks</option>
						</select></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p>
			<input type="submit" value="Execute" class="button-primary" id="sch_maintenance" name="sch_maintenance">
		</p>
	</form>
	
	<br>
	
</div>

<script type="text/javascript">
(function($){
	toggle_check_field('#menu_renameposts','#menu_renameposts_name, #menu_renameposts_singular')
	$('#menu_renameposts').change(function(){
		toggle_check_field('#menu_renameposts','#menu_renameposts_name, #menu_renameposts_singular')
	});
	function toggle_check_field (check,field) {
		$checkbox = $(check);
		$textbox = $(field);
		if ($checkbox.attr('checked')) {
			$textbox.removeAttr('disabled');
		} else {
			$textbox.attr('disabled','disabled');
		}
	}
})(this.jQuery);
</script>

<?php

function sch_checkbox ($name,$label,$checked=false) {
	echo '<label><input type="checkbox" name="'.$name.'" id="'.$name.'"'.(($checked)?' checked':'').'> '.$label.'</label>';
}
