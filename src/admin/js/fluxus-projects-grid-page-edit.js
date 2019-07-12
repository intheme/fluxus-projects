(function($) {
	$(
		function() {
			var $aspectRatioSelect = $(
				'select[name="fluxus_portfolio_grid_aspect_ratio"]'
			),
			$autoAspectRatioNote   = $( '.fluxus-meta-field-aspect-ratio .notes' ),
			$layout                = $( '.js-button-grid-layout' ),
			$buttonReset           = $( '.js-button-grid-layout-reset' ),
			updateNotesVisibility;

			updateNotesVisibility = function() {
				if ($aspectRatioSelect.val() == 'auto') {
					$autoAspectRatioNote.show();
				} else {
					$autoAspectRatioNote.hide();
				}
			};

			updateNotesVisibility();
			$aspectRatioSelect.on( 'change keyup', updateNotesVisibility );

			$layout.click(
				function(e) {
					e && e.preventDefault();

					var $el = $( this );
					/**
					 * Note that using <a href='' /> is not ideal. Since if script fails,
					 * new window will still be opened, but it won't work. Fail early instead,
					 * do not open new window if JS fails.
					 */
					window.open( $el.data( 'url' ), 'layoutWindow' );
				}
			);

			$buttonReset.click(
				function(e) {
					e && e.preventDefault();

					var $el = $( this );

					if (window.confirm( $el.data( 'confirm' ) )) {
						$( 'input[name="fluxus_portfolio_grid_image_sizes"]' ).val( '' );
					}
				}
			);
		}
	);
})( jQuery );
