<template>
    <div class="text-xs-center">
        <!-- <v-btn
      :disabled="dialog"
      :loading="dialog"
      class="white--text"
      color="purple darken-2"
      @click="dialog = true"
    >
      Start loading
    </v-btn> -->
        <v-dialog v-model="dialog" persistent width="300">
            <v-card color="primary">
                <v-card-text class="pt-4">
                    <slot name="title"><h5>Please wait ...</h5></slot>
                    <v-progress-linear
                        indeterminate
                        color="black"
                        class="mb-0 mt-2"
                    ></v-progress-linear>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "loading-dialog",
    props: {
        dialog: {
            type: Boolean,
            default: false,
            required: true,
        },
    },
    data() {
        return {};
    },

    watch: {
        dialog(val) {
            if (!val) return;
            //setTimeout(() => (this.dialog = false), 4000)
        },
    },
};
</script>
