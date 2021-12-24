/**
 * Mailchimp submission logic.
 * For an example, see Mailchimp's own code.
 * http://s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js
 */
(function ($, Drupal) {
  Drupal.behaviors.mailchimpSticky = {
    attach: function (context, settings) {
      $(function() {
        var $block = $('.mailchimp-sticky');
        var $form = $('.mailchimp-sticky form');

        // Only show anything if there is no session cookie indicating submission.
        if (document.cookie.search('mailchimpsticky') == -1) {

          // By default the block starts display: none and then gets shown.
          $block.css({display: "block"});
          if ($block.is(":visible")) {
            if ($(window).width() < 768) {
              $('.region--site_footer').css({"padding-bottom": "88px"});
            }
            else {
              $('.region--site_footer').css({"padding-bottom": "65px"});
            }
          }

          window.addEventListener('resize', Drupal.debounce(function() {
            $block.css({display: "block"});
            if ($block.is(":visible")) {
              if ($(window).width() < 768) {
                $('.region--site_footer').css({"padding-bottom": "88px"});
              }
              else {
                $('.region--site_footer').css({"padding-bottom": "65px"});
              }
            }
          }, 250));
        }

        $('.mailchimp-sticky input[type="submit"]').once('mailchimp-sticky').bind('click', function ( event ) {
          // Prevent the form from submitting.
          if (event) event.preventDefault();

          // Get a better action. This way, if no JS, we fall back to a regular post.
          var url = $form.attr("action");
          url = url.replace("/post?u=", "/post-json?u=") + '&c=?';

          // Do an explicit Ajax post.
          $.ajax({
            type: 'POST',
            url: url,
            data: $form.serialize(),
            cache: false,
            dataType: 'jsonp',
            contentType: "application/json; charset=utf-8",
            error: function(err) {
              console.log("Could not connect to the registration server. Please try again later.");
            },
            success: function(data) {
              // This is how mailchimp processes messages in their own JS.
              if (data.result != "success") {
                var error;
                try {
                  var parts = data.msg.split(' - ', 2);
                  if (parts[1] == undefined){
                    error = data.msg;
                  }
                  else {
                    var i = parseInt(parts[0]);
                    if (i.toString() == parts[0]){
                      error = parts[1];
                    }
                    else {
                      error = data.msg;
                    }
                  }
                }
                catch(e) {
                  error = data.msg;
                }
                $('.mailchimp-sticky .mailchimp-sticky--errors').html(error);
                $('.mailchimp-sticky .mailchimp-sticky--errors').css({ display: "block" });
              }
              else {
                document.cookie = 'mailchimpsticky=1';
                $('.region--site_footer').css({"padding-bottom": "60px"});
                $('.mailchimp-sticky .mailchimp-sticky--label').css({ display: "none" });
                $('.mailchimp-sticky form').css({ display: "none" });
                $('.mailchimp-sticky .mailchimp-sticky--success').css({ display: "block" });
              }
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal);
