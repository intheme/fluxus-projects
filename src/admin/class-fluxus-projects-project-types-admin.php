<?php

class Fluxus_Projects_Project_Types_Admin {

	public function layouts() {
		return array(
			''           => __( 'Default', 'fluxus' ),
			'horizontal' => __( 'Horizontal', 'fluxus' ),
			'grid'       => __( 'Grid', 'fluxus' ),
		);
	}


	public function fluxus_project_type_option( $project_type_id, $option_name ) {
		$options = new Fluxus_Projects_Project_Type_Options( $project_type_id );
		$value   = $options->$option_name;

		if ( $value === false ) {
			$project_type = get_term_by( 'id', $project_type_id, 'fluxus-project-type' );

			if ( $project_type->parent ) {
				return fluxus_project_type_option( $project_type->parent, $option_name );
			}
		}

		return $value;
	}


	/**
	 * Recursively checks if project type has any overridden options.
	 * Returns an array with overriden options, so it can be used with extract().
	 */
	public function fluxus_project_type_grid_options( $project_type_id ) {
		$vars = array();

		$grid_size = fluxus_project_type_option( $project_type_id, 'grid_size' );
		$grid_size = explode( ' ', $grid_size );
		if ( is_array( $grid_size ) && count( $grid_size ) == 2 ) {
			$vars['columns'] = $grid_size[0];
			$vars['rows']    = $grid_size[1];
		}

		$grid_orientation = fluxus_project_type_option( $project_type_id, 'grid_orientation' );
		if ( $grid_orientation ) {
			$vars['orientation'] = $grid_orientation;
		}

		$grid_aspect_ratio = fluxus_project_type_option( $project_type_id, 'grid_aspect_ratio' );
		if ( $grid_aspect_ratio ) {
			$vars['aspect_ratio'] = $grid_aspect_ratio;
		}

		$grid_image_sizes = fluxus_project_type_option( $project_type_id, 'grid_image_sizes' );
		if ( $grid_image_sizes ) {
			$vars['image_sizes_serialized'] = $grid_image_sizes;
			$vars['image_sizes']            = Fluxus_Projects_Grid_Portfolio::parse_image_sizes( $grid_image_sizes );
		} else {
			$vars['image_sizes_serialized'] = '';
			$vars['image_sizes']            = array();
		}

		$grid_image_cropping = fluxus_project_type_option( $project_type_id, 'grid_image_cropping' );
		if ( $grid_image_cropping ) {
			$vars['image_cropping_serialized'] = $grid_image_cropping;
			$vars['image_cropping']            = json_decode( $grid_image_cropping, true );
		} else {
			$vars['image_cropping_serialized'] = '';
			$vars['image_cropping']            = array();
		}

		return $vars;
	}



	public function fluxus_project_type_grid_size( $project_type_id ) {
		$options = new Fluxus_Projects_Project_Type_Options( $project_type_id );

		$grid_size = $options->grid_size;

		if ( ! $grid_size ) {
			$project_type = get_term_by( 'id', $project_type_id, 'fluxus-project-type' );

			if ( $project_type->parent ) {
				for ( $i = 0; $i <= 10; $i++ ) {
					$parent = get_term_by( 'id', $project_type->parent, 'fluxus-project-type' );

					$parent_options = new Fluxus_Projects_Project_Type_Options( $parent->term_id );
					$grid_size      = $parent_options->grid_size;

					if ( $grid_size || ( $parent->parent == 0 ) ) {
						break;
					}
				}
			}
		}

		return $grid_size ? explode( ' ', $grid_size ) : false;
	}


	public static function fluxus_project_type_layout( $project_type_id ) {
		$options = new Fluxus_Projects_Project_Type_Options( $project_type_id );

		$template = $options->layout;

		if ( ! $template ) {
			$project_type = get_term_by( 'id', $project_type_id, 'fluxus-project-type' );

			if ( $project_type->parent ) {
				for ( $i = 0; $i <= 10; $i++ ) {
					$parent = get_term_by( 'id', $project_type->parent, 'fluxus-project-type' );

					$parent_options = new Fluxus_Projects_Project_Type_Options( $parent->term_id );
					$template       = $parent_options->layout;

					if ( $template || ( $parent->parent == 0 ) ) {
						break;
					}
				}
			}
		}

		switch ( $template ) {

			case 'grid':
				$template = 'template-portfolio-grid.php';
				break;

			case 'horizontal':
				$template = 'template-portfolio.php';
				break;

			default:
				/*
				* Template is not set using Project Type options, so let's determinate it by ourselves
				* using following logic:
				*   1. If page with horizontal portfolio template is found, then use horizontal.
				*   2. If page with grid portfolio template is found, then use grid.
				*   3. If no page is found, then use horizontal.
				*/

				$template = 'template-portfolio.php';

				$horizontal_portfolio = Fluxus_Projects_Utils::get_pages_by_template( 'template-portfolio.php', array( 'post_status' => 'publish' ) );

				if ( $horizontal_portfolio ) {
					$template = 'template-portfolio.php';
				} else {
					$grid_portfolio = Fluxus_Projects_Utils::get_pages_by_template( 'template-portfolio-grid.php', array( 'post_status' => 'publish' ) );

					if ( $grid_portfolio ) {
						$template = 'template-portfolio-grid.php';
					}
				}
		}

		return $template;
	}

	/**
	 * Project Type Edit Form
	 */
	public function edit_form( $project_type ) {
		$grid_options = new Fluxus_Projects_Project_Type_Grid_Admin( $project_type->term_id );
		$options      = new Fluxus_Projects_Project_Type_Options( $project_type->term_id ); ?>
	  <tr class="form-field">
		  <th scope="row" valign="top">
			  <label for="project-type-layout"><?php _e( 'Layout', 'fluxus' ); ?></label>
		  </th>
		  <td>
			  <?php
				Fluxus_Projects_Utils::select_tag(
					array(
						'name' => 'project-type-layout',
						'id'   => 'project-type-layout',
					),
					$this->layouts(),
					$options->layout
				);
				?>
			  <br />
			  <span class="description"><?php _e( 'Portfolio layout that will be used to display the project type.', 'fluxus' ); ?></span>
		  </td>
	  </tr>
	  <tr id="project-type-grid-portfolio-options">
		  <th></th>
		  <td>
			  <div id="poststuff">
				  <div class="postbox">
					  <h3 class="hndle">
						  <span>
							  <?php _e( 'Grid Options', 'fluxus' ); ?>
						  </span>
					  </h3>
					  <div class="inside">
						  <?php echo wp_kses_post( $grid_options->admin_options_content() ); ?>
					  </div>
				  </div>
			  </div>
		  </td>
	  </tr>
		<?php
		  /**
		   * This is needed for WPML plugin, it clones the last <tr />,
		   * where WPML shows it's UI. Our tr:last has display: none, what also hides WPML's user UI
		   */
		?>
	  <tr>
		  <th></th>
		  <td></td>
	  </tr>
		<?php
	}


	/**
	 * Project Type Create Form
	 */
	public function create_form() {
		$grod_options = new Fluxus_Projects_Project_Type_Grid_Admin( 0 );
		?>
	  <div class="form-field">
		  <label for="project-type-layout"><?php _e( 'Layout', 'fluxus' ); ?></label>
		  <select name="project-type-layout" id="project-type-layout" class="postform">
			  <?php echo it_array_to_select_options( $this->layouts() ); ?>
		  </select>
		  <br>
		  <span class="description"><?php _e( 'Portfolio layout that will be used to display the project type.', 'fluxus' ); ?></span>
	  </div>
	  <div id="project-type-layout-option" class="form-field" style="display: none">
		  <label for="project-type-grid-size"><?php _e( 'Grid Size', 'fluxus' ); ?></label>
		  <select name="project-type-grid-size" id="project-type-grid-size">
			  <?php
				echo it_array_to_select_options(
					$grod_options->get_grid_size_options(),
					Fluxus_Projects_Grid_Portfolio::DEFAULT_GRID_SIZE
				);
				?>
		  </select>
	  </div>
		<?php
	}


	/**
	 * Saves project type additional options: layout and grid size.
	 */
	public function update( $term_id ) {
		if ( isset( $_POST['project-type-layout'] ) ) {

			// Since we use the same function for adding/updating terms, check for different nonces.
			if ( isset( $_POST['_wpnonce_add-tag'] ) ) {
				check_admin_referer( 'add-tag', '_wpnonce_add-tag' );
			} else {
				check_admin_referer( 'update-tag_' . $term_id );
			}

			$tax = get_taxonomy( 'fluxus-project-type' );

			if ( current_user_can( $tax->cap->edit_terms ) ) {
				$options = new Fluxus_Projects_Project_Type_Options( $term_id );

				$options->layout = $_POST['project-type-layout'];

				$whitelist = array(
					'grid_orientation',
					'grid_aspect_ratio',
					'grid_size',
					'grid_image_sizes',
					'grid_image_cropping',
				);

				foreach ( $whitelist as $key ) {
					if ( isset( $_POST[ 'fluxus_portfolio_' . $key ] ) ) {
						$options->$key = stripslashes( $_POST[ 'fluxus_portfolio_' . $key ] );
					}
				}
			}
		}
	}


	/**
	 * On Project Type term deletion deletes associated options.
	 */
	public function delete( $term_id ) {

		// Same action for bulk and single deletion
		if ( isset( $_POST['delete-tag'] ) ) {
			check_admin_referer( 'delete-tag_' . $term_id );
		} else {
			check_admin_referer( 'bulk-tags' );
		}

		$tax = get_taxonomy( 'fluxus-project-type' );
		if ( current_user_can( $tax->cap->delete_terms ) ) {
			$options = new Fluxus_Projects_Project_Type_Options( $term_id );
			$options->delete();
		}
	}
}
