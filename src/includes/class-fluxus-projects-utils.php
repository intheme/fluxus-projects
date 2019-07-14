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

	public static function select_tag( $attrs = array(), $options = array(), $active = false ) {
		echo '<select ' . self::array_to_attributes( $attrs ) . '>' .
			self::array_to_select_options( $options, $active ) .
		'</select>';
	}

	public static function array_to_select_options( $array, $active = '' ) {
		$html = '';

		foreach ( $array as $value => $label ) {
			$selected = $active == $value ? ' selected="selected"' : '';
			$html    .= '<option value="' . esc_attr( $value ) . '"' . $selected . '>' .
				$label .
			'</option>';
		}

		return $html;
	}

	public static function array_to_attributes( $array ) {
		$html = '';

		foreach ( $array as $attr => $value ) {
			if ( ! is_array( $value ) ) {
				$value = array( $value );
			}

			if ( 'style' == $attr ) {
				$html .= ' style="' . esc_attr( join( '; ', $value ) ) . '"';
				continue;
			}

			if ( 'class' == $attr ) {
				$html .= ' class="' . esc_attr( self::classnames( $value ) ) . '"';
				continue;
			}

			if ( $value[0] === null ) {
				$html .= ' ' . esc_attr( $attr );
			} else {
				$html .= ' ' . esc_attr( $attr ) . '="' . esc_attr( join( ' ', $value ) ) . '"';
			}
		}

		return $html;
	}

	/**
	 * Returns contents of HTML class="" attribute.
	 * Inspired by classNames JS library.
	 */
	public static function classnames( $array ) {
		$css_classes = array();

		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$css_classes = array_merge( $css_classes, explode( ' ', self::classnames( $value ) ) );
			} elseif ( is_integer( $key ) ) {
				$css_classes = array_merge( $css_classes, explode( ' ', $value ) );
			} elseif ( is_string( $key ) && is_bool( $value ) ) {
				if ( $value ) {
					$css_classes[] = $key;
				}
			} else {
				$value_type = gettype( $value );
				throw new Exception(
					"Unknown type provided to 'classnames': '$value_type'"
				);
			}
		}

		return join( ' ', array_unique( $css_classes ) );
	}

	public static function check_save_action( $id, $post_type = 'post', $no_inline_save = true ) {
		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( ! isset( $_POST['post_type'] ) ) {
			return false;
		}

		if ( $no_inline_save ) {
			if ( isset( $_POST['action'] ) && ( 'inline-save' == $_POST['action'] ) ) {
				return false;
			}
		}

		// Check permissions
		if ( $post_type == $_POST['post_type'] ) {
			if ( $post_type == 'page' ) {
				if ( ! current_user_can( 'edit_page', $id ) ) {
					return false;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $id ) ) {
					return false;
				}
			}
		} else {
			// it's not our post type, we are good to go.
			return $id;
		}

		return true;
	}
}

}
