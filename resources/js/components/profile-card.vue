<template>
    <v-container fluid class="pa-0 ma-0">
        <ChangePasswordDialog :dialog="cp_dialog" @close="closeCPDialog" />
        <v-card class="mx-auto" width="360px">
            <div
                style="
                    background-size: 100%;
                    background-image: url(/images/bg.png);
                    height: 180px;
                "
            ></div>
            <v-container
                class="pa-0 mx-0 my-3"
                fluid
                width="360"
                style="background-color: red"
            >
                <v-row justify="center">
                    <v-col
                        style="width: 360px"
                        align-self="start"
                        class="d-flex justify-center align-center pa-0"
                        cols="12"
                    >
                        <v-avatar
                            class="profile avatar-center-heigth avatar-shadow"
                            color="primary"
                            size="164"
                        >
                            <v-img
                                v-if="users.photo && users.photo != ''"
                                :src="users.photo"
                                :alt="getInitial(users.name)"
                            />
                            <span v-else class="white--text text-h2"
                                ><strong>{{
                                    getInitial(users.name)
                                }}</strong></span
                            >
                        </v-avatar>
                    </v-col>
                </v-row>
            </v-container>
            <v-list-item-content
                class="justify-center bg pa-0 pt-0 ma-0"
                style=""
            >
                <div class="mx-auto text-center bg2" style="margin-top: 90px">
                    <h4 class="ma-2">
                        <strong>{{ users.name }}</strong>
                    </h4>
                    <p class="text-caption">
                        {{ users.email }}
                    </p>
                    <v-divider class="my-3"></v-divider>
                    <router-link to="/profile">
                        <v-hover>
                            <v-btn
                                :class="hover ? 'primary accent-1' : ''"
                                slot-scope="{ hover }"
                                depressed
                                rounded
                                text
                                :light="hover"
                            >
                                <b>View Profile</b>
                            </v-btn>
                        </v-hover>
                    </router-link>

                    <v-divider class="my-3"></v-divider>

                    <v-hover>
                        <v-btn
                            :class="hover ? 'warning accent-1' : ''"
                            slot-scope="{ hover }"
                            depressed
                            rounded
                            text
                            :light="hover"
                            @click="openCPDialog"
                        >
                            <b>Change Password</b>
                        </v-btn>
                    </v-hover>

                    <v-divider class="my-3"></v-divider>

                    <v-hover>
                        <v-btn
                            :class="hover ? 'mb-3 error accent-1' : 'mb-3'"
                            slot-scope="{ hover }"
                            depressed
                            rounded
                            :light="hover"
                            text
                            @click="$root.logout"
                        >
                            <b>Logout</b>
                        </v-btn>
                    </v-hover>
                </div>
            </v-list-item-content>
        </v-card>
    </v-container>
</template>

<script>
import ChangePasswordDialog from "./change-password-dialog";
export default {
    name: "profile-card",

    created() {
        this.devLog("Profile Card created");
        this.initData();
    },

    components: {
        ChangePasswordDialog,
    },

    mounted() {
        this.devLog("Profile Card mounted");
    },

    props: {
        users: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            cp_dialog: false,
        };
    },

    methods: {
        openCPDialog() {
            this.cp_dialog = true;
        },

        closeCPDialog() {
            this.cp_dialog = false;
        },

        initData() {},

        onButtonClick() {
            this.isSelecting = true;
            window.addEventListener(
                "focus",
                () => {
                    this.isSelecting = false;
                },
                { once: true }
            );

            this.$refs.uploader.click();
        },
        onFileChanged(e) {
            this.selectedFile = e.target.files[0];
        },
    },
};
</script>

<style scoped>
.bg {
    background-image: url(/images/top_left.png);
    background-size: contain;
}

.bg2 {
    position: relative;
}

.bg2::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0.75;
    background: url(/images/bottom_right.png);
    background-size: 100%;
    /* z-index: -1; */
}

.avatar-shadow {
    box-shadow: 0px 0px 10px 0px rgba(50, 12, 112, 0.75);
    -webkit-box-shadow: 0px 0px 10px 0px rgba(50, 12, 112, 0.75);
    -moz-box-shadow: 0px 0px 10px 0px rgba(50, 12, 112, 0.75);
}

.upload-btn {
    position: absolute !important;
    z-index: 999;
    top: 121px;
    color: cadetblue;
    background: blueviolet;
    background: rgb(125, 198, 163);
    background: linear-gradient(
        50deg,
        rgba(125, 198, 163, 1) 0%,
        rgba(35, 216, 227, 1) 72%
    );
}

.avatar-center-heigth {
    position: absolute;
}

.profile-text-name {
    margin-top: 70px;
}

.subtitles {
    margin: 5px;
    padding: 16px;
}
</style>
