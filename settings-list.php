<!-- LIST OF LEGO IDEAS PROJECTS TO TRACK. -->
<?php
	$projects = get_option('cuusoolist_projects');
?>
	<table class="widefat">
		<colgroup>
			<col width="2em">
			<col width="5em">
			<col width="*">
			<col width="*">
			<col width="8em">
			<col width="6em">
			<col width="8%">
			<col width="8%">
			<col width="8%">
			<col width="8%">
		</colgroup>
	    <thead>
			<tr>
				<th scope="col" class="check-column">
					<span class="cl-tooltip" title="<?php _e('Select all', CUUSOOList::TEXT_DOMAIN) ?>">
						<input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));" />
					</span>
				</th>
				<th scope="col"><?php _e('ID', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" colspan="2"><?php _e('Title/Description', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" colspan="2" style="text-align: right;"><?php _e('Supporters', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Views', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Comments', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Followers', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Days left', CUUSOOList::TEXT_DOMAIN) ?></th>
			</tr>
		</thead>
	    <tfoot>
			<tr>
				<th scope="col" class="check-column">
					<span class="cl-tooltip" title="<?php _e('Select all ', CUUSOOList::TEXT_DOMAIN) ?>">
						<input type="checkbox" onclick="checkAll(document.getElementById('posts-filter'));" />
					</span>
				</th>
				<th scope="col"><?php _e('ID', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" colspan="2"><?php _e('Title/Description', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" colspan="2" style="text-align: right;"><?php _e('Supporters', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Views', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Comments', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Followers', CUUSOOList::TEXT_DOMAIN) ?></th>
				<th scope="col" style="text-align: right;"><?php _e('Days left', CUUSOOList::TEXT_DOMAIN) ?></th>
			</tr>
	    </tfoot>
	    <tbody id="the-list">
		<?php
		$index_start = $n * ( $p - 1 );
		$index_end   = $n * $p;
		$index       = 0;
		foreach ($projects as $id => $values)
		{
		    if (( '' == $s ) || ( ( false !== strpos(strtolower($id), strtolower($s)) ) || ( false !== strpos(strtolower($description), strtolower($s)) ) ))
			{
				if (0 == $n || (++$index >= $index_start && $index <= $index_end ))
				{
?>
		    	<tr class="iedit<?php if (0 == $i++ % 2) { echo ' alternate'; } ?>">

		    		<!-- Selection checkbox. -->
		    	    <th scope="row" class="check-column">
						<input type="checkbox" name="delete[]" value="<?php echo $id ?>" id="select-<?php echo $id ?>"/>
		    	    </th>

		    	    <!-- Project ID. -->
		    	    <td class="row-id">
						<label for="select-<?php echo $id ?>" style="display:block">
							<strong><?php echo $id; ?></strong>
						</label>
					</td>

					<!-- Thumbnail. -->
					<td>
						<label for="select-<?php echo $id ?>" style="display:block">
							<img src="<?php echo stripslashes($values['thumbnail']) ?>" alt="" width="88" height="64" />
						</label>
					</td>

		    	    <!-- Title/Author/Description, with row actions.-->
		    	    <td class="name column-name">
						<label class="row-title" for="select-<?php echo $id ?>">
							<?php echo ($values['title']) ?>
						</label>
						by <?php echo ($values['author']) ?>
						<br />
						<small><em><?php echo ($values['description']) ?></em></small>

						<div class="row-actions">
							<span class="url">
								<a href="<?php echo CUUSOOList::url( $id );?>" target="_blank">Project page</a>
							</span>
							&nbsp;
							<span class="delete">
<?php
					    $link = CUUSOOList::get_parent_url() . '&amp;action=delete&amp;id=' . urlencode($id);
					    $link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url($link, 'delete_cuusoolist') : $link;
?>
								<a class="submitdelete" href="<?php echo $link ?>" onclick="if ( confirm('<?php _e("Confirm the removal of project ID {$id}.", CUUSOOList::TEXT_DOMAIN) ?>') ) { return true; } return false;">
									<?php _e('Delete'); ?>
								</a>
							</span>
						</div>
					</td>

					<!-- Number of Supporters, with changes. -->
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['supporters'] ?></label>
					</td>
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block">
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
						</label>
					</td>

					<!-- Number of Pageviews. -->
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['views'] ?></label>
					</td>

					<!-- Number of Comments. -->
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['comments'] ?></label>
					</td>

					<!-- Number of followers. -->
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['followers']; ?></label>
					</td>

					<!-- Days left to support the project. -->
					<td style="text-align: right;">
						<label for="select-<?php echo $id ?>" style="display:block"><?php echo $values['days-left']; ?></label>
					</td>
		    	</tr>
<?php
				}
		    }
		}
?>
	    </tbody>
	</table>
