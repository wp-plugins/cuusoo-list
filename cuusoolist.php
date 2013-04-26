<?php
/*
 Plugin Name: CUUSOO List
 Description: Displays a list of specified LEGO&reg; CUUSOO projects in a widget.
 Author: Drew Maughan
 Version: 1.3.3
 Author URI: http://perfectzerolabs.com
*/

class CUUSOOList
{

	const DOMAIN      = 'cuusoolist';

	const METHOD_PAGE = 0;
	const METHOD_API  = 1;

	const EVENT_FETCH = 'cuusoolist_refresh';


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
	 * Adds a menu item for the plugin's settings page (under the Settings menu).
	 *
	 * @return void
	 */
	static function add_options_menu()
	{
		add_options_page('CUUSOO List', 'CUUSOO List', 1, 'cuusoolist.php', array('CUUSOOList', 'admin_page'));
	}


	/**
	 * CUUSOOList::get_parent_url()
	 * Returns the file name of the plugin file from the $_GET data.
	 *
	 * @return
	 */
	static function get_parent_url()
	{
		if ( isset($_GET['page']) )
		{
			return $_GET['page'];
		}
	}


	/**
	 * CUUSOOList::handler()
	 * Handles the addition and deletion of CUUSOO projects.
	 *
	 * @return void
	 */
	static function handler()
	{
		load_plugin_textdomain( CUUSOOList::DOMAIN, dirname( plugin_basename( __FILE__ ) ) );

		$title       = __('Definitions', CUUSOOList::DOMAIN);
		$parent_file = 'options-general.php?page=' . CUUSOOList::get_parent_url();

		if ( 'cuusoolist.php' == substr($parent_file, -14) )
		{
			$list = get_option('cuusoolist_projects');

			//wp_reset_vars(array('action', 'definition'));

			if ( isset($_GET['delete']) ) // Deleting more than one project
			{
				$action = 'delete-many';
			}
			else
			{
				$action = (isset($_GET['action'])) ? $_GET['action'] : null;
			}

			switch ($action)
			{
				case 'add':

					// Adding a CUUSOO project.

					check_admin_referer('add_cuusoolist');

					$id	     = intval( $_GET['id'] );
					$label   = $_GET['label'];
					$message = CUUSOOList::update($id, $label) ? 1 : 4;

					wp_redirect("{$parent_file}&message={$message}");

					exit;
					break;

				case 'update':

					// Updating a CUUSOO project without fetching data.

					check_admin_referer('update_cuusoolist');

					$id	     = intval( $_GET['id'] );
					$label   = $_GET['label'];
					$message = CUUSOOList::update($id, $label, false) ? 3 : 5;

					wp_redirect("{$parent_file}&message={$message}");

					exit;
					break;

				case 'delete':

					// Deleting (or removing) a CUUSOO project.

					check_admin_referer('delete_cuusoolist');

					if ( !current_user_can('manage_categories') )
					{
						wp_die( __('Nuh-uh!') );
					}

					$id      = $_GET['id'];
					$message = 2;

					CUUSOOList::delete($id);

					wp_redirect( "{$parent_file}&message={$message}" );

					exit;
					break;

				case 'delete-many':

					// Deleting (removing) at least one CUUSOO project.

					check_admin_referer('delete_cuusoolist');

					if ( !current_user_can('manage_categories') )
					{
						wp_die( __('Nuh-uh!') );
					}

					$projects = $_GET['delete'];
					foreach ( (array) $projects as $id )
					{
						CUUSOOList::delete($id);
					}

					// Display a slightly different message depending on how many (one or more) projects were removed.
					$message = ( count($projects) > 1 ) ? 6 : 2;

					wp_redirect("{$parent_file}&message={$message}");

					exit;
					break;

				case 'method':

					// Changing the data fetching method.

					check_admin_referer('method_cuusoolist');

					if ( !current_user_can('manage_categories') )
					{
						wp_die( __('Nuh-uh!') );
					}

					$method  = $_GET['which_method'];
					$message = 7;
					update_option('cuusoolist_method', $method);

					wp_redirect("{$parent_file}&message={$message}");

					exit;
					break;

			}
		}
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
		$project_id = array_key_exists('id', $_GET) ? $_GET['id'] : null;
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
		if ( empty($id) )
		{
			return false;
		}
		else
		{
			if ( $fetch )
			{
				CUUSOOList::refresh( $id, $label );
			}
			else
			{
				$list = get_option('cuusoolist_projects');
				$list[$id]['label'] = esc_html( stripslashes($label) );
				update_option('cuusoolist_projects', $list);
			}
			return true;
		}
	}


	/**
	 * CUUSOOList::delete()
	 * Removes a CUUSOO project.
	 *
	 * @return
	 */
	function delete($project_id)
	{
		$list = get_option('cuusoolist_projects');
		if ( array_key_exists($project_id, $list) )
		{
			unset($list[$project_id]);
			update_option('cuusoolist_projects', $list);
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * CUUSOOList::count_projects()
	 * Returns the number of defined CUUSOO projects.
	 *
	 * @return
	 */
	static function count_projects()
	{
		$list = get_option('cuusoolist_projects');
		return count($list);
	}


	/**
	 * CUUSOOList::refresh()
	 * "Refreshes" a CUUSOO project with the current project information.
	 * This is reluctantly done by "page scraping" as the CUUSOO API doesn't provide the number of page views (required
	 * to calculate the supporters/viewers ratio). This probably results in incrementing views by one every time.
	 *
	 * @return
	 */
	static function refresh($project_id, $label)
	{
		$project_id = intval($project_id);
		if ( $project_id == 0 ) return;

		$list   = get_option('cuusoolist_projects');
		$method = get_option('cuusoolist_method');

		// Perform some page scraping, which is not ideal because it may trigger a page view - but is necessary to get
		// information that the API doesn't provide (title and views).
		try
		{
			$values       = array();
			$supports_num = 0;

			switch ( $method )
			{
				case CUUSOOList::METHOD_API:

					// Fetching data via the API.

					$url  = "http://lego.cuusoo.com/api/participations/get/{$project_id}.json";
					$json = file_get_contents($url);
					$data = json_decode($json);

					$values = array(
						'supports'  => $data->participants->supporters,
						'bookmarks' => $data->participants->bookmarks
					);

					$supports_num = intval($data->participants->supporters);

					break;

				case CUUSOOList::METHOD_PAGE:

					// Fetching data via page scraping (ugh).

					$url  = "http://lego.cuusoo.com/ideas/view/{$project_id}";
					$page = file_get_contents($url);

					// Main image (to get the URL of the thumbnail).
					preg_match('/src="(.*)thumb640x360.jpg" alt="Idea Image"\/>/i', $page, $image);

					// Project title.
					preg_match('/<div id="ideaName" class="projectTitle"><p>(.*)<\/p><\/div>/i', $page, $title);

					// Number of supporters.
					preg_match('/<ul class="supporters">(.*) supporters<\/ul>/i', $page, $support);

					// Number of views.
					preg_match('/<ul class="views">(.*) views<\/ul>/i', $page, $view);

					// Number of bookmarks.
					preg_match('/<ul class="followers">(.*) bookmarked<\/ul>/i', $page, $bookmark);

					// Just to be sure, remove any commas from the supports and views counts.
					$supports_num = intval(str_replace(',', '', $support[1]));
					$views_num    = intval(str_replace(',', '', $view[1]));

					// Get the juice!
					$values = array(
						'title'      => $title[1],
						'thumbnail'  => $image[1] . 'thumb81x55.jpg',
						'supports'   => $support[1],
						'views'      => $view[1],
						'bookmarks'  => $bookmark[1],
						'ratio'      => round(($supports_num / $views_num) * 100), // ratio of supports/views.
					);

					break;
			}

			// If we're updating an existing project, calculate the difference in supporters between now and the last
			// update.
			$values['diff'] = 0;
			if ( array_key_exists($project_id, $list) )
			{
				$last_supports  = intval(str_replace(',', '', $list[$project_id]['supports']));
				$values['diff'] = $supports_num - $last_supports;
			}

			// Don't forget to add the label!
			if ($label) $values['label'] = $label;

			// Replace the existing data for the project.
			$list[$project_id] = $values;

			update_option('cuusoolist_fetch_error', '');
		}
		catch (Exception $e)
		{
			// If for some reason there was a problem with updating a project.
			update_option('cuusoolist_fetch_error', 'Failed fetch for ({{$project_id}) on ' .
			  current_time('timestamp') . ': ' . $e->getMessage());
?>
		<div id="message" class="updated fade">
			<p><?php echo "Could not fetch project information for {$project_id}: " . $e->getMessage() ?></p>
		</div>
<?php
		}

		// Update the list of CUUSOO projects.
		update_option('cuusoolist_projects', $list);
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


}

// Initialise the plugin, adding hooks for installation and uninstallation.
add_action( 'admin_init', array('CUUSOOList', 'handler') );
register_activation_hook( __FILE__, array( 'CUUSOOList', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CUUSOOList', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'CUUSOOList', 'uninstall' ) );

// Check whether a fetching event is scheduled.
add_action('wp', array('CUUSOOList', 'schedule_refresh'));

// Display a version of the widget for the admin-side dashboard.
add_action( 'wp_dashboard_setup', array('CUUSOOList', 'register_dashboard_widget') );

// Set up an action for refreshing the data for each project.
add_action( CUUSOOList::EVENT_FETCH, array('CUUSOOList', 'refresh_projects') );

// Add a menu item for the plugin.
add_action( 'admin_menu', array('CUUSOOList', 'add_options_menu') );


// Include the widget class and initialise it so it can be added to a theme.
include('widget.php');
add_action( 'widgets_init', array( 'CUUSOOList', 'register_widget' ) );
