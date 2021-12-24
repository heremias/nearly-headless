import { jarallax, jarallaxVideo } from 'jarallax';


(function ($, Drupal) {
  Drupal.behaviors.tweetable = {
    attach: function (context, settings) {
      jarallaxVideo();

    jarallax(document.querySelectorAll('.jarallax'), {
        speed: 0.2
    });
    }
  };
})(jQuery, Drupal);
