(function ($, Drupal) {

  Drupal.behaviors.floorslideshow = {
    attach: function (context, settings) {
      var path = settings.path ? settings.path.baseUrl + settings.path.activeTheme + '/images' : '../../images'
      $('.my-slideshow').slick({
        dots: true,
        prevArrow: '<button type="button" class="slick-prev"><img src="' + path + '/arrow_left.png" alt="Previous" /></button>',
        nextArrow: '<button type="button" class="slick-next"><img src="' + path + '/arrow_right.png" alt="Next" /></button>'
      });

    }
  };
})(jQuery, Drupal);
