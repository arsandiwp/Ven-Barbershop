<template>
    <v-dialog
        v-model="dialog"
        :max-width="options.width"
        :style="{ zIndex: options.zIndex }"
        @keydown.esc="cancel"
    >
        <v-card class="rounded-card" min-width="300">
            <v-toolbar dark :color="options.color" dense flat>
                <v-toolbar-title class="white--text">{{
                    title
                }}</v-toolbar-title>
            </v-toolbar>
            <v-card-text v-show="!!message" class="pa-4" min-height="100">{{
                message
            }}</v-card-text>
            <v-card-actions :class="!!message ? 'pt-0 pb-4' : 'pt-2 pb-2'">
                <v-spacer></v-spacer>
                <v-btn
                    v-if="options.close"
                    small
                    outlined
                    :color="options.color"
                    @click.native="options.close"
                    >&nbsp;&nbsp;OK&nbsp;&nbsp;</v-btn
                >
                <v-btn
                    v-else
                    small
                    outlined
                    :color="options.color"
                    @click.native="agree"
                    >&nbsp;&nbsp;OK&nbsp;&nbsp;</v-btn
                >
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

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
            this.resolve(true);
            this.dialog = false;
        },
        cancel() {
            this.resolve(false);
            this.dialog = false;
        },
    },
};
</script>
