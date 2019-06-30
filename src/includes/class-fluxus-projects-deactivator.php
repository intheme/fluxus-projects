<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://inthe.me/
 * @since      1.0.0
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/includes
 * @author     inTheme <contact@inthe.me>
 */
class Fluxus_Projects_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function on_deactivate() {

	}

	public static function deactivate() {
		deactivate_plugins( plugin_basename( FLUXUS_PROJECTS_PATH . 'fluxus-projects.php' ) );
	}

	public static function verify_dependencies() {
		if ( ! class_exists( 'Media_Meta_Box_Interface' ) ) {
			self::deactivate();
			add_action( 'admin_notices', array( 'Fluxus_Projects_Deactivator', 'media_meta_box_missing_notice' ) );
		} elseif ( Media_Meta_Box_Interface::MAJOR_VERSION !== 1 ) {
			self::deactivate();
			add_action( 'admin_notices', array( 'Fluxus_Projects_Deactivator', 'media_meta_box_wrong_version_notice' ) );
		}
	}

	public static function automatic_deactivation_notice() {
		?>
	<div class="notice notice-error is-dismissible">
		<p><?php _e( 'Fluxus Projects plugin was automatically deactivated because Media Meta Box plugin is not active.', 'fluxus-projects' ); ?></p>
	</div>
		<?php
	}

	public static function media_meta_box_wrong_version_notice() {
		?>
	<div class="notice notice-error is-dismissible">
		<p><?php printf( esc_html__( 'Fluxus Projects plugin was automatically deactivated because Media Meta Box plugin has incorrect version Expected version 1.x, got %s.', 'fluxus-projects' ), Media_Meta_Box_Interface::VERSION ); ?></p>
	</div>
		<?php
	}
}
