<template>
    <div class="text-center">
        <v-dialog v-model="dialog" width="50vw" height="50vh" persistent>
            <v-card :color="default_opt.bg_color">
                <v-system-bar
                    color="primary"
                    dark
                    class="pa-6 white--text text--lighten-1"
                >
                    <h3 class="pt-2"><b>Change Password</b></h3>
                    <v-spacer></v-spacer>

                    <v-hover>
                        <v-btn
                            color="primary"
                            icon
                            @click="closeDialog"
                            slot-scope="{ hover }"
                        >
                            <v-icon
                                :class="hover ? 'error--text' : 'white--text'"
                                >mdi-close</v-icon
                            >
                        </v-btn>
                    </v-hover>
                </v-system-bar>

                <v-card-text class="pt-4">
                    <v-form
                        ref="form_pass"
                        v-model="valid"
                        lazy-validation
                        @submit.prevent="validate()"
                    >
                        <div class="px-0 py-0">
                            <v-flex xs12 class="mt-5 px-1">
                                <v-text-field
                                    v-if="!id"
                                    label="Old Password"
                                    hint="Your registered Password (Min 6 character)"
                                    min="6"
                                    :append-icon="
                                        !pass.old.visible
                                            ? 'mdi-eye'
                                            : 'mdi-eye-off'
                                    "
                                    @click:append="
                                        () =>
                                            (pass.old.visible =
                                                !pass.old.visible)
                                    "
                                    v-model="pass.old.value"
                                    @input="checkOldPass()"
                                    :success="pass.old.ok == 1 ? true : false"
                                    :error="pass.old.ok == -1 ? true : false"
                                    :rules="pass.old.rule"
                                    :type="
                                        !pass.old.visible ? 'password' : 'text'
                                    "
                                ></v-text-field>
                                <v-text-field
                                    label="New Password"
                                    hint="Min 6 characters"
                                    min="6"
                                    :append-icon="
                                        !pass.new.visible
                                            ? 'mdi-eye'
                                            : 'mdi-eye-off'
                                    "
                                    @click:append="
                                        () =>
                                            (pass.new.visible =
                                                !pass.new.visible)
                                    "
                                    v-model="pass.new.value"
                                    @input="checkNewPass()"
                                    :success="pass.new.ok == 1 ? true : false"
                                    :error="pass.new.ok == -1 ? true : false"
                                    :rules="pass.new.rule"
                                    :type="
                                        !pass.new.visible ? 'password' : 'text'
                                    "
                                ></v-text-field>
                                <v-text-field
                                    label="Confirm New Password"
                                    hint="The same as your inputed New Password"
                                    min="6"
                                    :append-icon="
                                        !pass.retype.visible
                                            ? 'mdi-eye'
                                            : 'mdi-eye-off'
                                    "
                                    @click:append="
                                        () =>
                                            (pass.retype.visible =
                                                !pass.retype.visible)
                                    "
                                    v-model="pass.retype.value"
                                    @input="checkRePass()"
                                    :success="pass.new.ok == 1 ? true : false"
                                    :error="pass.new.ok == -1 ? true : false"
                                    :rules="pass.retype.rule"
                                    :type="
                                        !pass.retype.visible
                                            ? 'password'
                                            : 'text'
                                    "
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 py-4>
                                <v-btn
                                    raised
                                    rounded
                                    outlined
                                    color="primary"
                                    class="right"
                                    type="submit"
                                    >Submit</v-btn
                                >
                                <!-- <v-btn outline round color="primary" class="right" @click="updateNavMaps(false)" dark>Cancel</v-btn> -->
                                <br />
                            </v-flex>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
function passField() {
    return {
        value: null,
        visible: false,
        ok: 0,
        rule: [(v) => !!v || "Input Required"],
    };
}
export default {
    name: "change-password-dialog",
    props: {
        dialog: {
            type: Boolean,
            required: true,
        },
        item: {
            type: Object,
            required: false,
        },
    },
    created() {},

    data() {
        return {
            id: null,
            valid: true,
            default_opt: {
                type: "circular",
                width: "300",
                height: "auto",
                bg_color: "white",
                color: "primary",
            },
            oldrule: [
                (v) => !!v || "Input Required",
                (v) => !!v || "Input Required",
            ],
            pass: { old: passField(), new: passField(), retype: passField() },
            post_data: {},
        };
    },

    methods: {
        closeDialog() {
            this.$emit("close");
        },
        checkOldPass() {
            if (this.pass.old.value && !this.id) {
                if (!!this.pass.old.value && this.pass.old.value.length >= 6) {
                    this.pass.old.ok = 1;
                } else if (
                    !!this.pass.old.value &&
                    this.pass.old.value.length == 0
                ) {
                    this.pass.old.ok = 0;
                } else {
                    this.pass.old.ok = -1;
                }
            } else if (this.id) {
                this.pass.old.ok = 1;
            }
        },

        checkNewPass() {
            this.pass.retype.value = "";
            this.checkOldPass();
        },

        checkRePass() {
            if (this.pass.new.value) {
                if (
                    !!this.pass.new.value &&
                    this.pass.new.value == this.pass.retype.value &&
                    this.pass.new.value.length >= 6
                ) {
                    this.pass.new.ok = 1;
                } else {
                    this.pass.new.ok = -1;
                }
                this.checkOldPass();
            }
        },

        validate() {
            this.valid = this.$refs.form_pass.validate();
            if (this.valid) {
                this.submit();
            } else {
                document.getElementsByClassName(
                    "v-dialog--active"
                )[0].scrollTop = 0;
            }
        },

        submit() {
            if (
                (this.pass.old.value == "" && this.pass.old.ok != -1) ||
                (this.pass.new.ok == 1 && this.pass.old.ok == 1)
            ) {
                if (this.pass.old.ok == 1) {
                    this.putPassword();
                }
            }
        },

        putPassword() {
            this.LOADING(true);
            let apiUrl = this.API + "/user/change-password";
            if (this.id && this.id != 0) {
                apiUrl += "/" + this.id;
            }
            this.devLog("Updating User Password: put to " + apiUrl);
            if (this.item) {
                this.post_data = this.item;
            }
            this.post_data.old_password = this.pass.old.value;
            this.post_data.new_password = this.pass.new.value;
            this.post_data.confirm_password = this.pass.retype.value;

            axios
                .put(apiUrl, this.post_data, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    this.devLog(
                        "Loading " +
                            apiUrl +
                            " - Result Status: " +
                            response.status
                    );

                    this.simpleSnackbar("Password berhasil diubah!", "success");
                })
                .catch((err) => {
                    if (err.response) {
                        this.showErr(err.response, "Failed");
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                })
                .finally(() => {
                    this.$refs.form_pass.reset();
                    this.LOADING(false);
                    this.$emit("close");
                });
        },
    },

    watch: {
        dialog(val) {
            if (val) {
                if (this.item && this.item.id) {
                    this.id = this.item.id;
                }
                this.pass = {
                    old: passField(),
                    new: passField(),
                    retype: passField(),
                };
            }
        },
    },
};
</script>
