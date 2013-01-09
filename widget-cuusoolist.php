<?php

if ( $projects )
{
	// Format the last fetched date.
	$fetched = date_i18n( get_option('date_format') . ' ' . get_option('time_format'),
		get_option('cuusoolist_fetched') );

	echo '<ol>';
	foreach ($projects as $id => $values)
	{

// ---------------------------------------------------------------------------------------
// Available values:
//
//  $id                    numeric CUUSOO project ID
//  $updated               timestamp of the last data fetch for all projects
//  $values['label']       the label manually assigned to the project
//  $values['supports']    number of people supporting the project
//  $values['bookmarks']   number of people who have bookmarked the project
//  $values['difference']  difference in number of supporters since the last update.
//
//  ONLY AVAILABLE THROUGH THE PAGE SCRAPE METHOD:
//  $values['title']       the CUUSOO project name
//  $values['thumbnail']   thumbnail associated with the project (88x51 pixels)
//  $values['views']       number of people who've viewed the project page
//  $values['ratio']       ratio of supporters to viewers (as percentage).
//
// To override this template, create a file in your theme called 'widget-cuusoolist.php'.
// ---------------------------------------------------------------------------------------

		$url     = "http://lego.cuusoo.com/ideas/view/{$id}";

		// Make the actual project title take precedence if available.
		$label = $values['title'];
		if ( empty($label) ) $label = $values['label'];
		if ( empty($label) ) $label = '<em>untitled</em>';

		echo '<li>';
		echo "<a href=\"{$url}\" rel=\"external\" target=\"_blank\">{$label}</a>";
		echo '</li>';
	}
	echo '</ol>';
	echo "<p><small><strong>Last updated</strong>: {$fetched}</small></p>";
}
else
{
	echo '<p><em>'.__('No projects listed.').'</em></p>';
}
