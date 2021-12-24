(function ($, Drupal) {
  Drupal.behaviors.sectionCardsCarousel = {
    attach: function (context, settings) {
      $(function() {
        $('.c-section-cards-many .field--name-items').slick({

          dots: false,
          infinite: true,
          speed: 300,
          slidesToShow: 2,
          slidesToScroll: 1,
          adaptiveHeight: true,
          prevArrow: "<i class='fa fa-5 fa-chevron-left a-left control-c prev slick-prev' aria-hidden='true'></i>",
          nextArrow: "<i class='fa fa-5 fa-chevron-right a-right control-c next slick-next' aria-hidden='true'></i>",
          responsive: [
            {
              breakpoint: 991,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1
              }
            },
            {
              breakpoint: 991,
              settings: "unslick"
            }
          ]
        });
      });
    }
  };
  Drupal.behaviors.sectionCardsEqualizer = {
    attach: function (context, settings) {
      $(function() {

        // Only on desktop
        if ($(window).width() > 991) {

          /**
           * Loop over every set of cards to equalize the height of cards in a row.
           */
          $('.c-section-cards').each(function(i, cards) {
            var height = 0;
            var wrappers = '.m-third-cta,.m-third-blurb';
            var expandables = '.m-third-cta__label,.m-third-blurb__content';

            // Discover the tallest card in a set.
            $(cards).find(wrappers).each(function(i, card) {
              height = height > $(card).height() ? height : $(card).height();
            });

            // Expand the expandable field of each card to meet the tall height.
            $(cards).find(wrappers).each(function (i, card) {
              var expandable = $(card).find(expandables).first();
              expandable.css('min-height', height - $(card).height() + expandable.height());
            });
          });
        }

      });
    }
  };
})(jQuery, Drupal);
