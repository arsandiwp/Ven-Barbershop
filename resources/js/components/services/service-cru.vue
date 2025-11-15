<template>
    <v-container grid-list-md pt-10>
        <v-row>
            <v-col class="mt-3 btn-back">
                <router-link
                    to="/service"
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
                                v-model="model.name"
                                :rules="nameRules"
                                :readonly="!editable"
                                dense
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
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

                    <!-- NEW FIELDS: DURATION & PRICE -->
                    <v-row>
                        <v-col cols="12" md="6">
                            <label for="duration" class="text-label"
                                >Duration*</label
                            >
                            <v-text-field
                                id="duration"
                                placeholder="Enter Duration (ex: 60 Minutes)"
                                outlined
                                type="text"
                                v-model="model.duration"
                                :readonly="!editable"
                                dense
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12" md="6">
                            <label for="price" class="text-label"
                                >Price*</label
                            >
                            <v-text-field
                                id="price"
                                placeholder="Enter Price"
                                outlined
                                type="number"
                                v-model="model.price"
                                :readonly="!editable"
                                dense
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <label for="description" class="text-label"
                                >Description</label
                            >
                            <v-textarea
                                id="description"
                                placeholder="Enter Description"
                                outlined
                                auto-grow
                                no-resize
                                rows="3"
                                v-model="model.description"
                                :readonly="!editable"
                                dense
                            ></v-textarea>
                        </v-col>
                    </v-row>

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

export default {
    name: "cru-service",
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
                name: "",
                description: "",
                status: "",
                duration: "",
                price: "",
            },

            avatar: {
                photo: null,
                editable: this.editable,
                imageURL: null,
            },

            items: [
                { text: "Published", value: "Published" },
                { text: "Draft", value: "Draft" },
            ],

            nameRules: [(v) => !!v || "Name is required"],
        };
    },

    created() {
        this.initData();
    },

    watch: {
        "$route.path": function () {
            this.initData();
        },
    },

    methods: {
        initData() {
            this.id = this.$route.params.id;

            let path = this.$route.path.split("/");
            let last = path.length - 1;

            for (let i = 1; i <= last; i++) {
                if (path[i] === "add") {
                    this.editable = true;
                    this.nav_title = "add";
                    this.initAvatar();
                } else if (path[i] === "edit") {
                    this.editable = true;
                    this.nav_title = "edit";
                } else {
                    this.nav_title = "detail";
                }
            }

            if (!isNaN(this.id)) {
                this.initAxio();
            }
        },

        initAxio() {
            axios
                .get(this.api + "/" + this.id, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    if (response.status === 200) {
                        this.model = response.data.data;
                        this.initAvatar();
                    }
                })
                .catch((err) => {
                    this.showErr(err.response ?? err);
                });
        },

        initAvatar() {
            this.avatar = {
                editable: this.editable,
                imageURL: this.model.photo,
                imgFile: null,
            };
        },

        validateForm() {
            if (this.$refs.form.validate()) {
                this.submitForm();
            } else {
                this.simpleSnackbar("Please check some Field!!", "error");
                window.scrollTo(0, 0);
            }
        },

        submitForm() {
            this.model.photo = this.avatar.imgFile;

            if (this.nav_title === "add") this.postData();
            else this.putData();
        },

        postData() {
            this.LOADING(true);

            let bodyPost = new FormData();
            bodyPost.append("name", this.model.name);
            if (this.model.photo) bodyPost.append("photo", this.model.photo[0]);

            bodyPost.append("description", this.model.description);
            bodyPost.append("status", this.model.status);
            bodyPost.append("duration", this.model.duration);
            bodyPost.append("price", this.model.price);

            axios
                .post(this.api, bodyPost, {
                    headers: {
                        Authorization: localStorage.token,
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    if (response.status == 201) {
                        this.$router.replace({
                            name: "service",
                            params: {
                                snackbarOpt: {
                                    text: "Success! Data added!",
                                    type: "success",
                                },
                            },
                        });
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
            if (this.model.photo) bodyPost.append("photo", this.model.photo[0]);

            bodyPost.append("description", this.model.description);
            bodyPost.append("status", this.model.status);
            bodyPost.append("duration", this.model.duration);
            bodyPost.append("price", this.model.price);

            axios
                .post(this.api + "/" + this.id, bodyPost, {
                    headers: {
                        Authorization: localStorage.token,
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    if (response.status === 202) {
                        this.$router.replace({
                            name: "service",
                            params: {
                                snackbarOpt: {
                                    text: "Success! Data updated!",
                                    type: "success",
                                },
                            },
                        });
                    }
                })
                .finally(() => {
                    this.LOADING(false);
                });
        },
    },
};
</script>
