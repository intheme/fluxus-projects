<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://inthe.me/
 * @since      1.0.0
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/includes
 * @author     inTheme <contact@inthe.me>
 */
class Fluxus_Projects {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Fluxus_Projects_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FLUXUS_PROJECTS_VERSION' ) ) {
			$this->version = FLUXUS_PROJECTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'fluxus-projects';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->register_post_types();

	}

	/**
	 * Registers:
	 *   - fluxus_portfolio post type
	 *   - fluxus_project_type post taxonomy
	 */
	private function register_post_types() {
    /**
     * First we register taxonomy, then custom post type.
     * The order is important, because of rewrite rules.
     */
    $args = array(
			'label'          => __( 'Project Types', 'fluxus' ),
			'singular_label' => __( 'Project Type', 'fluxus' ),
			'public'         => true,
			'show_tagcloud'  => false,
			'hierarchical'   => true,
			'rewrite'        => false
		);
		register_taxonomy( 'fluxus-project-type', 'fluxus_portfolio',  $args );

		/**
		 * Register portfolio_project custom post type.
		 */
		$args = array(
				'label'            => __( 'Portfolio', 'fluxus' ),
				'labels' => array(
					'singular_label' => __( 'Project', 'fluxus' ),
					'all_items'      => __( 'Projects', 'fluxus' ),
				),
				'public'           => true,
				'capability_type'  => 'page',
				'rewrite'          => false,
				'taxonomy'         => 'fluxus-project-type',
				'menu_icon'        => 'dashicons-portfolio',
				'supports'         => array( 'title', 'editor',  'excerpt',  'page-attributes' )
			);
		register_post_type( 'fluxus_portfolio' , $args );

		/**
		 * Permalink structure
		 */
		add_rewrite_tag( '%fluxus-project-type%', '([^&/]+)', 'fluxus-project-type=' );
		add_rewrite_tag( '%fluxus_portfolio%', '([^&/]+)', 'fluxus_portfolio=' );

		if ( fluxus_wpml_active() ) {
				$languages = array_keys( icl_get_languages( 'skip_missing=0' ) );
				$bases = array();
				foreach ( $languages as $language ) {
						$bases[$language] = fluxus_get_default_portfolio_slug( $language );
				}
				$bases = array_unique( $bases );

				foreach ( $bases as $language => $base ) {
						add_permastruct( "fluxus-project-type-{$language}", "{$base}/%fluxus-project-type%", false );
						add_permastruct( "fluxus_portfolio-{$language}", "{$base}/%fluxus-project-type%/%fluxus_portfolio%", false );
				}
		}

		$base = fluxus_get_default_portfolio_slug();
		add_permastruct( 'fluxus-project-type', "{$base}/%fluxus-project-type%", false );
		add_permastruct( 'fluxus_portfolio', "{$base}/%fluxus-project-type%/%fluxus_portfolio%", false );
		add_permastruct( 'fluxus-project-type-default', 'portfolio/%fluxus-project-type%', false );
		add_permastruct( 'fluxus_portfolio-default', 'portfolio/%fluxus-project-type%/%fluxus_portfolio%', false );

		if ( isset( $_GET['fluxus-action'] ) && ( $_GET['fluxus-action'] == 'flush' ) && is_admin() ) {
				it_flush_rewrite_rules();
		}

		/**
		 * Grid image layout customization.
		 */
		if ( isset( $_GET['customize-layout'] ) && is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
				add_action( 'before', 'fluxus_customize_grid_layout_init' );
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Fluxus_Projects_Loader. Orchestrates the hooks of the plugin.
	 * - Fluxus_Projects_i18n. Defines internationalization functionality.
	 * - Fluxus_Projects_Admin. Defines all hooks for the admin area.
	 * - Fluxus_Projects_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fluxus-projects-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fluxus-projects-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fluxus-projects-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fluxus-projects-public.php';

		$this->loader = new Fluxus_Projects_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Fluxus_Projects_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Fluxus_Projects_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Fluxus_Projects_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Fluxus_Projects_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Fluxus_Projects_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
