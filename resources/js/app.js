import "./bootstrap";

import Vue from "vue";
import vuetify from "./vuetify";
import router from "./router.js";

import HeaderBar from "./components/header-bar";

import ErrorDialog from "./components/error-dialog";
import Loading from "./components/loading-dialog";
import Confirm from "./components/confirm-dialog";
import Alert from "./components/alert-dialog";

import LoginPage from "./components/auth/login";
import Cloacking from "./components/cloacking";
import RegisterPage from "./components/auth/register";
import ForgotPasswordPage from "./components/auth/forgot-password";
import ResetPasswordPage from "./components/auth/reset-password";

Vue.component("HeaderBar", HeaderBar);

Vue.mixin({
    data() {
        return {};
    },
    methods: {
        getInitial(fn = "") {
            let rgx = new RegExp(/(\p{L}{1})\p{L}+/, "gu");

            let initials = [...fn.matchAll(rgx)] || [];

            initials = (
                (initials.shift()?.[1] || "") + (initials.shift()?.[1] || "")
            ).toUpperCase();

            return initials;
        },

        titleCase(kebab) {
            let interim = kebab.replace(/-/g, " ");
            return interim
                .toLowerCase()
                .replace(/\b\w/g, (s) => s.toUpperCase());
        },

        LOADING(dialog) {
            return this.$root.toggleLoading(dialog);
        },

        confirm(title, message, options) {
            return app.confirm(title, message, options);
        },
        alert(title, message, options) {
            return app.alert(title, message, options);
        },

        showErr(res, title = "") {
            return app.showErr(res, title);
        },

        devLog(item) {
            if (process.env.NODE_ENV == "development") {
                console.log(item);
            }
        },

        /**
         * SNACKBAR options
         * @param text               - SNACKBAR text
         * @param options.buttonText - Button text
         * @param options.closeable  - Whether SNACKBAR is closeable
         * @param options.onClick    - Button handler
         * @param options.timeout    - SNACKBAR timeout
         * @param options.type       - SNACKBAR type
         */
        addNotification(text, options = {}) {
            // Hide previous notification
            // this.devLog("SNACKBAR Notif: "+ app.snackbar.notification);
            app.snackbar.notification = false;

            // Display new notification (but give time for previous to disappear)
            setTimeout(() => {
                app.snackbar.notificationText = text;
                app.snackbar.notification = true;
                // this.devLog("SNACKBAR Notif 3: "+ app.snackbar.notification);
                // Customization
                app.snackbar.buttonColor = options.type ? "black" : "primary";
                app.snackbar.buttonText = options.buttonText
                    ? options.buttonText
                    : "";
                app.snackbar.hasButton = options.buttonText && options.onClick;
                app.snackbar.onClick = options.onClick
                    ? options.onClick
                    : function () {};
                app.snackbar.timeout = options.timeout ? options.timeout : 2000;
                app.snackbar.type = options.type ? options.type : "secondary";

                // Closeable SNACKBARs override other properties
                if (options.closeable) {
                    app.snackbar.timeout = 5000;
                    app.snackbar.buttonText = "close";
                    app.snackbar.buttonColor = "success";
                    app.snackbar.hasButton = true;
                    app.snackbar.onClick = this.closeNotification;
                }
            }, 100);
            // this.devLog("SNACKBAR Notif 2: "+ app.snackbar.notification);
        },

        /**
         * Close the SNACKBAR and execute callback
         */
        clickHandler(callback) {
            this.closeNotification();
            callback();
        },

        /**
         * Close the SNACKBAR
         */
        closeNotification() {
            app.snackbar.notification = false;
        },

        simpleSnackbar(text, type) {
            this.addNotification(text, { type: type });
        },

        closeableSnackbar(text) {
            this.addNotification(text, { closeable: true });
        },

        complexSnackbar(text, olddata, api, fx) {
            const options = {
                buttonText: "Restore",
                onClick: () => {
                    axios
                        .put(api, olddata, {
                            headers: { Authorization: localStorage.token },
                        })
                        .then((response) => {
                            // this.devLog(JSON.stringify(response));
                            if (response.status == 201 && !!response.data.id) {
                                this.closeableSnackbar(
                                    "Deleted data has been successfully recovered!!"
                                );
                                fx();
                            } else {
                                this.simpleSnackbar(
                                    "Sorry, we've failed to undo your previous action!",
                                    "danger"
                                );
                                this.showErr(response);
                            }
                        })
                        .catch((err) => {
                            if (!!err.response) {
                                this.showErr(err.response, "Failed");
                            } else {
                                this.showErr({
                                    status: "Code Error",
                                    statusText: err,
                                });
                            }
                        });
                },
                timeout: 5000,
                type: "success",
            };
            this.addNotification(text, options);
        },

        updateadminLogin(id) {
            this.devLog("updateadminLogin with TOKEN: " + localStorage.token);
            axios
                .get(this.API + "/user/" + id, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.devLog(
                        "Hasil Check User: " + JSON.stringify(response.data)
                    );
                    if (response.status == 200) {
                        if (
                            !!response.data.data &&
                            response.data.data.length > 0
                        ) {
                            app.adminLogin = response.data.data[0];
                            localStorage.adminLogin = JSON.stringify(
                                app.adminLogin
                            );
                            app.roles = app.adminLogin.roles;
                            localStorage.is_admin =
                                app.adminLogin.akses == "Admin" ||
                                app.adminLogin.akses == "Super Admin"
                                    ? true
                                    : false;
                        } else {
                            app.adminLogin = false;
                        }
                    }
                })
                .catch((err) => {
                    app.userLogin = false;
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        getRoles() {
            this.devLog("getRoles with TOKEN: " + localStorage.token);
            return axios.get(this.API + "/roles", {
                headers: { Authorization: localStorage.token },
            });
        },
    },
});

router.beforeEach((to, from, next) => {
    const adminLogin = JSON.parse(localStorage.getItem("adminLogin"));
    const token = localStorage.getItem("token");
    const publicName = ["register", "login", "forget", "reset"];

    if (!to.matched.length) {
        next({ name: "404" });
    } else {
        next();
    }
    
    if(to.matched.some((record) => record.meta.requiresAuth)){
        if (adminLogin == null && token == null) {
            next({ path: "/login" });
            return;
        }

        if (publicName.includes(to.name)) {            
            next();
            return;
        } else {
            next({ path: "/login" });
            return;
        }
    }else {
        if (publicName.includes(to.name)) {
            if (adminLogin && token) {
                next({ path: "/dashboard" });
                return;
            }
        }

        if (!adminLogin && !token) {
            next()
        }else if(adminLogin && token){
            if(to.path == '/'){
                next({ path: "/dashboard" });
                return;
            }
        }

        next();
    }
});

const app = new Vue({
    vuetify,
    router,
    el: "#app",

    components: {
        ErrorDialog,
        Confirm,
        Alert,
        Loading,
        Cloacking,

        RegisterPage,
        LoginPage,
        ForgotPasswordPage,
        ResetPasswordPage,
    },

    created() {
        this.devLog("Admin App Component created...");
        this.updateSnackBar();

        const activeDate = localStorage.getItem("active_until");
        if (activeDate) {
            const activeUntilDate = activeDate.split("/")[0];
            const activeUntilMonth = activeDate.split("/")[1];
            const activeUntilYear = activeDate.split("/")[2];
            const dateNow = new Date();
            if (
                (dateNow.getDate() > activeUntilDate &&
                    dateNow.getMonth() + 1 == activeUntilMonth) ||
                (dateNow.getMonth() + 1 > activeUntilMonth &&
                    dateNow.getFullYear() == activeUntilYear) ||
                dateNow.getFullYear() > activeUntilYear
            ) {
                axios
                    .get(this.API + "/logout", {
                        headers: {
                            Authorization: localStorage.token,
                        },
                    })
                    .then((response) => {
                        localStorage.clear();

                        this.$router.replace({ name: "login" });
                        this.userLogin = false;
                        this.is_register = false;
                        this.refresh();
                    });
            } else {
                this.initData();
            }
        } else {
            this.initData();
        }

        if (this.$route.name == "register") {
            this.is_register = true;
        }

        if (this.$route.name == "forgot-password") {
            this.is_forgot = true;
        }

        if (this.$route.name == "reset-password") {
            this.is_reset = true;
        }
    },

    computed: {
        currentRouteName() {
            return this.$router.name;
        },
    },

    data() {
        return {
            app_ready: false,
            drawer_logo: "",
            err_dialog: { show: false, text: "", status: 0 },
            loading_dialog: false,
            router: router,
            snackbar: null,
            is_register: false,
            is_forgot: false,
            is_reset: false,
            adminLogin: false,
            pageReady: false,
            login: {
                api: "",
            },
            items: [],
            navDrawerKey: 0,
            listKey: 0,
            drawer: false,
            cloacking_dialog: false,
        };
    },

    methods: {
        showErr(res, title) {
            if (!res.config) {
                res.config = {
                    url: "-",
                    method: "-",
                };
            }

            switch (title) {
                case "Failed":
                    this.err_dialog = {
                        show: true,
                        title: title,
                        subtitle: res.data.message,
                        // message: "("+res.config.url+")",
                        customClose: !!res.customClose ? res.customClose : null,
                    };
                    break;
                case "Warning":
                    this.err_dialog = {
                        show: true,
                        title: title,
                        type: "warning",
                        subtitle: res.data.message,
                        // message: "("+res.config.url+")",
                        customClose: !!res.customClose ? res.customClose : null,
                    };
                    break;
                case "Error":
                    var sub = "";
                    var msg = "";
                    var resdat = null;
                    if (!!res.response && !!res.response.data) {
                        resdat = res.response.data;
                        if (!!resdat.exception) {
                            sub = resdat.exception;
                        }
                        if (!!resdat.file) {
                            //sub += " at "+ resdat.file;
                        }

                        if (!!resdat.message) {
                            msg = resdat.message;
                        }
                    } else {
                        msg = res;
                        sub = "Code Error";
                    }

                    this.err_dialog = {
                        show: true,
                        title: title,
                        subtitle: sub,
                        message: msg,
                        customClose: !!res.customClose ? res.customClose : null,
                    };
                    break;

                default:
                    this.err_dialog = {
                        show: true,
                        title: title,
                        subtitle: res.status,
                        message:
                            res.statusText +
                            "\r\n(" +
                            res.config.method.toUpperCase() +
                            ": " +
                            res.config.url +
                            ")",
                        customClose: !!res.customClose ? res.customClose : null,
                    };
                    break;
            }
        },

        toggleLoading(dialog) {
            this.loading_dialog = dialog;
        },

        updateSnackBar() {
            this.snackbar = {
                buttonColor: "",
                buttonText: "",
                hasButton: true,
                notificationText: "",
                notification: false,
                timeout: 5000,
                type: null,
                onClick: function () {},
            };
        },

        getRegex(group) {
            let comS = new RegExp(`(?:\\b|')(${group})(?:\\b|')`);
            return comS;
        },

        updateItems() {
            this.devLog("Item updated!");
            this.items = [
                // Dashboard
                {
                    title: "Dashboard",
                    icon: "mdi-view-dashboard-outline",
                    name: "dashboard",
                    link: "/dashboard",
                    class: "pl-0",
                    show: true,
                    click: function () {},
                },

                //Member  ?? Use regex for group value, refer to child link ??
                {
                    title: "Member",
                    icon: "mdi-account-group-outline",
                    class: "menu",
                    hasChild: true,
                    group: "users|roles|privileges",
                    show: true,
                    children: [
                        {
                            title: "Users",
                            expandItem: true,
                            name: "users",
                            link: "/users",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                        {
                            title: "Roles",
                            expandItem: true,
                            name: "roles",
                            link: "/roles",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                        {
                            title: "Privileges",
                            expandItem: true,
                            name: "privileges",
                            link: "/privileges",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                    ],
                },

                {
                    title: "Artificial Intelligence",
                    icon: "mdi-brain",
                    name: "ai",
                    link: "/ai",
                    class: "pl-0",
                    show: true,
                    click: function () {},
                },

                {
                    title: "Master Data",
                    icon: "mdi-database",
                    class: "menu",
                    hasChild: true,
                    group: "template-web|qna",
                    show: true,
                    children: [
                        {
                            title: "Template Web",
                            expandItem: true,
                            name: "template-web",
                            link: "/template-web",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                        {
                            title: "Qna",
                            expandItem: true,
                            name: "qna",
                            link: "/qna",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                    ],
                },

                {
                    title: "Setting",
                    icon: "mdi-cog-outline",
                    class: "menu",
                    hasChild: true,
                    group: "ai-setting",
                    show: true,
                    children: [
                        {
                            title: "AI Setting",
                            expandItem: true,
                            name: "ai-setting",
                            link: "/ai-setting",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                    ],
                },

                {
                    title: "Trip Advisor",
                    icon: "mdi-plane-train",
                    class: "menu",
                    hasChild: true,
                    group: "hotels|restaurants|attractions",
                    show: true,
                    children: [
                        {
                            title: "Hotels",
                            expandItem: true,
                            name: "hotels",
                            link: "/hotels",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                        {
                            title: "Restaurants",
                            expandItem: true,
                            name: "restaurants",
                            link: "/restaurants",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                        {
                            title: "Attractions",
                            expandItem: true,
                            name: "attractions",
                            link: "/attractions",
                            class: "pl-0",
                            show: true,
                            click: function () {},
                        },
                    ],
                },
            ];
        },

        initData() {
            this.devLog("checking login data");
            this.login.api = this.API + "/login";
            if (!!localStorage.adminLogin) {
                this.adminLogin = JSON.parse(localStorage.adminLogin);
                this.devLog(localStorage.adminLogin);
                this.updateadminLogin(this.adminLogin.id);
                this.devLog("Logged, continuing...");

                this.updateItems();
            } else {
                this.adminLogin = false;
                if (this.$route.name == "register") {
                    this.is_register = true;
                } else {
                    this.is_register = false;
                }
                this.devLog("Not Logged, redirecting...");
            }
        },

        refresh() {
            this.devLog("refresh() called. Initiate refresh...");
            this.initData();
        },

        logout() {
            this.drawer = false;
            axios
                .get(this.API + "/logout", {
                    headers: {
                        Authorization: localStorage.token,
                    },
                })
                .then((response) => {
                    localStorage.clear();

                    this.$router.replace({ name: "login" });
                    this.refresh();
                });
        },
    },

    mounted() {
        this.pageReady = true;
        this.confirm = this.$refs.confirm.open;
        this.alert = this.$refs.alert.open;
    },

    watch: {
        "localStorage.adminLogin": function (user) {
            this.adminLogin = localStorage.adminLogin;
            this.initData();
        },
        $route(to, from) {
            if (to.name == "register") {
                this.is_register = true;
            } else if (to.name === "forgot-password") {
                this.is_forgot = true;
            } else if (to.name === "reset-password") {
                this.is_reset = true;
            } else {
                this.is_register = false;
                this.is_forgot = false;
            }
        },
    },
});

export default app;