<template>
    <v-container grid-list-md pt-10>
        <v-row>
            <v-col class="mt-3 btn-back">
                <router-link
                    to="/users"
                    style="text-decoration: none; color: black"
                >
                    <v-icon>mdi-arrow-left-thin</v-icon> Back
                </router-link>
            </v-col>
        </v-row>

        <v-sheet color="white" elevation="2" class="mt-4" rounded>
            <v-form
                @submit.prevent="validateForm()"
                v-model="valid"
                ref="form"
                autofocus
                lazy-validation
            >
                <v-container>
                    <image-input
                        v-model="avatar"
                        :editable="editable"
                        name="photo"
                        class="text-center"
                    >
                        <v-card-text slot="activator">
                            <Avatar
                                id="profile"
                                :image="
                                    avatar.imageURL != null
                                        ? avatar.imageURL
                                        : ''
                                "
                                :size="175"
                                @radius="175"
                                color="lightgrey"
                            ></Avatar>
                        </v-card-text>
                    </image-input>

                    <v-row>
                        <v-col cols="12" md="6">
                            <label for="name" class="text-label">Name*</label>
                            <v-text-field
                                id="name"
                                placeholder="Enter Name"
                                outlined
                                type="text"
                                v-model="model.name"
                                :rules="nameRules"
                                :readonly="!editable"
                                dense
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
                            <label for="email" class="text-label">Email*</label>
                            <v-text-field
                                id="email"
                                placeholder="Enter Email"
                                outlined
                                type="email"
                                v-model="model.email"
                                :rules="emailRules"
                                :readonly="!editable"
                                dense
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12" md="4">
                            <label for="phone" class="text-label">Phone</label>
                            <v-text-field
                                id="phone"
                                placeholder="Enter Phone"
                                outlined
                                type="text"
                                v-model="model.phone"
                                :readonly="!editable"
                                dense
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="4">
                            <label for="role" class="text-label">Role*</label>
                            <v-select
                                id="role"
                                :placeholder="
                                    model.role_id === 1
                                        ? 'Super Admin'
                                        : 'Select Role'
                                "
                                outlined
                                v-model="model.role_id"
                                :items="this.roles"
                                :readonly="!editable"
                                :disabled="model.role_id === 1"
                                dense
                                :rules="[(v) => !!v || 'Role is required']"
                            ></v-select>
                        </v-col>

                        <v-col cols="12" md="4">
                            <label for="status" class="text-label"
                                >Status*</label
                            >
                            <v-select
                                id="status"
                                placeholder="Select Status"
                                outlined
                                v-model="model.status"
                                item-value="value"
                                :items="items"
                                :readonly="!editable"
                                dense
                            ></v-select>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <label for="address" class="text-label"
                                >Address</label
                            >
                            <v-textarea
                                id="address"
                                placeholder="Enter Address"
                                outlined
                                auto-grow
                                no-resize
                                rows="3"
                                v-model="model.address"
                                :readonly="!editable"
                                dense
                            ></v-textarea>
                        </v-col>
                    </v-row>
                    <!-- Form -->
                    <div class="text-center mt-10" v-if="editable">
                        <v-btn
                            class="elevation-1"
                            color="primary"
                            type="submit"
                            large
                            ref="form"
                            >Save</v-btn
                        >
                    </div>
                </v-container>
            </v-form>
        </v-sheet>
    </v-container>
</template>

<script>
import Avatar from "vue-avatar-component";
import ImageInput from "../image-input.vue";
import Loading from "../loading-dialog";
import Vue from "vue";

export default {
    name: "cru-user",
    components: {
        ImageInput,
        Avatar,
        Loading,
    },
    props: {
        api: {
            type: String,
            required: true,
        },
        editable: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            id: null,
            valid: true,
            model: {
                id: null,
                photo: "",
                photo: "",
                name: "",
                email: "",
                phone: "",
                position: "",
                division: "",
                status: "Pending",
                address: "",
                role_id: 0,
            },
            avatar: {
                photo: null,
                editable: this.editable,
                imageURL: null,
            },
            roles: [],
            items: [
                { text: "Active", value: "Active" },
                { text: "Pending", value: "Pending" },
            ],
            nameRules: [
                (v) => !!v || "Name is required",
                // (v) => v.length <= 10 || "Name must be less than 10 characters",
            ],
            emailRules: [
                (v) => !!v || "E-mail is required",
                (v) => /.+@.+/.test(v) || "E-mail must be valid",
            ],
            loading: false,
            userAvatarColor: "white",
        };
    },

    created() {
        this.initData();
    },

    watch: {
        "$route.path": function (path) {
            this.devLog("watching change");
            this.initData();
        },

        "model.phone": function (newValue, oldValue) {
            if (newValue !== null && newValue !== undefined) {
                const result = newValue.replace(/[^0-9+]/g, "");
                Vue.nextTick(() => (this.model.phone = result));
            }
        },
    },

    mounted() {
        this.getRoles()
            .then((response) => {
                this.devLog(
                    "Hasil List Roles: " + JSON.stringify(response.data)
                );
                if (response.status == 200) {
                    if (!!response.data && response.data.length > 0) {
                        this.roles = response.data;
                        this.devLog("ini roleee", this.roles);
                    } else {
                        this.roles = [];
                    }
                }
            })
            .catch((err) => {
                result = [];
                if (!!err.response) {
                    this.showErr(err.response, "Failed");
                } else {
                    this.showErr({ status: "Code Error", statusText: err });
                }
            });
    },

    methods: {
        initData() {
            this.id = this.$route.params.id;
            this.nav_path = this.$route.path.split("/");
            var last = this.nav_path.length - 1;
            for (var i = 1; i <= last; i++) {
                var xpath = "/" + this.nav_path[i];
                if (this.nav_path[i] == "add") {
                    this.editable = true;
                    this.nav_title = "add";
                    this.initAvatar();
                } else if (this.nav_path[i] == "edit") {
                    this.editable = true;
                    this.nav_title = "edit";
                } else {
                    this.nav_title = "detil";
                }
            }
            if (!isNaN(this.id)) {
                this.devLog("ada id");
                this.initAxio();
            }
        },

        initAxio() {
            axios
                .get(this.api + "/" + this.id, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    if (response.status == 200) {
                        this.devLog(response.data.data);
                        // if(response.data.length > 0){
                        this.devLog("get data");
                        this.model = response.data.data[0];
                        this.devLog("ini member", this.model);
                        this.initAvatar();
                        // }else{
                        //     this.model.not_found = true;
                        // }
                        // this.model.ready = true;
                    } else {
                        alert(response);
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

        initAvatar() {
            this.model.editable = this.editable;
            this.avatar = {
                editable: this.editable,
                imageURL: this.model.photo,
                formData: null,
                imgFile: null,
            };
        },

        validateForm() {
            this.devLog("validating");
            if (this.$refs.form.validate()) {
                this.submitForm();
            } else {
                let snackbarOpt = {
                    text: "Please check some Field!!",
                    type: "error",
                };
                this.simpleSnackbar(snackbarOpt.text, snackbarOpt.type);
                window.scrollTo(0, 0);
            }
        },

        submitForm() {
            this.model.photo = this.avatar.imgFile;
            switch (this.nav_title) {
                case "add":
                    this.postData();
                    break;

                default:
                    this.putData();
                    break;
            }
        },

        postData() {
            this.LOADING(true);

            let bodyPost = new FormData();
            bodyPost.append("name", this.model.name);
            bodyPost.append("email", this.model.email);
            if (this.model.photo) {
                bodyPost.append("photo", this.model.photo[0]);
            }
            bodyPost.append("status", this.model.status);
            bodyPost.append("phone", this.model.phone);
            bodyPost.append("position", this.model.position);
            bodyPost.append("division", this.model.division);
            bodyPost.append("address", this.model.address);
            bodyPost.append("role_id", this.model.role_id);

            this.devLog(this.model);

            axios
                .post(this.api, bodyPost, {
                    headers: {
                        Authorization: localStorage.token,
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.devLog(JSON.stringify(response));
                    this.devLog(response.status);
                    if (response.status == 201) {
                        this.$router.replace({
                            name: "users",
                            params: {
                                snackbarOpt: {
                                    text: "Success! Data added!!",
                                    type: "success",
                                },
                            },
                        });
                    } else {
                        alert(response);
                        this.showErr(response);
                    }
                })
                .catch((err) => {
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                })
                .finally(() => {
                    this.LOADING(false);
                });
        },

        putData() {
            this.LOADING(true);

            let bodyPost = new FormData();
            bodyPost.append("name", this.model.name);
            bodyPost.append("email", this.model.email);
            if (this.model.photo) {
                bodyPost.append("photo", this.model.photo[0]);
            }
            bodyPost.append("status", this.model.status);
            if (this.model.phone) {
                bodyPost.append("phone", this.model.phone);
            }
            if (this.model.position) {
                bodyPost.append("position", this.model.position);
            }
            if (this.model.division) {
                bodyPost.append("division", this.model.division);
            }
            if (this.model.address) {
                bodyPost.append("address", this.model.address);
            }
            bodyPost.append("role_id", this.model.role_id);

            this.devLog(this.model);
            axios
                .post(this.api + "/" + this.id, bodyPost, {
                    headers: {
                        Authorization: localStorage.token,
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.devLog(JSON.stringify(response));
                    if (response.status == 202) {
                        this.$router.replace({
                            name: "users",
                            params: {
                                snackbarOpt: {
                                    text: "Success! Data edited!!",
                                    type: "success",
                                },
                            },
                        });

                        let userLogin = JSON.parse(
                            localStorage.getItem("userLogin")
                        );
                        if (this.model.id == userLogin.id) {
                            this.updateUserLogin(this.model.id);
                        }
                    } else {
                        alert(JSON.stringify(response));
                        this.showErr(response);
                    }
                })
                .catch((err) => {
                    if (!!err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                })
                .finally(() => {
                    this.LOADING(false);
                });
        },
    },
};
</script>
