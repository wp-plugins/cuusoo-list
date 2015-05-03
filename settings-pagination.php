<div class="tablenav">
	<div class="alignleft actions">

		<!-- Refresh button. -->
		<button type="submit" name="delete-many" class="button-secondary delete" onclick="if ( confirm('<?php _e("\'OK\' to remove these projects? (\'Cancel\' to abort.)", CUUSOOList::TEXT_DOMAIN) ?>') ) { return true; } return false;"><?php _e('Delete') ?></button>
		<?php wp_nonce_field('delete_cuusoolist'); ?>
	</div>
	<div class="tablenav-pages">
<?php
		// Display pagination links
		$page_links = paginate_links(array(
			'base' => add_query_arg('p', '%#%'),
			'format' => '',
			'total' => $t,
			'current' => $p,
			'add_args' => $n
		));
		if (0 < $n && $page_links)
		{
			echo '<div class="tablenav-pages">';
			$range = $n * ( $p - 1 ) + 1 . '-' . $n * $p;
			$total = CUUSOOList::count_projects();
			echo '<span class="displaying-num">';
			_e("Displaying $range of $total");
			echo '</span>';
			echo "$page_links</div>";
		}
?>
	</div>
</div>
<br class="clear" />
