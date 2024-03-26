import './bootstrap'
import '../css/app.css'
import 'primeicons/primeicons.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import BadgeDirective from 'primevue/badgedirective'
import PrimeVue from 'primevue/config'
import ConfirmationService from 'primevue/confirmationservice'
import Ripple from 'primevue/ripple'
import Tooltip from 'primevue/tooltip'
import { createApp, h } from 'vue'

import { ZiggyVue } from '../../vendor/tightenco/ziggy'
import Wind from '../css/presets/recipebox'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup ({ el, App, props, plugin }) {
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(PrimeVue, {
        unstyled: true,
        pt: Wind,
        ripple: true
      })
      .directive('ripple', Ripple)
      .directive('badge', BadgeDirective)
      .directive('tooltip', Tooltip)
      .use(ConfirmationService)
      .mount(el)
  },
  progress: {
    color: '#fb923c',
    delay: 200,
    includeCSS: true,
    showSpinner: true
  }
})
