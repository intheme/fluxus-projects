<?php

class Fluxus_Projects_Grid_Customizer {
	public function __construct() {
		add_action( 'before', array( $this, 'render_html' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	public function enqueue_assets() {
		wp_enqueue_style(
			FLUXUS_PROJECTS_PLUGIN_NAME . '-grid-image-sizes',
			FLUXUS_PROJECTS_URL . 'admin/css/grid-image-sizes.css',
			array(),
			FLUXUS_PROJECTS_VERSION,
			'all'
		);
		wp_enqueue_style( 'dashicons' );

		$script_id = FLUXUS_PROJECTS_PLUGIN_NAME . '-grid-image-sizes';
		wp_enqueue_script(
			$script_id,
			FLUXUS_PROJECTS_URL . 'admin/js/grid-image-sizes.js',
			array( 'jquery', 'json2' ),
			FLUXUS_PROJECTS_VERSION,
			'all'
		);
		$wp_vars = array(
			'clickToChangeSize' => __( 'Change size', 'fluxus-projects' ),
		);
		wp_localize_script( $script_id, 'fluxusProjectsGridCustomizer', $wp_vars );

	}

	public function render_html() {
		?>
	<div class="fluxus-customize-note">
			<p>
				<?php _e( 'Hover any image to customize size and cropping.', 'fluxus-projects' ); ?>
			</p>
			<a href="#" class="js-cancel-save-positions button button-blended btn-cancel"><?php _e( 'Cancel', 'fluxus-projects' ); ?></a>
			<a href="#" class="js-save-positions button button-black btn-save"><?php _e( 'Done', 'fluxus-projects' ); ?></a>
	</div>
		<?php
	}
}
