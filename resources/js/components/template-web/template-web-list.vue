<template>
    <v-container grid-list-md pt-10>
        <!-- <add-edit-dialog
            v-if="add_edit_dialog"
            :dialog="add_edit_dialog"
            :model="form"
            @cancel="cancelDialog()"
            @submit="submitDialog()"
        ></add-edit-dialog> -->
        <HeaderBar />
        <paginated-data-list
            :key="list_key"
            :api="api"
            :list="list"
        ></paginated-data-list>
    </v-container>
</template>

<script>
import PaginatedDataList from "../paginate-data-list";
// import AddEditDialog from "../add-edit-dialog";

export default {
    name: "roles",
    components: {
        PaginatedDataList,
        // AddEditDialog,
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
        this.devLog("Roles Page Created...");
        if (!!this.$route.params.snackbarOpt) {
            let snackbarOpt = this.$route.params.snackbarOpt;
            this.simpleSnackbar(snackbarOpt.text, snackbarOpt.type);
        }
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

    data() {
        return {
            list_key: 1,
            list: {
                title: "Template Web List",
                search: "",
                add: this.addItem,
                // edit: this.editItem
                datas: [],
                headers: [],
                loading: false,
                data_loaded: false,
                actions: [
                    // { label: "Detail", icon: "info", color: "disabled", fx: this.infoItem },
                    {
                        label: "Edit",
                        icon: "mdi-pencil-outline",
                        color: "warning",
                        fx: this.editItem,
                    },
                    {
                        label: "Delete",
                        icon: "mdi-trash-can-outline",
                        color: "red",
                        fx: this.deleteItem,
                    },
                ],
            },
            // add_edit_dialog: false,

            // form: {
            //     title: "",
            //     data: {},
            //     inputs: [],
            //     grid: 1,
            //     width: 500,
            // },
        };
    },

    methods: {
        initData() {
            this.devLog("Initialize Roles Page");
            this.initHead();
        },

        initHead() {
            this.list.headers = [];
            this.list.headers = [
                {
                    text: "No.",
                    value: "index",
                    align: "center",
                    sortable: false,
                },
                {
                    text: "Title",
                    type: "text",
                    value: "title",
                    align: "left",
                    sortable: false,
                },
                {
                    text: "Token",
                    type: "text",
                    value: "token",
                    align: "left",
                    sortable: false,
                },
                {
                    text: "Actions",
                    value: "actions",
                    sortable: false,
                    align: "center",
                },
            ];
        },

        // initForm() {
        //     this.form.inputs = [
        //         { label: "Name", type: "text", value: "name", required: true },
        //         {
        //             label: "Description",
        //             type: "text",
        //             value: "description",
        //             required: true,
        //         },
        //     ];
        // },

        // resetFormData() {
        //     this.form.data = {};
        // },

        // cancelDialog() {
        //     this.form.title = "";
        //     this.resetFormData();

        //     this.add_edit_dialog = false;
        // },

        // submitDialog() {
        //     this.add_edit_dialog = false;
        //     if (this.form.title == "Add Role") {
        //         this.postData();
        //     } else if (this.form.title == "Edit Role") {
        //         this.putData();
        //     }
        // },

        addItem() {
            this.$router.push(this.$route.path + "/add");
        },

        infoItem(item) {
            document.activeElement.blur();
            this.alert("Note", "Under Development", { color: "primary" });
        },

        editItem(item) {
            this.$router.push(this.$route.path + "/edit/" + item.id);
        },

        deleteItem(item) {
            document.activeElement.blur();
            this.confirm(
                "Delete Data",
                "Are you sure you want to delete Qna [" + item.title + "]?",
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

        // postData() {
        //     let userData = this.form.data;
        //     this.devLog(
        //         "Trying to connect... " +
        //             this.apiCUD +
        //             " with : " +
        //             JSON.stringify(userData)
        //     );
        //     this.LOADING(true);
        //     axios
        //         .post(this.apiCUD, userData, {
        //             headers: { Authorization: localStorage.token },
        //         })
        //         .then((response) => {
        //             this.LOADING(false);
        //             this.devLog("Login Result Code: " + response.status);
        //             if (response.status == 201) {
        //                 this.simpleSnackbar(
        //                     "Data berhasil ditambahkan!",
        //                     "success"
        //                 );
        //                 this.reload();
        //             }
        //         })
        //         .catch((err) => {
        //             this.LOADING(false);
        //             if (!!err.response) {
        //                 this.showErr(err.response, "Failed");
        //             } else {
        //                 this.showErr({ status: "Code Error", statusText: err });
        //             }
        //         });
        // },

        // putData() {
        //     let userData = this.form.data;
        //     let url = this.apiCUD + "/" + userData.id;
        //     this.devLog(
        //         "Trying to connect... " +
        //             url +
        //             " with : " +
        //             JSON.stringify(userData)
        //     );
        //     this.LOADING(true);
        //     axios
        //         .put(url, userData, {
        //             headers: { Authorization: localStorage.token },
        //         })
        //         .then((response) => {
        //             this.LOADING(false);
        //             this.devLog("Login Result Code: " + response.status);
        //             if (response.status == 202) {
        //                 this.simpleSnackbar("Data berhasil diubah!", "success");
        //                 this.reload();
        //             }
        //         })
        //         .catch((err) => {
        //             this.LOADING(false);
        //             if (!!err.response) {
        //                 let res = err.response;
        //                 res.customClose = this.reload;
        //                 this.showErr(res, "Failed");
        //             } else {
        //                 this.showErr({ status: "Code Error", statusText: err });
        //             }
        //         });
        // },

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
                        this.simpleSnackbar(
                            "Data has been successfully deleted!!",
                            "success"
                            // item,
                            // this.apiCUD + "/restore/" + item.id,
                            // () => {
                            //     this.list_key++;
                            // }
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

<style scoped></style>
