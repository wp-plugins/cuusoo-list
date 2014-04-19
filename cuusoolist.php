<?php
/*
 Plugin Name: CUUSOO List
 Description: Displays a list of specified LEGO&reg; CUUSOO projects in a widget.
 Author: Drew Maughan
 Version: 1.4
 Author URI: http://perfectzerolabs.com
*/

class CUUSOOList
{

	const DOMAIN      = 'cuusoolist';

	const METHOD_PAGE = 0;
	const METHOD_API  = 1;

	const EVENT_FETCH = 'cuusoolist_refresh';

	static private $message_id = 0;


	/**
	 * CUUSOOList::install()
	 * Run when the plugin is activated.
	 *
	 * @return void
	 */
	function activate()
	{
		$list   = get_option('cuusoolist_projects', array());
		$method = get_option('cuusoolist_method', CUUSOOList::METHOD_API);

		add_option('cuusoolist_projects', $list);
		add_option('cuusoolist_method', $method);
		CUUSOOList::schedule_refresh();
	}


	/**
	 * CUUSOOList::deactivate()
	 * Runs when the plugin is deactivated.
	 *
	 * @return void
	 */
	function deactivate()
	{
		wp_clear_scheduled_hook( CUUSOOList::EVENT_FETCH );
		// In the horrific event where multiple events are registered, this should remove all of them.
	}


	/**
	 * CUUSOOList::uninstall()
	 * Run when the plugin is removed.
	 *
	 * @return void
	 */
	function uninstall()
	{
		CUUSOOList::deactivate();
		delete_option('cuusoolist_projects');
		delete_option('cuusoolist_method');
		delete_option('cuusoolist_fetched');
	}


	/**
	 * CUUSOOList::get_parent_url()
	 * Returns the file name of the plugin file from the $_GET data.
	 *
	 * @return
	 */
	static function get_parent_url()
	{
		return 'admin.php?page=cuusoo-list';

		// if ( isset($_POST['page']) )
		// {
		// 	return $_POST['page'];
		// }
	}


	/**
	 * CUUSOOList::admin_page()
	 * Provides the HTML for the plugin's admin page.
	 *
	 * @return void
	 */
	static function admin_page()
	{
		include('settings.php');
	}


	/**
	 * CUUSOOList::add_options_menu()
	 * Adds a menu item for the plugin's settings page.
	 *
	 * @return void
	 */
	static function add_options_menu()
	{
		add_menu_page(
			'CUUSOO List Settings', // page title
			'CUUSOO List',                     // menu title
			'manage_options',                  // capability (to see this menu item)
			'cuusoo-list',                     // menu slug
			array('CUUSOOList', 'admin_page'), // function to display the page
			null,                              // icon URL
			55                                 // menu item position.
		);
	}


	/**
	 * CUUSOOList::handler()
	 * Handles the addition and deletion of CUUSOO projects.
	 *
	 * @return void
	 */
	static function handler()
	{
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		load_plugin_textdomain( CUUSOOList::DOMAIN, dirname( plugin_basename( __FILE__ ) ) );

		$title = __('Definitions', CUUSOOList::DOMAIN);
		$list  = get_option('cuusoolist_projects');

		// What action are we performing?
		if ( isset($_POST['delete']) ) // Deleting more than one project
		{
			$action = 'delete-many';
		}
		else
		{
			$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
		}

		switch ($action)
		{
			case 'add':
				// Adding a CUUSOO project.
				self::$message_id = CUUSOOList::_project_new();
				break;

			case 'update':
				// Updating a CUUSOO project without fetching data.
				self::$message_id = CUUSOOList::_project_update();
				break;

			case 'delete':
				// Deleting (or rather removing) a CUUSOO project.
				self::$message_id = CUUSOOList::_project_remove();
				break;

			case 'delete-many':
				// Deleting (removing) at least one CUUSOO project.
				self::$message_id = CUUSOOList::_project_remove_many();
				break;

			case 'method':
				// Changing the data fetching method.
				self::$message_id = CUUSOOList::_project_method();
				break;

		}
	}


	/**
	 * CUUSOOList::_project_new()
	 * Code for adding a new CUUSOO project.
	 *
	 * @return int message number to use.
	 */
	static private function _project_new()
	{
		check_admin_referer('add_cuusoolist');

		$id	     = intval( $_POST['id'] );
		$label   = $_POST['label'];

		return CUUSOOList::update($id, $label) ? 1 : 4;
	}


	/**
	 * CUUSOOList::_project_update()
	 * Code for updating a CUUSOO project.
	 *
	 * @return int message number to use.
	 */
	static private function _project_update()
	{
		check_admin_referer('update_cuusoolist');

		$id	     = intval( $_POST['id'] );
		$label   = $_POST['label'];

		return CUUSOOList::update($id, $label, false) ? 3 : 5;
	}


	/**
	 * CUUSOOList::_project_remove()
	 * Code for removing a CUUSOO project.
	 *
	 * @return int message number to use.
	 */
	static private function _project_remove()
	{
		check_admin_referer('delete_cuusoolist');

		if ( !current_user_can('manage_categories') )
		{
			wp_die( __('Nuh-uh!') );
		}

		$id = intval($_REQUEST['id']);
		CUUSOOList::delete($id);

		return 2;
	}


	/**
	 * CUUSOOList::_project_remove_many()
	 * Code for removing one or more CUUSOO projects.
	 *
	 * @return int message number to use.
	 */
	static private function _project_remove_many()
	{
		check_admin_referer('delete_cuusoolist');

		if ( !current_user_can('manage_categories') )
		{
			wp_die( __('Nuh-uh!') );
		}

		$projects = $_POST['delete'];
		foreach ( (array) $projects as $id )
		{
			CUUSOOList::delete($id);
		}

		// Display a slightly different message depending on how many (one or more) projects were removed.
		return (count($projects) > 1) ? 6 : 2;
	}


	/**
	 * CUUSOOList::_project_method()
	 * Code for changing the data fetching method.
	 *
	 * @return int message number to use.
	 */
	static private function _project_method()
	{
		check_admin_referer('method_cuusoolist');

		if ( !current_user_can('manage_categories') )
		{
			wp_die( __('Nuh-uh!') );
		}

		$method = $_POST['which_method'];
		update_option('cuusoolist_method', intval($method));

		return 7;
	}


	/**
	 * CUUSOOList::show_list()
	 * Displays a list of defined CUUSOO projects in a table.
	 *
	 * @return void
	 */
	static function show_list($s, $n, $p = '1')
	{
		include('settings-list.php');
	}


	/**
	 * CUUSOOList::show_add_form()
	 * Displays a form for adding a CUUSOO project, or editing if the id variable is present in $_GET.
	 *
	 * @return void
	 */
	static function show_add_form()
	{
		$project_id = isset($_GET['id']) ? $_GET['id'] : null;
		include('settings-add.php');
	}


	/**
	 * CUUSOOList::show_method_form()
	 * Displays a form for changing the data fetching method.
	 *
	 * @return void
	 */
	static function show_method_form()
	{
		include('settings-method.php');
	}


	/**
	 * CUUSOOList::show_pagination()
	 * Displays a pagination bar for the list of CUUSOO projects (in conjunction with CUUSOOList::show_list()).
	 *
	 * @return void
	 */
	static function show_pagination($s, $n, $p, $t, $filter = false)
	{
		include('settings-pagination.php');
	}


	/**
	 * CUUSOOList::update()
	 * Updates (or adds) a project with the current project information.
	 *
	 * @return
	 */
	static function update($id, $label, $fetch = true)
	{
		$id    = intval($id);
		$label = sanitize_text_field($label);

		if ( !$id )
		{
			// No project ID was provided.
			return false;
		}

		if ( $fetch )
		{
			// Fetch the project details.
			CUUSOOList::refresh( $id, $label );
		}
		else
		{
			$projects               = get_option('cuusoolist_projects');
			$projects[$id]['label'] = $label;
			update_option('cuusoolist_projects', $projects);
		}
		return true;
	}


	/**
	 * CUUSOOList::delete()
	 * Removes a CUUSOO project.
	 *
	 * @return
	 */
	function delete($project_id)
	{
		$projects = get_option('cuusoolist_projects');
		if ( array_key_exists($project_id, $projects) )
		{
			unset($projects[$project_id]);
			update_option('cuusoolist_projects', $projects);
			return true;
		}
		return false;
	}


	/**
	 * CUUSOOList::count_projects()
	 * Returns the number of defined CUUSOO projects.
	 *
	 * @return
	 */
	static function count_projects()
	{
		$projects = get_option('cuusoolist_projects');
		return count($projects);
	}


	/**
	 * CUUSOOList::refresh()
	 * "Refreshes" a CUUSOO project with the current project information.
	 *
	 * @return
	 */
	static function refresh($project_id, $label)
	{
		$project_id = intval($project_id);
		if ( !$project_id )
		{
			return;
		}

		$projects = get_option('cuusoolist_projects');
		$method   = get_option('cuusoolist_method');

		try
		{
			$values     = array();
			$supporters = 0;

			switch ( $method )
			{
				case CUUSOOList::METHOD_API:

					// Fetching project data via the API.

					$url    = "http://lego.cuusoo.com/api/participations/get/{$project_id}.json";
					$json   = file_get_contents($url);
					$data   = json_decode($json);

					$values = array(
						'supports'  => intval($data->participants->supporters),
						'bookmarks' => intval($data->participants->bookmarks)
					);

					$supporters = intval($data->participants->supporters);

					break;

				case CUUSOOList::METHOD_PAGE:

					// Fetching data via page scraping. More data can be obtained this way, but it will obviously add
					// one page hit to the total.

					$url  = "http://lego.cuusoo.com/ideas/view/{$project_id}";
					$page = file_get_contents($url);

					// Main image (to get the URL of the thumbnail).
					preg_match('/src="(.*)thumb640x360.jpg" alt="Idea Image"\/>/i', $page, $image);

					// Project title.
					preg_match('/<div id="ideaName" class="projectTitle"><p>(.*)<\/p><\/div>/i', $page, $title);

					// Number of supporters.
					preg_match('/<ul class="supporters">(.*) supporters<\/ul>/i', $page, $support);

					// Number of project views.
					preg_match('/<ul class="views">(.*) views<\/ul>/i', $page, $view);

					// Number of people who bookmarked the project.
					preg_match('/<ul class="followers">(.*) bookmarked<\/ul>/i', $page, $bookmark);

					// Just to be sure, remove any commas from the supports and views counts.
					$supporters = intval(str_replace(',', '', $support[1]));
					$views      = intval(str_replace(',', '', $view[1]));
					$bookmarks  = intval(str_replace(',', '', $bookmark[1]));

					// Get the juice!
					$values = array(
						'title'     => sanitize_text_field( $title[1] ),
						'thumbnail' => sanitize_text_field( $image[1] ) . 'thumb81x55.jpg',
						'supports'  => $supporters,
						'views'     => $views,
						'bookmarks' => $bookmarks,
						'ratio'     => round(($supporters / $views) * 100), // ratio of supports/views.
					);

					break;
			}

			// If we're updating an existing project, calculate the difference in supporters between now and the last
			// update.
			$values['diff'] = 0;
			if ( array_key_exists($project_id, $projects) )
			{
				$prev_supporters = intval(str_replace(',', '', $projects[$project_id]['supports']));
				$values['diff']  = $supporters - $prev_supporters;
			}

			// Set the project's label, even if it's blank.
			if ($label)
			{
				$values['label'] = sanitize_text_field($label);
			}

			// Replace the existing data for the project.
			$projects[$project_id] = $values;

			update_option('cuusoolist_fetch_error', '');
		}
		catch (Exception $e)
		{
			// There was a problem with updating the project data.
			update_option('cuusoolist_fetch_error', sprintf('Failed fetch for (%1$u) on %2$s: %3$s',
				$project_id,
				current_time('timestamp'),
				$e->getMessage()
			));
?>
		<div id="message" class="updated fade">
			<p><?php echo "Could not fetch project information for {$project_id}: " . $e->getMessage() ?></p>
		</div>
<?php
			return;
		}

		// Update the list of CUUSOO projects.
		update_option('cuusoolist_projects', $projects);
	}


	/**
	 * CUUSOOList::refresh_projects()
	 * Refreshes each defined CUUSOO project with current information.
	 *
	 * @return void
	 */
	function refresh_projects()
	{
		$list = get_option('cuusoolist_projects');
		foreach ( $list as $id => $values )
		{
			CUUSOOList::refresh( $id );
		}

		// Set a 'last updated' date.
		update_option('cuusoolist_fetched', current_time('timestamp'));
	}


   	/**
   	 * CUUSOOList::register_widget()
   	 * Registers the sidebar widget for inclusion in themes.
   	 *
   	 * @return void
   	 */
   	static function register_widget()
   	{
   		register_widget( 'CUUSOOList_Widget' );
	}


	/**
	 * CUUSOOList::register_dashboard_widget()
	 * Registers a widget to be displayed in the WordPress dashboard.
	 *
	 * @return void
	 */
	function register_dashboard_widget()
	{
		global $wp_meta_boxes;
		wp_add_dashboard_widget( 'CUUSOOList_Dashboard_Widget', 'CUUSOO List', array('CUUSOOList', 'dashboard_widget') );
	}


	/**
	 * CUUSOOList::dashboard_widget()
	 * Outputs the contents of the dashboard widget.
	 *
	 * @return void
	 */
	function dashboard_widget()
	{
		include 'widget-dashboard.php';
	}


	/**
	 * CUUSOOList::schedule_refresh()
	 * Sets up an event for refreshing CUUSOO project data, if one hasn't been set up.
	 * Refreshing of data occurs once a day.
	 *
	 * @return void
	 */
	static function schedule_refresh()
	{
		if ( CUUSOOList::next_fetch() === false )
		{
			wp_schedule_event( time(), 'daily', CUUSOOList::EVENT_FETCH );
		}
	}


	/**
	 * CUUSOOList::next_fetch()
	 * Returns the timestamp of the next fetch event.
	 *
	 * @return int
	 */
	static function next_fetch()
	{
		return wp_next_scheduled( CUUSOOList::EVENT_FETCH );
	}


	static function message()
	{
		$messages = array(
			1 => __('CUUSOO project added.', CUUSOOList::DOMAIN),
			2 => __('CUUSOO project removed.', CUUSOOList::DOMAIN),
			3 => __('CUUSOO project updated.', CUUSOOList::DOMAIN),
			4 => __('CUUSOO project was not added.', CUUSOOList::DOMAIN),
			5 => __('CUUSOO project was not updated.', CUUSOOList::DOMAIN),
			6 => __('CUUSOO project removed.', CUUSOOList::DOMAIN),
			7 => __('Data fetching method updated.', CUUSOOList::DOMAIN),
		);

		return isset($messages[self::$message_id]) ? $messages[self::$message_id] : null;
	}

}

// Initialise the plugin, adding hooks for installation and uninstallation.
add_action( 'admin_init', array('CUUSOOList', 'handler') );
register_activation_hook( __FILE__, array( 'CUUSOOList', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CUUSOOList', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'CUUSOOList', 'uninstall' ) );

// Check whether a fetching event is scheduled.
add_action( 'wp', array('CUUSOOList', 'schedule_refresh') );

// Display a version of the widget for the admin-side dashboard.
add_action( 'wp_dashboard_setup', array('CUUSOOList', 'register_dashboard_widget') );

// Set up an action for refreshing the data for each project.
add_action( CUUSOOList::EVENT_FETCH, array('CUUSOOList', 'refresh_projects') );

// Add a menu item for the plugin.
add_action( 'admin_menu', array('CUUSOOList', 'add_options_menu') );

// Include the widget class and initialise it so it can be added to a theme.
include('widget.php');
add_action( 'widgets_init', array( 'CUUSOOList', 'register_widget' ) );
