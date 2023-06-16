/**
 * Init
 * Author: Taigo Ito (https://qwel.design/)
 * Location: Fukui, Japan
 * @package Qwel-Assets
 */

(() => {

  // Fader
  wp.blocks.registerBlockStyle('core/gallery', {
    name: 'fader-items',
    label: 'Fader Items'
  });

  // Slider
  // wp:group.slider で囲んで使用する
  wp.blocks.registerBlockStyle('core/gallery', {
    name: 'slider-items',
    label: 'Slider Items'
  });

})();
