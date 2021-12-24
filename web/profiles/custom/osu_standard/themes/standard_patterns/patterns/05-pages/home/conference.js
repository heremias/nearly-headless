(function ($, Drupal) {
  Drupal.behaviors.homeConference = {
    attach: function (context, settings) {

      $(function() {

        var isMobile = false;
        var $region = $('.region--content_before');
        var $infoCard = $('.o-info-cards');

        var checkMobile = function() {
          if ($('.button-menu').is(':visible')) {
            isMobile = true;
          }
          else {
            isMobile = false;
          }
        };

        var setInfoCardHeight = function() {
          if (!isMobile) {
            $region.height($infoCard.height() / 2);
          }
          else {
            $region.height('auto');
          }
        }

        // =====================================================================
        // Set height of Info Card wrapper to be half, to accommodate
        // negative top margin
        // =====================================================================

        checkMobile();
        setInfoCardHeight();

        $(window).on('resize', function() {
          checkMobile();
          setInfoCardHeight();
        });
      });
    }
  };
})(jQuery, Drupal);
