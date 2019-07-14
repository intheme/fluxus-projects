<?php

if ( ! class_exists( 'Media_Meta_Box_Interface' ) ) {
	return;
}

class Fluxus_Projects_Project_Media_Meta_Box extends Media_Meta_Box_Interface {
	function __construct( $post_id ) {
		parent::__construct( $post_id, 'project' );
		$this->meta_box_screen = 'fluxus_portfolio';
		$this->meta_box_title  = __( 'Project Media', 'fluxus' );
	}

	function enqueue_styles() {
		parent::enqueue_styles();
		wp_enqueue_style( 'wp-color-picker' );
	}

	function enqueue_scripts() {
		parent::enqueue_scripts();
		wp_enqueue_script(
			'fluxus-projects-meta-box',
			plugin_dir_url( __FILE__ ) . '../../admin/js/fluxus-projects-meta-box.js',
			array( 'media-meta-box', 'wp-color-picker' ),
			FLUXUS_PROJECTS_VERSION,
			true
		);
	}

	static function item_defaults() {
		return array_merge(
			parent::item_defaults(),
			array(
				'featured'      => false,
				'hideControls'  => false,
				'autoplay'      => false,
				'imageUrl'      => null,
				'width'         => null,
				'height'        => null,
				'srcset'        => null,
				'attachmentAlt' => '',
				'aspectRatio'   => 16 / 9,
			)
		);
	}

	function items( $options = array() ) {
		$default_options       = array( 'published' => null );
		$options               = wp_parse_args( $options, $default_options );
		$return_only_published = $options['published'] === true;

		$data  = $this->data_with_defaults( self::item_defaults() );
		$items = array();

		if ( isset( $data['items'] ) ) {
			foreach ( $data['items'] as $item ) {
				if ( $return_only_published && ( $item['published'] != 1 ) ) {
					continue;
				}
				$image = null; // $image local variable is shared between iterations, make it null.

				if ( $item['type'] == 'embed' ) {
					list( $item['width'], $item['height'] ) = self::get_iframe_size(
						$item['embed'],
						1920,
						1280
					);
				}

				if ( $item['type'] !== 'raw-embed' ) {
					if ( ! empty( $item['attachmentId'] ) ) {
						$item['mime'] = get_post_mime_type( $item['attachmentId'] );

						if ( $item['type'] == 'video' ) {
								$item['videoUrl'] = wp_get_attachment_url( $item['attachmentId'] );
								$item['mime']     = get_post_mime_type( $item['attachmentId'] );
								// Video attachment can have featured image, which will act as poster attachment.
								$item['posterAttachmentId'] = get_post_thumbnail_id( $item['attachmentId'] );
						} elseif ( $item['type'] == 'image' ) {
							  $image = wp_get_attachment_image_src( $item['attachmentId'], 'fluxus-max' );
							  // Use WP attachment alt text as alt text
							  $item['attachmentAlt'] = get_post_meta( $item['attachmentId'], '_wp_attachment_image_alt', true );
						}
					}

					if ( ! empty( $item['posterAttachmentId'] ) ) {
						$image = wp_get_attachment_image_src( $item['posterAttachmentId'], 'fluxus-max' );
					}

					if ( $image ) {
						$item['imageUrl'] = $image[0];
						$item['width']    = $image[1];
						$item['height']   = $image[2];
					}
				}

				// Trim description
				$item['description'] = trim( $item['description'] );

				// Aspect ratio
				$has_width_and_height = is_numeric( $item['width'] ) &&
				is_numeric( $item['height'] )
				&& $item['height'] !== 0;
				if ( $has_width_and_height ) {
					$item['aspectRatio'] = $item['width'] / $item['height'];
				}

				$items[ $item['order'] ] = $item;
			}
			krsort( $items );
		}

		return $items;
	}

	function get_featured_item() {
		$first_image_media = null;

		foreach ( $this->items() as $item ) {
			if ( ! $first_image_media && ( $item['type'] == 'image' ) ) {
				$first_image_media = $item;
			}

			if ( $item['featured'] ) {
				return $item;
			}
		}

		return $first_image_media;
	}

	protected function item_details_template() {
		$custom_content_position = add_query_arg(
			array( 'set-infobox-position' => 1 ),
			get_permalink( $this->post_id )
		);

		?>
	<% if (type == 'embed') { %>
	  <div class="control-group">
		<label><%- t('embed_code') %></label>
		<div class="control">
		  <textarea name="embed" cols="30" rows="10"><%- embed %></textarea>
		</div>
		<div class="control-info"></div>
	  </div>
	<% } %>
	<% if (type == 'embed' || type == 'video') { %>
	  <div class="control-group">
		<label><%- t('loop_mode') %></label>
		<div class="control">
		  <input type="checkbox" name="hideControls" value="1"<% if (hideControls) { print(' checked="checked"'); } %> />
		</div>
		<div class="control-info"><%- t('loop_mode_hint') %></div>
	  </div>
	  <% if (type == 'video') { %>
		<div class="control-group">
		  <label><%- t('autoplay') %></label>
		  <div class="control">
			<input type="checkbox" name="autoplay" value="1"<% if (autoplay || hideControls) { print(' checked="checked"'); } %><% if (hideControls) { print(' disabled="disabled"'); } %> />
		  </div>
		  <div class="control-info"><%- t('autoplay_hint') %></div>
		</div>
	  <% } %>
	<% } %>
	<div class="control-group">
	  <label><%- t('description') %></label>
	  <div class="control">
		<textarea name="description" cols="30" rows="10"><%- description %></textarea>
	  </div>
	  <div class="control-info"></div>
	</div>
	<% if (type == 'image') { %>
	  <div class="control-group">
		<label><%- t('featured') %></label>
		<div class="control">
		  <input type="checkbox" name="featured" value="1"<% if (featured) { print(' checked="checked"'); } %> />
		</div>
		<div class="control-info"><%- t('featured_hint') %></div>
	  </div>
	<% } %>
	<input type="hidden" name="attachmentId" value="<% if (typeof(attachmentId) != 'undefined') { print(attachmentId); } %>" />
		<?php
	}

	public function translations() {
		return array_merge(
			parent::translations(),
			array(
				'title'          => __( 'Title', 'fluxus' ),
				'title_hint'     => __( 'Title to be displayed at the bottom of the image. You can use HTML to include links.', 'fluxus' ),
				'featured'       => __( 'Featured', 'fluxus' ),
				'featured_hint'  => __( 'Featured image will be used as thumbnail on Grid Portfolio and Horizontal Portfolio. Tip: You can feature unpublished image if you want to have a thumbnail that is now shown inside the project.', 'fluxus' ),
				'published_hint' => __( 'Not published items do not appear on project page.', 'fluxus' ),
				'loop_mode'      => __( 'Loop mode', 'fluxus' ),
				'loop_mode_hint' => __( 'Hides player controls, mutes and autoplays video.', 'fluxus' ),
				'autoplay'       => __( 'Autoplay', 'fluxus' ),
				'autoplay_hint'  => __( 'Attempts to autoplay video.', 'fluxus' ),
			)
		);
	}

	static function get_iframe_size( $iframe_html, $default_width, $default_height ) {
		$width   = $default_width;
		$height  = $default_height;
		$matches = null;

		$matched_width = preg_match( '/width="(\d+)"/i', $iframe_html, $matches );
		if ( $matched_width ) {
			$width = (int) $matches[1];
		}

		$matched_height = preg_match( '/height="(\d+)"/i', $iframe_html, $matches );
		if ( $matched_height ) {
			$height = (int) $matches[1];
		}

		if ( $height == 0 || $width == 0 ) {
			$height = $default_height;
			$width  = $default_width;
		}

		return array( $width, $height );
	}
}
