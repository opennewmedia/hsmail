// hsmail backend module js
require(["jquery"], function($) {
    $(document).ready(function() {
        $('.js-fetch-forms').on('click', function(e) {
            $(this).addClass("disabled").text('Loading...');
        });
    });
 });