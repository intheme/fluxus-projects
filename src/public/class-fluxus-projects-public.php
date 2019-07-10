<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://inthe.me/
 * @since      1.0.0
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/public
 * @author     inTheme <contact@inthe.me>
 */
class Fluxus_Projects_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fluxus_Projects_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fluxus_Projects_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fluxus-projects-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fluxus_Projects_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fluxus_Projects_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fluxus-projects-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Generates correct Project Type links when WPML is active.
	 *
	 * @param $termlink URL
	 * @param $term term
	 * @param $taxonomy taxonomy
	 */
	public function project_type_permalink( $termlink, $term, $taxonomy ) {
		/**
		 * Don't replace anything if it's not a fluxus-project-type taxonomy,
		 * if there was an error or we are not using fancy links.
		 */
		if ( is_wp_error( $term ) || 'fluxus-project-type' !== $term->taxonomy || empty( $termlink ) ) {
			return $termlink;
		}

		if ( Fluxus_Projects_Wpml::is_active() ) {
				$project_type_language = Fluxus_Projects_Wpml::get_element_language( $term->term_id, 'tax_fluxus-project-type' );
				$default_base          = Fluxus_Projects::get_default_portfolio_slug();
				$correct_base          = Fluxus_Projects::get_default_portfolio_slug( $project_type_language );
				$termlink              = str_replace(
					$default_base . '/' . $term->slug,
					$correct_base . '/' . $term->slug,
					$termlink
				);
		}

		return $termlink;
	}

	/**
	 * Generates links to portfolio projects.
	 */
	public function portfolio_permalink( $permalink, $post, $leavename ) {
		/**
		 * If there's an error with post, or this is not fluxus_portfolio
		 * or we are not using fancy links.
		 */
		if ( is_wp_error( $post ) || 'fluxus_portfolio' !== $post->post_type || empty( $permalink ) ) {
			return $permalink;
		}

		// Find out project type.
		$project_type = '';
		if ( strpos( $permalink, '%fluxus-project-type%' ) !== false ) {
			$terms = get_the_terms( $post->ID, 'fluxus-project-type' );

			if ( $terms ) {
				// sort terms by ID.
				usort( $terms, array( 'Fluxus_Projects_Utils', 'compare_by_term_id' ) );
				$project_type = $terms[0]->slug;
			} else {
				$project_type = 'uncategorized';
			}
		}

		$rewrite_codes = array(
			'%fluxus-project-type%',
			$leavename ? '' : '%fluxus_portfolio%',
		);

		if ( Fluxus_Projects_Wpml::is_active() ) {
			$project_language = Fluxus_Projects_Wpml::get_element_language(
				$post->ID,
				'post_fluxus_portfolio'
			);
			$default_base     = Fluxus_Projects::get_default_portfolio_slug();
			$correct_base     = Fluxus_Projects::get_default_portfolio_slug(
				$project_language
			);
			$permalink        = str_replace(
				$default_base . '/%fluxus-project-type%/',
				$correct_base . '/%fluxus-project-type%/',
				$permalink
			);
		}

		$rewrite_replace = array( $project_type, $post->post_name );
		$permalink       = str_replace( $rewrite_codes, $rewrite_replace, $permalink );

		return $permalink;
	}
}
