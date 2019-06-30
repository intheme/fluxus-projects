<?php

class Fluxus_Projects_Grid_Portfolio extends Fluxus_Projects_Page {
	const DEFAULT_GRID_SIZE              = '4 3';
	const DEFAULT_ORIENTATION            = 'horizontal';
	const DEFAULT_ASPECT_RATIO           = 'auto';
	const DEFAULT_DEFAULT_IMAGE_SIZES    = '';
	const DEFAULT_DEFAULT_IMAGE_CROPPING = '';

	protected $META_PREFIX        = 'fluxus_portfolio_';
	protected $meta_data_defaults = array(
		'grid_size'           => self::DEFAULT_GRID_SIZE,
		'grid_orientation'    => self::DEFAULT_ORIENTATION,
		'grid_aspect_ratio'   => self::DEFAULT_ASPECT_RATIO,
		'grid_image_sizes'    => self::DEFAULT_DEFAULT_IMAGE_SIZES,
		'grid_image_cropping' => self::DEFAULT_DEFAULT_IMAGE_CROPPING,
	);

	function get_options() {
		return self::grid_options( $this->post_id );
	}

	static function parse_image_sizes( $json ) {
		$result = array();

		$decoded = json_decode( $json );

		if ( is_array( $decoded ) ) {
			foreach ( $decoded as $image_size ) {
				$result[ $image_size->id ] = $image_size->size;
			}
		}

		return $result;
	}

	static function grid_options( $page_id = null ) {
		if ( ! $page_id ) {
			$default_grid_portfolio = it_find_page_by_template(
				'template-portfolio-grid.php',
				array(
					'post_status' => 'publish',
				)
			);
			if ( $default_grid_portfolio ) {
				$page_id = $default_grid_portfolio[0]->ID;
			}
		}

		if ( $page_id ) {
			$grid_portfolio = new Fluxus_Projects_Grid_Portfolio( $page_id );
			$options        = array(
				'orientation'               => $grid_portfolio->meta_grid_orientation,
				'aspect_ratio'              => $grid_portfolio->meta_grid_aspect_ratio,
				'grid_size'                 => $grid_portfolio->meta_grid_size,
				'image_sizes_serialized'    => $grid_portfolio->meta_grid_image_sizes,
				'image_cropping_serialized' => $grid_portfolio->meta_grid_image_cropping,
			);
		} else {
			$options = array(
				'orientation'               => self::DEFAULT_ORIENTATION,
				'aspect_ratio'              => self::DEFAULT_ASPECT_RATIO,
				'grid_size'                 => self::DEFAULT_GRID_SIZE,
				'image_sizes_serialized'    => self::DEFAULT_DEFAULT_IMAGE_SIZES,
				'image_cropping_serialized' => self::DEFAULT_DEFAULT_IMAGE_CROPPING,
			);
		}

		$options['image_sizes']    = self::parse_image_sizes(
			$options['image_sizes_serialized']
		);
		$options['image_cropping'] = json_decode(
			$options['image_cropping_serialized'],
			true
		);

		$grid_size = explode( ' ', $options['grid_size'] );

		if ( is_array( $grid_size ) && count( $grid_size ) == 2 ) {
			$options['columns'] = $grid_size[0];
			$options['rows']    = $grid_size[1];
		} else {
			$options['columns'] = 4;
			$options['rows']    = 3;
		}

		return $options;
	}
}
