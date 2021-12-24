(function ($, Drupal) {
  Drupal.behaviors.cardsCarousel = {
    attach: function (context, settings) {
      $(function() {
        $('.m-cards-many .field--name-items').slick({

          dots: false,
          infinite: true,
          speed: 300,
          slidesToShow: 3,
          slidesToScroll: 1,
          adaptiveHeight: true,
          prevArrow: "<i class='fa fa-5 fa-chevron-left a-left control-c prev slick-prev' aria-hidden='true'></i>",
          nextArrow: "<i class='fa fa-5 fa-chevron-right a-right control-c next slick-next' aria-hidden='true'></i>",
          responsive: [
            {
              breakpoint: 768,
              settings: {
                slidesToShow: 2,
                slidesToScroll: 1
              }
            },
            {
              breakpoint: 576,
              settings: {
                slidesToShow: 1,
                slidesToScroll: 1
              }
            },
          ]
        });
      });
    }
  };

  Drupal.behaviors.cardsEqualizer = {
    attach: function (context, settings) {

      // Equalize the cards just once.
      $('body').once('cards').each(function() { setTimeout(cardsEqualizer, 500); });

      // Equalize the cards at most every 250ms on resize.
      window.addEventListener('resize', Drupal.debounce(cardsEqualizer, 250));

      /**
       * Callback to equalize cards.
       */
      function cardsEqualizer() {
        // By default, we don't equalize any cards.
        var equalizees = null;

        // On tablet+, we equalize all cards, but on mobile just the many we carousel.
        if ($(window).width() > 768) {
          equalizees = '.m-cards';
        }
        else {
          equalizees = '.m-cards-many';
        }

        /**
         * Loop over every set of cards to equalize the height of cards in a row.
         */
        $(equalizees).each(function(i, cards) {
          var height = 0;
          var wrappers = '.m-third-cta,.m-third-blurb';
          var expandables = '.m-third-cta__label,.m-third-blurb__content';

          // Unset min-heights if set.
          $(cards).find(wrappers).each(function(i, card) {
            var expandable = $(card).find(expandables).first();
            expandable.css('min-height', 0);
          });

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
    }
  };
})(jQuery, Drupal);
