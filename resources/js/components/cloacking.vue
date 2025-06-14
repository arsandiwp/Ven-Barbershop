<template>
    <div class="cloacking">
        <v-dialog v-model="dialog" persistent> </v-dialog>
    </div>
</template>

<script>
export default {
    name: "cloacking",
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
