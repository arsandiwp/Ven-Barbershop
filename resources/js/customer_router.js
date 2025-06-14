import Vue from "vue";
import Router from "vue-router";

Vue.use(Router);

var baseUrl = "";
var nodeENV = "";
if (process.env.NODE_ENV == "production") {
    baseUrl = "https://www.zkdigimax.com";
    nodeENV = "prod";
} else if (process.env.NODE_ENV == "staging") {
    baseUrl = "https://sandbox-zk-digimax.solen.id";
    nodeENV = "staging";
} else if (process.env.NODE_ENV == "localhost") {
    baseUrl = "http://localhost:8000";
    nodeENV = "dev";
}
else {
    baseUrl = "http://127.0.0.1:8000";
    nodeENV = "dev";
}

const apiUrl = "/api/v1";
const api = baseUrl + apiUrl;

let routeArr = [];

routeArr.push({
    title: "Page Not Found",
    meta: { namePage: "Page Not Found" },
    name: "404",
    path: "*",
    component: {
        template:
            '<v-main><v-container style="height:100% !important;"><v-layout row class="text-xs-center" align-center justify-center><v-flex class="text-xs-center" style="height: 100vh;" id="card" d-flex xs12 mx-auto align-center justify-center>' +
            '<span class="title pt-5">404 | Page Not Found</span>' +
            "</v-flex></v-layout></v-container></v-main>",
    },
});

let custRoute = new Router({
    mode: "history",
    routes: routeArr,
});

export default custRoute;