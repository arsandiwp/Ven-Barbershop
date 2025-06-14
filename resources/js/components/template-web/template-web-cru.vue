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
                        <v-col cols="12" sm="12" md="12" xl="12" lg="12">
                            <label for="title-template">Title*</label>
                            <v-text-field
                                id="title-template"
                                background-color="#F6F7FB"
                                flat
                                solo
                                v-model="model.title"
                                :rules="non_null_rules"
                                type="text"
                            ></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" xl="12" lg="12">
                            <label for="token-template">Token*</label>
                            <v-text-field
                                id="token-template"
                                background-color="#F6F7FB"
                                flat
                                solo
                                v-model="model.token"
                                :rules="non_null_rules"
                                type="text"
                                append-icon="mdi-refresh"
                                @click:append="generateToken"
                            >
                                <template v-slot:append>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-icon
                                                v-bind="attrs"
                                                v-on="on"
                                                @click="generateToken"
                                            >
                                                mdi-refresh
                                            </v-icon>
                                        </template>
                                        <span>Generate Token</span>
                                    </v-tooltip>
                                </template>
                            </v-text-field>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
        </v-form>
    </v-container>
</template>

<script>
import LoadingDialog from "../loading-dialog.vue";

export default {
    components: {
        LoadingDialog,
    },
    data() {
        return {
            model: {
                title: "",
                token: "",
            },
            name_ok: false,
            name_error_msg: "",
            name_error_constraint: false,
            non_null_rules: [(v) => !!v || "Field is required"],
            id: "",

            templates: [],

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
        "$route.path"(newValue, oldVal) {
            if (newValue) {
                this.rerenderKey++;
                this.initData();
            }
        },
    },
    methods: {

        initData() {
            this.id = this.$route.params.id;

            if (this.id) {
                this.getMainData();
            }

            this.model_ready = true;
        },

        getMainData() {
            axios
                .get(`${this.API}/template-web/${this.id}`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    const data = response.data.data;

                    this.model = data;

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
            const bodyRequest = this.model;

            const isValid = this.$refs.form.validate();

            if (this.id) {
                if (isValid) {
                    axios
                        .put(`${this.API}/template-web/${this.id}`, bodyRequest, {
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
                                name: "template-web",
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
                if (isValid) {
                    axios
                        .post(`${this.API}/template-web`, bodyRequest, {
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
                                name: "template-web",
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
        generateToken() {
            let token = [...Array(32)]
                .map(() => Math.random().toString(36)[2])
                .join('');
            this.model.token = token;
        }
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
