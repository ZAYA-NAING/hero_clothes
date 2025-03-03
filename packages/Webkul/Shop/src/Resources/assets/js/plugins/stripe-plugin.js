import { StripePlugin } from "@vue-stripe/vue-stripe";

export default {
    install(app, options) {
        app.config.globalProperties.$stripe = StripePlugin;
    },
};
