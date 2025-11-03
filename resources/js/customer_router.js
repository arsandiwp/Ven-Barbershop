import Vue from "vue";
import Router from "vue-router";

import Register from "./components/customer/auth/register.vue";
import Login from "./components/customer/auth/login.vue";
import ForgotPassword from "./components/customer/auth/forgot-password.vue";
import ResetPassword from "./components/customer/auth/reset-password.vue";
import Home from "./components/customer/dashboard.vue";
import Reservation from "./components/customer/reservation-page.vue";
import Profile from "./components/customer/profile-page.vue";
import NewsDetail from "./components/customer/news-detail.vue";

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
} else {
  baseUrl = "http://127.0.0.1:8000";
  nodeENV = "dev";
}

const apiUrl = "/api/v1";
const api = baseUrl + apiUrl;

Vue.mixin({
  data() {
    return {
      API: api,
      nodeENV: nodeENV,
    };
  },
});

let routeArr = [];

routeArr.push({
  title: "Home",
  meta: { namePage: "Home" },
  icon: "user",
  path: "/home",
  redirect: "/",
});
routeArr.push({
  title: "Home",
  meta: { namePage: "Home" },
  icon: "user",
  path: "/dashboard",
  redirect: "/",
});

routeArr.push({
  title: "Home",
  meta: { namePage: "Home" },
  path: "/",
  component: Home,
  name: "home",
});

routeArr.push({
  title: "Register",
  name: "register",
  path: "/register",
  component: Register,
});

routeArr.push({
  title: "Login",
  name: "login",
  path: "/login",
  component: Login,
});

routeArr.push({
  title: "Forgot Password",
  name: "forgot-password",
  path: "/forgot-password",
  component: ForgotPassword,
});

routeArr.push({
  title: "Reset Password",
  name: "reset-password",
  path: "/reset-password/:token",
  component: ResetPassword,
});

routeArr.push({
  title: "Reservation",
  name: "reservation",
  path: "/reservation",
  component: Reservation,
});

routeArr.push({
  title: "Profile",
  name: "profile",
  path: "/profile",
  component: Profile,
  props: { api: api + "/profile" },
});

routeArr.push({
  title: "News Detail",
  icon: "user",
  path: "/news/:id",
  meta: { namePage: "News Detail" },
  component: NewsDetail,
  name: "news-detail",
});

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
