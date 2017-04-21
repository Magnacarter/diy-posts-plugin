<?php
/**
 * Plugin Name: DIY Posts Plugin
 * Plugin URI: https://github.com/Magnacarter/diy-posts-plugin
 * Description: Allow user to assign meta data about a DIY job to the top of a post.
 * Version: 0.1.0
 * Author: Adam Carter
 * Author URI: http://adamkristopher.com
 * License: GPLv2+
 * Text Domain: diy-post
 */

class DIY_Posts_Plugin {

	/**
	 * Plugin version number
	 *
	 * @const string
	 */
	const VERSION = '0.1.0';

	/**
	 * Hold plugin instance
	 *
	 * @var string
	 */
	public static $instance;

	/**
	 * Class constructor
	 */
	private function __construct() {

		define( 'DIY_PLUGIN'    , plugin_basename( __FILE__ ) );

		define( 'DIY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

		define( 'DIY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

		define( 'DIY_HOMEPAGE_URL', 'https://github.com/Magnacarter/diy-posts-plugin' );

		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_enqueue_scripts' ) );

		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );

		require_once DIY_PLUGIN_DIR . 'diy-custom-fields.php';

		require_once DIY_PLUGIN_DIR . 'diy-front-end.php';

	}

	/**
	 * Register admin menu
	 *
	 * @action admin_menu
	 */
	public static function admin_menu() {

		add_options_page(

			__( 'DIY Posts', 'diy-posts' ),

			__( 'DIY Posts', 'diy-posts' ),

			'manage_options',

			'diy-posts',

			array( __CLASS__, 'settings_page' )

		);

	}

	/**
	 * Enqueue admin scripts and styles
	 *
	 * @action admin_enqueue_scripts
	 */
	public static function admin_enqueue_scripts() {

		if ( ! isset( get_current_screen()->id ) || 'settings_page_diy-posts' !== get_current_screen()->id ) {

			return;

		}

		wp_enqueue_style( 'farbtastic' );

		wp_enqueue_script( 'farbtastic' );

	}

	/**
	 * Register settings and fields
	 *
	 * @action admin_init
	 */
	public static function admin_init() {

		// Register setting
		register_setting( 'diy-settings-group', 'diy-settings' );

		// Register sections
		add_settings_section(

			'settings_section_one',

			__( 'DIY Colors', 'diy-posts' ),

			array( __CLASS__, 'settings_section_one_callback' ),

			'diy-posts'

		);

		// Register fields
		add_settings_field(

			'diy_background_color',

			__( 'Background Color', 'diy-posts' ),

			array( __CLASS__, 'background_color_callback' ),

			'diy-posts',

			'settings_section_one'

		);

	}

	/**
	 * Setting section one callback
	 */
	public static function settings_section_one_callback() {

		echo 'Select a background color that compliments your website, or leave blank to use the default styles.';

	}

	/**
	 * Setting field callbacks
	 */
	public static function background_color_callback() {

		$settings         = get_option( 'diy-settings', array() );

		$background_color = ( isset( $settings['background_color'] ) && ! empty( $settings['background_color'] ) ) ? $settings['background_color'] : '#f5f5f5'; ?>

		<input type="text" name="diy-settings[background_color]" id="diy_background_color" class="diy-color-picker-input" value="<?php echo esc_attr( $background_color ); ?>" />

		<div class="diy-color-picker" rel="diy_background_color"></div>

		<?php

	}

	/**
	 * Render the settings page
	 */
	public static function settings_page() {

		?>

		<script type="text/javascript">
		//<![CDATA[
			jQuery( document ).ready( function() {
				jQuery( '.diy-color-picker-input' ).on( 'focus', function() {
					var $this = jQuery( this );

					$this.next( '.diy-color-picker' ).show( 500 );
				});
				jQuery( '.diy-color-picker-input' ).on( 'focusout', function() {
					var $this = jQuery( this );

					$this.next( '.diy-color-picker' ).hide( 500 );
				});
				jQuery( '.diy-color-picker' ).each( function() {
					var $this = jQuery( this ),
					    id    = $this.attr( 'rel' );

					$this.farbtastic( '#' + id );
				});
			});
		//]]>
		</script>

		<style type="text/css">

			.diy-color-picker { display: none; }

		</style>

		<div class="wrap">

			<h2><?php _e( 'DIY Posts', 'diy-posts' ) ?></h2>

			<form action="options.php" method="POST">

				<?php settings_fields( 'diy-settings-group' ) ?>

				<?php do_settings_sections( 'diy-posts' ) ?>

				<?php submit_button() ?>

			</form>

		</div>

		<?php

	}

	/**
	 * Return active instance of DIY_Posts_plugin, create one if it doesn't exist
	 *
	 * @return DIY_Posts_Plugin
	 */
	public static function get_instance() {

		if ( empty( self::$instance ) ) {

			$class = __CLASS__;

			self::$instance = new $class;

		}

		return self::$instance;

	}

}

$GLOBALS['diy_posts_plugin'] = DIY_Posts_Plugin::get_instance();