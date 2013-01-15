<?php
	$list       = get_option('cuusoolist_projects');
	$method     = get_option('cuusoolist_method');
	$last_fetch = date_i18n( get_option('date_format') . ' ' . get_option('time_format'),
		get_option('cuusoolist_fetched') );
?>
	<p><strong>Last fetched:</strong> <?php echo $last_fetch; ?></p>

	<table class="widefat">
	    <thead>
			<tr>
<?php if ( $method == CUUSOOList::METHOD_PAGE ) : ?>
				<th scope="col" colspan="2"><?php _e('title', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('views', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('supporters', CUUSOOList::DOMAIN) ?></th>
				<th scope="col">&nbsp;</th>
				<th scope="col" style="text-align: right;"><?php _e('ratio', CUUSOOList::DOMAIN) ?></th>
<?php else: ?>
				<th scope="col"><?php _e('label', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('supporters', CUUSOOList::DOMAIN) ?></th>
				<th scope="col">&nbsp;</th>
				<th scope="col" style="text-align: right;"><?php _e('bookmarks', CUUSOOList::DOMAIN) ?></th>
<?php endif; ?>
			</tr>
	    </thead>
	    <tbody id="the-list">
		<?php
		$index_start = $n * ( $p - 1 );
		$index_end   = $n * $p;
		$index       = 0;
		foreach ($list as $id => $values)
		{
		    if (( '' == $s ) || ( ( false !== strpos(strtolower($id), strtolower($s)) ) || ( false !== strpos(strtolower($description), strtolower($s)) ) ))
			{
				if (0 == $n || (++$index >= $index_start && $index <= $index_end ))
				{
?>
		    	<tr class="iedit<?php if (0 == $i++ % 2) { echo ' alternate'; } ?>">
<?php if ( $method == CUUSOOList::METHOD_PAGE ) : ?>
		    	    <td>
						<img src="<?php echo stripslashes($values['thumbnail']) ?>" alt="project thumbnail" width="88" height="51" />
					</td>
		    	    <td>
<?php if ( !empty($values['title']) ) : ?>
						<strong><?php echo stripslashes($values['title']) ?></strong>
						<?php if ( !empty($values['label']) ) : ?><br /><?php echo stripslashes($values['label']) ?><?php endif; ?>
<?php elseif ( !empty($values['label']) ) : ?>
						<strong><?php echo stripslashes($values['label']) ?></strong>
<?php else : ?>
						<em>untitled</em>
<?php endif; ?>
					</td>
		    	    <td style="text-align: right;">
						<?php echo $values['views'] ?>
					</td>
		    	    <td style="text-align: right;">
						<?php echo $values['supports'] ?>
					</td>
					<td style="text-align: right;">
						<?php
							$diff = intval($values['diff']);
							if ( $diff > 0 ) :
							?><span style="color: green">+<?php echo $diff; ?></span><?php
							elseif ( $diff < 0 ) :
							?><span style="color: red">-<?php echo $diff; ?></span><?php
							else :
							?>--<?php
							endif;
						?>
					</td>
		    	    <td style="text-align: right;">
						<?php echo ($values['ratio']) ? $values['ratio'].'%' : '--'; ?>
					</td>
<?php else: ?>
		    	    <td>
						<strong><?php echo $values['label'] ? stripslashes($values['label']) : '<em>untitled</em>' ?></strong>
					</td>
		    	    <td style="text-align: right;">
						<?php echo $values['supports'] ?>
					</td>
					<td style="text-align: right;">
						<?php
							$diff = intval($values['diff']);
							if ( $diff > 0 ) :
							?><span style="color: green">+<?php echo $diff; ?></span><?php
							elseif ( $diff < 0 ) :
							?><span style="color: ref">-<?php echo $diff; ?></span><?php
							else :
							?>--<?php
							endif;
						?>
					</td>
		    	    <td style="text-align: right;">
						<?php echo $values['bookmarks'] ?>
					</td>
<?php endif; ?>
		    	</tr>
<?php
				}
		    }
		}
?>
	    </tbody>
	</table>