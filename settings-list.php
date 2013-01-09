<?php
	$list       = get_option('cuusoolist_projects');
	$method     = get_option('cuusoolist_method');
?>
	<table class="widefat">
	    <thead>
			<tr>
				<th scope="col" class="check-column">
					<span class="cl-tooltip" title="<?php _e('Select all ', CUUSOOList::DOMAIN) ?>">
						<input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));" />
					</span>
				</th>
				<th scope="col"><?php _e('ID', CUUSOOList::DOMAIN) ?></th>
<?php if ( $method == CUUSOOList::METHOD_PAGE ) : ?>
				<th scope="col" colspan="2"><?php _e('title', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('views', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('supports', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('ratio', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('bookmarks', CUUSOOList::DOMAIN) ?></th>
<?php else: ?>
				<th scope="col"><?php _e('label', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('supports', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('bookmarks', CUUSOOList::DOMAIN) ?></th>
<?php endif; ?>
			</tr>
		</thead>
	    <tfoot>
			<tr>
				<th scope="col" class="check-column">
					<span class="cl-tooltip" title="<?php _e('Select all ', CUUSOOList::DOMAIN) ?>">
						<input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));" />
					</span>
				</th>
				<th scope="col"><?php _e('ID', CUUSOOList::DOMAIN) ?></th>
<?php if ( $method == CUUSOOList::METHOD_PAGE ) : ?>
				<th scope="col" colspan="2"><?php _e('title', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('views', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('supports', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('ratio', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('bookmarks', CUUSOOList::DOMAIN) ?></th>
<?php else: ?>
				<th scope="col"><?php _e('label', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('supports', CUUSOOList::DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('bookmarks', CUUSOOList::DOMAIN) ?></th>
<?php endif; ?>
			</tr>
	    </tfoot>
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
		    	    <th scope="row" class="check-column">
						<input type="checkbox" name="delete[]" value="<?php echo $id ?>" id="select-<?php echo $id ?>"/>
		    	    </th>
		    	    <td class="name column-name">
						<label for="select-<?php echo $id ?>" style="display:block">
							<strong>
								<span class="acronym-tooltip" title="<?php _e("Edit", CUUSOOList::DOMAIN) ?>">
									<?php echo $id; ?>
								</span>
							</strong>
						</label>
						<div class="row-actions">

							<span class="edit">
								<a href="options-general.php?page=<?php echo CUUSOOList::get_parent_url() ?>&amp;action=edit&amp;id=<?php echo $id ?>">
									<?php _e('Edit'); ?>
								</a>
							</span>
							&nbsp;|&nbsp;
							<span class="delete">
<?php
					    $link = 'options-general.php?page=' . CUUSOOList::get_parent_url() . '&amp;action=delete&amp;id=' . urlencode($id);
					    $link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($link, 'delete_cuusoolist') : $link;
?>
								<a class="submitdelete" href="<?php echo $link ?>" onclick="if ( confirm('<?php _e("Confirm the removal of project ID {$id}.", CUUSOOList::DOMAIN) ?>') ) { return true; } return false;">
									<?php _e('Delete'); ?>
								</a>
							</span>
						</div>
					</td>
<?php if ( $method == CUUSOOList::METHOD_PAGE ) : ?>
					<td>
						<label for="select-<?php echo $id ?>" style="display:block"><img src="<?php echo stripslashes($values['thumbnail']) ?>" alt="project thumbnail" width="88" height="51" /></label>
					</td>
					<td>
						<label for="select-<?php echo $id ?>" style="display:block">
							<strong><?php echo stripslashes($values['title']) ?></strong>
							<?php if ( $values['label'] ) : ?><br /><?php echo $values['label'] ?><?php endif; ?>
						</label>
					</td>
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['views'] ?></label>
					</td>
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['supports'] ?></label>
					</td>
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo ($values['ratio']) ? $values['ratio'].'%' : '--'; ?></label>
					</td>
<?php else: ?>
					<td>
						<label for="select-<?php echo $id ?>" style="display:block">
							<strong><?php echo stripslashes($values['label']) ?></strong>
						</label>
					</td>
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['supports'] ?></label>
					</td>
<?php endif; ?>
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['bookmarks']; ?></label>
					</td>
		    	</tr>
<?php
				}
		    }
		}
?>
	    </tbody>
	</table>