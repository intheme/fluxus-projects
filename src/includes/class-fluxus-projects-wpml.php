<?php

/**
 * Various small utility functions
 *
 * @link       https://inthe.me/
 * @since      1.0.0
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/includes
 */

/**
 * Namespace for various utility methods
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/includes
 * @author     inTheme <contact@inthe.me>
 */
class Fluxus_Projects_Wpml {

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	static public function is_active() {
		global $sitepress;
    return defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE && $sitepress;
  }

  static public function get_element_language( $id, $element_type ) {
    global $sitepress;
    return $sitepress->get_language_for_element( $id, $element_type );
  }

}
