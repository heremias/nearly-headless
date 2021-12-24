( function($, Drupal, drupalSettings) {

  Drupal.behaviors.practiceListing = {
    attach: function(context, settings) {
      if(drupalSettings['vue-settings'] === undefined){
        console.error('Vue paragraph not set correctly\
          in drupalSettings. Check .theme preprocess_paragraph\
          function to make sure this is setting drupalSettings\
          properly.')
      }
      var paragraphs = Object.keys(drupalSettings['vue-components'])
      paragraphs.forEach( function(p) {
        var component = drupalSettings['vue-components'][p]
        console.log('setting up data', component)
        window[p] = new Vue({
          el: '#' + component.id,
          data: function(){ return component.data }
        })
      })
    }
  }
})($, Drupal, drupalSettings)

//Object.assign polyfill for IE 11
if (typeof Object.assign !== 'function') {
  // Must be writable: true, enumerable: false, configurable: true
  Object.defineProperty(Object, "assign", {
    value: function assign(target, varArgs) { // .length of function is 2
      'use strict';
      if (target === null || target === undefined) {
        throw new TypeError('Cannot convert undefined or null to object');
      }

      var to = Object(target);

      for (var index = 1; index < arguments.length; index++) {
        var nextSource = arguments[index];

        if (nextSource !== null && nextSource !== undefined) {
          for (var nextKey in nextSource) {
            // Avoid bugs when hasOwnProperty is shadowed
            if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
              to[nextKey] = nextSource[nextKey];
            }
          }
        }
      }
      return to;
    },
    writable: true,
    configurable: true
  });
}
