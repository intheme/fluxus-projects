<?php

class Fluxus_Projects_Project_Type_Grid_Admin extends Fluxus_Projects_Grid_Admin {
	protected $term_id;
	protected $term;
	protected $options;

	function __construct( $term_id ) {
		parent::__construct( 0 );

		$this->term_id = $term_id;
		$this->term = get_term( $term_id, 'fluxus-project-type' );
		$this->options = new Fluxus_Projects_Project_Type_Options( $term_id );
	}

	function get_orientation_options() {
		return array_merge(
			array( '' => __( 'Default', 'fluxus-projects' ) ),
			parent::get_orientation_options()
		);
	}

	function get_aspect_ratio_options() {
		return array_merge(
			array( '' => __( 'Default', 'fluxus-projects' ) ),
			parent::get_aspect_ratio_options()
		);
	}

	function get_grid_size_options() {
		return array_merge(
			array( '' => __( 'Default', 'fluxus-projects' ) ),
			parent::get_grid_size_options()
		);
	}

	function get_grid_options() {
			$url = get_term_link( $this->term );

			$options = array(
				'orientation'								=> $this->options->grid_orientation,
				'aspect_ratio'							=> $this->options->grid_aspect_ratio,
				'grid_size'     						=> $this->options->grid_size,
				'image_sizes_serialized' 		=> $this->options->grid_image_sizes,
				'image_cropping_serialized' => $this->options->grid_image_cropping,
				'customize_url' 						=> add_query_arg( 'customize-layout', 1, $url )
			);

			$options['image_sizes'] = Fluxus_Projects_Grid_Portfolio::parse_image_sizes(
				$options['image_sizes_serialized']
			);
			$options['image_cropping'] = json_decode(
				$options['image_cropping_serialized'],
				true
			);

			return $options;
	}

}