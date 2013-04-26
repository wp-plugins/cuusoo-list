<?php
$heading      = __('Settings', CUUSOOList::DOMAIN);
$submit_text  = __('Save', CUUSOOList::DOMAIN);
$form         = '<form name="setmethod" id="setmethod" method="get" action="" class="add:the-list: validate">';
$action       = 'method';
$nonce_action = 'method_cuusoolist';

$current_method = intval( get_option('cuusoolist_method') );
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
			<!-- Method used to fetch the CUUSOO project data. -->
			<div class="form-field form-required">
				<label for="which_method">
					<?php _e('Data fetching method', CUUSOOList::DOMAIN) ?> &nbsp;
					<select name="which_method" id="which_method">
						<option value="<?php echo CUUSOOList::METHOD_API ?>"<?php echo selected( CUUSOOList::METHOD_API, $current_method) ?>>API</option>
						<option value="<?php echo CUUSOOList::METHOD_PAGE ?>"<?php echo selected( CUUSOOList::METHOD_PAGE, $current_method) ?>>Page scrape</option>
					</select>
				</label>
			</div>

			<p>
				<strong>API</strong> is the faster and least intrusive method, but only gets the number of supporters and bookmarks.<br />
				<strong>Page scrape</strong> obtains more data (and adds a supports/views ratio), but may trigger an extra "view" on each fetch.
			</p>

			<p class="submit">
				<input type="submit" class="button-primary alignleft" name="submit" value="<?php echo $submit_text ?>" />
			</p>
		</form>
	</div>