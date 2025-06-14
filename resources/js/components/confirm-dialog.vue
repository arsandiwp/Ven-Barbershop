<template>
    <v-dialog
        class="rounded-card"
        v-model="dialog"
        min-width="300"
        :max-width="options.width"
        :style="{ zIndex: options.zIndex }"
        @keydown.esc="cancel"
    >
        <v-card class="rounded-lg" color="grey lighten-3">
            <v-toolbar
                dark
                :color="options.color"
                dense
                flat
                v-if="!options.nohead"
            >
                <v-toolbar-title class="white--text">{{
                    title
                }}</v-toolbar-title>
            </v-toolbar>
            <v-card-text v-show="!!message" class="pa-4 pt-6">
                <span
                    class="secondary--text"
                    style="font-size: 12pt"
                    v-html="message"
                ></span
                ><br />
                <span
                    class="subheading"
                    style="font-size: 11pt"
                    v-if="!!options.message"
                    v-html="options.message"
                ></span>
            </v-card-text>
            <!-- <v-card-text v-show="!!message" class="pa-4"><b style="font-size:12pt;">{{ message }}</b>
          <span v-if="!!options.message">{{ options.message }}</span>
        </v-card-text> -->
            <!-- <v-card-actions class="pt-0 pb-4"> -->
            <v-card-actions class="rounded-card">
                <v-flex class="pa-2 confirm_dialog_btn d-flex justify-end">
                    <!-- <v-btn v-if="err_dialog.customClose" raised rounded color="primary" @click="err_dialog.customClose" class="right ma-2">Ok</v-btn> -->
                    <v-btn
                        small
                        outlined
                        :color="options.color"
                        @click.native="agree"
                        class="right ma-2"
                        >{{ options.agree ? options.agree : "Yes" }}</v-btn
                    >
                    <v-btn
                        small
                        light
                        raised
                        :color="options.color"
                        @click.native="cancel"
                        class="right ma-2 elevation-1"
                        >{{ options.cancel ? options.cancel : "Cancel" }}</v-btn
                    >
                </v-flex>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.confirm_dialog_btn .theme--light.v-btn.v-btn--outlined.v-btn--text {
    border-color: black;
}
/* .v-overlay__scrim{
      opacity: 0.5 !important;
      background-color: rgb(255, 255, 255) !important;
      border-color: rgb(33, 33, 33);
    } */
.rounded-card {
    border-radius: 10px !important;
}
</style>

<script>
/**
 * Vuetify Confirm Dialog component
 *
 * Insert component where you want to use it:
 * <confirm ref="confirm"></confirm>
 *
 * Call it:
 * this.$refs.confirm.open('Delete', 'Are you sure?', { color: 'red' }).then((confirm) => {})
 * Or use await:
 * if (await this.$refs.confirm.open('Delete', 'Are you sure?', { color: 'red' })) {
 *   // yes
 * }
 * else {
 *   // cancel
 * }
 *
 * Alternatively you can place it in main App component and access it globally via this.$root.$confirm
 * <template>
 *   <v-app>
 *     ...
 *     <confirm ref="confirm"></confirm>
 *   </v-app>
 * </template>
 *
 * mounted() {
 *   this.$root.$confirm = this.$refs.confirm.open
 * }
 */
export default {
    name: "confirm-dialog",
    data: () => ({
        dialog: false,
        resolve: null,
        reject: null,
        message: null,
        title: null,
        options: {
            color: "primary",
            width: 400,
            zIndex: 200,
        },
    }),
    methods: {
        open(title, message, options) {
            this.dialog = true;
            this.title = title;
            this.message = message;
            this.options = Object.assign(this.options, options);
            return new Promise((resolve, reject) => {
                this.resolve = resolve;
                this.reject = reject;
            });
        },
        agree() {
            this.options.message = null;
            this.resolve(true);
            this.dialog = false;
        },
        cancel() {
            this.options.message = null;
            this.resolve(false);
            this.dialog = false;
        },
    },
    mounted() {
        //this.devLog('confirm-dialog component mounted!');
    },
};
</script>
