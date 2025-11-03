<template>
    <v-container fluid>
        <v-row id="row-login">
            <v-col
                cols="12"
                class="container-column-login"
                id="container-main-login"
            >
                <v-container fluid id="container-box-main-login">
                    <v-container fluid id="container-header-login">
                        <h1>Reset Password</h1>
                        <p>
                            You’re just one step away! Enter your new password
                            to complete the reset process and regain access to
                            your account.
                        </p>
                    </v-container>
                    <v-form
                        @submit.prevent="postReset"
                        lazy-validation
                        ref="form"
                    >
                        <v-container fluid id="container-input-area-login">
                            <v-container class="container-text-field-login">
                                <label for="login-password" class="text-bolder"
                                    >Password*</label
                                >
                                <v-text-field
                                    id="login-password"
                                    placeholder="******"
                                    :append-icon="
                                        password.visible_main
                                            ? 'mdi-eye'
                                            : 'mdi-eye-off'
                                    "
                                    @click:append="
                                        () =>
                                            (password.visible_main =
                                                !password.visible_main)
                                    "
                                    :type="
                                        password.visible_main
                                            ? 'text'
                                            : 'password'
                                    "
                                    outlined
                                    :success="password.main_ok"
                                    :error="password.main_ok"
                                    :rules="password_rules"
                                    v-model="admin.password"
                                    required
                                    autocomplete="current-password"
                                ></v-text-field>
                            </v-container>

                            <v-container class="container-text-field-login">
                                <label for="login-password" class="text-bolder"
                                    >Confirm Password*</label
                                >
                                <v-text-field
                                    id="login-password"
                                    placeholder="******"
                                    :append-icon="
                                        password.visible_validation
                                            ? 'mdi-eye'
                                            : 'mdi-eye-off'
                                    "
                                    @click:append="
                                        () =>
                                            (password.visible_validation =
                                                !password.visible_validation)
                                    "
                                    :type="
                                        password.visible_validation
                                            ? 'text'
                                            : 'password'
                                    "
                                    outlined
                                    :success="password.retype_ok"
                                    :error="password.retype_ok"
                                    :rules="password_validation"
                                    v-model="admin.password_validation"
                                    required
                                    autocomplete="current-password"
                                ></v-text-field>
                            </v-container>
                            <v-container id="container-button-login">
                                <v-btn
                                    block
                                    elevation="2"
                                    id="btn-login"
                                    type="submit"
                                    color="primary"
                                    >Reset Password</v-btn
                                >
                            </v-container>
                        </v-container>
                    </v-form>
                </v-container>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: "reset-password",
    data() {
        return {
            token: this.$route.params.token,
            admin: {},
            password: {
                visible_main: false,
                visible_validation: false,
                main_ok: null,
                retype_ok: null,
            },
            password_rules: [
                (v) => !!v || "Field is required",
                (v) => (v && v.length >= 6) || "Minimum 6 characters",
                (v) => /\d/.test(v) || "Containing minimum 1 number",
            ],
            password_validation: [
                (v) => !!v || "Field is required",
                (v) => v == this.admin.password || "Incorrect re-type password",
            ],
        };
    },
    methods: {
        // checkPassword() {
        //     if (
        //         this.admin.password &&
        //         this.admin.password.length >= 6 &&
        //         /\d/.test(this.admin.password)
        //     ) {
        //         this.password.main_ok = true;
        //     } else {
        //         this.password.main_ok = false;
        //     }
        // },
        // checkConfirmPassword() {
        //     if (this.admin.password_validation === this.admin.password) {
        //         this.password.retype_ok = true;
        //     } else {
        //         this.password.retype_ok = false;
        //     }
        // },

        // validateForm() {
        //     this.checkPassword();
        //     this.checkConfirmPassword();
        // },

        postReset() {
            const bodyReq = {
                new_password: this.admin.password,
                confirm_password: this.admin.password_validation,
            };

            this.LOADING(true);

            axios
                .post(`${this.API}/reset-password/` + this.token, bodyReq)
                .then((response) => {
                    this.LOADING(false);
                    if (response.status == 200) {
                        this.alert(
                            "Password Successfully Reset!",
                            "Your password has been updated. You can now log in using your new credentials."
                        );

                        this.redirectToSignIn();
                        window.location.reload();
                    }
                })
                .catch((error) => {
                    const responseErr = error.response;
                    this.LOADING(false);

                    if (responseErr.status == 500) {
                        this.alert(
                            "Authentication Failed (Error Code : 500)",
                            "Please contact AIOI technical support to report this issue",
                            { color: "#d9534f" }
                        );
                        return;
                    }
                });
        },

        redirectToSignIn() {
            window.location = "admin#/login";
        },
    },
};
</script>
