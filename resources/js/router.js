import Vue from "vue";
import Router from "vue-router";

import Home from "./components/home/dashboard";

import Register from "./components/auth/register";
import Login from "./components/auth/login";
import ForgotPassword from "./components/auth/forgot-password";
import ResetPassword from "./components/auth/reset-password";

import UserList from "./components/users/user-list";
import User from "./components/users/user-cru";
import Profile from "./components/users/profile";

import UserImport from "./components/import-file.vue";

import RolesList from "./components/roles/role-list";
import Role from "./components/roles/role-cru";

import QnaList from "./components/qna/qna-list";
import Qna from "./components/qna/qna-cru";

import TemplateWebList from "./components/template-web/template-web-list";
import TemplateWeb from "./components/template-web/template-web-cru";

import PrivilegesList from "./components/privileges/privilege-list";

import ArtificialIntelligence from "./components/artificial-intelligence/artificial-intelligence";

import ModelList from "./components/model/model-list";

import Hotels from "./components/trip-advisor/hotels";
import Restaurants from "./components/trip-advisor/restaurants";
import Attractions from "./components/trip-advisor/attractions";

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
    icon: "user",
    path: "/dashboard",
    redirect: "/",
});

routeArr.push({
    title: "Home",
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
    title: "Add User",
    path: "/users/add",
    name: "add-user",
    component: User,
    props: { api: api + "/user", editable: true },
});
routeArr.push({
    title: "Edit User",
    path: "/users/edit/:id",
    name: "edit-user",
    component: User,
    props: { api: api + "/user", editable: true },
});
routeArr.push({
    title: "Detail User",
    path: "/users/:id",
    name: "detil-user",
    component: User,
    props: { api: api + "/user", editable: false },
});
routeArr.push({
    title: "User",
    path: "/users",
    name: "users",
    component: UserList,
    props: { api: api + "/user/paginate?", apiCUD: api + "/user" },
});

routeArr.push({
    title: "Profile",
    path: "/profile",
    component: Profile,
    props: { api: api + "/profile" },
});

routeArr.push({
    title: "User Import",
    path: "/import-users",
    component: UserImport,
    // props: { api: api + "/profile" },
});

routeArr.push({
    title: "Add Roles",
    path: "/roles/add",
    name: "add-role",
    component: Role,
});
routeArr.push({
    title: "Edit Roles",
    path: "/roles/edit/:id",
    name: "edit-role",
    component: Role,
});
routeArr.push({
    title: "Roles",
    path: "/roles",
    name: "roles",
    component: RolesList,
    props: { api: api + "/role/paginate?", apiCUD: api + "/role" },
});

routeArr.push({
    title: "Privileges",
    path: "/privileges",
    name: "privileges",
    component: PrivilegesList,
    props: { api: api + "/privilege/paginate?" },
});

routeArr.push({
    title: "Artificial Intelligence",
    name: "ai",
    path: "/ai",
    component: ArtificialIntelligence,
});

routeArr.push({
    title: "Model AI",
    path: "/ai-setting",
    name: "ai-setting",
    component: ModelList,
    props: { api: api + "/model-settings", apiCUD: api + "/model-settings" },
});

routeArr.push({
    title: "Add Qna",
    path: "/qna/add",
    name: "add-qna",
    component: Qna,
});
routeArr.push({
    title: "Edit Qna",
    path: "/qna/edit/:id",
    name: "edit-qna",
    component: Qna,
});
routeArr.push({
    title: "Qna",
    path: "/qna",
    name: "qna",
    component: QnaList,
    props: { api: api + "/qna/paginate?", apiCUD: api + "/qna" },
});

routeArr.push({
    title: "Add Template Web",
    path: "/template-web/add",
    name: "add-template-web",
    component: TemplateWeb,
});
routeArr.push({
    title: "Edit Template Web",
    path: "/template-web/edit/:id",
    name: "edit-template-web",
    component: TemplateWeb,
});
routeArr.push({
    title: "Template Web",
    path: "/template-web",
    name: "template-web",
    component: TemplateWebList,
    props: {
        api: api + "/template-web/paginate?",
        apiCUD: api + "/template-web",
    },
});

routeArr.push({
    title: "Hotels",
    path: "/hotels",
    name: "hotels",
    component: Hotels,
});

routeArr.push({
    title: "Restaurants",
    path: "/restaurants",
    name: "restaurants",
    component: Restaurants,
});

routeArr.push({
    title: "Attractions",
    path: "/attractions",
    name: "attractions",
    component: Attractions,
});

routeArr.push({
    title: "Page Not Found",
    name: "404",
    path: "*",
    component: {
        template:
            '<v-main><v-container style="height:100% !important;"><v-layout row class="text-xs-center" align-center justify-center><v-flex class="text-xs-center" style="height: 100vh;" id="card" d-flex xs12 mx-auto align-center justify-center>' +
            '<span class="title pt-5">404 | Page Not Found</span>' +
            "</v-flex></v-layout></v-container></v-main>",
    },
});

let admRoute = new Router({
    // mode: "history",
    routes: routeArr,
});

export default admRoute;
