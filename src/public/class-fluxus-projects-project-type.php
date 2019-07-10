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
}
