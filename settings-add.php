<!-- ADD/UPDATE CUUSOO PROJECT FORM. -->
<?php
	// New project.
	$heading      = __('Add a CUUSOO project', CUUSOOList::TEXT_DOMAIN);
	$submit_text  = __('Add', CUUSOOList::TEXT_DOMAIN);
	$action       = 'add';
	$nonce_action = 'add_cuusoolist';
	$project      = array();
?>
	<div class="form-wrap">
	    <h3><?php echo $heading ?></h3>
	    <form name="add_project" id="add_project" method="post" action="<?php echo CUUSOOList::get_parent_url(); ?>">
			<input type="hidden" name="page" value="<?php echo CUUSOOList::get_parent_url() ?>"/>
			<input type="hidden" name="action" value="add" />
<?php
	wp_original_referer_field(true, 'previous');
	wp_nonce_field($nonce_action);
?>

			<!-- CUUSOO project ID. -->
			<div class="form-field form-required">
				<label for="new-project-id"><?php _e('CUUSOO Project ID', CUUSOOList::TEXT_DOMAIN) ?></label>
				<input name="new_project" id="new-project-id" class="all-options alignleft" type="text" placeholder="Numeric ID or https://ideas.lego.com/projects/xxxxx" value="<?php echo $project_id; ?>" size="8"
					<?php if ( $action == 'update' ) : ?> readonly="readonly"<?php endif; ?> />
				<button type="submit" class="button-primary" name="submit"><?php echo $submit_text; ?></button>

				<br class="clear" />

				<small>Data for the project will be fetched and then automatically updated once a day.</small>
			</div>
		</form>
	</div>
