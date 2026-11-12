/**
 * Single entry point for jQuery in the admin area. Plugins are attached to this instance here,
 * so every admin script imports $ from this file to be sure select2 / jquery-modal are registered
 * before it runs, regardless of the order the @vite tags end up in the page head.
 */
import $ from 'jquery';
import select2 from 'select2';
import 'jquery-modal';

// select2 is shipped as a CommonJS factory that has to be called to register $.fn.select2
select2(window, $);

/**
 * select2 4.1.0 passes AjaxAdapter.prototype._normalizeItem unbound to Array.prototype.map, and the
 * method reads this.container. In a classic script `this` falls back to window, but Vite bundles
 * everything as ES modules (always strict), so `this` is undefined and every ajax select2 throws
 * inside jQuery's Deferred and stays stuck on "Searching…". Falling back to an empty object restores
 * the non-strict behaviour.
 */
const AjaxAdapter = $.fn.select2.amd.require('select2/data/ajax');
const normalizeItem = AjaxAdapter.prototype._normalizeItem;

AjaxAdapter.prototype._normalizeItem = function (item) {
    return normalizeItem.call(this ?? {}, item);
};

export default $;
