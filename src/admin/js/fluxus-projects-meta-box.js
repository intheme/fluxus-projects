(function($, MediaBoxApp, MediaBoxItem, MediaBoxItemList) {
	// When updating model schema also update item_defaults() PHP method.
	var ProjectMediaItem = MediaBoxItem.extend(
		{
			defaults: _.extend(
				MediaBoxItem.prototype.defaults,
				{
					featured: false,
					hideControls: false,
					autoplay: false
				}
			)
		}
	);

	var ProjectMediaItemList = MediaBoxItemList.extend(
		{
			model: ProjectMediaItem
		}
	);

	new MediaBoxApp(
		{
			el: $( '#project-media-meta-box' ),
			CollectionClass: ProjectMediaItemList
		}
	);
})( jQuery, window.MediaBoxApp, window.MediaBoxItem, window.MediaBoxItemList );
