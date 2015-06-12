<?php
/**
 * Define callback functions for templater
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

/**
 * Callbcks for team shortcode templater
 *
 * @since  1.0.0
 */
class Cherry_Team_Template_Callbacks {

	/**
	 * Shortcode attributes array
	 * @var array
	 */
	public $atts = array();

	/**
	 * Current post team-related meta
	 * @var array
	 */
	public static $post_meta = array();

	function __construct( $atts ) {
		$this->atts = $atts;
	}

	/**
	 * Get post thumbnail
	 * @since 1.0.0
	 */
	public function get_photo( $link = 'link' ) {

		global $post;

		$photo = ( isset( $post->image ) && $post->image ) ? $post->image  : '';

		if ( ! $photo ) {
			return;
		}

		if ( 'link' == $link ) {
			$format = '<a href="%2$s">%1$s</a>';
			$link   = get_permalink( $post->ID );
		} else {
			$format = '%1$s';
			$link   = false;
		}

		if ( true === $this->atts['show_photo'] || 'yes' === $this->atts['show_photo'] ) {
			return sprintf( $format, $photo, $link );
		}

	}

	/**
	 * Get team memeber name (post title)
	 * @since 1.0.0
	 */
	public function get_name() {
		global $post;
		if ( true === $this->atts['show_name'] ) {
			return get_the_title( $post->ID );
		}
	}

	/**
	 * Get team member position
	 * @since 1.0.0
	 */
	public function get_position() {

		global $post;

		$meta     = ( isset( $post->{CHERRY_TEAM_POSTMETA} ) ) ? $post->{CHERRY_TEAM_POSTMETA} : false;
		$position = ( ! empty( $meta['position'] ) ) ? $this->meta_wrap( $meta['position'], 'position' ) : '';

		return $position;
	}

	/**
	 * Get team member location
	 * @since 1.0.0
	 */
	public function get_location() {

		global $post;

		$meta     = ( isset( $post->{CHERRY_TEAM_POSTMETA} ) ) ? $post->{CHERRY_TEAM_POSTMETA} : false;
		$location = ( ! empty( $meta['location'] ) ) ? $this->meta_wrap( $meta['location'], 'location' ) : '';

		return $location;
	}

	/**
	 * Get team member email
	 * @since 1.0.0
	 */
	public function get_email() {

		global $post;

		$meta  = ( isset( $post->{CHERRY_TEAM_POSTMETA} ) ) ? $post->{CHERRY_TEAM_POSTMETA} : false;
		$email = ( ! empty( $meta['email'] ) )
						? $this->meta_wrap( $this->mail_wrap( $meta['email'] ), 'email' )
						: '';

		return $email;
	}

	/**
	 * Get team member phone number
	 * @since 1.0.0
	 */
	public function get_phone() {

		global $post;

		$meta     = ( isset( $post->{CHERRY_TEAM_POSTMETA} ) ) ? $post->{CHERRY_TEAM_POSTMETA} : false;
		$telephone = ( ! empty( $meta['telephone'] ) ) ? $this->meta_wrap( $meta['telephone'], 'telephone' ) : '';

		return $telephone;
	}

	/**
	 * Get team memeber website HTML markup
	 */
	public function get_website() {

		global $post;

		$meta    = ( isset( $post->{CHERRY_TEAM_POSTMETA} ) ) ? $post->{CHERRY_TEAM_POSTMETA} : false;
		$website = ( ! empty( $meta['website'] ) )
					? $this->meta_wrap( $this->get_website_html( $meta['website'] ), 'website' )
					: '';

		return $website;

	}

	/**
	 * Get post exerpt
	 * @since 1.0.0
	 */
	public function get_excerpt() {

		global $post;

		$post_type = get_post_type( $post->ID );

		$excerpt = has_excerpt( $post->ID ) ? apply_filters( 'the_excerpt', get_the_excerpt() ) : '';

		if ( ! $excerpt ) {

			$excerpt_length = ( ! empty( $this->atts['excerpt_length'] ) )
								? $this->atts['excerpt_length']
								: 20;

			$content = get_the_content();
			$excerpt = strip_shortcodes( $content );
			$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
			$excerpt = wp_trim_words( $excerpt, $excerpt_length, '' );

		}

		$format = '<div class="cherry-team_excerpt">%s</div>';

		return sprintf( $format, $excerpt );

	}

	/**
	 * Get post content
	 * @since  1.0.0
	 */
	public function get_content() {

		$content = apply_filters( 'the_content', get_the_content() );

		if ( ! $content ) {
			return;
		}

		$format = '<div class="post-content">%s</div>';

		return sprintf( $format, $content );
	}

	/**
	 * Get team memeber socials list
	 * @since 1.0.0
	 */
	public function get_socials() {

		global $post;
		$meta = ( isset( $post->{CHERRY_TEAM_POSTMETA} ) ) ? $post->{CHERRY_TEAM_POSTMETA} : false;

		if ( empty( $meta['socials'] ) ) {
			return;
		}

		$socials = $meta['socials'];

		$defaults = array(
			'external-link' => '',
			'font-class'    => '',
			'link-label'    => ''
		);

		$format = apply_filters(
			'cherry_team_socials_item_format',
			'<div class="team-socials_item"><a href="%s" class="team-socials_link" rel="nofollow"><i class="team-socials_icon %s"></i><span class="team-socials_label">%s</span></a></div>'
		);

		$result = '';

		foreach ( $socials as $data ) {

			$data  = wp_parse_args( $data, $defaults );
			$url   = esc_url( $data['external-link'] );
			$icon  = esc_attr( $data['font-class'] );
			$label = esc_attr( $data['link-label'] );

			$label = sprintf( $label, get_the_title( $post->ID ) );

			$result .= sprintf( $format, $url, $icon, $label );

		}

		Cherry_Team::enqueue_icon_font();

		return '<div class="team-socials">' . $result . '</div>';

	}

	/**
	 * Get link URL to team member page
	 */
	public function get_link() {
		global $post;
		return get_permalink( $post->ID );
	}

	/**
	 * Wrap single team item into HTML wrapper with custom class
	 *
	 * @since  1.0.0
	 *
	 * @param  string $value meta value
	 * @param  string $class custom CSS class
	 */
	public function meta_wrap( $value = null, $class = null ) {

		if ( ! $value ) {
			return;
		}

		$css_class = 'team-meta_item';

		if ( $class ) {
			$css_class .= ' ' . sanitize_html_class( $class );
		}

		return sprintf( '<span class="%s">%s</span>', $css_class, $value );

	}

	/**
	 * Wrap person email into link with mailto:
	 *
	 * @since  1.0.0
	 *
	 * @param  string $email Person email
	 */
	public function mail_wrap( $email ) {

		if ( ! is_email( $email ) ) {
			return;
		}

		return sprintf( '<a href="mailto:%1$s" class="team-email-link">%1$s</a>', $email );

	}

	/**
	 * Get user website HTML
	 *
	 * @since  1.0.0
	 *
	 * @param  string $url  personal wesite URL
	 * @param  string $name person name
	 */
	public function get_website_html( $url = null, $name = null ) {

		$format = apply_filters(
			'cherry_team_personal_website_format',
			'<a href="%s" class="team-website" rel="nofollow">%s</a>'
		);

		$url   = esc_url( $url );
		$label = __( 'Personal website', 'cherry-team' );

		return sprintf( $format, $url, $label );

	}

}