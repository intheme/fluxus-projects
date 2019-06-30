<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://inthe.me/
 * @since      1.0.0
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fluxus_Projects
 * @subpackage Fluxus_Projects/admin
 * @author     inTheme <contact@inthe.me>
 */
class Fluxus_Projects_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->load_dependencies();
	}

	private function load_dependencies() {
		/**
		 * Shows extra column information in Portfolio > Index view
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fluxus-projects-wp-ui.php';

		/**
		 * Project page options.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fluxus-projects-project-admin.php';

		/**
		 * Project type options.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fluxus-projects-project-type-options.php';

		/**
		 * Grid options.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fluxus-projects-grid-admin.php';

		/**
		 * Project type grid options.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fluxus-projects-project-type-grid-admin.php';
	}

	public function admin_init() {
		Fluxus_Projects_Deactivator::verify_dependencies();
		global $pagenow;

    $post_type = isset( $_GET[ 'post_type' ] ) ? $_GET[ 'post_type' ] : '';

    if ( $post_id = Fluxus_Projects_Utils::post_id_from_query_params() ) {
        $post = get_post( $post_id );
        $post_type = $post->post_type;
    }

    if ( $post_type == 'fluxus_portfolio' ) {
        // Project List Page
        if ( 'edit.php' == $pagenow ) {
					$fluxus_projects_wp_ui = new Fluxus_Projects_Wp_Ui();

					// Custom columns in Project List
					add_filter( 'manage_edit-fluxus_portfolio_columns', array( $fluxus_projects_wp_ui, 'list_columns' ) );
					add_action( 'manage_posts_custom_column', array( $fluxus_projects_wp_ui, 'list_data' ) );
        }

        // Post Edit or Post New Page
        if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin-ajax.php' ) ) ) {
					new Fluxus_Projects_Project_Admin( $post_id, $this->plugin_name, $this->version );
        }
    }

    if ( $post_id ) {
			if ( get_page_template_slug( $post_id ) === 'template-portfolio-grid.php' ) {
				new Fluxus_Projects_Grid_Admin( $post_id );
			}
    }
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/fluxus-projects-admin.css',
			array(),
			$this->version,
			'all'
		);
	}
}
