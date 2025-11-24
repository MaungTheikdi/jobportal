import '../css/app.css';
import './bootstrap';

// import 'datatables.net-buttons-dt';
// import 'datatables.net-fixedcolumns-dt';
// import 'datatables.net-fixedheader-dt';
// import 'datatables.net-responsive-dt';
// import 'datatables.net-searchbuilder-dt';
// import 'datatables.net-searchpanes-dt';


import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
