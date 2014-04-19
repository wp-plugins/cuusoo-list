<!-- ADD/UPDATE CUUSOO PROJECT FORM. -->
<?php
if ( !is_null($project_id) )
{
	// Edit project
	$heading      = __('Edit CUUSOO project', CUUSOOList::DOMAIN);
	$submit_text  = __('Update', CUUSOOList::DOMAIN);
	$form         = '<form name="edit_project" id="edit_project" method="post" action="' . CUUSOOList::get_parent_url() . '">';
	$action       = 'update';
	$nonce_action = 'update_cuusoolist';

	$list         = get_option('cuusoolist_projects');
	$project      = $list[$project_id];

}
else
{
	// New project
	$heading      = __('Add a CUUSOO project', CUUSOOList::DOMAIN);
	$submit_text  = __('Add', CUUSOOList::DOMAIN);
	$form         = '<form name="add_project" id="add_project" method="post" action="' . CUUSOOList::get_parent_url() . '">';
	$action       = 'add';
	$nonce_action = 'add_cuusoolist';

	$project      = array();
}
?>
	<div class="form-wrap">
	    <h3><?php echo $heading ?></h3>
	    <?php echo $form ?>
			<input type="hidden" name="page" value="<?php echo CUUSOOList::get_parent_url() ?>"/>
			<input type="hidden" name="action" value="<?php echo $action ?>" />
<?php
	wp_original_referer_field(true, 'previous');
	wp_nonce_field($nonce_action);
?>
			<!-- CUUSOO project ID. -->
			<div class="form-field form-required">
				<label for="id"><?php _e('CUUSOO Project ID', CUUSOOList::DOMAIN) ?></label>
				<input name="id" id="id" type="text" placeholder="numeric" value="<?php echo $project_id; ?>" size="8"
					<?php if ( $action == 'update' ) : ?> readonly="readonly"<?php endif; ?> />
			</div>

			<!-- Label (useful for API fetching). -->
			<div class="form-field form-required">
				<label for="label"><?php _e('Label for this project', CUUSOOList::DOMAIN) ?></label>
				<input name="label" id="label" type="text" placeholder="eg. the project title" value="<?php if (isset($project['label'])) echo $project['label']; ?>" />
			</div>

			<p>Data for the CUUSOO project will be fetched and then automatically updated once a day.</p>

			<p class="submit">
				<button type="submit" class="button-primary alignleft" name="submit"><?php echo $submit_text; ?></button>
			</p>
		</form>
	</div>