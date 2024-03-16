import axios from 'axios';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.axios = axios;
window.axios.defaults.withCredentials = true;
window.axios.defaults.timeout = 30000;