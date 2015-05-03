<?php
	$projects   = CUUSOOList::get();
	$error      = get_option('cuusoolist_fetch_error');
	$last_fetch = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), CUUSOOList::last_update() );
	$i          = 0;
?>
<form name="add_project" id="add_project" method="post" action="<?php echo CUUSOOList::get_parent_url(); ?>">
	<input type="hidden" name="page" value="<?php echo admin_url() ?>"/>
	<input type="hidden" name="action" value="refresh" />
	<?php
		wp_original_referer_field(true, 'previous');
		wp_nonce_field('refresh_cuusoolist');
	?>
	<p><strong>Last fetched:</strong> <?php echo $last_fetch; ?></p>
	<p>
	<button type="submit" class="button-primary"
		onclick="if (!confirm(
			'<?php _e("Please use this sparingly, as results are fetched through page scraping.", CUUSOOList::TEXT_DOMAIN) ?>'
			) ) return false;">
		<?php _e('Refresh projects') ?>
	</button>
	</p>
</form>

<?php if ( $error ) : ?>
	<p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" colspan="2">&nbsp;</th>
				<th scope="col" style="text-align: right;"><b><?php _e('supp', CUUSOOList::TEXT_DOMAIN) ?></b></th>
				<th scope="col" style="text-align: right;"><?php _e('views', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: center;">+/-</th>
				<th scope="col" style="text-align: right;">days</th>
			</tr>
		</thead>
		<tbody id="the-list">
<?php
	if ($projects) :
		foreach ($projects as $id => $values) :
?>
			<tr class="iedit<?php if (0 == $i++ % 2) { echo ' alternate'; } ?>">
				<td>
					<a href="<?php echo CUUSOOList::URL_BASE . $id; ?>" target="_blank" rel="external nofollow">
						<img src="<?php echo stripslashes($values['thumbnail']) ?>" alt="project thumbnail" width="88" />
					</a>
				</td>
				<td>
					<a href="<?php echo CUUSOOList::URL_BASE . $id; ?>" target="_blank" rel="external nofollow">
						<strong><?php echo $values['title'] ?></strong>
					</a>
					<br />
					<small>by <?php echo $values['author'] ?></small>
				</td>
				<td style="text-align: right;">
					<b><?php echo $values['supporters'] ?></b>
				</td>
				<td style="text-align: right;">
					<?php echo $values['views'] ?>
				</td>
				<td style="text-align: right;">
					<?php
						$diff = intval($values['diff']);
						if ( $diff > 0 ) :
						?><span style="color: green">+<?php echo abs($diff); ?></span><?php
						elseif ( $diff < 0 ) :
						?><span style="color: red">-<?php echo abs($diff); ?></span><?php
						else :
						?>-<?php
						endif;
					?>
				</td>
				<td style="text-align: right;">
					<span<?php echo $values['days-left'] < 30 ? ' style="color: red;"' : ''; ?>>
						<?php echo $values['days-left'] ?>
					</span>
				</td>
			</tr>
<?php
		endforeach;
	else :
?>
			<tr>
				<td colspan="5">No projects!</td>
			</tr>
<?php
	endif;
?>
		</tbody>
	</table>
