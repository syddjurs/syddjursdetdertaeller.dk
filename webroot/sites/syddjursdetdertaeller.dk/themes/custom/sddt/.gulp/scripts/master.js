/*!

 Syddjurs - det der tæller

 Proud authors:      www.novicell.dk
 People behind:      Mikkel Mandal, @mikkelmandal

 !*/

var sddt = sddt || {};

// Document ready
jQuery(function () {
// sddt.polyfills.init();
// sddt.navigation.init();
// sddt.overlay.init();
// sddt.animations.init();
// sddt.tooltip.init();

  svg4everybody(); // Fix SVG spritemap in IE/Edge
});

// Window resize
jQuery(window).resize(function(){

}).resize();

/**
 * @file
 * Javascript for the node content editing form.
 */
 (function ($) {

    'use strict';

    /**
     * Behaviors for setting summaries on content type form.
     *
     * @type {Drupal~behavior}
     *
     * @prop {Drupal~behaviorAttach} attach
     *   Attaches summary behaviors on content type edit forms.
     */
    Drupal.behaviors.sddt = {
      attach: function (context) {
        var context = $(context);

        // $('body').on('click','.notification__close',function(){
        //   $(this).closest('.notification').fadeOut(2000).slideUp(2000,function(){
        //     $(this).remove();
        //   });
        // });
      }
    };

 })(jQuery);
