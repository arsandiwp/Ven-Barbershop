<template>
    <v-dialog
        persistent
        v-model="dialog"
        max-width="80%"
        @keydown.esc="$emit('cancel')"
    >
        <v-form
            @submit.prevent="validateForm()"
            v-model="valid"
            ref="form"
            autofocus
            lazy-validation
        >
            <v-card
                class="rounded-card"
                min-width="300"
                :width="model.width || '600'"
            >
                <v-toolbar dark color="primary" dense flat>
                    <v-toolbar-title class="white--text"
                        ><b>{{ model.title }}</b></v-toolbar-title
                    >
                </v-toolbar>

                <v-card-text class="pt-4 pb-0 px-4">
                    <v-container
                        width="100%"
                        pa-0
                        ma-0
                        style="border-bottom: 1px solid lightgray"
                    >
                        <v-layout
                            row
                            wrap
                            ma-0
                            pa-0
                            style="border-bottom: 1px solid lightgray"
                        >
                            <!-- <v-flex style="border-bottom: 1px solid lightgray;"  pa-0><b>100</b> Likes</v-flex> -->
                            <v-flex
                                :class="
                                    'xs' +
                                    12 / (model.grid || 1) +
                                    ' d-flex align-center text-center pa-1'
                                "
                                v-for="(input, idx) in model.inputs"
                                :key="'keyInput-' + input.value"
                            >
                                <!-- <label :for="input.value">{{input.label}}</label><br> -->
                                <v-select
                                    v-if="input.type == 'select'"
                                    :autofocus="idx == 0 ? true : false"
                                    :items="input.options"
                                    :item-text="input.itemText"
                                    :item-value="input.itemValue"
                                    :label="input.label"
                                    :required="input.required"
                                    outlined
                                    dense
                                    :placeholder="input.label"
                                    v-model="model.data[input.value]"
                                    :class="
                                        'white-bg ' +
                                        (input.required ? 'required' : '')
                                    "
                                ></v-select>
                                <v-file-input
                                    v-else-if="input.type == 'image'"
                                    accept="image/png, image/jpeg, image/bmp"
                                    :placeholder="
                                        input.placeholder || 'Select Image'
                                    "
                                    append-icon="mdi-camera"
                                    prepend-icon=""
                                    :label="input.label"
                                    outlined
                                    dense
                                    show-size
                                    v-model="model.data[input.value]"
                                ></v-file-input>
                                <v-textarea
                                    v-else-if="input.type == 'textarea'"
                                    :autofocus="idx == 0 ? true : false"
                                    :class="
                                        'white-bg ' +
                                        (input.required ? 'required' : '')
                                    "
                                    outlined
                                    :auto-grow="true"
                                    rows="3"
                                    hide-details
                                    v-model="model.data[input.value]"
                                    :label="input.label"
                                    :required="input.required"
                                ></v-textarea>
                                <v-text-field
                                    v-else-if="
                                        input.append &&
                                        input.append.includes('visibility')
                                    "
                                    :autofocus="idx == 0 ? true : false"
                                    :label="input.label"
                                    :required="input.required"
                                    v-model="model.data[input.value]"
                                    :type="input.type"
                                    dense
                                    outlined
                                    persistent-hint
                                    :placeholder="input.label"
                                    :class="
                                        'white-bg ' +
                                        (input.required ? 'required' : '')
                                    "
                                    :rules="getRules(input)"
                                    :append-icon="input.append"
                                    @click:append="
                                        (e) => {
                                            appendClick(e, idx);
                                        }
                                    "
                                ></v-text-field>

                                <v-text-field
                                    v-else
                                    :autofocus="idx == 0 ? true : false"
                                    :label="input.label"
                                    :required="input.required"
                                    v-model="model.data[input.value]"
                                    :type="input.type"
                                    dense
                                    outlined
                                    persistent-hint
                                    :placeholder="input.label"
                                    :class="
                                        'white-bg ' +
                                        (input.required ? 'required' : '')
                                    "
                                    :rules="getRules(input)"
                                ></v-text-field>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>

                <v-card-actions py-0 px-4 ma-0>
                    <v-flex class="ma-0 pa-2 d-flex justify-end">
                        <v-btn
                            outlined
                            small
                            color="primary"
                            class="right mx-2"
                            @click="$emit('cancel')"
                            dark
                            >Cancel</v-btn
                        >
                        <v-btn
                            raised
                            small
                            color="primary"
                            class="right mx-2"
                            type="submit"
                            >Submit</v-btn
                        >
                    </v-flex>
                </v-card-actions>
            </v-card>
        </v-form>
    </v-dialog>
</template>

<script>
export default {
    name: "add-dialog",
    components: {},
    props: {
        model: {
            type: Object,
            required: true,
        },
        dialog: {
            type: Boolean,
            required: true,
        },
    },
    beforeCreate() {},

    created() {
        this.devLog("User Add Dialog Created...");
        this.initData();
    },

    watch: {},

    data() {
        return {
            ready: false,
            valid: true,

            not_null_rule: [(v) => !!v || "Input is required"],
            pass_rules: [(v) => (!!v && v.length >= 6) || "Minimum 6 digit"],
            email_rules: [
                (v) =>
                    !v ||
                    /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
                        v
                    ) ||
                    "E-mail must be valid",
            ],
        };
    },

    methods: {
        getRules(input) {
            let rules1 =
                input.rules || (input.required ? this.not_null_rule : []);
            let rules2 =
                input.type == "email"
                    ? this.email_rules
                    : input.type == "password" ||
                      (!!input.append && input.append.includes("visibility"))
                    ? this.pass_rules
                    : [];
            let rules = rules1.concat(rules2);
            return rules;
        },
        validateForm() {
            this.devLog("validating");
            this.valid = this.$refs.form.validate();
            if (this.$refs.form.validate()) {
                this.$emit("submit");
            } else {
                // let snackbarOpt = {text: "Please check some Field!!", type:"error"};
                // this.simpleSnackbar(snackbarOpt.text, snackbarOpt.type);
                window.scrollTo(0, 0);
            }
        },

        initData() {
            this.devLog("Initialize User Add Dialog");
            //Do something here
        },

        appendClick(e, idx) {
            this.devLog(e);
            this.devLog(this.model.inputs[idx]);
            let input = this.model.inputs[idx];
            if (input.append == "visibility") {
                input.append = "visibility_off";
                input.type = "password";
            } else if (input.append == "visibility_off") {
                input.append = "visibility";
                input.type = "text";
            }
        },
    },
};
</script>
˝
