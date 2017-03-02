<?php
/**
 * Handles custom post meta boxes for the 'team' post type.
 *
 * @package   Cherry_Team_Admin
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Admin meta boxes management class
 */
class Cherry_Team_Meta_Boxes {

	/**
	 * Holds the instances of this class.
	 *
	 * @since 1.0.0
	 * @var   object
	 */
	private static $instance = null;

	/**
	 * Sets up the needed actions for adding and saving the meta boxes.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'add_meta_boxes_' . CHERRY_TEAM_NAME, array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

	}

	/**
	 * Adds the meta box container.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		/**
		 * Filter the array of team members default socials pages
		 *
		 * @since  1.0.0
		 */
		$socials = apply_filters( 'cherry_team_personal_socials', array(
			array(
				'external-link'	=> '#',
				'font-class'	=> 'fa fa-facebook',
				'link-label'	=> __( 'Follow %s on Facebook', 'cherry-team' ),
			),
			array(
				'external-link'	=> '#',
				'font-class'	=> 'fa fa-twitter',
				'link-label'	=> __( 'Follow %s on Twitter', 'cherry-team' ),
			),
			array(
				'external-link'	=> '#',
				'font-class'	=> 'fa fa-google-plus',
				'link-label'	=> __( 'Follow %s on Google +', 'cherry-team' ),
			),
			array(
				'external-link'	=> '#',
				'font-class'	=> 'fa fa-linkedin',
				'link-label'	=> __( 'Find %s at LinkedIn', 'cherry-team' ),
			),
		) );

		/**
		 * Filter the array of 'add_meta_box' parametrs.
		 *
		 * @since 1.0.0
		 */
		$metabox = apply_filters(
			'cherry_team_metabox_params',
			array(
				'id'            => 'cherry-team-options',
				'title'         => __( 'Team Options', 'cherry-team' ),
				'page'          => CHERRY_TEAM_NAME,
				'context'       => 'normal',
				'priority'      => 'core',
				'callback_args' => array(
					'position' => array(
						'id'			=> 'position',
						'type'			=> 'text',
						'title'			=> __( 'Position:', 'cherry-team' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'location' => array(
						'id'			=> 'location',
						'type'			=> 'text',
						'title'			=> __( 'Location:', 'cherry-team' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'telephone' => array(
						'id'			=> 'telephone',
						'type'			=> 'text',
						'title'			=> __( 'Telephone:', 'cherry-team' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'email' => array(
						'id'			=> 'email',
						'type'			=> 'text',
						'title'			=> __( 'Email:', 'cherry-team' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'website' => array(
						'id'			=> 'website',
						'type'			=> 'text',
						'title'			=> __( 'Personal website:', 'cherry-team' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> '',
					),
					'socials' => array(
						'id'			=> 'socials',
						'type'			=> 'repeater',
						'title'			=> __( 'Socials:', 'cherry-team' ),
						'label'			=> '',
						'description'	=> '',
						'value'			=> $socials,
					),
				),
			)
		);

		/**
		 * Add meta box to the administrative interface.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 */
		add_meta_box(
			$metabox['id'],
			$metabox['title'],
			array( $this, 'callback_metabox' ),
			$metabox['page'],
			$metabox['context'],
			$metabox['priority'],
			$metabox['callback_args']
		);
	}

	/**
	 * Prints the box content.
	 *
	 * @since  1.0.0
	 * @param  object $post    Current post object.
	 * @param  array  $metabox metabox attributes.
	 * @return void
	 */
	public function callback_metabox( $post, $metabox ) {

		if ( ! class_exists( 'Cherry_Interface_Builder' ) ) {
			return;
		}

		// open core UI wrappers
		echo '<div class="cherry-ui-core">';

		// Add an nonce field so we can check for it later.
		wp_nonce_field( plugin_basename( __FILE__ ), 'cherry_team_options_meta_nonce' );

		$builder = new Cherry_Interface_Builder(
			array(
				'name_prefix' => CHERRY_TEAM_POSTMETA,
				'pattern'     => 'inline',
				'class'       => array( 'section' => 'single-section' ),
			)
		);

		foreach ( $metabox['args'] as $field ) {

			// Check if set the 'id' value for custom field. If not - don't add field.
			if ( ! isset( $field['id'] ) ) {
				continue;
			}

			$field['value'] = Cherry_Team::get_meta( $post->ID, $field['id'], $field['value'] );

			echo $builder->add_form_item( $field );

		}

		/**
		 * Fires after testimonial fields of metabox.
		 *
		 * @since 1.0.0
		 * @param object $post                Current post object.
		 * @param array  $metabox             metabox arguments.
		 * @param string CHERRY_TEAM_POSTMETA Name for 'meta_key' value in the 'wp_postmeta' table.
		 */
		do_action( 'cherry_team_metabox_after', $post, $metabox, CHERRY_TEAM_POSTMETA );

		// close core UI wrappers
		echo '</div>';
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @since 1.0.0
	 * @param int    $post_id current post ID.
	 * @param object $post    current post object.
	 */
	public function save_post( $post_id, $post ) {

		// Verify the nonce.
		if ( ! isset( $_POST['cherry_team_options_meta_nonce'] ) || ! wp_verify_nonce( $_POST['cherry_team_options_meta_nonce'], plugin_basename( __FILE__ ) ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Get the post type object.
		$post_type = get_post_type_object( $post->post_type );

		// Check if the current user has permission to edit the post.
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		// Don't save if the post is only a revision.
		if ( 'revision' == $post->post_type ) {
			return;
		}

		// Array of new post meta value.
		$new_meta_value = array();

		// Check if $_POST have a needed key.
		if ( ! isset( $_POST[ CHERRY_TEAM_POSTMETA ] ) || empty( $_POST[ CHERRY_TEAM_POSTMETA ] ) ) {
			return;
		}
		// Check if socials have empty value.
		if ( ! isset( $_POST[ CHERRY_TEAM_POSTMETA ]['socials'] ) ) {
			$_POST[ CHERRY_TEAM_POSTMETA ]['socials'] = array();
		}

		foreach ( $_POST[ CHERRY_TEAM_POSTMETA ] as $key => $value ) {

			if ( 'socials' == $key ) {

				$new_meta_value[ $key ] = $this->sanitize_socials( $value );
				continue;
			}

			// Sanitize the user input.
			$new_meta_value[ $key ] = sanitize_text_field( $value );
		}

		// Check if nothing found in $_POST array.
		if ( empty( $new_meta_value ) ) {
			return;
		}

		update_post_meta( $post_id, CHERRY_TEAM_POSTMETA, $new_meta_value );
	}

	/**
	 * Sanitize socials array before save
	 *
	 * @since  1.0.0
	 *
	 * @param  array $input social items array.
	 * @return array
	 */
	public function sanitize_socials( $input ) {

		if ( ! is_array( $input ) || empty( $input ) ) {
			return array();
		}

		array_walk_recursive( $input, array( $this, 'sanitize_socials_item' ) );
		return $input;

	}

	/**
	 * Sanitize single socials array value
	 *
	 * @since  1.0.0
	 *
	 * @param string $item array value.
	 * @param string $key  array key.
	 */
	public function sanitize_socials_item( &$item, $key ) {

		if ( 'external-link' == $key ) {
			if ( is_email( $item ) ) {
				$item = 'mailto:' . $item;
			} else {
				$item = esc_url( $item );
			}
		} else {
			$item = sanitize_text_field( $item );
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

Cherry_Team_Meta_Boxes::get_instance();
