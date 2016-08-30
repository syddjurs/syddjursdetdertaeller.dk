/**
 * Component: Overlay
 * Both overlay and backdrop
 */


var sddt = sddt || {};

sddt.overlay = sddt.overlay || function () {

  this.init = function() {
   openOverlay();
  };

  this.openOverlay = function() {
    jQuery('body').on('click', '.js-open-overlay', function(e){
      e.preventDefault();

      var src = jQuery(this).data('src');

      jQuery.ajax({
        url : src,
        context: document.body
      }).done(function(data){
        create({
          content: data
        });
      });
    });
  };

  // Overlay functions
  this.backdrop = function() {
    if (jQuery('#js-overlay-backdrop').length !== 1) {
      jQuery('body').append('<div id="js-overlay-backdrop" class="overlay-backdrop"></div>');
      // jQuery('#js-overlay-backdrop').height(jQuery('body').height());

      // Lock scroll
      jQuery.scrollLock(true);

      jQuery('#js-overlay-backdrop').on('click', function(){
        destroy();
      });
    }
  };

  this.create = function(data) {
    // reset overlay the hard way
    if (jQuery('#js-overlay').length === 1) {
      jQuery('#js-overlay, #js-overlay-content').remove();
    }
    else {
      backdrop();
    }

    var btnClose = jQuery('<a>x</a>').attr('href', '#closeOverlay').addClass('js-overlay-close');
    var overlayInner = jQuery('<div>').addClass('js-overlay-inner').append(jQuery('<div>').addClass('js-overlay-inner-scroll').append(data.content));

    jQuery('body').append(jQuery('<div>').attr('id', 'js-overlay-content').append(btnClose).append(overlayInner).fadeIn("fast"));

    if (data['class'] !== undefined) {
      jQuery('#js-overlay-content').addClass(data['class']);
    }
    jQuery('.js-overlay-close').on("click", function (e) {
      e.preventDefault();
      destroy();
    });
    // escape click bind on close button
    jQuery(document).keyup(function (e) {
      if (e.keyCode == 27) {
        destroy();
      }
    });
  };

  this.destroy = function() {
    if (jQuery('#js-overlay-backdrop').length === 1) {

      // Remove all js-active elements
      jQuery('.js-active').removeClass('js-active');

      // Remove the site header menu open if its there
      if (jQuery('#js-site-header').hasClass('js-menu-open')) {
        jQuery('#js-site-header').removeClass('js-menu-open');
      }

      jQuery('#js-overlay-backdrop, #js-overlay-content').remove();

      // Unlock scroll
      jQuery.scrollLock(false);
    }
  };

  return {
    init: init,
    backdrop : backdrop,
    destroy : destroy,
    create : create
  };
}();
