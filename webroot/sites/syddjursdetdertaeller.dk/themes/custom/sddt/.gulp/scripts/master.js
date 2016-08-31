/*!

 Syddjurs - det der tæller

 Proud authors:      www.novicell.dk
 People behind:      Mikkel Mandal, @mikkelmandal

 !*/

var sddt = sddt || {};

// Document ready
jQuery(function () {
// sddt.polyfills.init();
  sddt.navigation.init();
  sddt.overlay.init();
// sddt.overlay.init();
// sddt.animations.init();
// sddt.tooltip.init();

  svg4everybody(); // Fix SVG spritemap in IE/Edge
});

// Window resize
jQuery(window).resize(function () {

}).resize();

/**
 * @file
 * Javascript for the node content editing form.
 */
(function ($, drupalSettings) {

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
      // var context = $(context);

      // $('body').on('click','.notification__close',function(){
      //   $(this).closest('.notification').fadeOut(2000).slideUp(2000,function(){
      //     $(this).remove();
      //   });
      // });
    }
  };

  Drupal.behaviors.googleMaps = {
    attach: function (context) {
      if(typeof google == 'undefined'){
        return false;
      }

      var bounds = new google.maps.LatLngBounds();
      var infowindow = new google.maps.InfoWindow();

      $.each(drupalSettings.google_maps.data,function(){
        var data = this;

        $('.google-maps__map-container__id-' + data.map_id).each(function(){
          if(!$(this).data('initialized')){
            $(this).data('initialized',true);

            var map = new google.maps.Map(this, {
              center: {lat: 56.24, lng: 10.58},
              // zoom: 8,
              scrollwheel: false
            });

            $.each(data.pins,function(){
              var pin = this;

              var marker = new google.maps.Marker({
                position: new google.maps.LatLng(pin.latitude, pin.longitude),
                title: pin.name,
                map: map
              });

              bounds.extend(marker.position);

              var infoWindowContent = '<h3>' + pin.name + '</h3>';
              if(pin.image_path){
                infoWindowContent += '<div class="google-maps__info-window-image" style="background-image: url(' + pin.image_path + ');"></div>';
              }
              if(pin.description) {
                infoWindowContent += pin.description;
              }

              google.maps.event.addListener(marker, 'click', (function (marker, pin) {
                return function () {
                  infowindow.setContent(infoWindowContent);
                  infowindow.open(map, marker);
                }
              })(marker, pin));
            });

            // map.fitBounds(bounds);

            var listener = google.maps.event.addListener(map, "idle", function () {
              map.setZoom(10);
              google.maps.event.removeListener(listener);
            });
          }
        });
      });

      // jQuery(function () {
      //   // Create a map object and specify the DOM element for display.
      //   var map = new google.maps.Map($('.google-maps__id-{{ map_id }}'), {
      //     center: {lat: -34.397, lng: 150.644},
      //     scrollwheel: false,
      //     zoom: 8
      //   });
      //
      //   {% for pin in pins %}
      //   var marker = new google.maps.Marker({
      //     map: map,
      //     position: {lat: {{ pin.latitude }}, lng: {{ pin.longitude }}},
      //   title: '{{ pin.name }}'
      // });
      //   {% endfor %}
      // });
    }
  };



})(jQuery, drupalSettings);
