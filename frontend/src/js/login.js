// Using the built-in jQuery that's loaded on the login page.
(function ($) {

    const loginFormElem = $('#loginform');
    const failedHandler = (msg) => {
        $('#login_error').remove();
        loginFormElem.before('<div id="login_error">' + msg + '</div>');
    };

    loginFormElem.on('submit', function (e) {

        e.preventDefault();

        if (typeof scriptvars == 'undefined' || scriptvars.recaptcha_v3_site_key == '' || scriptvars.nonce == '') {
            failedHandler('reCAPTCHA validation failed.');
            console.log('reCAPTCHA is not configured correctly.')
            return;
        }

        grecaptcha.ready(function () {

            grecaptcha.execute(scriptvars.recaptcha_v3_site_key, { action: 'submit' }).then(function (token) {

                $.post(
                    scriptvars.base_path.replace(/\/$/, '') + '/ajax/recaptchaVerify.do',
                    {
                        token: token,
                        nonce: scriptvars.nonce
                    },
                    function (resp) {
                        if ('result' in resp && resp.result) {
                            e.currentTarget.submit();
                            return;
                        }
                        failedHandler('reCAPTCHA validation failed.');
                    }
                ).fail(function (xhr) {
                    failedHandler(xhr.responseJSON.error)
                });

            });

        });

    });

})(jQuery);