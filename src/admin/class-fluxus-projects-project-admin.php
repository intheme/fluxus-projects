<?php

class Fluxus_Projects_Project_Admin {
	function __construct( $post_id, $plugin_name, $version ) {
		$this->post_id     = $post_id;
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_meta_box(
			'fluxus-project-info-meta',
			__( 'Project Options', 'fluxus-projects' ),
			array( $this, 'meta_box_options_content' ),
			'fluxus_portfolio',
			'normal',
			'low'
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'save_post', array( $this, 'meta_box_options_content_save' ), 1, 1 );

		// Setup project media manager
		$project_media_meta_box = new Fluxus_Projects_Project_Media_Meta_Box( $post_id );
		$project_media_meta_box->admin_init();
	}

	function meta_box_options_content() {
		$project = new Fluxus_Projects_Project( $this->post_id );

		?>
		<div class="fluxus-meta-field">
			<label for="fluxus_project_subtitle"><?php _e( 'Project Subtitle', 'fluxus-projects' ); ?></label>
			<div class="field">
				<input type="text" name="fluxus_project_subtitle" value="<?php echo esc_attr( $project->meta_subtitle ); ?>" />
			</div>
		</div>
		<div class="fluxus-meta-field">
			<label for="fluxus_project_link"><?php _e( 'Project External Link', 'fluxus-projects' ); ?></label>
			<div class="field">
				<input type="text" name="fluxus_project_link" value="<?php echo esc_attr( $project->meta_link ); ?>" class="url" />
			</div>
		</div>
		<div class="fluxus-meta-group">
			<h2><?php _e( 'Project information', 'fluxus-projects' ); ?></h2>
			<table class="fluxus-table fluxus-project-information">
				<thead>
					<tr>
						<td><?php _e( 'Title', 'fluxus-projects' ); ?></td>
						<td><?php _e( 'Content', 'fluxus-projects' ); ?></td>
					</tr>
				</thead>
				<tbody>
				<?php
				if ( $project->meta_info && is_array( $project->meta_info ) ) :
					foreach ( $project->meta_info as $info ) :
						?>
							<tr>
								<td>
									<input type="text" name="fluxus_project_info_title[]" value="<?php echo esc_attr( $info['title'] ); ?>" />
								</td>
								<td>
									<textarea name="fluxus_project_info_content[]">
										<?php echo esc_textarea( $info['content'] ); ?>
									</textarea>
								</td>
							</tr>
							<?php
						endforeach;
					endif;
				?>
					<tr class="add-element">
						<td colspan="2">
							<?php _e( 'To add project information enter the title and content fields below.', 'fluxus-projects' ); ?>
						</td>
					</tr>
					<tr>
						<td><input type="text" name="fluxus_project_info_add_title" value="" /></td>
						<td><textarea name="fluxus_project_info_add_content"></textarea></td>
					</tr>
					<tr>
						<td colspan="2">
							<a href="#" id="fluxus-add-project-info" class="button-secondary"><?php _e( 'Add project information', 'fluxus-projects' ); ?></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="fluxus-meta-group">
			<h2><?php _e( 'Project navigation', 'fluxus-projects' ); ?></h2>
			<div class="fluxus-meta-field">
				<label for="fluxus_project_other_projects"><?php _e( 'Other projects', 'fluxus-projects' ); ?></label>
				<div class="field">
					<?php

					wp_dropdown_categories(
						array(
							'show_option_all' => __( 'All Projects', 'fluxus-projects' ),
							'hide_empty'      => 0,
							'selected'        => $project->meta_other_projects,
							'hierarchical'    => 1,
							'name'            => 'fluxus_project_other_projects',
							'id'              => 'fluxus_project_other_projects',
							'taxonomy'        => 'fluxus-project-type',
						)
					);

					?>
				</div>
			</div>
			<div class="fluxus-meta-field">
				<label for="fluxus_project_back_to_link"><?php _e( 'Back to link', 'fluxus-projects' ); ?></label>
				<div class="field">
					<?php

					wp_dropdown_categories(
						array(
							'show_option_all' => __( 'Back to Portfolio', 'fluxus-projects' ),
							'hide_empty'      => 0,
							'selected'        => $project->meta_back_to_link,
							'hierarchical'    => 1,
							'name'            => 'fluxus_project_back_to_link',
							'id'              => 'fluxus_project_back_to_link',
							'taxonomy'        => 'fluxus-project-type',
						)
					);

					?>
				</div>
			</div>
		</div>

		<?php

	}


	function meta_box_options_content_save( $post_id ) {

		if ( ! Fluxus_Projects_Utils::check_save_action( $post_id, 'fluxus_portfolio' ) ) {
			return $post_id;
		}

		$project = new Fluxus_Projects_Project( $post_id );

		$project->update_from_array( $_POST );

		if ( isset( $_POST['fluxus_project_info_title'] ) && is_array( $_POST['fluxus_project_info_title'] ) ) {

			$titles   = $_POST['fluxus_project_info_title'];
			$contents = $_POST['fluxus_project_info_content'];

			$data = array();

			foreach ( $titles as $index => $title ) {

				if ( ! empty( $title ) && ! empty( $contents[ $index ] ) ) {

					$data[] = array(
						'title'   => $title,
						'content' => $contents[ $index ],
					);

				}
			}

			$project->meta_info = $data;

		} else {

			$project->meta_info = array();

		}

		$project->save();

	}

	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/fluxus-projects-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}
}
