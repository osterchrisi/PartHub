export function initializeSignupView() {
    if (typeof grecaptcha === 'undefined') {
        grecaptcha = {};
    }

    grecaptcha.ready = function (cb) {
        if (typeof grecaptcha === 'undefined') {
            const c = '___grecaptcha_cfg';
            window[c] = window[c] || {};
            (window[c]['fns'] = window[c]['fns'] || []).push(cb);
        } else {
            cb();
        }
    }
};

// This function defined as callback on the recaptcha
export function processChallenge() {
    document.getElementById('signupBtn').disabled = false; // Enable Signup Button after challenge is completed
    response = grecaptcha.getResponse(); // Get challenge response
    document.getElementById('recaptchaResponse').value = response; // Send challenge response with the form
}

window.processChallenge = processChallenge;
