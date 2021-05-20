import $ from 'jquery';

$(function() {

    // Run hello world ajax on homepage.
    if (window.location.pathname == '/') {
        $.post(scriptvars.ajax_url + '/helloWorld.do', function(d) {
            $('#hello-world').html(d.html).addClass('bg-info text-dark bg-gradient');
        });
    }

});