<?php
/**
 * A widget that displays available project types.
 *
 * If this widget is used on a project page,
 * then it will only show the types that are assigned
 * to current project.
 * Otherwise it will show all available types that
 * are assigned to at least one project.
 *
 * @since fluxus-projects 1.0
 */
class Fluxus_Projects_Project_Types_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array(
			'classname'   => 'fluxus-project-types',
			'description' => __( 'A widget that displays project types.', 'fluxus-projects' ),
		);

		$control_ops = array(
			'width'   => 300,
			'height'  => 350,
			'id_base' => 'fluxus-projects-project-types-widget',
		);

		parent::__construct(
			'fluxus-projects-project-types-widget',
			__( 'Project Types', 'fluxus-projects' ),
			$widget_ops,
			$control_ops
		);
	}

	function widget( $args, $instance ) {
		/**
		 * If widget was not called using default parameters,
		 * then do class and id substitution manually.
		 */
		if ( strpos( $args['before_widget'], '%2$s' ) ) {
			$args['before_widget'] = sprintf(
				$args['before_widget'],
				$this->id_base,
				$this->widget_options['classname']
			);
		}

		if ( is_single() ) :
			/**
			 * We are on the project page.
			 * So we show tags that belong to the active project.
			 */

			$tags = wp_get_post_terms( get_the_ID(), 'fluxus-project-type' );

			if ( $tags ) :
				echo wp_kses_post( $args['before_widget'] );
				echo wp_kses_post(
					$args['before_title'] . __( 'Project Type', 'fluxus-projects' ) . $args['after_title']
				);
				?>
				<ul>
					<?php

					foreach ( $tags as $tag ) :
						?>
						<li class="project-type-<?php echo esc_attr( $tag->term_id ); ?>">
							<a href="<?php echo esc_url( get_term_link( $tag, 'fluxus-project-type' ) ); ?>">
								<b class="hash">#</b><?php echo esc_html( $tag->name ); ?>
							</a>
						</li>
						<?php
					endforeach;

					?>
				</ul>
				<?php
				echo wp_kses_post( $args['after_widget'] );
			endif;

			else :

				$active_tag_slug = get_query_var( 'fluxus-project-type' );
				$active_tag      = false;
				$children        = false;

				$widget_title = __( 'Project Types', 'fluxus-projects' );

				if ( $active_tag_slug ) {
					$active_tag = get_term_by( 'slug', $active_tag_slug, 'fluxus-project-type' );
					$children   = get_terms(
						'fluxus-project-type',
						array(
							'parent'     => $active_tag->term_id,
							'hide_empty' => false, // We need to detect if this is a parent element
						)
					);

					if ( ! empty( $active_tag->description ) ) {
						echo wp_kses_post( $args['before_widget'] );
						echo wp_kses_post( $args['before_title'] . $active_tag->name . $args['after_title'] );
						echo wp_kses_post(
							'<div class="textwidget">' . nl2br( $active_tag->description ) . '</div>'
						);
						echo wp_kses_post( $args['after_widget'] );
					}
				}

				$all_class = '';
				// If this is a children, then show all siblings
				if ( $active_tag && $active_tag->parent ) {

					$tags = get_terms(
						'fluxus-project-type',
						array(
							'parent' => $active_tag->parent,
						)
					);

					$parent_tag   = get_term( $active_tag->parent, 'fluxus-project-type' );
					$widget_title = $parent_tag->name;
					$all_link     = get_term_link( $parent_tag, 'fluxus-project-type' );

					// If this is a parent, then show children
				} elseif ( $children ) {

					$tags         = $children;
					$widget_title = $active_tag->name;
					$all_link     = get_term_link( $active_tag, 'fluxus-project-type' );
					$all_class    = 'active';

					// Show all tags
				} else {

					$tags      = get_terms( 'fluxus-project-type' );
					$all_link  = Fluxus_Projects::get_default_portfolio_permalink();
					$all_class = $active_tag_slug ? '' : 'active';

				}

				// Manual check if there are at least one category with a project count > 0
				$empty = true;
				if ( $tags ) {
					foreach ( $tags as $tag ) {
						if ( $tag->count != 0 ) {
								$empty = false;
								break;
						}
					}
				}

				if ( $empty ) {
					return '';
				}

				echo wp_kses_post( $args['before_widget'] );
				echo wp_kses_post( $args['before_title'] . $widget_title . $args['after_title'] );

				?>
			<ul>
				<li class="project-type-all">
					<a class="<?php echo esc_attr( $all_class ); ?>" href="<?php echo esc_url( $all_link ); ?>">
						<?php _e( 'All', 'fluxus-projects' ); ?>
					</a>
				</li>
				<?php

				foreach ( $tags as $tag ) :
						$class = $active_tag_slug == $tag->slug ? 'active' : '';
					?>

						<li class="project-type-<?php echo esc_attr( $tag->term_id ); ?>">
							<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( get_term_link( $tag, 'fluxus-project-type' ) ); ?>">
								<b class="hash">#</b><?php echo esc_html( $tag->name ); ?>
							</a>
						</li>
						<?php

					endforeach;

				?>
			</ul>
				<?php

				echo wp_kses_post( $args['after_widget'] );

		endif;
	}

	function update( $new_instance, $old_instance ) {
		return $instance;
	}

	function form( $instance ) {
		echo '<p>' . __( 'This widget has no options.', 'fluxus-projects' ) . '</p>';
	}
}
