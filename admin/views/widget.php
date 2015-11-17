<?php
/**
 * View for widget control form
 *
 * @package   Cherry_Team
 * @author    Cherry Team
 * @license   GPL-2.0+
 * @link      http://www.cherryframework.com/
 * @copyright 2015 Cherry Team
 */

/**
 * Fires before a widget form.
 *
 * @since 1.0.0
 */
do_action( 'cherry_team_widget_form_before' );

?>
<!-- Widget Title: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cherry-team' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
</p>
<!-- Widget Limit: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit:', 'cherry-team' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'limit' ); ?>" value="<?php echo $limit; ?>" class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" />
</p>
<!-- Widget Show Name: Checkbox Input -->
<p>
	<input id="<?php echo $this->get_field_id( 'show_name' ); ?>" name="<?php echo $this->get_field_name( 'show_name' ); ?>" type="checkbox"<?php checked( $show_name, 1 ); ?> />
	<label for="<?php echo $this->get_field_id( 'show_name' ); ?>"><?php _e( 'Show name', 'cherry-team' ); ?></label>
</p>
<!-- Widget Show Photo: Checkbox Input -->
<p>
	<input id="<?php echo $this->get_field_id( 'show_photo' ); ?>" name="<?php echo $this->get_field_name( 'show_photo' ); ?>" type="checkbox"<?php checked( $show_photo, 1 ); ?> />
	<label for="<?php echo $this->get_field_id( 'show_photo' ); ?>"><?php _e( 'Show photo', 'cherry-team' ); ?></label>
</p>
<!-- Widget Photo Size: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Photo Size (in pixels):', 'cherry-team' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'size' ); ?>" value="<?php echo $size; ?>" class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>" />
</p>
<!-- Widget ID: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'specific_id' ); ?>"><?php _e( 'Specific ID:', 'cherry-team' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'specific_id' ); ?>" value="<?php echo $specific_id; ?>" class="widefat" id="<?php echo $this->get_field_id( 'specific_id' ); ?>" />
</p>
<p><small><?php _e( 'Post IDs, separated by commas.', 'cherry-team' ); ?></small></p>
<!-- Widget Order By: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order By:', 'cherry-team' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>">
	<?php foreach ( $orderby as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['orderby'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Order: Select Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order Direction:', 'cherry-team' ); ?></label>
	<select name="<?php echo $this->get_field_name( 'order' ); ?>" class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>">
	<?php foreach ( $order as $k => $v ) { ?>
		<option value="<?php echo $k; ?>"<?php selected( $instance['order'], $k ); ?>><?php echo $v; ?></option>
	<?php } ?>
	</select>
</p>
<!-- Widget Custom CSS Class: Text Input -->
<p>
	<label for="<?php echo $this->get_field_id( 'custom_class' ); ?>"><?php _e( 'Custom CSS Class:', 'cherry-team' ); ?></label>
	<input type="text" name="<?php echo $this->get_field_name( 'custom_class' ); ?>" value="<?php echo $custom_class; ?>" class="widefat" id="<?php echo $this->get_field_id( 'custom_class' ); ?>" />
</p>
<?php
	/**
	 * Fires after a widget form.
	 *
	 * @since 1.0.0
	 */
	do_action( 'cherry_team_widget_form_after' );
