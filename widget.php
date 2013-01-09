<?php

class CUUSOOList_Widget extends WP_Widget
{

	/**
	 * CUUSOOList_Widget::CUUSOOList_Widget()
	 * Constructor function.
	 *
	 * @return void
	 */
	function CUUSOOList_Widget()
	{
		parent::WP_Widget( false, $name = 'CUUSOO List', array(
			'description' => __( 'Displays a list of specified CUUSOO projects.')
		) );
	}


	/**
	 * CUUSOOList_Widget::widget()
	 * Displays the front-end widget.
	 *
	 * @return void
	 */
	function widget( $args, $instance )
	{
		extract( $args, EXTR_SKIP );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( !$title ) $title = 'CUUSOO List';

		// Beginning of widget.
		echo $before_widget;

		// Widget title.
		if ($title)
		{
			echo $before_title . $title . $after_title;
		}

		// Here's where we display the CUUSOO projects. Create a theme template called 'widget-cuusoolist.php' to
		// override the default template.
		$projects = get_option('cuusoolist_projects');
		$updated  = get_option('cuusoolist_fetched');

		$widget_template = get_stylesheet_directory() . '/widget-cuusoolist.php';
		if ( !file_exists($widget_template) )
		{
			$widget_template = 'widget-cuusoolist.php';
		}
		include $widget_template;

		// End of widget.
		echo $after_widget;
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


	/**
	 * CUUSOOList_Widget::form()
	 * Sets the contents of the widget's settings form.
	 *
	 * @return void
	 */
	function form( $instance )
	{
		$title = esc_attr( $instance['title'] );
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Widget title' ); ?>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
	</label>
	</p>
<?php
	}
}

