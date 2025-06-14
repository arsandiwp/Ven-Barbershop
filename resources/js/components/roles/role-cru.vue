<template>
    <v-container fluid>
        <LoadingDialog :dialog="!model_ready"></LoadingDialog>
        <v-form
            lazy-validation
            ref="form"
            class="px-4"
            @submit.prevent="sendData"
            :key="rerenderKeyForm"
        >
            <v-row id="container-first-row">
                <v-col cols="5">
                    <a @click.stop="$router.go(-1)">
                        <v-btn color="#404040" plain>
                            <v-icon dark> mdi-arrow-left-thin </v-icon>
                            <div class="spacer-xs-cru-product"></div>
                            Back
                        </v-btn>
                    </a>
                </v-col>
                <v-col cols="7" id="container-button-navigation-cru-product">
                    <v-btn color="#2C80C9" style="color: white" type="submit">
                        Save
                    </v-btn>
                </v-col>
            </v-row>
            <v-row id="container-second-row-cru-roles">
                <v-col cols="12" sm="12" md="12" xl="12" lg="12">
                    <v-row>
                        <v-col cols="12" sm="12" md="4" xl="4" lg="4">
                            <label for="roles-name">Name*</label>
                            <v-text-field
                                id="roles-name"
                                background-color="#F6F7FB"
                                placeholder="Super Admin"
                                flat
                                solo
                                v-model="role.name"
                                :error-messages="name_error_msg"
                                @input="checkIsNameExistsDebounce"
                                :rules="non_null_rules"
                                :error="name_ok"
                                type="text"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="4" xl="4" lg="4">
                            <label for="parent-role">Parent Role</label>
                            <v-select
                                background-color="#F6F7FB"
                                placeholder="Select Parent Role"
                                flat
                                solo
                                v-model="role.parent"
                                @change="checkedPrivilegesListFromParentRole"
                                :items="allRoles"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" sm="12" md="4" xl="4" lg="4">
                            <label for="role-score">Role Score</label>
                            <v-text-field
                                id="role-score"
                                background-color="#F6F7FB"
                                flat
                                placeholder="%"
                                solo
                                v-model="role_score"
                                type="text"
                                readonly
                            ></v-text-field>
                        </v-col>
                    </v-row>
                    <v-row>
                        <v-col cols="12">
                            <v-data-table
                                :headers="headerPrivileges"
                                :items="privilegeItems"
                                :page.sync="page"
                                :items-per-page="itemsPerPage"
                                hide-default-footer
                                :loading="data_table_loading"
                                loading-text="Please wait, while we fetch the data."
                                id="data-table-roles"
                            >
                                <template v-slot:[`item.enable`]="{ item }">
                                    <v-simple-checkbox
                                        v-model="item.enable"
                                        color="primary"
                                        @click="calculateRoleScore(item)"
                                    ></v-simple-checkbox>
                                </template>
                            </v-data-table>
                            <v-container fluid id="pagination-cru-roles">
                                <v-row>
                                    <v-col cols="12" sm="6">
                                        <v-row class="pagination">
                                            <v-col
                                                cols="8"
                                                sm="8"
                                                align-self="center"
                                            >
                                                <p class="text-center my-auto">
                                                    Showing Result Page
                                                </p>
                                            </v-col>
                                            <v-col cols="4" sm="4">
                                                <v-select
                                                    dense
                                                    outlined
                                                    hide-details
                                                    :value="itemsPerPage"
                                                    @change="
                                                        handleItemsPerPageChange
                                                    "
                                                    :items="perPageChoices"
                                                ></v-select>
                                            </v-col>
                                        </v-row>
                                    </v-col>

                                    <v-col
                                        cols="12"
                                        sm="6"
                                        class="d-flex justify-end number-pagination"
                                    >
                                        <v-pagination
                                            v-model="page"
                                            :length="pageCount"
                                        ></v-pagination>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
        </v-form>
    </v-container>
</template>

<script>
import _debounce from "lodash/debounce";
import LoadingDialog from "../loading-dialog.vue";

export default {
    components: {
        LoadingDialog,
    },
    data() {
        return {
            role: {
                name: "",
                parent: "",
            },
            role_score: 0,
            name_ok: false,
            name_error_msg: "",
            name_error_constraint: false,
            non_null_rules: [(v) => !!v || "Field is required"],
            id: "",
            selectedPrivilegeId: [],
            allRoles: [],
            headerPrivileges: [
                {
                    text: "No",
                    sortable: false,
                    align: "center",
                    value: "no",
                },
                {
                    text: "Module Name",
                    sortable: false,
                    align: "center",
                    value: "moduleName",
                },
                {
                    text: "Enable",
                    sortable: false,
                    align: "center",
                    value: "enable",
                },
            ],
            privilegeItems: [],
            countPrivilegeSelected: 0,
            rerenderKeyForm: 1,

            // Custom Pagination
            page: 1,
            itemsPerPage: 10,
            perPageChoices: [5, 10, 20, 25],

            pageCount: 0,

            // Loading
            model_ready: false,
            data_table_loading: true,
        };
    },
    created() {
        this.initData();
    },
    mounted() {
        const file = document.createElement("link");
        file.rel = "stylesheet";
        file.href = "/css/data-table-roles.css";
        document.head.appendChild(file);
    },
    watch: {
        countPrivilegeSelected: function (newValue, oldVal) {
            this.role_score = Math.round(
                (newValue / this.privilegeItems.length) * 100
            );
        },
        "role.name"(newVal, oldVal) {
            // if (newVal.length > 30) {
            //     this.simpleSnackbar(
            //         "Role name cannot contain more than 30 characters!",
            //         "info"
            //     );
            //     Vue.nextTick(() => (this.role.name = newVal.substring(0, 30)));
            // }
        },
        "$route.path"(newValue, oldVal) {
            if (newValue) {
                this.rerenderKey++;
                this.initData();
            }
        },
    },
    methods: {
        handleItemsPerPageChange(value) {
            this.itemsPerPage = parseInt(value, 10);
            this.pageCount = Math.ceil(
                this.privilegeItems.length / this.itemsPerPage
            );
        },

        initData() {
            this.id = this.$route.params.id;

            this.privilegeItems.length = 0;
            this.privilegeItems = [];
            this.countPrivilegeSelected = 0;

            this.getAllRoles();
            if (!this.id) {
                this.getAllPrivilege();
            }
        },
        checkIsNameExistsDebounce: _debounce(function () {
            this.checkIsNameExists();
        }, 500),
        checkIsNameExists() {
            const bodyPost = {
                name: this.role.name,
                id: this.id ? this.id : null,
            };

            axios
                .post(`${this.API}/role/checker/check-name-exists`, bodyPost, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    if (!response.data.data) {
                        this.name_ok = false;
                        this.name_error_msg =
                            "Role name already used by another role!";
                        this.name_error_constraint = true;
                        this.addNotification(
                            "Current role name already used by another role!"
                        );
                        return;
                    }

                    this.name_ok = true;
                    this.name_error_msg = "";
                    this.name_error_constraint = false;
                })
                .catch((error) => {
                    const responseErr = error.response;

                    // if (responseErr.status == 401) {
                    //     this.alert(
                    //         "Unauthorized Access",
                    //         "Please login to get token to access this feature",
                    //         { color: "#d9534f" }
                    //     );
                    //     return;
                    // }

                    // if (responseErr.status == 500) {
                    //     this.alert(
                    //         "Server Error",
                    //         "Please contact ZK Digimax technical support to report this issue!",
                    //         { color: "#d9534f" }
                    //     );
                    //     return;
                    // }
                });
        },
        calculateRoleScore(item) {
            if (item.enable) {
                this.countPrivilegeSelected += 1;
            } else {
                this.countPrivilegeSelected -= 1;
            }
        },
        checkRoleName() {
            if (this.role.name && !this.name_error_constraint) {
                this.name_ok = true;
            } else {
                this.name_ok = false;
            }
        },
        getAllPrivilege() {
            axios
                .get(`${this.API}/privileges`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.devLog("Privileges : ");
                    this.devLog(response);

                    const data = response.data;

                    data.forEach((item) => {
                        this.privilegeItems.push({
                            no: item.id,
                            moduleName: item.text,
                            enable: false,
                        });
                    });

                    this.pageCount = Math.ceil(
                        this.privilegeItems.length / this.itemsPerPage
                    );

                    this.model_ready = true;
                    this.data_table_loading = false;
                })
                .catch((error) => {
                    const responseErr = error.response;

                    // if (responseErr.status == 500) {
                    //     this.alert(
                    //         "Server Error",
                    //         "Please contact ZK Digimax technical support to report this issue !",
                    //         { color: "#d9534f" }
                    //     );
                    // }

                    // if (responseErr.status == 401) {
                    //     this.alert(
                    //         "Unauthorized Access",
                    //         "Please login to get token to access this feature",
                    //         { color: "#d9534f" }
                    //     );
                    // }
                });
        },
        getMainRoleData() {
            axios
                .get(`${this.API}/role/${this.id}`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    const data = response.data.data;

                    this.devLog("Get Roles by Id : ");
                    this.devLog(data);

                    this.role.name = data.name;
                    this.role_score = data.score;
                    this.selectedPrivilegeId = data.privileges_ids;

                    // checking parent id
                    if (data.parent_id && data.parent_id >= 0) {
                        const selectedParentRole = this.allRoles.find(
                            (item) => {
                                return item.value == data.parent_id;
                            }
                        );
                        this.role.parent = selectedParentRole.value;
                    }

                    const idxToRemove = this.allRoles.findIndex((item) => {
                        return item.value == data.id;
                    });

                    if (idxToRemove >= 0) {
                        this.allRoles.splice(idxToRemove, 1);
                    }

                    this.getAllPrivilegesWithCalculatedData();
                })
                .catch((error) => {
                    const responseErr = error.response;

                    // if (responseErr.status == 500) {
                    //     this.alert(
                    //         "Server Error",
                    //         "Please contact ZK Digimax technical support to report this issue !",
                    //         { color: "#d9534f" }
                    //     );
                    // }

                    // if (responseErr.status == 401) {
                    //     this.alert(
                    //         "Unauthorized Access",
                    //         "Please login to get token to access this feature",
                    //         { color: "#d9534f" }
                    //     );
                    // }
                });
        },
        getAllPrivilegesWithCalculatedData() {
            axios
                .get(`${this.API}/privileges`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.devLog("Privileges with Calculated Data :");
                    this.devLog(response);

                    const data = response.data;
                    let countPrivSelected = 0;

                    data.forEach((item) => {
                        this.privilegeItems.push({
                            no: item.id,
                            moduleName: item.text,
                            enable: this.selectedPrivilegeId.includes(item.id)
                                ? true
                                : false,
                        });

                        if (this.selectedPrivilegeId.includes(item.id)) {
                            countPrivSelected += 1;
                        }
                    });

                    this.countPrivilegeSelected = countPrivSelected;

                    this.pageCount = Math.ceil(
                        this.privilegeItems.length / this.itemsPerPage
                    );

                    this.model_ready = true;
                    this.data_table_loading = false;
                })
                .catch((error) => {
                    const responseErr = error.response;

                    // if (responseErr.status == 500) {
                    //     this.alert(
                    //         "Server Error",
                    //         "Please contact ZK Digimax technical support to report this issue !",
                    //         { color: "#d9534f" }
                    //     );
                    // }

                    // if (responseErr.status == 401) {
                    //     this.alert(
                    //         "Unauthorized Access",
                    //         "Please login to get token to access this feature",
                    //         { color: "#d9534f" }
                    //     );
                    // }
                });
        },
        getAllRoles() {
            axios
                .get(`${this.API}/roles`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    const data = response.data;
                    this.allRoles = data;
                    this.allRoles.unshift({
                        id: 0,
                        text: "None",
                        value: 0,
                    });
                    this.devLog("Roles list : ");
                    this.devLog(this.allRoles);

                    if (this.id) {
                        this.getMainRoleData();
                    }
                })
                .catch((error) => {
                    const responseErr = error.response;

                    // if (responseErr.status == 500) {
                    //     this.alert(
                    //         "Server Error",
                    //         "Please contact ZK Digimax technical support to report this issue !",
                    //         { color: "#d9534f" }
                    //     );
                    // }

                    // if (responseErr.status == 401) {
                    //     this.alert(
                    //         "Unauthorized Access",
                    //         "Please login to get token to access this feature",
                    //         { color: "#d9534f" }
                    //     );
                    // }
                });
        },
        sendData() {
            const bodyRequest = {
                name: this.role.name,
            };

            if (this.role.parent) {
                bodyRequest["parent_id"] = this.role.parent;
            }

            if (this.role.parent >= 0) {
                bodyRequest["parent_id"] = this.role.parent;
            }

            if (this.role_score >= 0) {
                bodyRequest["score"] = this.role_score;
            }

            bodyRequest["role_privileges"] = this.processSelectedPrivileges(
                this.privilegeItems
            );

            this.checkRoleName();

            const isValid = this.$refs.form.validate();

            if (this.id) {
                bodyRequest["update_privileges"] = true;
                if (this.name_ok && isValid) {
                    axios
                        .put(`${this.API}/role/${this.id}`, bodyRequest, {
                            headers: { Authorization: localStorage.token },
                        })
                        .then((response) => {
                            // if (response.status != 202) {
                            //     this.alert(
                            //         `Something Went Wrong!`,
                            //         "Please contact ZK Digimax technical support to report this issue ! (error : no error in response but the status code is not 202)",
                            //         { color: "#d9534f" }
                            //     );
                            //     return;
                            // }
                            // this.alert(
                            //     `Role ${this.role.name} has been updated`,
                            //     `Role ${this.role.name} updated successfully!`,
                            //     { color: "#5cb85c" }
                            // );
                            // this.$router.push({ name: "roles" });

                            this.$router.replace({
                                name: "roles",
                                params: {
                                    snackbarOpt: {
                                        text: "Success! Data edited!!",
                                        type: "success",
                                    },
                                },
                            });
                        })
                        .catch((error) => {
                            const responseErr = error.response;

                            // if (responseErr.status == 500) {
                            //     this.alert(
                            //         "Server Error",
                            //         "Please contact ZK Digimax technical support to report this issue !",
                            //         { color: "#d9534f" }
                            //     );
                            //     return;
                            // }

                            // if (responseErr.status == 401) {
                            //     this.alert(
                            //         "Unauthorized Access",
                            //         "Please login to get token to access this feature",
                            //         { color: "#d9534f" }
                            //     );
                            //     return;
                            // }
                        });
                }
            } else {
                if (this.name_ok && isValid) {
                    axios
                        .post(`${this.API}/role`, bodyRequest, {
                            headers: { Authorization: localStorage.token },
                        })
                        .then((response) => {
                            // if (response.status != 201) {
                            //     this.alert(
                            //         `Something Went Wrong!`,
                            //         "Please contact ZK Digimax technical support to report this issue ! (error : no error in response but the status code is not 201)",
                            //         { color: "#d9534f" }
                            //     );
                            //     return;
                            // }
                            // this.alert(
                            //     `Role ${this.role.name} has been created`,
                            //     `Role ${this.role.name} created successfully!`,
                            //     { color: "#5cb85c" }
                            // );
                            // this.$router.push({ name: "roles" });

                            this.$router.replace({
                                name: "roles",
                                params: {
                                    snackbarOpt: {
                                        text: "Success! Data added!!",
                                        type: "success",
                                    },
                                },
                            });
                        })
                        .catch((error) => {
                            const responseErr = error.response;

                            // if (responseErr.status == 500) {
                            //     this.alert(
                            //         "Server Error",
                            //         "Please contact ZK Digimax technical support to report this issue !",
                            //         { color: "#d9534f" }
                            //     );
                            //     return;
                            // }

                            // if (responseErr.status == 401) {
                            //     this.alert(
                            //         "Unauthorized Access",
                            //         "Please login to get token to access this feature",
                            //         { color: "#d9534f" }
                            //     );
                            //     return;
                            // }
                        });
                }
            }
        },
        processSelectedPrivileges(arrPrivileges) {
            const rolePrivileges = [];
            arrPrivileges.forEach((item) => {
                if (item.enable) {
                    rolePrivileges.push({
                        id: item.no,
                        name: item.moduleName,
                    });
                }
            });

            return rolePrivileges;
        },
        async checkedPrivilegesListFromParentRole() {
            this.devLog("Role Parent Id : ");
            this.devLog(this.role.parent);
            if (this.role.parent) {
                const renewPrivilegesBasedOnParent = await this.confirm(
                    "Renew Selected Privileges",
                    "This action will override all selected privileges! If you want to undo this action, you must reload this page.",
                    { color: "warning" }
                );
                this.data_table_loading = true;
                if (renewPrivilegesBasedOnParent) {
                    axios
                        .get(`${this.API}/role/${this.role.parent}`, {
                            headers: { Authorization: localStorage.token },
                        })
                        .then((response) => {
                            const data = response.data.data;

                            const privilegesId = data.privileges_ids;

                            let insertingPrivilegeFromParentRole = [];
                            let countPrivSelected = 0;
                            this.privilegeItems.forEach((item) => {
                                insertingPrivilegeFromParentRole.push({
                                    no: item.no,
                                    moduleName: item.moduleName,
                                    enable: privilegesId.includes(item.no),
                                });

                                if (privilegesId.includes(item.no)) {
                                    countPrivSelected += 1;
                                }
                            });
                            this.countPrivilegeSelected = countPrivSelected;
                            this.privilegeItems =
                                insertingPrivilegeFromParentRole;
                            this.data_table_loading = false;
                            this.devLog("Get Role hasil response : ");
                            this.devLog(data);
                        })
                        .catch((error) => {
                            const responseErr = error.response;

                            // if (responseErr.status == 500) {
                            //     this.alert(
                            //         "Server Error",
                            //         "Please contact ZK Digimax technical support to report this issue !",
                            //         { color: "#d9534f" }
                            //     );
                            // }

                            // if (responseErr.status == 401) {
                            //     this.alert(
                            //         "Unauthorized Access",
                            //         "Please login to get token to access this feature",
                            //         { color: "#d9534f" }
                            //     );
                            // }
                        });
                }
            }
        },
    },
};
</script>

<style scoped>
#container-button-navigation-cru-product {
    display: flex;
    flex-direction: row;
    justify-content: flex-end;
    align-content: center;
    align-items: center;
}
.spacer-xs-cru-product {
    width: 10px;
}

#container-second-row-cru-roles {
    background-color: #fefefe;
    padding: 20px;
}

#pagination-cru-roles {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
}

.text-bolder {
    font-weight: 600;
}

.m-0 {
    margin: 0px !important;
}

@media only screen and (max-width: 430px) {
    .number-pagination {
        justify-content: center !important;
    }
}
@media only screen and (min-width: 430px) {
    .pagination {
        width: 300px;
    }
}
</style>
