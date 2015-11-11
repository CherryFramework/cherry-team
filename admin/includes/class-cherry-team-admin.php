<?php
/**
 * Sets up the admin functionality for the plugin.
 *
 * @package   Cherry_Team_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2014 Cherry Team
 */

/**
 * Class contains admin-related functionality
 */
class Cherry_Team_Admin {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up needed actions/filters for the admin to initialize.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {
		// Load post meta boxes on the post editing screen.
		add_action( 'load-post.php',     array( $this, 'load_post_meta_boxes' ) );
		add_action( 'load-post-new.php', array( $this, 'load_post_meta_boxes' ) );

		// Only run our customization on the 'edit.php' page in the admin.
		add_action( 'load-edit.php', array( $this, 'load_edit' ) );

		// Modify the columns on the "Testimonials" screen.
		add_filter( 'manage_edit-team_columns',        array( $this, 'edit_team_columns' ) );
		add_action( 'manage_team_posts_custom_column', array( $this, 'manage_team_columns' ), 10, 2 );
	}

	/**
	 * Loads custom meta boxes on the "Add New Testimonial" and "Edit Testimonial" screens.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_post_meta_boxes() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'team' === $screen->post_type ) {
			require_once( trailingslashit( CHERRY_TEAM_DIR ) . 'admin/includes/class-cherry-team-meta-boxes.php' );
		}
	}

	/**
	 * Adds a custom filter on 'request' when viewing the "Testimonials" screen in the admin.
	 *
	 * @since 1.0.0
	 */
	public function load_edit() {
		$screen = get_current_screen();

		if ( ! empty( $screen->post_type ) && 'team' === $screen->post_type ) {
			add_action( 'admin_head', array( $this, 'print_styles' ) );
		}
	}

	/**
	 * Style adjustments for the manage menu items screen.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function print_styles() {
		?>
		<style type="text/css">
		.edit-php .wp-list-table td.thumbnail.column-thumbnail,
		.edit-php .wp-list-table th.manage-column.column-thumbnail,
		.edit-php .wp-list-table td.author_name.column-author_name,
		.edit-php .wp-list-table th.manage-column.column-author_name {
			text-align: center;
		}
		</style>
		<?php
	}

	/**
	 * Filters the columns on the "Team" screen.
	 *
	 * @since  1.0.0
	 * @param  array $post_columns current post table columns.
	 * @return array
	 */
	public function edit_team_columns( $post_columns ) {
		unset(
			$post_columns['taxonomy-group'],
			$post_columns['date']
		);

		// Add custom columns.
		$post_columns['thumbnail'] = __( 'Photo', 'cherry-team' );
		$post_columns['position']  = __( 'Position', 'cherry-team' );
		$post_columns['group']     = __( 'Group', 'cherry-team' );
		$post_columns['date']      = __( 'Added', 'cherry-team' );

		// Return the columns.
		return $post_columns;
	}

	/**
	 * Add output for custom columns on the "menu items" screen.
	 *
	 * @since  1.0.0
	 * @param  string $column  current post list categories.
	 * @param  int    $post_id current post ID.
	 * @return void
	 */
	public function manage_team_columns( $column, $post_id ) {

		switch ( $column ) {

			case 'position' :

				$post_meta = get_post_meta( $post_id, CHERRY_TEAM_POSTMETA, true );

				if ( ! empty( $post_meta ) ) {
					echo ( isset( $post_meta['position'] ) && ! empty( $post_meta['position'] ) ) ? $post_meta['position'] : '&mdash;';
				}

				break;

			case 'thumbnail' :

				$thumb = get_the_post_thumbnail( $post_id, array( 50, 50 ) );

				echo ! empty( $thumb ) ? $thumb : '&mdash;';

				break;

			case 'group' :

				echo get_the_term_list( $post_id, 'group', '', ', ', '' );

				break;

			default :
				break;
		}
	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Cherry_Team_Admin::get_instance();
