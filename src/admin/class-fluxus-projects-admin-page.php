<?php

abstract class Fluxus_Projects_Admin_Page {
	public $post_id = null;
	public $styles = array();
	public $scripts = array();

	function __construct( $post_id ) {
		$this->post_id = $post_id;
		$this->load_styles();
		$this->load_scripts();
	}

	/**
	 * Enqueue styles
	 */
	function load_styles() {
		foreach ( $this->styles as $style ) {
			$name = isset( $style[0] ) ? $style[0] : '';
			$file = isset( $style[1] ) && ! empty( $style[1] ) ? get_template_directory_uri() . '/css/wp-admin/' . $style[1] : '';
			wp_enqueue_style( $name, $file );
		}
	}

	/**
	 * Enqueue scripts
	 */
	function load_scripts() {
		foreach ( $this->scripts as $script ) {
			$name = isset( $script[0] ) ? $script[0] : '';
			$file = isset( $script[1] ) && ! empty( $script[1] ) ? get_template_directory_uri() . '/js/wp-admin/' . $script[1] : '';
			$dependencies = isset( $script[2] ) ? $script[2] : array();
			wp_enqueue_script( $name, $file, $dependencies, '', true );
		}
	}
}
