<?php

// Base widget class.
abstract class CUUSOOList_Widget extends WP_Widget
{
	protected $template;

	/**
	 * CUUSOOList_Widget::widget()
	 * Creates a widget.
	 *
	 * @return void
	 */
	final function widget( $args, $instance )
	{
		$projects    = CUUSOOList::get();
		$last_update = CUUSOOList::last_update();

		if (!($projects && count($projects) > 0))
		{
			return;
		}

		extract( $args, EXTR_SKIP );
		$title = apply_filters( 'widget_title	', $instance['title'] );

		// Beginning of widget.
		echo $before_widget;

		// Widget title.
		if ($title)
		{
			echo $before_title . $title . $after_title;
		}

		// Choose which projects we want to display.
		$selected_projects = $this->select_projects($projects, $instance);

		// Find a template to use for this widget's output:
		// - specific template defined by the widget class;
		// - widget-cuusoolist.php in the current theme's folder;
		// - widget-cuusoolist.php in the plugin folder.
		$templates = array(
			get_stylesheet_directory() . DIRECTORY_SEPARATOR . $this->template,
			get_stylesheet_directory() . DIRECTORY_SEPARATOR . '/widget-cuusoolist.php',
			plugin_dir_path(__FILE__) . 'widget-cuusoolist.php'
		);
		foreach ($templates as $tpl)
		{
			if ( file_exists($tpl) )
			{
				include $tpl;

				// End of widget.
				echo $after_widget;
				return;
			}
		}

		// Warning message if a template was not found.
		echo 'Template not found!';

		// End of widget.
		echo $after_widget;
	}


	/**
	 * CUUSOOList_Widget::select_projects()
	 * Returns an array of projects to use in the displayed widget.
	 *
	 * @return array
	 */
	protected function select_projects($projects, $instance)
	{
		return $projects;
	}


	/**
	 * CUUSOOList_Widget::update()
	 * Updates the widget's settings.
	 *
	 * @return
	 */
	function update( $new_instance, $old_instance )
	{
		$instance          = $old_instance;
	    $instance['title'] = $new_instance['title'];
	    return $instance;
	}

}


/////////////////////////////////////////////////////
// List Widget: display a list of chosen projects. //
/////////////////////////////////////////////////////
class CUUSOOList_ListWidget extends CUUSOOList_Widget
{

	function __construct()
	{
		parent::WP_Widget( false, $name = 'CUUSOO List - List', array(
			'classname'   => 'widget_cuusoolist widget_cuusoolist_list',
			'description' => __( 'Displays a list of specified LEGO ideas projects.')
		) );
		$this->template = 'widget-cuusoolist-list.php';
	}


	function select_projects( $projects, $instance )
	{
		if ($instance['projects'])
		{
			$list = array();
			foreach ($instance['projects'] as $id)
			{
				$list[$id] = $projects[$id];
			}
			return $list;
		}
		else
		{
			return $projects;
		}
	}


	function update( $new_instance, $old_instance )
	{
		$instance             = parent::update($new_instance, $old_instance);
	    $instance['projects'] = esc_sql( $new_instance['projects'] );
	    return $instance;
	}


	function form( $instance )
	{
		$title = esc_attr( $instance['title'] );
		if (!($selected_projects = $instance['projects']))
		{
			$selected_projects = array();
		}
?>
	<!-- Widget title. -->
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget title' ); ?>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</label>
	</p>

	<!-- Select projects to display. -->
	<p>
		<label for="<?php echo $this->get_field_id( 'projects' ); ?>"><?php _e( 'Display these projects:' ); ?>
<?php
if ($project_list = CUUSOOList::get()) :
?>
			<select multiple size="<?php echo min(count($project_list), 6); ?>" class="widefat" name="<?php echo $this->get_field_name( 'projects' ); ?>[]" id="<?php echo $this->get_field_id( 'projects' ); ?>">
<?php
	foreach ($project_list as $id => $row) :
?>
				<option value="<?php echo $id; ?>" <?php if (in_array($id, $selected_projects)) :?>selected<?php endif; ?>><?php echo $row['title']; ?></option>
<?php
	endforeach;
?>
			</select>
<?php
else:
?>
			<em>No projects added!</em>
<?php
endif;
?>
		</label>
	</p>

	<p><small>You can override the default template by copying <code>widget-cuusoolist.php</code> from the plugin folder to
	your theme's folder. Rename it to <code>widget-cuusoolist-list.php</code> to customise CUUSOO List list widgets.</small></p>
<?php
	}
}


//////////////////////////////////////////////
// Random Widget: display a random project. //
//////////////////////////////////////////////
class CUUSOOList_RandomWidget extends CUUSOOList_Widget
{

	function __construct()
	{
		parent::WP_Widget( false, $name = 'CUUSOO List - Random', array(
			'classname'   => 'widget_cuusoolist widget_cuusoolist_random',
			'description' => __( 'Displays a random LEGO Ideas project.')
		) );
		$this->template = 'widget-cuusoolist-random.php';
	}


	function select_projects( $projects, $instance )
	{
		if ($instance['projects'] && count($instance['projects']))
		{
			// Select from only the selected projects.
			$list = array();
			foreach ($instance['projects'] as $id)
			{
				$list[$id] = $projects[$id];
			}
		}
		else
		{
			// Select from all projects.
			$list = $projects;
		}
		$random = array_rand($list, 1);
		return array($random => $list[$random]);
	}


	function update( $new_instance, $old_instance )
	{
		$instance             = parent::update($new_instance, $old_instance);
	    $instance['projects'] = esc_sql( $new_instance['projects'] );
	    return $instance;
	}


	function form( $instance )
	{
		$title    = esc_attr( $instance['title'] );
		if (!($selected_projects = $instance['projects']))
		{
			$selected_projects = array();
		}
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget title' ); ?>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</label>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'project_id' ); ?>"><?php _e( 'Select from these projects:' ); ?>
<?php
if ($projects = CUUSOOList::get()) :
?>
		<select multiple size="<?php echo min(count($projects), 6); ?>" class="widefat" name="<?php echo $this->get_field_name( 'projects' ); ?>[]" id="<?php echo $this->get_field_id( 'projects' ); ?>">
<?php
	foreach ($projects as $id => $row) :
?>
			<option value="<?php echo $id; ?>" <?php if (in_array($id, $selected_projects)) :?>selected<?php endif; ?>><?php echo $row['title']; ?></option>
<?php
	endforeach;
?>
		</select>
<?php
else:
?>
		<em>No projects added!</em>
<?php
endif;
?>
	</label>
	</p>

	<p><small>You can override the default template by copying <code>widget-cuusoolist.php</code> from the plugin folder to
	your theme's folder. Rename it to <code>widget-cuusoolist-random.php</code> to customise CUUSOO List random widgets.</small></p>
<?php
	}
}


//////////////////////////////////////////////
// Single Widget: display a single project. //
//////////////////////////////////////////////
class CUUSOOList_SingleWidget extends CUUSOOList_Widget
{

	function __construct()
	{
		parent::WP_Widget( false, $name = 'CUUSOO List - Single', array(
			'classname'   => 'widget_cuusoolist widget_cuusoolist_single',
			'description' => __( 'Displays a single LEGO Ideas project.')
		) );
		$this->template = 'widget-cuusoolist-single.php';
	}


	function select_projects( $projects, $instance )
	{
		if (isset($projects[ $instance['project_id'] ]))
		{
			// Display the chosen project.
			return array($instance['project_id'] => $projects[ $instance['project_id'] ]);
		}
		else
		{
			// Display the first defined project.
			return array_slice($projects, 0, 1);
		}
	}


	function update( $new_instance, $old_instance )
	{
		$instance          = parent::update($new_instance, $old_instance);
	    $instance['project_id'] = $new_instance['project_id'];
	    return $instance;
	}


	function form( $instance )
	{
		$title      = esc_attr( $instance['title'] );
		$project_id = esc_attr( $instance['project_id'] );
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget title' ); ?>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</label>
	</p>

	<p>
		<label for="<?php echo $this->get_field_id( 'project_id' ); ?>"><?php _e( 'Display this project:' ); ?>
<?php
if ($projects = CUUSOOList::get()) :
?>
		<select class="widefat" name="<?php echo $this->get_field_name( 'project_id' ); ?>" id="<?php echo $this->get_field_id( 'project_id' ); ?>">
<?php
	foreach ($projects as $id => $row) :
?>
			<option value="<?php echo $id; ?>" <?php selected($id, $project_id); ?>><?php echo $row['title']; ?></option>
<?php
	endforeach;
?>
		</select>
<?php
else:
?>
		<em>No projects added!</em>
<?php
endif;
?>
	</label>
	</p>

	<p><small>You can override the default template by copying <code>widget-cuusoolist.php</code> from the plugin folder to
	your theme's folder. Rename it to <code>widget-cuusoolist-single.php</code> to customise CUUSOO List single widgets.</small></p>
<?php
	}
}
