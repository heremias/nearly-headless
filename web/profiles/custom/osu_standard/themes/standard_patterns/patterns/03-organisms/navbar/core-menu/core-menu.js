(function ($, Drupal) {
  Drupal.behaviors.coreMenu = {
    attach: function (context, settings) {

      $(function() {

        /*
          OSU WAI-ARIA Navigation

          Created by Arnell Damasco
          August 4, 2017
          damasco.2@osu.edu
        */

        var $nav = $('.menu');
        var menuLevel = 0;
        var initialFocus = true;
        var isMobile = false;
        var currentItemIndex = 0;

        $nav.addClass('osu-aria-nav');
        $nav.attr('id', 'global_nav');

        var isConference = ($nav.parents('.core-menu-conference').length) ? true : false;

        var keyCode = Object.freeze({
          TAB: 9,
          RETURN: 13,
          ESC: 27,
          SPACE: 32,
          PAGEUP: 33,
          PAGEDOWN: 34,
          END: 35,
          HOME: 36,
          LEFT: 37,
          UP: 38,
          RIGHT: 39,
          DOWN: 40
        });

        var checkMobile = function() {
          if ($('.button-menu').is(':visible')) {
            isMobile = true;
          }
          else {
            isMobile = false;
          }
        };

        var checkMenuState = function() {
          if (!isMobile) {
            $('.menu-wrapper').show();
            $('.menu__item ul').hide();
            if (!isConference) $('.menu-arrow').removeClass('closed-arrow').addClass('opened-arrow');
            if (!isConference) $('li.menu__item--active > .svg-triangle').show();
          }
          else {
            if (!isConference) $('.svg-triangle').hide();
            $('.selected').removeClass('selected');
            $('.menu-wrapper').hide();
            $('.menu__item ul').hide();
            if (!isConference) $('.menu-arrow').removeClass('opened-arrow').addClass('closed-arrow');
          }
        };

        var activateMenu = function (mymenu, direction) {
          if (!isConference && !isMobile) $('.svg-triangle').hide();
          if (!isConference && !isMobile) $('li.menu__item--active > .svg-triangle').show();
          if (direction === 'open') {
            mymenu.show();
            mymenu.parent().attr('aria-expanded', 'true');
            mymenu.attr('aria-hidden', 'false');
            if (!isConference && !isMobile) mymenu.parent().find('.svg-triangle').show();
          }
          else {
            mymenu.hide();
            mymenu.parent().attr('aria-expanded', 'false');
            mymenu.attr('aria-hidden', 'true');
            if (!isConference && !isMobile) mymenu.parent().find('li:not(.menu__item--active) > .svg-triangle').hide();
          }
        };

        var closeSubMenu = function (myMenuLevel) {
          switch (myMenuLevel) {
            case 0:
              activateMenu($('.level-one ul'), 'close');
            break;

            case 1:
              activateMenu($('.level-two ul'), 'close');
            break;

            case 2:
              activateMenu($('.level-three ul'), 'close');
            break;
          }
        }

        var resetTabindex = function () {
          $nav.find('a').attr('tabindex', '-1');
          if (!isConference) $nav.find('.menu-arrow').attr('tabindex', '-1');
        };

        var selectedState = function() {
          $('.selected').removeClass('selected');
          $(':focus').parents('li').addClass('selected');
          $(':focus').parent().addClass('selected');

          if ($(':focus').parents('.level-one').hasClass('item-with-ul')) {
            if (!isConference && !isMobile) $(':focus').parents('.level-one').find('.svg-triangle').show();
          }
        };

        // =====================================================================
        // Parse through UL list and add hooks and WAI-ARIA attributes
        // =====================================================================
        $nav.find('li').each(function () {
          if ($(this).has('ul').length) {
            $(this).addClass('item-with-ul');
            if (!isConference) $(this).append('<button class="menu-arrow" tab-index="-1" aria-expanded="false"><span></span></button>');
          }
        });

        $nav.find('> li').addClass('level-one');
        $nav.find('> li > ul > .item-with-ul').addClass('level-two');
        $('.item-with-ul > a').attr('aria-haspopup', 'true');
        $('.item-with-ul ul').addClass('sub-menu');
        $('.item-with-ul ul').attr('role', 'menu');
        $('.item-with-ul').attr('aria-expanded', 'false');
        $('.item-with-ul ul').attr('aria-hidden', 'true');
        $nav.attr('role', 'menubar');
        $nav.find('li').attr('role', 'presentation');
        $nav.find('li a').attr('role', 'menuitem');
        $nav.find('li a').attr('tabindex', '-1');
        $nav.find('li:first-child a').attr('tabindex', '0');

        checkMobile();
        checkMenuState();

        $(window).on('resize', function() {
          checkMobile();
          checkMenuState();
        });

        // =====================================================================
        // Menu hover events
        // =====================================================================
        $('.item-with-ul').hover(
          function () {
            if (!isMobile) {
              $(this).find('> ul').show();
              $(this).attr('aria-expanded', 'true');
              $(this).find('> ul').attr('aria-hidden', 'false');
              if (!isConference && !isMobile) $(this).find('> .svg-triangle').show();
              resetTabindex();
              $('.selected').removeClass('selected');
            }
          },
          function () {
            if (!isMobile) {
              $(this).find('> ul').hide();
              $(this).attr('aria-expanded', 'false');
              $(this).find('> ul').attr('aria-hidden', 'true');
              if (!isConference && !isMobile && !($(this).parents('.level-one').hasClass('item-with-ul'))) {
                $('li:not(.menu__item--active) > .svg-triangle').hide();
              }
              $nav.find('li:first-child a').attr('tabindex', '0');
            }
          }
        );

        // =====================================================================
        // Mobile menu button toggle
        // =====================================================================
        $('.button-menu').on('click', function() {
          if ($('.menu-wrapper').is(':visible')) {
            $('.menu-wrapper').hide();
          }
          else {
            $('.menu-wrapper').show();
          }
        });

        // =====================================================================
        // Dropdown menu arrow button (for mobile only)
        // =====================================================================
        $('.menu-arrow').on('click', function(){
          if (isMobile) {
            if($(this).prev().is(':visible')) {
              $(this).parent().find('sub-menu').attr('aria-hidden', 'true').hide();
              $(this).removeClass('opened-arrow').addClass('closed-arrow');

              $(this).prev().hide();
              $(this).prev().attr('aria-expanded', 'false');
              $(this).prev().attr('aria-hidden', 'true');
            }
            else {
              $(this).parent().find('sub-menu').attr('aria-hidden', 'false').show();
              $(this).removeClass('closed-arrow').addClass('opened-arrow');

              $(this).prev().show();
              $(this).prev().attr('aria-expanded', 'true');
              $(this).prev().attr('aria-hidden', 'false');
            }
          }
        });

        // =====================================================================
        // Click outside navigation: close all menus
        // =====================================================================
        $(document).click(function () {
          if (!isMobile) {
            if (this.id !== 'global_nav') {
              resetTabindex();
              activateMenu($nav.find('ul'), 'close');
              $('.selected').removeClass('selected');
              $nav.find('li a').attr('tabindex', '-1');
              $nav.find('li:first-child a').attr('tabindex', '0');
            }
          }
        });

        // -------------------------------------------------------------------------
        // Add ARIA menu item counts
        // -------------------------------------------------------------------------
        var menuItemCount = 1;
        $nav.find('> li').each(function() {
          $(this).find('> a').attr('aria-label', 'menu level 1, item ' + menuItemCount + ' of ' + $nav.find('> li').length + ', link ' + $(this).find('> a').html());
          menuItemCount++;
        });

        $nav.find('> li > ul').each(function() {
          menuItemCount = 1;
          $(this).find('> li').each(function() {
            $(this).find('> a').attr('aria-label', 'menu level 2, item ' + menuItemCount + ' of ' + $(this).parent().find('> li').length + ', link ' + $(this).find('> a').html());
            menuItemCount++;
          });
        });

        $nav.find('> li > ul > li > ul').each(function() {
          menuItemCount = 1;
          $(this).find('> li').each(function() {
            $(this).find('> a').attr('aria-label', 'menu level 3, item ' + menuItemCount + ' of ' + $(this).parent().find('> li').length + ', link ' + $(this).find('> a').html());
            menuItemCount++;
          });
        });

        $nav.find('> li > ul > li > ul > li > ul').each(function() {
          menuItemCount = 1;
          $(this).find('> li').each(function() {
            $(this).find('> a').attr('aria-label', 'menu level 4, item ' + menuItemCount + ' of ' + $(this).parent().find('> li').length + ', link ' + $(this).find('> a').html());
            menuItemCount++;
          });
        });

        // =====================================================================
        // ESC key: close all menus
        // =====================================================================
        $nav.on('keydown', function (e) {
          switch (e.keyCode) {
            case keyCode.ESC:

            activateMenu($nav.find('ul'), 'close');
            resetTabindex();

            if (menuLevel > 0) {
              // -------------------------------------------------------------
              // Reset focus to top level of the menus
              // -------------------------------------------------------------
              $(':focus').parents('.level-one').find('> a').focus().attr('tabindex', '0');
              selectedState();
              activateMenu($(':focus').parent().find('> ul'), 'open');
            }
            else {
              // -------------------------------------------------------------
              // Escape out of entire menu, reset tab index to first menu item
              // -------------------------------------------------------------
              $(':focus').blur();
              $('.selected').removeClass('selected');
              $nav.find('li:first-child > a').attr('tabindex', '0');
            }
            menuLevel = 0;

            e.preventDefault();

            break;

            default:
            break;
          }
        });

        // -------------------------------------------------------------------------
        // ACCESSIBILITY Keyboard navigation
        // -------------------------------------------------------------------------
        $nav.find(':first-child > a').on('focusin', function (e) {
          if (initialFocus) {
            initialFocus = false;
            if ($nav.find(':first-child > a').attr('aria-haspopup') === 'true') {
              $('.selected').removeClass('selected');
              $(':focus').parent().addClass('selected');
              activateMenu($(':focus').parent().find('> ul'), 'open');
            }
          }

          e.preventDefault();
        });

        $nav.on('keydown', function (e) {
          // -------------------------------------------------------------
          // Character input to jump to item with matching first letter
          // -------------------------------------------------------------
          if ((e.keyCode > 47) && (e.keyCode < 91)) {
            var charCode = e.which;
            if (!charCode) {
              return;
            }

            menuItemFound = false;

            $(':focus').parent().parent().find('> li > a').each(function() {

              if (!menuItemFound) {
                if (($(this).html().charAt(0).toLowerCase() === String.fromCharCode(charCode).toLowerCase()) && (currentItemIndex != $(this).parent().index())) {
                  menuLevel = ($(':focus').parentsUntil('.level-one').length) / 2;

                  closeSubMenu(menuLevel);

                  resetTabindex();
                  $(this).focus().attr('tabindex', '0');
                  currentItemIndex = $(this).parent().index();
                  menuItemFound = true;
                  selectedState();
                }
              }
            });
          }
          else {
            switch (e.keyCode) {

              case keyCode.TAB:

              activateMenu($nav.find('ul'), 'close');
              resetTabindex();
              $nav.find('li:first-child a').attr('tabindex', '0');
              $('.selected').removeClass('selected');
              initialFocus = true;

              break;

              // ***************************************************************

              case keyCode.END:
              menuLevel = ($(':focus').parentsUntil('.level-one').length) / 2;

              closeSubMenu(menuLevel);

              resetTabindex();
              $(':focus').parent().parent().find('> li:last-child > a').focus().attr('tabindex', '0');

              selectedState();

              e.preventDefault();

              break;

              // ***************************************************************

              case keyCode.HOME:
              menuLevel = ($(':focus').parentsUntil('.level-one').length) / 2;

              closeSubMenu(menuLevel);

              resetTabindex();
              $(':focus').parent().parent().find('> li:first-child > a').focus().attr('tabindex', '0');

              selectedState();

              e.preventDefault();

              break;

              // ***************************************************************

              case keyCode.RETURN:

              // -------------------------------------------------------------
              // If newly selected menu item has dropdown menu, then open it
              // -------------------------------------------------------------
              if ($(':focus').attr('aria-haspopup') === 'true') {

                // -------------------------------------------------------------
                // Set tabindex of all menu items to -1
                // -------------------------------------------------------------
                resetTabindex();

                activateMenu($(':focus').parent().find('> ul'), 'open');
                $(':focus').parent().find('> ul li:first-child > a').focus().attr('tabindex', '0');

                selectedState();

                e.preventDefault();
              }

              break;

              // ***************************************************************

              case keyCode.SPACE:

              var thelink = $(':focus').attr('href');
              window.location = thelink;

              e.preventDefault();

              break;

              // ***************************************************************

              case keyCode.DOWN:

              menuLevel = ($(':focus').parentsUntil('.level-one').length) / 2;

              // -------------------------------------------------------------
              // For menu items not on first level, close all open SUB menus
              // And set tabindex of all menu items to -1
              // -------------------------------------------------------------
              if (menuLevel >= 1) {
                activateMenu($(':focus').parent().parent().find('li ul'), 'close');
                resetTabindex();
              }

              // -------------------------------------------------------------
              // If menu item has a dropdown menu, then set focus first item
              // -------------------------------------------------------------
              if (($(':focus').attr('aria-haspopup') === 'true') && (menuLevel === 0)) {
                activateMenu($(':focus').parent().find('> ul'), 'open');
                $(':focus').parent().find('ul > li:first-child > a').focus().attr('tabindex', '0');
              }
              else {
                // -----------------------------------------------------------
                // If menu item is last in list, then set focus to first item
                // Otherwise, set focus to next menu item
                // -----------------------------------------------------------
                if (menuLevel > 0) {
                  if ($(':focus').parent().index() == ($(':focus').parent().siblings().length)) {
                    $(':focus').parent().parent().find('li:first-child > a').focus().attr('tabindex', '0');
                  }
                  else {
                    $(':focus').parent().next().find('a').focus().attr('tabindex', '0');
                  }
                }
              }

              // -------------------------------------------------------------
              // If newly selected menu item has dropdown menu, then open it
              // -------------------------------------------------------------
              if ($(':focus').attr('aria-haspopup') === 'true') {
                activateMenu($(':focus').parent().find('> ul'), 'open');
              }

              selectedState();

              e.preventDefault();
              break;

              // ***************************************************************

              case keyCode.UP:

              // -------------------------------------------------------------
              // Do the thing if not focused on menu arrow button
              // -------------------------------------------------------------

              menuLevel = ($(':focus').parentsUntil('.level-one').length) / 2;

              if (menuLevel > 0) {
                // -----------------------------------------------------------
                // Set tabindex of all menu items to -1
                // -----------------------------------------------------------
                resetTabindex();

                // -----------------------------------------------------------
                // Close all open SUB menus
                // -----------------------------------------------------------
                if (menuLevel >= 1) {
                  activateMenu($(':focus').parent().parent().find('li ul'), 'close');
                }

                // -----------------------------------------------------------
                // If menu item is last in list, then set focus to first item
                // Otherwise, set focus to next menu item
                // -----------------------------------------------------------
                if ($(':focus').parent().index() == 0) {
                  $(':focus').parent().parent().find(':last-child > a').focus().attr('tabindex', '0');
                }
                else {
                  $(':focus').parent().prev().find('a').focus().attr('tabindex', '0');
                }

                // -----------------------------------------------------------
                // If new selected menu item has dropdown menu, then open it
                // -----------------------------------------------------------
                if ($(':focus').attr('aria-haspopup') === 'true') {
                  activateMenu($(':focus').parent().find('> ul'), 'open');
                }
              }

              selectedState();

              e.preventDefault();
              break;

              // ***************************************************************

              case keyCode.RIGHT:

              menuLevel = ($(':focus').parentsUntil('.level-one').length) / 2;

              // ---------------------------------------------------------------
              // Set tabindex of all menu items to -1
              // ---------------------------------------------------------------
              resetTabindex();

              // ---------------------------------------------------------------
              // If this menu item has a dropdown button, then set focus to it
              // If nested menu arrow focus, then set focus to submenu
              // ---------------------------------------------------------------
              if (($(':focus').attr('aria-haspopup') === 'true') && (menuLevel > 0)) {
                $(':focus').parent().find('> ul li:first-child > a').focus().attr('tabindex', '0');
              }
              else {
                // -------------------------------------------------------------
                // Close all open menus
                // -------------------------------------------------------------
                activateMenu($nav.find('ul'), 'close');

                // -------------------------------------------------------------
                // If menu item is last in list, then set focus to first item
                // Otherwise, set focus to next menu item
                // -------------------------------------------------------------
                if ($(':focus').parents('.level-one').index() == ($nav.find('> li').length - 1)) {
                  $nav.find('li:first-child > a').focus().attr('tabindex', '0');
                }
                else {
                  $(':focus').parents('.level-one').next().find('> a').focus().attr('tabindex', '0');
                }
              }

              // -------------------------------------------------------------
              // If newly selected menu item has dropdown menu, then open it
              // -------------------------------------------------------------
              if ($(':focus').attr('aria-haspopup') == 'true') {
                activateMenu($(':focus').parent().find('> ul'), 'open');
              }

              selectedState();

              e.preventDefault();
              break;

              // ***************************************************************

              case keyCode.LEFT:

              menuLevel = ($(':focus').parentsUntil('.level-one').length) / 2;

              // -------------------------------------------------------------
              // Set tabindex of all menu items to -1
              // -------------------------------------------------------------
              resetTabindex();

              // -------------------------------------------------------------
              // If 2nd level flyout menu
              // If menu item is first in list, then set focus to last item
              // Otherwise, set focus to previous menu item
              // -------------------------------------------------------------
              if (menuLevel >= 2) {
                $(':focus').parent().parent().parent().find('> a').focus().attr('tabindex', '0');
              }
              else if ($(':focus').parents('.level-one').index() == 0) {
                $nav.find('li:last-child > a').focus().attr('tabindex', '0');
                activateMenu($nav.find('ul'), 'close');
              }
              else {
                $(':focus').parents('.level-one').prev().find('a').focus().attr('tabindex', '0');
                activateMenu($nav.find('ul'), 'close');
              }

              // -------------------------------------------------------------
              // If newly selected menu item has dropdown menu, then open it
              // -------------------------------------------------------------
              if ($(':focus').attr('aria-haspopup') === 'true') {
                activateMenu($(':focus').parent().find('> ul'), 'open');
              }

              selectedState();

              e.preventDefault();
              break;

              // ***************************************************************

              default:
              break;

            }
          }
        });
      });
    }
  };
})(jQuery, Drupal);
