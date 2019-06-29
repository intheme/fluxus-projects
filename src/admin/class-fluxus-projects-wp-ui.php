<?php

class Fluxus_Projects_Wp_Ui {
  /**
   * Add additional columns in project list table.
   */
  public function list_columns() {
    return array(
      'cb'          => '<input type="checkbox" />',
      'title'       => 'Project',
      'thumbnail'   => 'Thumbnail',
      'description' => 'Description',
      'type'        => 'Project Types',
    );
  }

  /**
   * Populate added columns with data.
   */
  public function list_data( $column ) {
    global $post;

    $project = new Fluxus_Projects_Project( $post->ID );

    switch ( $column ) {
      case 'description':
        the_excerpt();
      break;

      case 'thumbnail':
        $featured_media = $project->get_featured_media();
        if ( $featured_media ) :
          $image = wp_get_attachment_image_src( $featured_media['attachmentId'], 'thumbnail' );
          ?>
          <img src="<?php echo esc_url( $image[0] ) ?>" alt="Thumbnail" />
          <?php
        endif;
      break;

      case 'type':
        echo get_the_term_list( $post->ID, 'fluxus-project-type', '', ', ', '' );
      break;
    }
  }
}
