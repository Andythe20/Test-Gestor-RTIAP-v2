import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
}

// Ensure axios sends cookies (session/XSRF) on XHR requests. Useful when the
// frontend is served from a different origin/port during development or when
// the browser requires credentials to be explicitly allowed.
axios.defaults.withCredentials = true;

// Configure axios to read the XSRF token cookie set by Laravel and send it in
// the X-XSRF-TOKEN header. Laravel sets the XSRF-TOKEN cookie automatically
// and will accept the X-XSRF-TOKEN header when validating the CSRF token.
axios.defaults.xsrfCookieName = "XSRF-TOKEN";
axios.defaults.xsrfHeaderName = "X-XSRF-TOKEN";

// If the Inertia client (or other libraries) use fetch instead of axios, fetch
// does not send cookies by default. Force fetch to include credentials so that
// session cookies are sent with XHR requests. This is safe for local
// development and helps avoid 419 CSRF errors caused by missing session/cookie.
if (typeof window !== "undefined" && window.fetch) {
    const _fetch = window.fetch.bind(window);
    window.fetch = (input, init = {}) => {
        init = init || {};
        if (!init.credentials) {
            init.credentials = "include";
        }
        return _fetch(input, init);
    };
}
