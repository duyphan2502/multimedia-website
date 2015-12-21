$(window).load(function () {
    "use strict";
});

(function ($) {
    "use strict";

    function inputPlaceholders() {
        $('input, textarea').placeholder();
    }

    function init() {
        inputPlaceholders();
        Utility.detectIE();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    }

    init();

})(jQuery);