<?php

class Fluxus_Projects_Project_Type {

	public function __construct( $project_type_id ) {

		$this->project_type_id = $project_type_id;

	}

	public function get_template_filename() {
		$options = new Fluxus_Projects_Project_Type_Options( $this->project_type_id );

		$template = $options->layout;

		if ( ! $template ) {
			$project_type = get_term_by( 'id', $this->project_type_id, 'fluxus-project-type' );

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

				$horizontal_portfolio = Fluxus_Projects_Utils::get_pages_by_template(
					'template-portfolio.php',
					array( 'post_status' => 'publish' )
				);

				if ( $horizontal_portfolio ) {
					$template = 'template-portfolio.php';
				} else {
					$grid_portfolio = Fluxus_Projects_Utils::get_pages_by_template(
						'template-portfolio-grid.php',
						array( 'post_status' => 'publish' )
					);

					if ( $grid_portfolio ) {
						$template = 'template-portfolio-grid.php';
					}
				}
		}

		return $template;
	}

	public function get_grid_options() {
		$options = array(
			'columns' => 4,
			'rows'    => 3,
		);

		$grid_size = $this->get_option( 'grid_size' );
		$grid_size = explode( ' ', $grid_size );
		if ( is_array( $grid_size ) && count( $grid_size ) == 2 ) {
			$options['columns'] = $grid_size[0];
			$options['rows']    = $grid_size[1];
		}

		$grid_orientation = $this->get_option( 'grid_orientation' );
		if ( $grid_orientation ) {
			$options['orientation'] = $grid_orientation;
		}

		$grid_aspect_ratio = $this->get_option( 'grid_aspect_ratio' );
		if ( $grid_aspect_ratio ) {
			$options['aspect_ratio'] = $grid_aspect_ratio;
		}

		$grid_image_sizes = $this->get_option( 'grid_image_sizes' );
		if ( $grid_image_sizes ) {
			$options['image_sizes_serialized'] = $grid_image_sizes;
			$options['image_sizes']            = GridPortfolio::parse_image_sizes( $grid_image_sizes );
		} else {
			$options['image_sizes_serialized'] = '';
			$options['image_sizes']            = array();
		}

		$grid_image_cropping = $this->get_option( 'grid_image_cropping' );
		if ( $grid_image_cropping ) {
			$options['image_cropping_serialized'] = $grid_image_cropping;
			$options['image_cropping']            = json_decode( $grid_image_cropping, true );
		} else {
			$options['image_cropping_serialized'] = '';
			$options['image_cropping']            = array();
		}

		return $options;

	}

	private function get_option( $option_name ) {
		$options = new Fluxus_Projects_Project_Type_Options(
			$this->project_type_id
		);
		$value   = $options->$option_name;

		// If value is not set then try to inherit from parent Project Type
		if ( $value === false ) {
			$project_type = get_term_by(
				'id',
				$this->project_type_id,
				'fluxus-project-type'
			);

			if ( $project_type->parent ) {
				return $this->get_option( $project_type->parent, $option_name );
			}
		}

		return $value;
	}
}
