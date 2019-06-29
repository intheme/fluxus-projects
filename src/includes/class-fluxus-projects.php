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
		$this->define_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		/**
		 * TGM-Plugin-Activation class that managers dependencies of this plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/vendor/class-tgm-plugin-activation.php';

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

		/**
		 * Various small utility functions
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fluxus-projects-utils.php';

		/**
		 * WPML Plugin support
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fluxus-projects-wpml.php';

		/**
		 * Helper class for adding useful methods to page entity
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fluxus-projects-page.php';

		/**
		 * Class for storing project data.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fluxus-projects-project.php';

		/**
		 * Project Types widget
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/widgets/class-fluxus-projects-project-types-widget.php';

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
		$this->loader->add_action( 'before', $plugin_admin, 'before' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
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

	private function define_hooks() {
		$this->loader->add_action( 'init', $this, 'init' );
		$this->loader->add_action( 'pre_get_posts', $this, 'order_fluxus_portfolio' );
		$this->loader->add_action( 'tgmpa_register', $this, 'required_plugins' );
		$this->loader->add_action( 'widgets_init', $this, 'widgets_init' );
	}

	/**
	 * Registers:
	 *   - fluxus_portfolio post type
	 *   - fluxus_project_type post taxonomy
	 */
	public function init() {
		/**
		 * Following class depends on Media Meta Box plugin therefore we load it in 'init' hook when all plugins were already initialized.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/project-media/class-fluxus-projects-project-media-meta-box.php';

    /**
     * First we register taxonomy, then custom post type.
     * The order is important, because of rewrite rules.
     */
    $args = array(
			'label'          => __( 'Project Types', 'fluxus-projects' ),
			'singular_label' => __( 'Project Type', 'fluxus-projects' ),
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
			'label'            => __( 'Portfolio', 'fluxus-projects' ),
			'labels' => array(
				'singular_label' => __( 'Project', 'fluxus-projects' ),
				'all_items'      => __( 'Projects', 'fluxus-projects' ),
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

		if ( Fluxus_Projects_Wpml::is_active() ) {
			$languages = array_keys( icl_get_languages( 'skip_missing=0' ) );
			$bases = array();
			foreach ( $languages as $language ) {
				$bases[$language] = $this->get_default_portfolio_slug( $language );
			}
			$bases = array_unique( $bases );

			foreach ( $bases as $language => $base ) {
				add_permastruct( "fluxus-project-type-{$language}", "{$base}/%fluxus-project-type%", false );
				add_permastruct( "fluxus_portfolio-{$language}", "{$base}/%fluxus-project-type%/%fluxus_portfolio%", false );
			}
		}

		$base = $this->get_default_portfolio_slug();
		add_permastruct( 'fluxus-project-type', "{$base}/%fluxus-project-type%", false );
		add_permastruct( 'fluxus_portfolio', "{$base}/%fluxus-project-type%/%fluxus_portfolio%", false );
		add_permastruct( 'fluxus-project-type-default', 'portfolio/%fluxus-project-type%', false );
		add_permastruct( 'fluxus_portfolio-default', 'portfolio/%fluxus-project-type%/%fluxus_portfolio%', false );

		if ( isset( $_GET['fluxus-action'] ) && ( $_GET['fluxus-action'] == 'flush' ) && is_admin() ) {
			Fluxus_Projects_Utils::flush_rewrite_rules();
		}

		/**
		 * Grid image layout customization.
		 */
		if ( isset( $_GET['customize-layout'] ) && is_user_logged_in() && current_user_can( 'edit_pages' ) ) {
			add_action( 'before', 'fluxus_customize_grid_layout_init' );
		}
	}

	public function order_fluxus_portfolio( $query ) {
		$vars = $query->query_vars;
    $is_projects_query = isset( $vars['post_type'] ) && ( $vars['post_type'] == 'fluxus_portfolio' );
    $is_project_type_query = isset( $vars['fluxus-project-type'] ) && $vars['fluxus-project-type'];

    if ( $is_projects_query || $is_project_type_query ) {
        // Set the default fluxus_portfolio order to menu_order ASC, ID DESC
        if ( ! $query->get( 'orderby' ) ) {
            $query->set( 'orderby', array( 'menu_order' => 'ASC', 'ID' => 'DESC' ) );
				}
				$query->set( 'posts_per_page', -1 );
    }
	}

	public function required_plugins() {
		$plugins = array(
			array(
				'name'     => 'Media Meta Box',
				'slug'     => 'media-meta-box',
				'required' => true,
				'source'   => 'http://miami.local/media-meta-box.zip',
				'external_url' => 'http://miami.local/media-meta-box.zip'
			)
		);

		$config = array(
			'id'           => 'fluxus-prjects',        // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'themes.php',            // Parent menu slug.
			'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.
		);

		tgmpa( $plugins, $config );
	}

	public function widgets_init() {
		register_widget( 'Fluxus_Projects_Project_Types_Widget' );
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

	/**
	 * Returns slug of a page that has a 'Horizontal Portfolio' template assigned.
	 * If no such page can be found, then 'portfolio' is returned.
	 */
	private function get_default_portfolio_slug( $language = '' ) {
		$portfolio_page = $this->get_default_portfolio_page( $language );

		if ( $portfolio_page ) {
			$slug = $portfolio_page->post_name;
		} else {
			$slug = 'portfolio';
		}

		return apply_filters( 'fluxus_projects_base_slug', $slug );
	}

	/**
	 * Returns a default portfolio page, which should be used when generating URLs.
	 * If WPML plugin is used, then it will automatically return a page for current language.
	 *
	 * @param string $language Return default portfolio page for a specific language.
	 * @return mixed returns page object or FALSE if a page couldn't be found.
	 */
	private function get_default_portfolio_page( $language = '' ) {
		$cache_key = Fluxus_Projects_Utils::get_save_post_cache_key( 'portfolio-base-' . $language );
		$found = false;
		$cached_data = wp_cache_get( $cache_key, 'fluxus-projects', false, $found );

		if ( $found ) {
			return $cached_data;
		}

		$horizontal_portfolios = Fluxus_Projects_Utils::it_find_page_by_template( 'template-portfolio.php' );
		$horizontal_portfolios = empty( $horizontal_portfolios ) ? array() : $horizontal_portfolios;

		$grid_portfolios = Fluxus_Projects_Utils::it_find_page_by_template( 'template-portfolio-grid.php' );
		$grid_portfolios = empty( $grid_portfolios ) ? array() : $grid_portfolios;

		$portfolios = array_merge( $horizontal_portfolios, $grid_portfolios );
		usort( $portfolios, array( 'Fluxus_Projects_Utils', 'compare_by_page_id' ) );

		$result = false;

		if ( Fluxus_Projects_Wpml::is_active() && $language ) {
			foreach ( $portfolios as $portfolio ) {
				if ( $language == Fluxus_Projects_Wpml::get_element_language( $portfolio->ID, 'post_page' ) ) {
					$result = $portfolio;
					break;
				}
			}
		} else {
			if ( ! empty( $portfolios ) ) {
				$result = $portfolios[0];
			}
		}

		wp_cache_set( $cache_key, $result, 'fluxus-projects' );

		return $result;
	}

}
