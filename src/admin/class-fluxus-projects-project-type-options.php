<?php

class Fluxus_Projects_Project_Type_Options {
	protected $id;
	protected $options = array(
		'layout'              => '',
		'grid_orientation'    => '',
		'grid_aspect_ratio'   => '',
		'grid_size'           => '',
		'grid_image_sizes'    => '',
		'grid_image_cropping' => '',
	);

	public function __construct( $project_type_term_id ) {
		$this->id = $project_type_term_id;
		return $this;
	}

	public function __set( $name, $value ) {
		if ( isset( $this->options[ $name ] ) ) {
			$this->options[ $name ] = $value;
			update_option( 'project_type_' . $name . '_' . $this->id, $value );
			return $this;
		}
	}

	public function __get( $name ) {
		if ( isset( $this->options[ $name ] ) ) {
			return get_option( 'project_type_' . $name . '_' . $this->id );
		}
	}

	public function delete() {
		delete_option( 'project_type_grid_size_' . $this->id );
		delete_option( 'project_type_layout_' . $this->id );
	}
}
