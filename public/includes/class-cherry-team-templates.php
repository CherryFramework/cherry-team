<?php
/**
 * Cherry Team
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Class for including page templates.
 *
 * @since 1.0.0
 */
class Cherry_Team_Templater {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Posts number per team archive and page template
	 *
	 * @since 1.0.0
	 * @var   integer
	 */
	public static $posts_per_archive_page = null;

	/**
	 * The array of templates that this plugin tracks.
	 *
	 * @since 1.0.0
	 * @var   array
	 */
	protected $templates;

	/**
	 * Sets up needed actions/filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->templates = array();

		// Set posts per archive team page
		add_action( 'pre_get_posts', array( $this, 'set_posts_per_archive_page' ) );

		// Add a filter to the page attributes metabox to inject our template into the page template cache.
		add_filter( 'theme_page_templates', array( $this, 'register_custom_template' ), 10, 3 );

		// Add a filter to the template include in order to determine if the page has our template assigned and return it's path.
		add_filter( 'template_include', array( $this, 'view_template' ) );

		// Add a filter to load a custom template for a given post.
		add_filter( 'single_template', array( $this, 'get_single_template' ) );

		add_filter( 'theme_page_templates', array( $this, 'add_templates' ) );

		// Add your templates to this array.
		$this->templates = array(
			'template-team.php' => __( 'Team Page', 'cherry-team' ),
		);

	}

	/**
	 * Add services page templates.
	 *
	 * @param  array $templates Existing templates array.
	 * @return array
	 */
	public function add_templates( $templates = array() ) {
		return array_merge( $templates, $this->templates );
	}

	/**
	 * Register custom page tamplate for Services page
	 *
	 * @since  1.0.4
	 * @param  array  $page_templates existing page templates array.
	 * @param  object $instance       instanse of WP_Theme class.
	 * @param  object $post           current post object.
	 * @return array
	 */
	public function register_custom_template( $page_templates, $instance, $post ) {

		if ( ! empty( $post->post_type ) && 'page' === $post->post_type ) {
			$page_templates = array_merge( $page_templates, $this->templates );
		}

		return $page_templates;
	}

	/**
	 * Setup posts number per archive page
	 *
	 * @since  1.0.0
	 * @param  object $query main query object.
	 * @return void|bool false
	 */
	public function set_posts_per_archive_page( $query ) {

		// Must work only for public.
		if ( is_admin() ) {
			return $query;
		}

		// And only for main query
		if ( ! $query->is_main_query() ) {
			return $query;
		}

		$is_archive = $query->is_post_type_archive( CHERRY_TEAM_NAME );

		if ( $is_archive || $this->is_team_tax( $query ) ) {
			$query->set( 'posts_per_page', self::get_posts_per_archive_page() );
		}
	}

	/**
	 * Check if passed query is services taxonomy
	 *
	 * @since  1.0.5
	 * @param  object $query current query object.
	 * @return boolean
	 */
	public function is_team_tax( $query ) {

		$tax = 'group';
		return ! empty( $query->query_vars[ $tax ] );
	}

	/**
	 * Get number of posts per archive page
	 *
	 * @since  1.0.5
	 * @return int
	 */
	public static function get_posts_per_archive_page() {

		if ( null !== self::$posts_per_archive_page ) {
			self::$posts_per_archive_page;
		}

		/**
		 * Filter posts per archive page value
		 * @var int
		 */
		self::$posts_per_archive_page = apply_filters( 'cherry_team_posts_per_archive_page', 6 );

		return self::$posts_per_archive_page;
	}

	/**
	 * Checks if the template is assigned to the page.
	 *
	 * @since  1.0.0
	 * @param  string $template current template name.
	 * @return string
	 */
	public function view_template( $template ) {

		global $post;

		// check if we need archive template to include
		if ( is_post_type_archive( CHERRY_TEAM_NAME ) || is_tax( 'group' ) ) {

			$file = trailingslashit( CHERRY_TEAM_DIR ) . 'templates/archive-team.php';

			if ( file_exists( $file ) ) {
				return $file;
			}
		}

		if ( ! is_page( $post ) ) {
			return $template;
		}

		$page_template_meta = get_post_meta( $post->ID, '_wp_page_template', true );

		if ( ! isset( $this->templates[ $page_template_meta ] ) ) {
			return $template;
		}

		$file = trailingslashit( CHERRY_TEAM_DIR ) . 'templates/' . $page_template_meta;

		// Just to be safe, we check if the file exist first.
		if ( file_exists( $file ) ) {
			return $file;
		}

		return $template;
	}

	/**
	 * Adds a custom single template for a 'Team' post.
	 *
	 * @since 1.0.0
	 */
	public function get_single_template( $template ) {
		global $post;

		if ( $post->post_type == CHERRY_TEAM_NAME ) {
			$template = trailingslashit( CHERRY_TEAM_DIR ) . 'templates/single-team.php';
		}

		return $template;
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

Cherry_Team_Templater::get_instance();
