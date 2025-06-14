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
                        <h1>Forgot Password</h1>
                        <p>
                            Enter your email address, and we’ll send you a
                            message with steps to reset your password.
                        </p>
                    </v-container>

                    <v-form
                        @submit.prevent="postForgot"
                        lazy-validation
                        ref="form"
                    >
                        <v-container fluid id="container-input-area-login">
                            <v-container class="container-text-field-login">
                                <label for="login-email" class="text-bolder"
                                    >Email*</label
                                >
                                <v-text-field
                                    id="login-email"
                                    placeholder="Enter Email"
                                    outlined
                                    type="email"
                                    :error="email_ok"
                                    :success="email_ok"
                                    :rules="email_rules"
                                    v-model="admin.email"
                                    required
                                    autocomplete="username"
                                ></v-text-field>
                            </v-container>
                        </v-container>
                        <v-container id="container-button-login">
                            <v-btn
                                block
                                elevation="2"
                                id="btn-login"
                                type="submit"
                                >Sign In</v-btn
                            >
                        </v-container>
                    </v-form>
                </v-container>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: "forget-password",
    data() {
        return {
            admin: {},
            email_rules: [
                (v) => !!v || "Field is required",
                (v) =>
                    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
                        v
                    ) || "E-mail must be valid email address",
            ],
            email_ok: null,
        };
    },
    methods: {
        checkEmail() {
            if (
                this.admin.email &&
                /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
                    this.admin.email
                )
            ) {
                this.email_ok = true;
            } else {
                this.email_ok = false;
            }
        },

        onInputEmail() {
            this.checkEmail();
        },

        postForgot() {
            const bodyReq = {
                email: this.admin.email,
            };

            this.LOADING(true);

            axios
                .post(`${this.API}/forgot-password`, bodyReq)
                .then((response) => {
                    this.LOADING(false);
                    if (response.status == 200) {
                        this.alert(
                            "Forgot Password success",
                            "Please check your inbox for a message with instructions on how to reset your password. Make sure to check your spam folder if you don’t see it."
                        );
                    }
                })
                .catch((error) => {
                    const responseErr = error.response;
                    this.LOADING(false);

                    if (responseErr.status == 500) {
                        this.alert(
                            "Authentication Failed (Error Code : 500)",
                            "Please contact Template Project support to report this issue",
                            { color: "#d9534f" }
                        );
                        return;
                    }
                });
        },
    },
};
</script>
