(function ($, Drupal) {
  Drupal.behaviors.personQuoteAudio = {
    attach: function (context, settings) {
      $('.m-person-quote-audio__button-wrapper button').click(function(){
        var component = $(this).closest('.m-person-quote-audio');
        component.find('.m-person-quote-audio__button-wrapper').hide();
        component.find('.m-person-quote-audio-playback').show();
        component.find('.m-person-quote-audio-playback audio')[0].play();
      });
    }
  };
})(jQuery, Drupal);
