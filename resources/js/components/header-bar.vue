<template>
    <v-row class="mb-1">
        <v-col cols="12">
            <v-toolbar rounded elevation="1">
                <v-toolbar-title class="title">
                    <v-tooltip bottom v-if="back">
                        <template v-slot:activator="{ on }">
                            <v-btn v-on="on" @click="backFx()" icon
                                ><v-icon>mdi-arrow-left-thin</v-icon></v-btn
                            >
                        </template>
                        <span>Back</span>
                    </v-tooltip>

                    <span>{{ custom_title }}</span>
                </v-toolbar-title>

                <v-spacer></v-spacer>

                <v-menu
                    :close-on-content-click="false"
                    right
                    min-width="200px"
                    rounded
                    width="360px"
                >
                    <template v-slot:activator="{ on }">
                        <v-btn icon x-large v-on="on">
                            <v-avatar color="primary" size="40">
                                <v-avatar
                                    size="40"
                                    v-if="users.photo && users.photo != ''"
                                >
                                    <img
                                        :src="users.photo"
                                        :alt="getInitial(adminLogin.name)"
                                    />
                                </v-avatar>
                                <span v-else class="white--text text-h5">{{
                                    getInitial(adminLogin.name)
                                }}</span>
                            </v-avatar>
                        </v-btn>
                    </template>
                    <ProfileCard :users="users" />
                </v-menu>
                <div class="username">
                    <b>{{ users.name }}</b>
                </div>
            </v-toolbar>
        </v-col>
    </v-row>
</template>

<script>
import ProfileCard from "./profile-card.vue";
export default {
    name: "header-bar",

    created() {
        this.devLog("Header Component created");
        this.initData();
    },

    components: {
        ProfileCard,
    },

    mounted() {
        this.devLog("Header Component mounted");
    },

    props: {
        title: {
            type: String,
            required: false,
        },
        actions: {
            type: Array,
            required: false,
        },

        back: {
            type: Boolean,
            default: false,
        },
    },

    data: () => ({
        id: null,
        custom_title: null,
        profile_menu: false,
        adminLogin: {},
        users: {},
    }),

    methods: {
        backFx() {
            this.$router.go(-1);
        },

        initData() {
            this.adminLogin = JSON.parse(localStorage.adminLogin);
            this.id = this.adminLogin.id;
            this.initAxio();

            this.custom_title = this.title;
            if (!this.custom_title) {
                this.getDefaultTitle();
            }
        },

        initAxio() {
            axios
                .get(`${this.API}/user/${this.id}`, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    if (response.status == 200) {
                        this.devLog(response.data.data[0]);
                        this.devLog("get data");
                        this.users = response.data.data[0];
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

        getDefaultTitle() {
            let path = this.$route.path.split("/")[1];
            this.custom_title = this.titleCase(path);
        },
    },
};
</script>
