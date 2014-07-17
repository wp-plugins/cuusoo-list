<?php
	$projects   = CUUSOOList::get();
	$error      = get_option('cuusoolist_fetch_error');
	$last_fetch = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), CUUSOOList::last_update() );
?>
	<p><strong>Last fetched:</strong> <?php echo $last_fetch; ?></p>
<?php if ( $error ) : ?>
	<p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" colspan="2">&nbsp;</th>
				<th scope="col" style="text-align: right;"><b><?php _e('votes', CUUSOOList::DOMAIN) ?></b></th>
				<th scope="col" style="text-align: right;"><?php _e('views', CUUSOOList::DOMAIN) ?></th>
				<th scope="col">+/-</th>
				<th scope="col">days</th>
			</tr>
		</thead>
		<tbody id="the-list">
<?php
	$index_start = $n * ( $p - 1 );
	$index_end   = $n * $p;
	$index       = 0;
	foreach ($projects as $id => $values)
	{
		if (( '' == $s ) ||
			( ( false !== strpos(strtolower($id), strtolower($s)) ) ||
			( false !== strpos(strtolower($description), strtolower($s)) ) ))
		{
			if (0 == $n || (++$index >= $index_start && $index <= $index_end ))
			{
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
			}
		}
	}
?>
		</tbody>
	</table>