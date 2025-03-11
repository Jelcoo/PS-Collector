import './assets/main.css';
import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';

const app = createApp(App);

const i18n = createI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: {},
});

async function loadLocalesFromAPI() {
    try {
        // Replace with your actual API endpoint
        const response = await fetch('/api/languages');

        if (!response.ok) {
            throw new Error('Failed to fetch locales');
        }

        const data = await response.json();

        Object.keys(data).forEach((locale) => {
            i18n.global.setLocaleMessage(locale, data[locale]);
        });

        const userPreferredLocale = localStorage.getItem('language') || navigator.language.split('-')[0];
        if (Object.keys(data).includes(userPreferredLocale)) {
            i18n.global.locale = userPreferredLocale;
        }

        console.log('Locales loaded successfully');
    } catch (error) {
        console.error('Error loading locales:', error);
    }
}

app.use(i18n);
app.use(createPinia());
app.use(router);

loadLocalesFromAPI().finally(() => {
    app.mount('#app');
});
