(function ($, Drupal) {
  Drupal.behaviors.page = {
    attach: function (context, settings) {
      console.log('theme script loaded')
    }
  };
})(jQuery, Drupal);
