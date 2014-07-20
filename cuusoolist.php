<?php
/*
 Plugin Name: CUUSOO List
 Description: Displays a list of specified LEGO&reg; Ideas (formerly CUUSOO) projects in a widget.
 Author: Drew Maughan
 Version: 2.1
 Author URI: http://perfectzerolabs.com
*/

class CUUSOOList
{

	const DOMAIN       = 'cuusoolist';

	const TARGET_VOTES = 10000; // 10,000 supporters required.
	const TARGET_DAYS  = 365;   // one year to reach the target.

	const EVENT_FETCH  = 'cuusoolist_refresh';

	const URL_BASE     = 'https://ideas.lego.com/projects/';


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
		add_option('cuusoolist_projects', $list);
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
		delete_option('cuusoolist_method'); // from previous version
		delete_option('cuusoolist_fetched');
		delete_option('cuusoolist_fetch_error');
	}


	/**
	 * CUUSOOList::get_parent_url()
	 * Returns the file name of the plugin file from the $_GET data.
	 *
	 * @return
	 */
	function get_parent_url()
	{
		return 'admin.php?page=cuusoo-list';
	}


	/**
	 * CUUSOOList::add_options_menu()
	 * Adds a menu item for the plugin's settings.
	 *
	 * @return void
	 */
	function add_admin_menu()
	{
		add_menu_page(
			'CUUSOO List Settings',            // page title
			'CUUSOO List',                     // menu title
			'manage_options',                  // capability (to see this menu item)
			'cuusoo-list',                     // identifier/menu slug
			array('CUUSOOList', 'admin_page'), // callback function to display the page.
			plugin_dir_url(__FILE__) . 'logo-ideas-menu.png', // icon URL
			55                                 // menu item position.
		);
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
	 * CUUSOOList::handler()
	 * Handles the addition and deletion of LEGO Ideas projects.
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
				// Adding a LEGO Ideas project.
				self::$message_id = CUUSOOList::_project_new();
				break;

			case 'update':
				// Updating a LEGO Ideas project without fetching data.
				self::$message_id = CUUSOOList::_project_update();
				break;

			case 'delete':
				// Deleting (or rather removing) a LEGO Ideas project.
				self::$message_id = CUUSOOList::_project_remove();
				break;

			case 'delete-many':
				// Deleting (removing) at least one LEGO Ideas project.
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
	 * Code for adding a new LEGO Ideas project.
	 *
	 * @return int message number to use.
	 */
	static private function _project_new()
	{
		check_admin_referer('add_cuusoolist');

		return CUUSOOList::update( $_POST['new_project'] ) ? 1 : 4;
	}


	/**
	 * CUUSOOList::_project_remove()
	 * Code for removing a LEGO Ideas project.
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
	 * Code for removing one or more LEGO Ideas projects.
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
	 * CUUSOOList::get()
	 * Returns the list of saved LEGO Ideas projects.
	 *
	 * @static
	 * @return array
	 */
	static function get()
	{
		return get_option('cuusoolist_projects');
	}


	/**
	 * CUUSOOList::last_update()
	 * Returns the last fetch date.
	 *
	 * @return string
	 */
	static function last_update()
	{
		return get_option('cuusoolist_fetched');
	}


	/**
	 * [url description]
	 * @param  [type] $project_id [description]
	 * @return [type]             [description]
	 */
	static function url($project_id)
	{
		return self::URL_BASE . $project_id;
	}

	/**
	 * CUUSOOList::show_list()
	 * Displays a list of defined LEGO Ideas projects in a table.
	 *
	 * @return void
	 */
	static function show_list($s, $n, $p = '1')
	{
		include('settings-list.php');
	}


	/**
	 * CUUSOOList::show_add_form()
	 * Displays a form for adding a LEGO Ideas project, or editing if the id variable is present in $_GET.
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
	 * Displays a pagination bar for the list of LEGO Ideas projects (in conjunction with CUUSOOList::show_list()).
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
	static function update($id)
	{
		// Accept the ID or the project URL.
		$id = intval( str_replace(self::URL_BASE, '', $id) );
		if ( !$id )
		{
			// No project ID was provided.
			return false;
		}

		// Fetch the project details.
		CUUSOOList::refresh( $id );

		return true;
	}


	/**
	 * CUUSOOList::delete()
	 * Removes a LEGO Ideas project.
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
	 * Returns the number of defined LEGO Ideas projects.
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
	 * "Refreshes" a LEGO Ideas project with the current project information.
	 *
	 * @return
	 */
	static function refresh($project_id)
	{
		$project_id = intval($project_id);
		if ( !$project_id )
		{
			return;
		}

		$projects = get_option('cuusoolist_projects');

		try
		{
			$values     = array();
			$supporters = 0;

			// There's no API this time, so fetching the data has to be done via page scraping. This will obviously add
			// one to the pageview total.

			$url  = self::url($project_id);
			$page = file_get_contents($url);

			// We parse the contents of the page to get what we're after.

			// The URL of the project thumbnail, from an Open Graph tag.
			preg_match('/<meta property="og:image" content="(.*)"/i', $page, $thumbnail);

			// The project title, also from an Open Graph tag.
			preg_match('/<meta property="og:title" content="(.*)"/i', $page, $title);

			// The description excerpt, again from an Open Graph tag.
			preg_match('/<meta property="og:description" content="(.*)"/i', $page, $description);

			// The author.
			preg_match('/media-body".*tile-author">(\w*)<\/a>/is', $page, $author);

			// The number of supporters.
			preg_match('/tile-supporters">\s.*h3>(\d+)<\/h3/i', $page, $supporters);

			// The number of days left to support the project.
			preg_match('/tile-days-remaining">\s.*h3>(\d+)<\/h3/i', $page, $days_left);

			// The number of page views.
			preg_match('/<span title="(.*) views">/i', $page, $views);

			// The number of project comments.
			preg_match('/<span title="(.*) comments">/i', $page, $comments);

			// The number of project followers.
			preg_match('/<span title="(.*) followers">/i', $page, $followers);

			// Get the juice!
			$values = array(
				'title'       => sanitize_text_field( $title[1] ),
				'description' => sanitize_text_field( $description[1] ),
				'author'      => sanitize_text_field( $author[1] ),
				'thumbnail'   => sanitize_text_field( $thumbnail[1] ),
				'supporters'  => intval( $supporters[1] ),
				'followers'   => intval( $followers[1] ),
				'comments'    => intval( $comments[1] ),
				'views'       => intval( $views[1] ),
				'days-left'   => intval( $days_left[1] )
			);

			// If we're updating an existing project, calculate the difference in supporters between now and the last
			// update.
			$values['diff'] = 0;
			if ( isset($project_id, $projects) )
			{
				$prev_supporters = intval($projects[$project_id]['supporters']);
				$values['diff']  = $supporters[1] - $prev_supporters;
			}

			// Add or update data for the project.
			$projects[$project_id] = $values;

			// Clear any fetching errors.
			update_option('cuusoolist_fetch_error', '');
		}
		catch (Exception $e)
		{
			// There was a problem with updating the project data - record an error message.
			update_option('cuusoolist_fetch_error', sprintf('Problem when fetching data for project %1$u on %2$s: %3$s',
				$project_id,
				current_time('timestamp'),
				$e->getMessage()
			));

			// Also display an error message.
?>
		<div id="message" class="updated fade">
			<p><?php echo "Could not fetch project information for {$project_id}: " . $e->getMessage() ?></p>
		</div>
<?php
			return;
		}

		// Update the stored list of LEGO Ideas projects.
		update_option('cuusoolist_projects', $projects);
	}


	/**
	 * CUUSOOList::refresh_projects()
	 * Refreshes each defined LEGO Ideas project with current information.
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
	 * CUUSOOList::register_widgets()
	 * Registers sidebar widgets for inclusion in themes.
	 *
	 * @return void
	 */
	static function register_widgets()
	{
		register_widget( 'CUUSOOList_ListWidget' );
		register_widget( 'CUUSOOList_RandomWidget' );
		register_widget( 'CUUSOOList_SingleWidget' );
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
	 * Sets up an event for refreshing LEGO Ideas project data, if one hasn't been set up. Refreshing of data occurs
	 * once a day.
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
			1 => __('Project was added.', CUUSOOList::DOMAIN),
			2 => __('Project was removed.', CUUSOOList::DOMAIN),
			3 => __('Project was updated.', CUUSOOList::DOMAIN),
			4 => __('Project was not added.', CUUSOOList::DOMAIN),
			5 => __('Project was not updated.', CUUSOOList::DOMAIN),
			6 => __('Projects were removed.', CUUSOOList::DOMAIN),
			7 => __('Data fetching method updated.', CUUSOOList::DOMAIN),
		);

		return isset($messages[self::$message_id]) ? $messages[self::$message_id] : null;
	}

	static function message_is_error()
	{
		return in_array(self::$message_id, array(4, 5));
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
add_action( 'admin_menu', array('CUUSOOList', 'add_admin_menu') );

// Include the widget class and initialise it so it can be added to a theme.
include('widget.php');
add_action( 'widgets_init', array( 'CUUSOOList', 'register_widgets' ) );
