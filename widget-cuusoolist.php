<?php
if ( $selected_projects ) :
?>

<ol style="list-style: none; margin: 0; padding: 0;">

<?php
	// Format the last fetched date.
	$last_update_date = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), $last_update );

	foreach ($selected_projects as $id => $values) :
		// ---------------------------------------------------------------------------------------
		// Available variables:
		//  $id                    numeric LEGO Ideas project ID.
		//  $last_update           timestamp of the last update for all listed projects.
		//  $values['title']       the LEGO Ideas project name.
		//  $values['description'] a summary of the description.
		//  $values['author']      the author of the project.
		//  $values['supporters']  number of people supporting the project.
		//  $values['followers']   number of people who are following the project.
		//  $values['diff']        difference in the number of supporters since the last update.
		//  $values['thumbnail']   thumbnail associated with the project.
		//  $values['views']       number of people who've viewed the project page.
		//  $values['comments']    number of comments on the project.
		//  $values['days-left']   number of days left to support the project.
		// ---------------------------------------------------------------------------------------
?>
<!-- BEGIN LEGO Ideas project listing -->
	<li>
		<a href="<?php echo CUUSOOList::url($id); ?>" rel="external nofollow" target="_blank">
			<?php echo $values['title']; ?>
		</a>
		<small>by <?php echo $values['author']; ?></small>
	</li>
<!-- END LEGO Ideas project listing -->
<?php
	endforeach;
?>

</ol>

<!-- BEGIN last fetch date -->
<p>
	<small><strong>Last updated</strong>: <?php echo $last_update_date; ?></small>
</p>
<!-- END last fetch date -->
<?php
else :
	echo '<p><em>'.__('No projects listed.').'</em></p>';
endif;
