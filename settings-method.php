<?php
$heading      = __('Settings', CUUSOOList::DOMAIN);
$submit_text  = __('Save', CUUSOOList::DOMAIN);
$form         = '<form name="setmethod" id="setmethod" method="post" action="">';
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
				<strong>API</strong> is faster and less intrusive, but can only obtain the number of supporters and
				bookmarks for each project.
			</p>
			<p>
				<strong>Page scraping</strong> can obtain more data (including a ratio of supporters to views), but adds
				one to the project's page views on each fetch.
			</p>

			<p class="submit">
				<button type="submit" class="button-primary alignleft" name="submit"><?php echo $submit_text ?></button>
			</p>
		</form>
	</div>