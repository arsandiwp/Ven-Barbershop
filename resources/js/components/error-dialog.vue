<template>
    <v-dialog
        persistent
        v-model="err_dialog.show"
        min-width="300"
        max-width="450"
        @keydown.esc="$emit('dialog')"
    >
        <v-card class="rounded-card">
            <v-toolbar dark :color="err_dialog.type" dense flat>
                <v-toolbar-title class="white--text">{{
                    err_dialog.title
                }}</v-toolbar-title>
            </v-toolbar>
            <!-- <v-card-text :class="'title '+err_dialog.type+'--text'"><b>{{err_dialog.title}}</b></v-card-text> -->
            <v-card-text class="pt-3">
                <span
                    class="secondary--text"
                    v-html="err_dialog.subtitle"
                ></span
                ><br />
                <span class="subheading" v-html="err_dialog.message"></span>
            </v-card-text>
            <!-- <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="err_dialog.show = false" flat>Got it!</v-btn>
        </v-card-actions> -->
            <v-card-actions>
                <v-flex class="pa-2 d-flex justify-end">
                    <v-btn
                        v-if="err_dialog.customClose"
                        small
                        outlined
                        rounded
                        :color="err_dialog.type"
                        @click="close(err_dialog.customClose)"
                        class="right ma-2"
                        >Ok</v-btn
                    >
                    <v-btn
                        v-else
                        small
                        rounded
                        outlined
                        @click="err_dialog.show = false"
                        :color="err_dialog.type"
                        class="right ma-2"
                        >Got It</v-btn
                    >
                </v-flex>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "error-dialog",
    data() {
        return {
            //dialog: {show: null, text: ""},
        };
    },
    props: {
        err_dialog: {
            type: Object,
            required: false,
            default: () => ({ show: false, text: "", status: 0 }),
        },
    },
    methods: {
        close(fx) {
            this.err_dialog.show = false;
            fx();
        },
        initData() {
            this.err_dialog.type = this.err_dialog.type || "error";
        },
    },
    created() {
        //this.devLog("Error Dialog Component Created...");
        this.initData();
    },
    watch: {
        err_dialog(val) {
            this.err_dialog = val;
            this.initData();
        },
    },
    mounted() {
        this.devLog("error-dialog component mounted");
    },
};
</script>
