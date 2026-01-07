import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";

import "./bootstrap";
import "../css/app.css";

import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/index.esm.js";

import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return (
            createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                // --- 2. REGISTRAR EL COMPONENTE ---
                .component("font-awesome-icon", FontAwesomeIcon)
                // ----------------------------------
                .mount(el)
        );
    },
    progress: {
        color: "#4B5563",
    },
});
