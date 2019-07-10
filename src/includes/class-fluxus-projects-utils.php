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
class Fluxus_Projects_Utils {

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public static function is_wpml_plugin_active() {
		global $sitepress;
		return defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE && $sitepress;
	}

	public static function is_media_meta_box_active() {
		return defined( 'Media_Meta_Box' );
	}

	/**
	 * Flushes WP rewrite rules cache
	 */
	public static function flush_rewrite_rules() {
		global $wp_rewrite;

		$wp_rewrite->flush_rules();
		flush_rewrite_rules();
	}

	public static function get_save_post_cache_key( $cache_key ) {
		$namespace = wp_cache_get( 'fluxus-projects-save-post-cache-key' );
		if ( $namespace === false ) {
			$namespace = 1;
			wp_cache_set( 'fluxus-projects-save-post-cache-key', $namespace );
		}

		return $cache_key . ':' . $namespace;
	}

	static function fluxus_clear_save_post_cache( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		self::flush_rewrite_rules();
		wp_cache_incr( 'fluxus_save_post_cache_key' );
	}

	/**
	 * Return posts that match given template filename
	 */
	public static function get_pages_by_template( $template_filename, $args = array() ) {
		$defaults = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'orderby'        => 'ID',
			'order'          => 'asc',
			'meta_query'     => array(
				array(
					'key'     => '_wp_page_template',
					'value'   => $template_filename,
					'compare' => '=',
				),
			),
		);

		$args = wp_parse_args( $args, $defaults );

		return get_posts( $args );
	}

	/**
	 * Returns post ID from $_GET / $_POST arrays.
	 */
	public static function post_id_from_query_params() {
		if ( isset( $_GET['post'] ) ) {
			return $_GET['post'];
		}

		if ( isset( $_POST['post_ID'] ) ) {
			return $_POST['post_ID'];
		}

		if ( isset( $_GET['post_ID'] ) ) {
			return $_GET['post_ID'];
		}

		if ( isset( $_POST['post'] ) ) {
			return $_POST['post'];
		}

		if ( isset( $_POST['post_id'] ) ) {
			return $_POST['post_id'];
		}

		if ( isset( $_GET['post_id'] ) ) {
			return $_GET['post_id'];
		}

		return null;
	}

	public static function compare_by_page_id( $page_1, $page_2 ) {
		return $page_1->ID > $page_2->ID;
	}

	public static function compare_by_term_id( $term_a, $term_b ) {
		if ( $term_a->term_id > $term_b->term_id ) {
			return 1;
		} elseif ( $term_a->term_id < $term_b->term_id ) {
			return -1;
		} else {
			return 0;
		}
	}

}
