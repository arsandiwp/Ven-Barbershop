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
                        <h1>Welcome Back!</h1>
                        <p>Sign in to continue to Template Project</p>
                    </v-container>
                    <v-form
                        @submit.prevent="send_data"
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
                            <v-container class="container-text-field-login">
                                <label for="login-password" class="text-bolder"
                                    >Password*</label
                                >
                                <v-text-field
                                    id="login-password"
                                    placeholder="Enter Password"
                                    :append-icon="
                                        password.visible
                                            ? 'mdi-eye'
                                            : 'mdi-eye-off'
                                    "
                                    @click:append="
                                        () =>
                                            (password.visible =
                                                !password.visible)
                                    "
                                    :type="
                                        password.visible ? 'text' : 'password'
                                    "
                                    outlined
                                    :success="password.ok"
                                    :error="password.ok"
                                    :rules="password_rules"
                                    v-model="admin.password"
                                    required
                                    autocomplete="current-password"
                                ></v-text-field>
                            </v-container>
                            <v-container id="container-remember-and-forget-pw">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        v-model="is_remembered"
                                        class="form-check-input"
                                        id="remember-me"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="remember-me"
                                        >Remember Me</label
                                    >
                                </div>
                                <p @click="redirectToForget" class="pointer">
                                    Forget Password?
                                </p>
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
                    <v-container id="container-footer-login">
                        <p>
                            Don't have an account?
                            <span
                                @click="redirectToSignUp"
                                id="sign-up"
                                class="pointer"
                            >
                                Sign Up
                            </span>
                        </p>
                    </v-container>
                </v-container>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {
    name: "Login",
    data() {
        return {
            admin: {},
            is_remembered: false,
            password: {
                visible: false,
                ok: false,
            },
            email_rules: [
                (v) => !!v || "Field is required",
                (v) =>
                    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
                        v
                    ) || "E-mail must be valid email address",
            ],
            email_ok: false,
            password_rules: [
                (v) => !!v || "Field is required",
                (v) => (v && v.length >= 6) || "Minimum 6 characters",
                (v) => /\d/.test(v) || "Containing minimum 1 number",
            ],
        };
    },
    methods: {
        checkPasswordPass() {
            if (
                this.admin.password &&
                this.admin.password.length >= 6 &&
                /\d/.test(this.admin.password)
            ) {
                this.password.ok = true;
            } else {
                this.password.ok = false;
            }
        },
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
        send_data() {
            this.checkPasswordPass();
            this.checkEmail();

            const isValid = this.$refs.form.validate();

            if (this.password.ok && this.email_ok && isValid) {
                const bodyReq = {
                    email: this.admin.email,
                    password: this.admin.password,
                };

                this.LOADING(true);

                axios
                    .post(`${this.API}/login`, bodyReq)
                    .then((response) => {
                        const responseData = response.data;
                        this.LOADING(false);

                        if (responseData.api_status == "fail") {
                            this.alert(
                                responseData.api_title,
                                responseData.api_message,
                                { color: "#d9534f" }
                            );
                            return;
                        }

                        const user = response.data.data[0];
                        localStorage.token = response.data.token;
                        localStorage.adminLogin = JSON.stringify(user);

                        if (this.is_remembered) {
                            const date = new Date();
                            date.setDate(date.getDate() + 1);
                            localStorage.setItem(
                                "active_until",
                                `${date.getDate()}/${
                                    date.getMonth() + 1
                                }/${date.getFullYear()}`
                            );
                        }

                        this.$router.replace({ name: "home" });
                        window.location.reload();
                    })
                    .catch((error) => {
                        const responseErr = error.response;
                        this.LOADING(false);

                        if (responseErr.status == 500) {
                            this.alert(
                                "Authentication Failed (Error Code : 500)",
                                "Please contact Template Project technical support to report this issue",
                                { color: "#d9534f" }
                            );
                            return;
                        }
                    });
            }
        },
        redirectToSignUp() {
            window.location = "admin#/register";
        },

        redirectToForget() {
            window.location = "admin#/forgot-password";
        },
    },
};
</script>
