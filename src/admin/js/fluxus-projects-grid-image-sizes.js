(function($) {
  $(function() {
    var $portfolio = $('.js-portfolio-grid');
    var grid = $portfolio.data('grid');
    var maxSize = $portfolio.data(
      $portfolio.data('orientation') === 'horizontal' ? 'rows' : 'columns'
    );
    var updateInfo = function() {
      $('.js-grid-project').each(function() {
        var $el = $(this);
        var size = $el.data('size') || 1;
        var cropping = $el.attr('data-cropping') || 'center center';

        $el
          .find('.meta-info')
          .html(
            'Size: x%d &nbsp;&nbsp; cropping: %s'
              .replace('%d', size)
              .replace('%s', cropping)
          );
      });
    };

    $('.js-grid-project')
      .each(function() {
        var $el = $(this);
        var $info = $('<span class="meta-info"></span>');
        var $inner = $el.find('.grid-project__inner');

        $inner.append(
          '<a href="#" class="btn js-change-size dashicons-before dashicons-editor-distractionfree">' +
            window.wpVars.clickToChangeSize +
            '</a>'
        );
        $inner.append(
          '<a href="#" class="btn js-change-cropping dashicons-before dashicons-image-crop">Change cropping mode</a>'
        );

        $el.append($info);
        updateInfo();
      })
      .click(function(event) {
        event.preventDefault();
      });

    // Clicking on project should change it's size,
    // if the maximum size is reached, the size should go back to the smallest.
    $('.js-change-size').click(function(event) {
      event.preventDefault();

      var $el = $(this),
        $project = $el.closest('.js-grid-project'),
        size = $project.data('size');

      size = size < maxSize ? size + 1 : 1;

      $project.data('size', size);

      grid.render({
        force: true
      });
      updateInfo();
    });

    $('.js-change-cropping').click(function(e) {
      e.preventDefault();

      var $el = $(this),
        $project = $el.closest('.js-grid-project'),
        croppingPoint = $project.attr('data-cropping') || 'center center',
        croppingOptions = [
          'center top',
          'right top',
          'right 20%',
          'right center',
          'right bottom',
          'center bottom',
          'left bottom',
          'left center',
          'left 20%',
          'left top'
        ],
        index = croppingOptions.indexOf(croppingPoint);

      index++;
      if (index === croppingOptions.length) {
        index = 0;
      }

      $project.attr('data-cropping', croppingOptions[index]);
      updateInfo();
    });

    // Close the window and populate input field on parent window.
    $('.js-save-positions').click(function(e) {
      e && e.preventDefault();

      var imageSizes = [],
        croppingPoints = {};

      $('.grid-project').each(function() {
        var $el = $(this),
          id = $el.data('id');

        imageSizes.push({
          id: id,
          size: $el.data('size')
        });

        croppingPoints[id] = $el.data('cropping');
      });

      window.opener
        .jQuery('[name="fluxus_portfolio_grid_image_sizes"]')
        .val(JSON.stringify(imageSizes));
      window.opener
        .jQuery('[name="fluxus_portfolio_grid_image_cropping"]')
        .val(JSON.stringify(croppingPoints));
      window.close();
    });

    $('.js-cancel-save-positions').click(function(event) {
      event.preventDefault();
      window.close();
    });
  });
})(jQuery);
