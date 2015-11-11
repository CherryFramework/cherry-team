<?php
/**
 * Cherry Team Widget
 *
 * @package   Cherry_Widget
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class for Team widget.
 *
 * @since 1.0.0
 */
class Cherry_Team_Widget extends WP_Widget {

	/**
	 * Unique identifier for widget.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	protected $widget_slug = 'cherry_team_widget';

	/**
	 * Instance of Cherry_Team_Data class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private $data;

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		parent::__construct(
			$this->get_widget_slug(),
			__( 'Cherry Team', 'cherry-team' ),
			array(
				'classname'   => $this->get_widget_slug(),
				'description' => __( "Your site's most recent Team.", 'cherry-team' ),
			)
		);

		$this->data = Cherry_Team_Data::get_instance();

		// Refreshing the widget's cached output with each new post.
		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );

	}

	/**
	 * Call method overloading.
	 *
	 * @since  1.0.0
	 * @param  string $method Name of the method being called.
	 * @param  array  $args   Array containing the parameters passed to the $name'ed method.
	 * @return mixed
	 */
	public function __call( $method, $args ) {
		return $this->data->$method( $args[0] );
	}

	/**
	 * Return the widget slug.
	 *
	 * @since  1.0.0
	 * @return Plugin slug variable.
	 */
	public function get_widget_slug() {
		return $this->widget_slug;
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @since 1.0.0
	 * @param array $args     The array of form elements.
	 * @param array $instance The current instance of the widget.
	 */
	public function widget( $args, $instance ) {

		// Check if there is a cached output.
		$cache = wp_cache_get( $this->get_widget_slug(), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->widget_slug;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			return print $cache[ $args['widget_id'] ];
		}

		/**
		 * Filter the widget title.
		 *
		 * @since 1.0.0
		 * @param string $title       The widget title.
		 * @param array  $instance    An array of the widget's settings.
		 * @param mixed  $widget_slug The widget ID.
		 */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->widget_slug );

		$atts = array();
		$widget_string = $args['before_widget'];

		// Display the widget title if one was input.
		if ( $title ) {
			$atts['before_title'] = $args['before_title'];
			$atts['title']        = $title;
			$atts['after_title']  = $args['after_title'];
		}

		/**
		 * Fires before a content widget.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->widget_slug . '_before' );

		// Integer values.
		if ( isset( $instance['limit'] ) && ( 0 < count( $instance['limit'] ) ) ) {
			$atts['limit'] = intval( $instance['limit'] );
		}
		if ( isset( $instance['specific_id'] ) && ( 0 < count( $instance['specific_id'] ) ) ) {
			$atts['id'] = $instance['specific_id'];
		}
		if ( isset( $instance['size'] ) && ( 0 < count( $instance['size'] ) ) ) {
			$atts['size'] = intval( $instance['size'] );
		}

		// Boolean values.
		if ( isset( $instance['show_name'] ) && ( 1 == $instance['show_name'] ) ) {
			$atts['show_name'] = true;
		} else {
			$atts['show_name'] = false;
		}
		if ( isset( $instance['show_photo'] ) && ( 1 == $instance['show_photo'] ) ) {
			$atts['show_photo'] = true;
		} else {
			$atts['show_photo'] = false;
		}

		// Select boxes.
		if ( isset( $instance['orderby'] ) && in_array( $instance['orderby'], array_keys( $this->get_orderby_options() ) ) ) {
			$atts['orderby'] = $instance['orderby'];
		}
		if ( isset( $instance['order'] ) && in_array( $instance['order'], array_keys( $this->get_order_options() ) ) ) {
			$atts['order'] = $instance['order'];
		}

		$atts['custom_class'] = esc_attr( $instance['custom_class'] );

		// Make sure we return and don't echo.
		$atts['echo'] = false;

		// set columns for team widget
		$atts['col_xs'] = 12;

		/**
		 * Filter the array of widget arguments.
		 *
		 * @since 1.0.0
		 * @param array  Arguments.
		 * @param string widget unique slug
		 */
		$atts = apply_filters( 'cherry_team_widget_args', $atts, $this->widget_slug );

		$widget_string .= $this->the_team( $atts );
		$widget_string .= $args['after_widget'];

		$cache[ $args['widget_id'] ] = $widget_string;

		wp_cache_set( $this->get_widget_slug(), $cache, 'widget' );

		print $widget_string;

		/**
		 * Fires after a content widget.
		 *
		 * @since 1.0.0
		 */
		do_action( $this->widget_slug . '_after' );
	}

	/**
	 * Clear widget cahce.
	 *
	 * @return  void
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->get_widget_slug(), 'widget' );
	}

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @since  1.0.0
	 * @param  array $new_instance The new instance of values to be generated via the update.
	 * @param  array $old_instance The previous instance of values before the update.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags for title and name to remove HTML (important for text inputs).
		$instance['title']       = strip_tags( $new_instance['title'] );
		$instance['specific_id'] = strip_tags( $new_instance['specific_id'] );

		// Make sure the integer values are definitely integers.
		$instance['limit'] = intval( $new_instance['limit'] );
		$instance['size']  = intval( $new_instance['size'] );

		// The select box is returning a text value, so we escape it.
		$instance['orderby'] = esc_attr( $new_instance['orderby'] );
		$instance['order']   = esc_attr( $new_instance['order'] );

		// The checkbox is returning a Boolean (true/false), so we check for that.
		$instance['show_name']  = (bool) esc_attr( $new_instance['show_name'] );
		$instance['show_photo'] = (bool) esc_attr( $new_instance['show_photo'] );

		$instance['custom_class'] = sanitize_html_class( $new_instance['custom_class'] );

		return apply_filters( 'cherry_team_widget_update', $instance );
	}

	/**
	 * Generates the administration form for the widget.
	 *
	 * @since 1.0.0
	 * @param array $instance The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		/**
		 * Filters some default widget settings.
		 *
		 * @since 1.0.0
		 * @param array
		 */
		$defaults = apply_filters( 'cherry_team_widget_form_defaults_args', array(
			'title'        => '',
			'limit'        => 2,
			'orderby'      => 'date',
			'order'        => 'DESC',
			'specific_id'  => '',
			'show_name'    => true,
			'show_photo'   => true,
			'size'         => 50,
			'custom_class' => '',
		) );

		$instance     = wp_parse_args( (array) $instance, $defaults );
		$title        = esc_attr( $instance['title'] );
		$limit        = absint( $instance['limit'] );
		$size         = absint( $instance['size'] );
		$show_name    = (bool) $instance['show_name'];
		$show_photo   = (bool) $instance['show_photo'];
		$specific_id  = esc_attr( $instance['specific_id'] );
		$orderby      = $this->get_orderby_options();
		$order        = $this->get_order_options();
		$custom_class = esc_attr( $instance['custom_class'] );

		// Display the admin form.
		include( apply_filters( 'cherry_team_widget_form_file', trailingslashit( CHERRY_TEAM_DIR ) . 'admin/views/widget.php' ) );
	}

	/**
	 * Get an array of the available orderby options.
	 *
	 * @since  1.0.0
	 * @param  string $template template name.
	 * @param  array  $args     arguments array.
	 * @return array
	 */
	public function item_template( $template, $args ) {
		return '<blockquote>%%TEXT%% %%AVATAR%% %%AUTHOR%%</blockquote>';
	}

	/**
	 * Get an array of the available orderby options.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	protected function get_orderby_options() {
		return apply_filters( 'cherry_team_get_orderby_options', array(
			'none'       => __( 'No Order', 'cherry-team' ),
			'ID'         => __( 'Entry ID', 'cherry-team' ),
			'title'      => __( 'Title', 'cherry-team' ),
			'date'       => __( 'Date Added', 'cherry-team' ),
			'menu_order' => __( 'Attributes Order', 'cherry-team' ),
			'rand'       => __( 'Random Order', 'cherry-team' ),
			) );
	}

	/**
	 * Get an array of the available order options.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	protected function get_order_options() {
		return array(
			'ASC'  => __( 'Ascending', 'cherry-team' ),
			'DESC' => __( 'Descending', 'cherry-team' ),
			);
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget("Cherry_Team_Widget");' ) );
