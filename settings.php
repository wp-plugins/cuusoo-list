<?php
// Settings page for CUUSOO List.
?>

<div class="wrap">

	<h2>
		<img src="<?php echo plugin_dir_url(__FILE__); ?>logo-ideas.png" alt="" style="display: inline-block; vertical-align: middle;" />
		<?php _e( 'CUUSOO List', CUUSOOList::DOMAIN ); ?> <small>for LEGO Ideas</small>
	</h2>
	<p>This plugin lets you maintain a list of <a href="https://ideas.lego.com">LEGO Ideas</a> projects you want to
	follow or promote. Display them on your site using CUUSOO List widgets.</p>

<?php
	// Set any error/notice messages based on the 'message' GET value.
	$message = CUUSOOList::message();
	if (!is_null($message)) :
?>
	<div id="message" class="<?php echo CUUSOOList::message_is_error() ? 'error' : 'updated'; ?> fade">
		<p><?php echo $message; ?></p>
	</div>
<?php
	endif;
?>

<?php
	// Display any fetch error messages.
	$fetch_error = get_option('cuusoolist_fetch_error');
	if ($fetch_error) :
?>
	<div class="error"><?php echo $fetch_error; ?></div>
<?php
	endif;
?>

	<!-- Last and next fetch dates, with an update button. -->
<?php
	$last_fetch = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), get_option('cuusoolist_fetched') );
	$next_fetch = date_i18n( get_option('date_format') . ' ' . get_option('time_format'), CUUSOOList::next_fetch() );
?>
	<div class="alternate">
		<p>
			<strong>Last fetch:</strong> <?php echo $last_fetch ?><br />
			<strong>Next fetch:</strong> <?php echo $next_fetch ?>
		</p>
	</div>

	<hr />

	<!-- Add or edit a LEGO Ideas project. -->
	<div class="col-wrap">
		<?php CUUSOOList::show_add_form(); ?>
	</div>

	<hr />

<?php
// Only display the table and pagination if we have projects.
$project_count = CUUSOOList::count_projects($s);
if ($project_count) :
?>

	<!-- A list of added projects. -->
	<form id="posts-filter" action="<?php echo CUUSOOList::get_parent_url(); ?>" method="post">
		<input type="hidden" name="page" value="<?php echo CUUSOOList::get_parent_url(); ?>"/>
<?php
// Retrieve and set pagination information
$s = isset($_GET['s']) ? urldecode($_GET['s']) : ''; // Number of acronyms per page
$p = isset($_GET['p']) && is_numeric($_GET['p']) && 0 < $_GET['p'] ? $_GET['p'] : 1; // Which page to display
$n = 15; // Default number of acronyms to show per page
if (0 == $n)
{
	$t = 1;
}
else
{
	$t = ceil(CUUSOOList::count_projects($s) / $n); // Total number of pages, rounded up to nearest integer
}
CUUSOOList::show_pagination($s, $n, $p, $t);
CUUSOOList::show_list($s, $n, $p);
CUUSOOList::show_pagination($s, $n, $p, $t);
?>
	</form>

	<hr class="clear" />
<?php
endif; // if ($project_count)
?>

	<!-- Make a donation! -->
	<div class="col-wrap">
		<p>
			If you've found this plugin useful, feel free to
			<a href="http://bit.ly/106ekd9" rel="external" target="_blank">buy me a coffee</a>
			or make a donation.
		</p>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick" />
			<input type="hidden" name="hosted_button_id" value="89QB8KQSAQ3RE" />
			<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="The dreaded PayPal button..." />
			<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
		</form>
	</div>

	<!-- Disclaimer. -->
	<p>
		<small>LEGO is a trademark of the LEGO Group, wicho has no involvement in this plugin.</small>
	</p>

</div>