<?php

class Fluxus_Projects_Grid_Admin {
	function __construct( $post_id ) {
		$this->post_id = $post_id;

		// Add options meta box
		add_meta_box(
			FLUXUS_PROJECTS_PLUGIN_NAME . '-grid-meta',
			__( 'Grid Options', 'fluxus' ),
			array( $this, 'admin_options_content' ),
			'page',
			'normal',
			'low'
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'save_post', array( $this, 'admin_options_save' ), 2 );
	}


	public function get_orientation_options() {
		return array(
			'horizontal' => __( 'Horizontal', 'fluxus' ),
			'vertical'   => __( 'Vertical', 'fluxus' ),
		);
	}

	public function get_aspect_ratio_options() {
		$options = array(
			'auto' => __( 'Auto', 'fluxus' ),
			'1:1'  => __( '1:1 (square)', 'fluxus' ),
			'4:3'  => '4:3',
			'2:1'  => '2:1',
			'16:9' => '16:9',
			'9:16' => '9:16',
		);

		return apply_filters( 'fluxus_portfolio_aspect_ratio', $options );
	}

	public function get_grid_size_options() {
		$size_options = array(
			'5 4' => __( '5 columns, 4 rows', 'fluxus' ),
			'5 3' => __( '5 columns, 3 rows', 'fluxus' ),
			'4 3' => __( '4 columns, 3 rows', 'fluxus' ),
			'3 3' => __( '3 columns, 3 rows', 'fluxus' ),
			'3 2' => __( '3 columns, 2 rows', 'fluxus' ),
		);

		return apply_filters( 'fluxus_portfolio_grid_sizes', $size_options );
	}


	public function get_grid_options() {
		$grid = new Fluxus_Projects_Grid_Portfolio( $this->post_id );

		return array_merge(
			$grid->get_options(),
			array(
				'customize_url' =>
					add_query_arg(
						'customize-layout',
						1,
						get_permalink( $this->post_id )
					),
			)
		);
	}

	public function admin_options_content() {
		global $post;
		extract( $this->get_grid_options() );

		?>
		<div class="fluxus-meta-field">
			<label for="fluxus_portfolio_grid_orientation"><?php _e( 'Orientation', 'fluxus' ); ?></label>
			<div class="field">
				<?php

				it_select_tag(
					array( 'name' => 'fluxus_portfolio_grid_orientation' ),
					$this->get_orientation_options(),
					$orientation
				);

				?>
			</div>
		</div>
		<div class="fluxus-meta-field fluxus-meta-field-aspect-ratio">
			<label for="fluxus_portfolio_grid_aspect_ratio"><?php _e( 'Aspect ratio', 'fluxus' ); ?></label>
			<div class="field">
				<?php

				it_select_tag(
					array( 'name' => 'fluxus_portfolio_grid_aspect_ratio' ),
					$this->get_aspect_ratio_options(),
					$aspect_ratio
				);

				?>
			</div>
			<div class="notes">
				<?php _e( 'When set to <b>auto</b> aspect ratio will be chosen depending on the grid size.', 'fluxus' ); ?>
			</div>
		</div>
		<div class="fluxus-meta-field">
			<label for="fluxus_portfolio_grid_size"><?php _e( 'Grid size', 'fluxus' ); ?></label>
			<div class="field">
				<?php

				it_select_tag(
					array( 'name' => 'fluxus_portfolio_grid_size' ),
					$this->get_grid_size_options(),
					$grid_size
				);

				?>
			</div>
		</div>
		<div class="fluxus-meta-field">
			<label for="fluxus_portfolio_grid_size"><?php _e( 'Image sizes', 'fluxus' ); ?></label>
			<div class="field">
				<button data-url="<?php echo esc_url( $customize_url ); ?>" class="button js-button-grid-layout" style="margin-right: 10px"><?php _e( 'Customize', 'fluxus' ); ?></button>
				<button class="button js-button-grid-layout-reset" data-confirm="<?php echo esc_attr( __( 'This will set image sizes to the default values. Are you sure you want to continue?', 'fluxus' ) ); ?>">
					<?php _e( 'Reset', 'fluxus' ); ?>
				</button>
				<input type="hidden" name="fluxus_portfolio_grid_image_sizes" value="<?php echo esc_attr( $image_sizes_serialized ); ?>">
				<input type="hidden" name="fluxus_portfolio_grid_image_cropping" value="<?php echo esc_attr( $image_cropping_serialized ); ?>">
			</div>
			<div class="notes">
				<?php _e( 'Allows to increase thumbnail sizes for chosen projects.', 'fluxus' ); ?>
			</div>
		</div>
		<div class="fluxus-meta-field-note">
			<b><?php _e( 'Note:', 'fluxus' ); ?></b>
			<?php
				_e( 'on smaller screens (eg. iPad, iPhone) grid settings are automatically changed to best fit the viewer\'s screen.', 'fluxus' );
			?>
		</div>
		<?php
	}

	public function admin_options_save( $post_id ) {
		if ( ! it_check_save_action( $post_id, 'page' ) ) {
			return $post_id;
		}

		$grid = new Fluxus_Projects_Grid_Portfolio( $this->post_id );
		$grid->update_from_array( stripslashes_deep( $_POST ) )->save();
	}

	public static function enqueue_scripts() {
		wp_enqueue_script(
			FLUXUS_PROJECTS_PLUGIN_NAME . '-grid-page-edit',
			plugin_dir_url( __FILE__ ) . 'js/fluxus-projects-grid-page-edit.js',
			array( 'jquery', 'backbone' ),
			FLUXUS_PROJECTS_VERSION,
			false
		);
	}
}
