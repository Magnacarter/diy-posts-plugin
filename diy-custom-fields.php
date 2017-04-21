<?php

class DIY_Meta_Box {

	/**
	 * Hold meta box instance
	 *
	 * @var string
	 */
	public static $instance;

	/**
	 * Hook our metabox into the post admin area
	 *
	 * @action load-post.php
	 * @action load-post-new.php
	 */
	public function __construct() {

		if ( is_admin() ) {

			add_action( 'load-post.php',     array( $this, 'init_metabox' ) );

			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );

		}

	}

	/**
	 * Hook our save and add methods into WordPress
	 *
	 * @action add_meta_boxes
	 * @action save_post
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'add_metabox'  )        );

		add_action( 'save_post',      array( $this, 'save_metabox' ), 10, 2 );

	}

	/**
	 * Tell WordPress to create the meta box using our custom callback 
	 *
	 * @action add_meta_boxes
	 *
	 */
	public function add_metabox() {

		add_meta_box(

			'diy_info',

			__( 'DIY Info', 'text_domain' ),

			array( $this, 'render_metabox' ),

			'post',

			'advanced',

			'default'

		);

	}

	/**
	 * Render the meta box in the post editor screen
	 *
	 * @param object $post
	 *
	 */
	public function render_metabox( $post ) {
 
		// Add nonce for security and authentication.
		wp_nonce_field( 'diy_nonce_action', 'diy_nonce' );

		// Retrieve an existing value from the database.
		$diy_cost   = get_post_meta( $post->ID, 'diy_cost', true );

		$diy_time   = get_post_meta( $post->ID, 'diy_time', true );

		$diy_rating = get_post_meta( $post->ID, 'diy_rating', true );

		// Set default values.
		if( empty( $diy_cost ) ) $diy_cost = '';

		if( empty( $diy_time ) ) $diy_time = '';

		if( empty( $diy_rating ) ) $diy_rating = '';

		// Form fields.
		echo '<table class="form-table">';

		echo '	<tr>';

		echo '		<th><label for="diy_time" class="diy_time_label">' . __( 'Time To Complete', 'text_domain' ) . '</label></th>';
		echo '		<td>';
		echo '			<input type="text" id="diy_time" name="diy_time" class="diy_time_field" placeholder="' . esc_attr__( '', 'text_domain' ) . '" value="' . esc_attr__( $diy_time ) . '">';
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="diy_cost" class="diy_cost_label">' . __( 'Cost', 'text_domain' ) . '</label></th>';
		echo '		<td>';
		echo '			<input type="text" id="diy_cost" name="diy_cost" class="diy_cost_field" placeholder="' . esc_attr__( '', 'text_domain' ) . '" value="' . esc_attr__( $diy_cost ) . '">';
		echo '		</td>';
		echo '	</tr>';

		echo '	<tr>';
		echo '		<th><label for="diy_rating" class="diy_rating_label">' . __( 'Difficulty Rating', 'text_domain' ) . '</label></th>';
		echo '		<td>';
		echo '			<select name="diy_rating_select" id="diy_rating_select">';
		echo '				<option value="Oil Changer"'     . selected( $diy_rating, "Oil Changer" )     . '>Oil Changer</option>';
		echo '				<option value="Weekend Warrior"' . selected( $diy_rating, "Weekend Warrior" ) . '>Weekend Warrior</option>';
		echo '				<option value="Tool Collector"'  . selected( $diy_rating, "Tool Collector" )  . '>Tool Collector</option>';
		echo '				<option value="Car Whisperer"'   . selected( $diy_rating, "Car Whisperer" )   . '>Car Whisperer</option>';
		echo '			</select>';
		echo '		</td>';
		echo '	</tr>';

		echo '</table>';

	}

	/**
	 * Do secutrity checks, sanitize and escape user inputs and 
	 * save the meta data from the meta box into WordPress database
	 *
	 * @param int $post_id
	 * @param object $post
	 */
	public function save_metabox( $post_id, $post ) {

		// Add nonce for security and authentication.
		$nonce_name   = $_POST['diy_nonce'];

		$nonce_action = 'diy_nonce_action';

		// Check if a nonce is set.
		if ( ! isset( $nonce_name ) )

			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )

			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )

			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )

			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )

			return;

		// Sanitize user input.
		$diy_cost = isset( $_POST[ 'diy_cost' ] ) ? sanitize_text_field( $_POST[ 'diy_cost' ] ) : '';

		$diy_time = isset( $_POST[ 'diy_time' ] ) ? sanitize_text_field( $_POST[ 'diy_time' ] ) : '';

		$diy_rating = isset( $_POST['diy_rating_select'] ) ? esc_attr( $_POST['diy_rating_select'] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, 'diy_time', $diy_time );

		update_post_meta( $post_id, 'diy_cost', $diy_cost );

		update_post_meta( $post_id, 'diy_rating', $diy_rating );

	}

	/**
	 * Return active instance of DIY_Meta_Box, create one if it doesn't exist
	 *
	 * @return DIY_Meta_Box
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}

}

$GLOBALS['diy_meta_box'] = DIY_Meta_Box::get_instance();