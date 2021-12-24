(function ($, Drupal) {
  Drupal.behaviors.listingMeeting = {
    attach: function (context, settings) {
      $(function() {

        var $activeMonth = $('.c-listing-meeting__navigation-months--mobile ul li.c-listing-meeting__navigation-months__active a');
        var $selectedMonth = $('.c-listing-meeting__navigation-months__selected');
        var $dropdownMonth = $('.c-listing-meeting__navigation-months--mobile ul');
        var $listMonth = $('.c-listing-meeting__navigation-months--mobile ul li a');

        $selectedMonth.html($activeMonth.html());

        $selectedMonth.on('click', function() {
          $dropdownMonth.toggle();
        });

        $listMonth.on('click', function() {
          $selectedMonth.html($(this).html());
          $dropdownMonth.toggle();
          window.location.href = $(this).attr('href');
        });

      });
    }
  };
})(jQuery, Drupal);
