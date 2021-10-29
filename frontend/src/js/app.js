import $ from 'jquery';

$(function() {

    // Run hello world ajax on homepage.
    if (window.location.pathname == '/' || window.location.pathname == '/hello.do') {
        $.get(scriptvars.ajax_url + '/helloWorld.do', function(d) {
            $('#hello-world').html(d.html).addClass('bg-info text-dark bg-gradient');
        });
    }

});