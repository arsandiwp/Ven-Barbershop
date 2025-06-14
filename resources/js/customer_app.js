import "./bootstrap";

import Vue from "vue";
import vuetify from "./vuetify";
window.Vue = require("vue");

import ErrorDialog from "./components/error-dialog";
import Loading from "./components/loading-dialog";
import Cloacking from "./components/cloacking";
import Confirm from "./components/confirm-dialog";
import Snackbar from "./components/snackbar";
import Alert from "./components/alert-dialog";
import router from "./customer_router.js";

Vue.mixin({
    data() {
        return {
            MENU: {
                PATH: null,
                INDEX: null,
            },
            WINDOW: {
                WIDTH: 0,
                HEIGHT: 0,
            },
            AddDialog: false,
        };
    },
    methods: {
        /**
         * @param {String} text The string
         */
        getTitle: function () {
            return app.appTitle;
        },

        setTitle: function (v) {
            app.appTitle = v;
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
        handleResize() {
            this.WINDOW.WIDTH = window.innerWidth;
            this.WINDOW.HEIGHT = window.innerHeight;
            this.devLog(
                `WINDOW: { WIDTH: ${this.WINDOW.WIDTH} , HEIGHT: ${this.WINDOW.HEIGHT} }`
            );
        },

        togglePrint() {
            app.triggerPrint();
        },

        getRoles() {
            this.devLog("USER TOKEN: " + localStorage.token);
            return axios.get(this.API + "/role/get-all", {
                headers: { Authorization: localStorage.token },
            });
        },

        updateUserLogin(id) {
            this.devLog("USER TOKEN: " + localStorage.token);
            axios
                .get(this.API + "/verify-token/", {
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
                            app.userLogin = response.data.data[0];
                            localStorage.userLogin = JSON.stringify(
                                app.userLogin
                            );
                            // this.devLog("USER LOGIN" + JSON.stringify(app.userLogin));
                            app.roles = app.userLogin.roles;
                            localStorage.is_admin =
                            (app.userLogin.akses == "Admin" || app.userLogin.akses == "Super Admin") ? true : false;
                            app.updateItems();
                        } else {
                            app.userLogin = false;
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
                    app.snackbar.buttonText = "Tutup";
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
                buttonText: "Pulihkan",
                onClick: () => {
                    axios
                        .put(api, olddata, {
                            headers: { Authorization: localStorage.token },
                        })
                        .then((response) => {
                            // this.devLog(JSON.stringify(response));
                            if (response.status == 201 && !!response.data.id) {
                                this.closeableSnackbar(
                                    "Data terhapus telah berhasil dipulihkan!!"
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

        refreshPath() {
            this.devLog("Refresh Path called. Initiate refresh...");
            app.refreshKey += 1;
        },
    },
});

router.beforeEach((to, from, next) => {
    if (!to.matched.length) {
        next({ name: "404" });
    } else {
        next();
    }
});

const app = new Vue({
    vuetify,
    el: "#customer_app",

    components: {
        ErrorDialog,
        Confirm,
        Alert,
        Loading,
        Snackbar,
        Cloacking
    },

    beforeCreate() {
        // console.log("Creating App component...");
    },

    created() {
        this.login.submitOk = this.refresh;
        this.devLog("Customer App Component created...");
        this.updateSnackBar();
        // this.initData();
    },

    computed: {},
    destroyed() {},
    data() {
        return {
            err_dialog: { show: false, text: "", status: 0 },
            app_ready: false,
            drawer_logo: "",
            windowWidth: 0,
            windowHeight: 0,
            showProfileMenu: false,
            print_mode: false,
            loading_dialog: false,
            cloacking_dialog: false,
            isExpand: [],
            expandIcon: "expand_more",

            refreshKey: 0,
            toolbarKey: 0,
            navDrawerKey: 0,
            listKey: 0,
            toolbarKey: 0,
            navDrawerKey: 0,
            listKey: 0,
            login: {
                email: "",
                password: "",
                api: "",
                submitOk: function () {},
            },
            userLogin: false,
            pageReady: false,
            router: router,
            drawer: false,
            items: [],
            snackbar: null,

            addDialog: false,
            appTitle: "PKS",
        };
    },

    methods: {
        needReload(){
            return JSON.parse(localStorage.getItem('need_reload'));
        },
        showErr(res, title) {
            // console.log("showErr called!!");

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

        updateDrawer() {
            let path = this.$route.path;
            this.devLog("Drawer updated!");
            this.isExpand = [];
            let lastExpand = null;
            let lastIdx = null;
            this.items.forEach((menu, index) => {
                if (!menu.click) {
                    menu.click = () => {
                        this.devLog("menu click");
                        this.drawer = false;
                        this.updateDrawer();
                    };
                }

                if (
                    menu.link == path ||
                    (menu.link == "/home" && path == "/")
                ) {
                    this.devLog(menu.link);
                    this.devLog(path);
                    menu.class = "tab-selected pl-3";
                } else {
                    let test = path.split("/");
                    test.pop();

                    menu.class = "pl-3";
                    while (test.length >= 2 && menu.link != test.join("/")) {
                        test.pop();
                    }
                    if (
                        menu.link &&
                        menu.link == test.join("/") &&
                        test.join("/") != ""
                    ) {
                        this.devLog(test);
                        menu.class = "tab-selected pl-3";
                    }
                }
            });
        },
        updateItems() {
            this.devLog("Item updated!");
            this.items = [
                {
                    title: "Beranda",
                    icon: "home",
                    name: "home",
                    link: "/",
                    childs: null,
                    class: "pr-3",
                    show: true,
                    click: false,
                },
                {
                    title: "Product",
                    icon: "inventory_2",
                    name: "product",
                    link: "/product",
                    childs: null,
                    class: "pr-3",
                    show: true,
                    click: false,
                },
            ];
            this.items.push({
                title: "Log Out",
                icon: "exit_to_app",
                childs: null,
                link: "/login",
                name: "logout",
                class: "pr-3",
                show: true,
                click: this.logout,
            });

            if (localStorage.is_admin == "true") {
                this.items.push({
                    title: "Admin Area",
                    icon: false,
                    childs: null,
                    name: "admin",
                    link: "/admin",
                    class: "pr-auto text-left",
                    disabled: true,
                    show: true,
                    click: false,
                });
                this.items.push({
                    title: "User",
                    icon: "mdi-account-cog-outline",
                    name: "user",
                    link: "/users",
                    childs: null,
                    class: "pr-3",
                    show: true,
                    click: false,
                }),
                    this.items.push({
                        title: "Roles",
                        icon: "mdi-account-star-outline",
                        childs: null,
                        link: "/roles",
                        name: "roles",
                        class: "pr-3",
                        show: true,
                        click: false,
                    });
                this.items.push({
                    title: "Privileges",
                    icon: "mdi-file-star-outline",
                    childs: null,
                    link: "/privileges",
                    name: "privileges",
                    class: "pr-3",
                    show: true,
                    click: false,
                });
            }
        },
        initData() {
            this.devLog("checking login data");
            this.login.api = this.API + "/login";
            if (!!localStorage.userLogin) {
                this.userLogin = JSON.parse(localStorage.userLogin);
                this.devLog(localStorage.userLogin);
                this.updateUserLogin(this.userLogin.id);
                this.devLog("Logged, continuing...");

                this.updateItems();
                this.updateDrawer();
            } else {
                this.userLogin = false;
                this.devLog("Not Logged, redirecting...");
            }
            // this.devLog("globals API: "+this.API);
            //this.devLog("ROLE ID: "+(this.role? this.role.id : null));
        },

        refresh() {
            this.devLog("refresh() called. Initiate refresh...");
            // this.refreshKey += 1;
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
                    if (response.status == 200) {
                        // this.devLog(response.data);
                    }
                    // this.devLog("Loading "+ this.API + " - Result Status: " +response.status);
                });
            if (localStorage.userLogin) {
                localStorage.removeItem("userLogin");
            }
            this.refresh();
        },

        toggleExpand(itemIdx, idx) {
            this.isExpand[idx] = !this.isExpand[idx];

            this.items[itemIdx].expandable = this.isExpand[idx]
                ? "expand_less"
                : "expand_more";
            this.items[itemIdx].class = this.isExpand[idx]
                ? "menu-selected"
                : "menu";
            let start = itemIdx + 1;
            while (
                !!this.items[start] &&
                !this.items[start].expandable &&
                start < this.items.length - 1
            ) {
                // this.devLog(this.items[start]);
                this.items[start].show =
                    this.isExpand[idx] && !this.items[start].hide;
                start++;
            }
            // this.devLog("toggling Index: "+idx);
        },

        triggerPrint() {
            this.print_mode = !this.print_mode;
        },

        refreshTab() {
            this.devLog("refreshTab() called...");
            //this.devLog("Log current router: "+ this.$route.path);
        },
    },

    mounted() {
        this.pageReady = true;
        this.devLog("Customer App Component mounted...");
        this.$nextTick(() => {
            window.addEventListener("resize", () => {
                this.windowHeight = window.innerHeight;
                this.windowWidth = window.innerWidth;
            });
        });

        this.confirm = this.$refs.confirm.open;
        this.alert = this.$refs.alert.open;
        // this.error = function(res, title){
        this.app_ready = true;
    },
    router,
    watch: {
        "localStorage.userLogin": function (user) {
            this.userLogin = localStorage.userLogin;
            this.initData();
        },
    },
});

export default app;
