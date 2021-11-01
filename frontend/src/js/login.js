import $ from 'jquery';

// Recaptcha callback handler.
window.onSubmit = function (token) {
    $.post(
        scriptvars.base_path.replace(/\/$/, '') + '/ajax/recaptchaVerify.do',
        { token: token },
        function (resp) {
            if ('result' in resp && resp.result) {
                $('#loginform').trigger('submit');
                return;
            }
            $('#loginform').before('<div id="login_error">reCAPTCHA validation failed.</div>');
        }
    );
}

$(function () {
    // Setup recaptcha elements.
    if (scriptvars.recaptcha_v3_site_key) {
        $('#wp-submit')
            .addClass('g-recaptcha')
            .attr('data-sitekey', scriptvars.recaptcha_v3_site_key)
            .attr('data-callback', 'onSubmit')
            .attr('data-action', 'WordpressLoginSubmit');
    }
});