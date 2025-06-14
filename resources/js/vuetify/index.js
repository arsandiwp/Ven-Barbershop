// src/plugins/vuetify.js

import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
import light from './theme'

Vue.use(Vuetify);

const opts = {
    theme: {
        themes: { light },
    },
}

export default new Vuetify(opts);