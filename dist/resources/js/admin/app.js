import $ from 'jquery';
import select2 from 'select2';
import 'jquery-modal';

// select2 is shipped as a CommonJS factory that has to be called to register $.fn.select2
select2(window, $);

export default $;
