/**
 * This will track all the images and fonts for publishing.
 */
import.meta.glob(["../images/**", "../fonts/**"]);

/**
 * Main vue bundler.
 */
import { createApp } from "vue/dist/vue.esm-bundler";

/**
 * Main root application registry.
 */
window.app = createApp({
    data() {
        return {};
    },

    mounted() {
        this.lazyImages();
    },

    methods: {
        onSubmit() {},

        onInvalidSubmit() {},

        lazyImages() {
            var lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));

            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;

                        lazyImage.src = lazyImage.dataset.src;

                        lazyImage.classList.remove('lazy');

                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        },
    },
});

/**
 * Global plugins registration.
 */
import Axios from "./plugins/axios";
import Emitter from "./plugins/emitter";
import Shop from "./plugins/shop";
import VeeValidate from "./plugins/vee-validate";
import Flatpickr from "./plugins/flatpickr";
import StripePlugin from "./plugins/stripe-plugin";

[
    Axios,
    Emitter,
    Shop,
    VeeValidate,
    Flatpickr,
    StripePlugin
].forEach((plugin, index) => {
    index == 5 ?  app.use(StripePlugin, {
        pk: 'pk_test_51PDRKRP0eVufA6Xrx3ou3mWQEjSTyXf6lYOQe4VvIdqYTNEQYoLJB4oMIsMdeOJ5SyRwDhtbk4dZXaqPgLoV3AI700xtVlbkI4',
        testMode: true, // Boolean. To override the insecure host warning
        stripeAccount: 'acct_1PDRKRP0eVufA6Xr',
        apiVersion: '2024-12-18.acacia',
        locale: 'en',
    }) : app.use(plugin);
});

export default app;
