<template>
    <v-container grid-list-md pt-10>
        <HeaderBar />
        <paginated-data-list
            :key="list_key"
            :api="api"
            :list="list"
            :model="form"
        ></paginated-data-list>        
    </v-container>
</template>

<script>
import PaginatedDataList from "../paginate-data-list";

export default {
    data() {
        return {
            list_key: 1,
            list: {
                title: "Member List",
                search: "",
                add: this.addItem,
                edit: this.editItem,
                detail: this.infoItem,
                // export: this.export,
                // import: this.import,
                datas: [],
                headers: [],
                loading: false,
                data_loaded: false,
                actions: [],
            },

            form: {
                title: "",
                data: {
                    role_id: 1,
                },
                inputs: [],
                grid: 2,
            },

            confirm_rule: [
                (v) => !!v || "Please confirm your password!",
                (v) => !!this.form.data.password || "Password is required!",
                (v) =>
                    v == this.form.data.password ||
                    "Password Confirmation mismatch!!",
            ],

            cp_dialog: false,
            cp_item: null,
        };
    },
    components: {
        PaginatedDataList,        
    },
    props: {
        api: {
            type: String,
            required: true,
        },
        apiCUD: {
            type: String,
            required: true,
        },
    },

    created() {
        this.devLog("User Page Created...");
        this.initData();
    },

    watch: {
        "$route.path": function (path) {
            this.devLog("watching change");
            this.initData();
        },

        "list.paginate.itemsPerPage"(val, old) {
            this.devLog(old + "-" + val);

            if (old != val && (val != "300" || val != 300)) {
                if (this.list.loading != true) {
                    this.updatePage(val);
                }
            }
        },
    },

    methods: {
        initAction() {
            let actions = [
                // {
                //     label: "Detail",
                //     icon: "mdi-information-outline",
                //     color: "primary",
                //     fx: this.infoItem,
                // },
                {
                    label: "Edit",
                    icon: "mdi-pencil-outline",
                    color: "warning",
                    fx: this.editItem,
                },
            ];
            actions.push({
                label: "Delete",
                icon: "mdi-trash-can-outline",
                color: "red",
                fx: this.deleteItem,
            });

            this.list.actions = actions;
        },
        initData() {
            this.devLog("Initialize User Page");
            if (!!this.$route.params.snackbarOpt) {
                let snackbarOpt = this.$route.params.snackbarOpt;
                this.simpleSnackbar(snackbarOpt.text, snackbarOpt.type);
            }
            this.initHead();
            this.initAction();
        },

        initHead() {
            this.list.headers = [
                {
                    text: "No.",
                    value: "index",
                    align: "center",
                    sortable: false,
                },
                {
                    text: "Photo",
                    field: "photo",
                    value: "image",
                    align: "center",
                    sortable: false,
                },
                {
                    text: "Name",
                    type: "text",
                    value: "name",
                    align: "center",
                    sortable: false,
                },
                {
                    text: "Role",
                    type: "text",
                    value: "role.name",
                    align: "center",
                    sortable: false,
                },
                {
                    text: "Status",
                    value: "status",
                    align: "center",
                    sortable: false,
                },
                {
                    text: "Action",
                    value: "actions",
                    sortable: false,
                    align: "center",
                },
            ];
        },

        export() {
            window.open(`${this.API + "/users-export"}`, "_blank");
        },

        import() {
            this.$router.push("/import-users");
        },

        infoItem(item) {
            this.$router.push(this.$route.path + "/" + item.id);
        },

        addItem() {
            this.$router.push(this.$route.path + "/add");
        },

        editItem(item) {
            this.$router.push(this.$route.path + "/edit/" + item.id);
        },

        changeItemPassword(item) {
            this.cp_item = item;
            this.cp_dialog = true;
        },

        closeCPDialog() {
            this.cp_dialog = false;
            this.cp_item = null;
        },

        deleteItem(item) {
            document.activeElement.blur();
            this.confirm(
                "Delete Data",
                "Are you sure you want to delete User [" + item.name + "]?",
                {
                    nohead: false,
                    color: "error",
                    agree: "Delete",
                    cancel: "Cancel",
                    width: 300,
                }
            ).then((confirm) => {
                if (confirm) {
                    this.deleteData(item);
                } else {
                    this.devLog("not confirmed");
                }
            });
        },

        appendFormData() {
            const keys = Object.keys(this.form.data);
            let formData = new FormData();
            this.devLog(keys.length);
            keys.forEach((key) => {
                this.devLog(key);
                formData.append(key, this.form.data[key]);
            });
            return formData;
        },

        postData() {
            let formData = this.appendFormData();
            // this.devLog(formData.getAll('image_file'));
            this.devLog(
                "Trying to connect... " +
                    this.apiCUD +
                    " with : " +
                    JSON.stringify(formData)
            );
            this.LOADING(true);
            axios
                .post(this.apiCUD, formData, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.LOADING(false);
                    this.devLog("Login Result Code: " + response.status);
                    if (response.status == 201) {
                        this.simpleSnackbar(
                            "Data berhasil ditambahkan!",
                            "success"
                        );
                        this.reload();
                    }
                })
                .catch((err) => {
                    this.LOADING(false);
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        putData() {
            let url = this.apiCUD + "/" + this.form.data.id;
            let formData = this.appendFormData();
            // this.devLog(formData.getAll('image_file'));
            this.devLog(
                "Trying to connect... " +
                    url +
                    " with : " +
                    JSON.stringify(formData)
            );
            this.LOADING(true);
            formData.append("_method", "PUT"); //form data can only be sent using axios post method, but we need put method on the server
            axios
                .post(url, formData, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.LOADING(false);
                    this.devLog("Login Result Code: " + response.status);
                    if (response.status == 202) {
                        this.simpleSnackbar("Data berhasil diubah!", "success");
                        this.reload();
                    }
                })
                .catch((err) => {
                    this.LOADING(false);
                    if (!!err.response) {
                        let res = err.response;
                        res.customClose = this.reload;
                        this.showErr(res, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        postPassword() {
            let passData = this.form.data;
            this.devLog(
                "Trying to connect... " +
                    this.apiCUD +
                    " with : " +
                    JSON.stringify(passData)
            );
            this.LOADING(true);
            axios
                .post(this.apiCUD + "/change-password", passData, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.LOADING(false);
                    this.devLog("Login Result Code: " + response.status);
                    if (response.status == 201) {
                        this.simpleSnackbar(
                            "Password berhasil diubah!",
                            "success"
                        );
                        this.reload();
                    }
                })
                .catch((err) => {
                    this.LOADING(false);
                    if (!!err.response) {
                        let res = err.response;
                        res.customClose = this.reload;
                        this.showErr(res, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        deleteData(item) {
            const index = this.list.datas.indexOf(item);
            axios
                .delete(this.apiCUD + "/" + item.id, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.devLog(JSON.stringify(response));
                    if (response.status == 204) {
                        this.list.datas.splice(index, 1);
                        this.complexSnackbar(
                            "Data has been successfully deleted!!",
                            item,
                            this.apiCUD + "/restore/" + item.id,
                            () => {
                                this.list_key++;
                            }
                        );
                    } else {
                        this.showErr(response);
                    }
                })
                .catch((err) => {
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        reload() {
            this.list_key++;
        },
    },

    mounted() {},
};
</script>
