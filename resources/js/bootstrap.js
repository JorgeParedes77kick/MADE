window._ = require('lodash');

try {
  require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Swal = require('sweetalert2');

import dayjs from 'dayjs';
import ES from 'dayjs/locale/es';
import customParseFormat from 'dayjs/plugin/customParseFormat';
import isoWeek from 'dayjs/plugin/isoWeek';
import utc from 'dayjs/plugin/utc';
import weekOfYear from 'dayjs/plugin/weekOfYear';
dayjs.locale(ES);

dayjs.extend(weekOfYear);
dayjs.extend(isoWeek);
dayjs.extend(customParseFormat);
dayjs.extend(utc);
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

// import { useTheme } from 'vuetify';

// function configureSwalWithVuetify() {
//   const theme = useTheme();
//   const currentTheme = theme.current.value;

//   return window.Swal.mixin({
//     customClass: {
//       popup: 'v-dialog',
//       header: 'v-toolbar__title',
//       title: 'text-h5',
//       confirmButton: 'v-btn v-btn--is-elevated v-btn--elevated',
//       cancelButton: 'v-btn v-btn--text',
//     },
//     buttonsStyling: false,
//     background: currentTheme.dark
//       ? currentTheme.colors['background-dark']
//       : currentTheme.colors['background'],
//     color: currentTheme.dark ? currentTheme.colors['font-dark'] : currentTheme.colors['font'],
//     confirmButtonColor: currentTheme.colors['primary'],
//     cancelButtonColor: currentTheme.colors['secondary'],
//   });
// }

// window.SwalVuetify = configureSwalWithVuetify();
