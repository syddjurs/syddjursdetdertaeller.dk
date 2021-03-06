/**
 * Component: Navigation
 */

 var sddt = sddt || {};

 sddt.navigation = sddt.navigation || function() {
  this.init = function() {
    toggleNavigation();
    //stickyHeader();
  };

  this.stickyHeader = function(){
    var siteHeight = jQuery('body').height(),
        siteHeader = jQuery('#js-site-header'),
        siteHeaderOriginalHeight = siteHeader.height();

    if (window.innerWidth > 991) {
      jQuery(window).scroll(function(){
        if (siteHeader.length > 0) {
          if (jQuery(window).scrollTop() > (siteHeader.height() - (siteHeader.outerHeight() - siteHeader.innerHeight())) && jQuery(window).scrollTop() > siteHeaderOriginalHeight) {
            siteHeader.addClass('js-fixed');
          }
          else {
            siteHeader.removeClass('js-fixed');
          }
        }
      });
    }
  };

  this.toggleNavigation = function(){
    if (jQuery('#js-navigation').length > 0) {
      jQuery('#js-navigation-open, #js-navigation-close').on('click', function(){
        if (!jQuery('#js-navigation').hasClass('js-active')) {
          // Add active class to the menu
          jQuery('#js-navigation').addClass('js-active');

          // Add menu-open class to the site-header
          jQuery('#js-site-header').addClass('js-menu-open');

          // Add a backdrop to the site
          sddt.overlay.backdrop();
        }
        else {
          // Remove active class to the menu
          jQuery('#js-navigation').removeClass('js-active');

          // Remove menu-open class to the site-header
          jQuery('#js-site-header').removeClass('js-menu-open');

          // Remove the backdrop
          sddt.overlay.destroy();
        }
      });
    }
  };


         // this.toggleMobileNavigation = function(){
         //     if (jQuery('#js-mobile-navigation').length > 0) {
         //         jQuery('#js-mobile-navigation-open, #js-mobile-navigation-close').on('click', function(){
         //             if (!jQuery('#js-mobile-navigation').hasClass('js-active')) {
         //                 // Add active class to the menu
         //                 jQuery('#js-mobile-navigation').addClass('js-active');
         //
         //                 // Add menu-open class to the site-header
         //                 jQuery('#js-site-header').addClass('js-menu-open');
         //
         //                 // Add a backdrop to the site
         //                 sddt.overlay.backdrop();
         //             }
         //             else {
         //                 // Remove active class to the menu
         //                 jQuery('#js-mobile-navigation').removeClass('js-active');
         //
         //                 // Remove menu-open class to the site-header
         //                 jQuery('#js-site-header').removeClass('js-menu-open');
         //
         //                 // Remove the backdrop
         //                 sddt.overlay.destroy();
         //             }
         //         });
         //     }
         // };
  /**
   * Script to move navigation elements around depending on the window inner width
   */
  this.moveNavigationElements = function () {
    if (jQuery('#js-mobile-navigation').length > 0) {
      var siteHeader = jQuery('#js-site-header'),
          siteSearch = jQuery('#js-site-search'),
          siteNavigation = jQuery('#js-site-navigation'),
          mobileNavigation = jQuery('#js-navigation'),
          breadcrumb = jQuery('#js-breadcrumb');

      setTimeout(function() {
        if (window.innerWidth < 992) {
        /*
         * Move elements to mobile view
         */
          // Search
          // siteSearch.appendTo(mobileNavigation);

          // Navigation
          siteNavigation.appendTo(mobileNavigation);
        }
        else {
          /*
           * Move elements to desktop view
           */
          // Search
          siteSearch.insertAfter(breadcrumb);

          // Navigation
          siteNavigation.insertAfter(jQuery('.logo', siteHeader));
        }
      }, 300);
    }
  };

  return {
    init: init,
    moveNavigationElements: moveNavigationElements
  };
}();
