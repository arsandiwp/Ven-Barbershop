<template>
    <v-dialog
        v-model="internalDialog"
        :width="img_width"
        allow-overflow
        @input="setDynamicHeight"
        content-class="grey lighten-2 rounded-dlg"
        @click:outside="closeDialog"
        persistent
    >
        <v-img width="100%" height="100%" :src="selectedImage" contain></v-img>
        <v-icon @click="closeDialog" color="error" class="my-2"
            >mdi-close</v-icon
        >
    </v-dialog>
</template>

<style>
.rounded-dlg {
    border-radius: 15px !important;
}
</style>
<script>
export default {
    props: {
        dialog: Boolean,
        selectedImage: String,
    },
    data() {
        return {
            img_width: "75vw",
            dynamicHeight: "auto",
            internalDialog: this.dialog,
        };
    },
    watch: {
        dialog(newValue) {
            this.internalDialog = newValue;
        },
        selectedImage() {
            this.setDynamicHeight();
        },
    },
    methods: {
        closeDialog() {
            this.$emit("update:dialog", false);
        },
        setDynamicHeight() {
            const img = new Image();
            img.src = this.selectedImage;

            img.onload = () => {
                const originalWidth = img.width;
                const originalHeight = img.height;

                const dialogWidth = window.innerWidth * 0.75;

                let calculatedHeight =
                    (originalHeight * dialogWidth) / originalWidth;

                const maxHeight = window.innerHeight * 0.85;

                if (calculatedHeight > maxHeight) {
                    calculatedHeight = maxHeight;
                }
                this.img_width = img.width;
                this.dynamicHeight = `${calculatedHeight}px`;
            };
        },
    },
    mounted() {
        this.setDynamicHeight();
    },
};
</script>
