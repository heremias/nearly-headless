/*(function tweetableScript($, Drupal) {
  Drupal.behaviors.tweetable = {
    attach(context) {
      $('.tweetable').click(function click() {
        const url = this.hasAttribute('data-tweetable-url') ? decodeHtml(this.getAttribute('data-tweetable-url')) : 'https://insights.osu.edu';
        const handle = this.hasAttribute('data-tweetable-handle') && (this.getAttribute('data-tweetable-handle').length > 0) ? decodeHtml(this.getAttribute('data-tweetable-handle')) : '';
        const input = this.hasAttribute('data-tweetable-text') && (this.getAttribute('data-tweetable-text').length > 0) ? decodeHtml(this.getAttribute('data-tweetable-text')) : decodeHtml(this.innerText);
        const suffix = (handle.length > 0) ? "\n" + url + ' via ' + handle : "\n" + url;

        // If the text is short enough to append a link, no special processing required.
        let text = input.length < 140 - suffix.length ? input : '';

        // If the text is too long, split on words and elipsify.
        if (text == '') {
          let words = input.split(' ');
          let i = 0;

          while ((i < words.length) && (text.length + words[i].length + 1 < 137 - suffix.length)) {
            text += ' ' + words[i];
            i++;
          }
          text = text.trim();
          text += '...';
        }
        text = encodeURIComponent(text + suffix);
        window.open('https://twitter.com/intent/tweet?text=' + text, "Twitter", "height=285,width=550,resizable=1");
      });

      function decodeHtml(html) {
        var txt = document.createElement('textarea');
        txt.innerHTML = html;
        return txt.value;
      }
    },
  };
}(jQuery, Drupal));
*/


(function ($, Drupal) {
  Drupal.behaviors.tweetable = {
    attach: function (context, settings) {

      function decodeHtml(html) {
        var txt = document.createElement('textarea');
        txt.innerHTML = html;
        return txt.value;
      }

      $('.js-tweetable').click(function click() {
        var url = this.hasAttribute('data-tweetable-url') ? decodeHtml(this.getAttribute('data-tweetable-url')) : 'https://insights.osu.edu';
        var handle = this.hasAttribute('data-tweetable-handle') && (this.getAttribute('data-tweetable-handle').length > 0) ? decodeHtml(this.getAttribute('data-tweetable-handle')) : '';
        var input = this.hasAttribute('data-tweetable-text') && (this.getAttribute('data-tweetable-text').length > 0) ? decodeHtml(this.getAttribute('data-tweetable-text')) : decodeHtml(this.innerText);
        var suffix = (handle.length > 0) ? "\n" + url + ' via ' + handle : "\n" + url;

        // If the text is short enough to append a link, no special processing required.
        var text = input.length < 140 - suffix.length ? input : '';

        // If the text is too long, split on words and elipsify.
        if (text == '') {
          var words = input.split(' ');
          var i = 0;

          while ((i < words.length) && (text.length + words[i].length + 1 < 137 - suffix.length)) {
            text += ' ' + words[i];
            i++;
          }
          text = text.trim();
          text += '...';
        }
        text = encodeURIComponent(text + suffix);
        window.open('https://twitter.com/intent/tweet?text=' + text, "Twitter", "height=285,width=550,resizable=1");
      });
    }
  };
})(jQuery, Drupal);
