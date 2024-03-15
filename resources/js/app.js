import './bootstrap';
import '../css/app.css';
import 'primeicons/primeicons.css'
import Wind from '../css/presets/recipebox';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config';
import Ripple from 'primevue/ripple';
import BadgeDirective from 'primevue/badgedirective';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                unstyled: true,
                pt: Wind,
                ripple: true
            })
            .directive("ripple", Ripple)
            .directive("badge", BadgeDirective)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
