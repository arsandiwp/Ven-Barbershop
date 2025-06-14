<template>
    <div class="px-0 py-0">
        <slot name="activator"></slot>
        <div class="" v-if="editable">
            <v-icon @click="launchFilePicker()" class="icon"
                >mdi-pencil-outline</v-icon
            >
        </div>
        <input
            type="file"
            ref="file"
            name="photo"
            accept="image/*"
            @change="onFileChange($event.target.name, $event.target.files)"
            style="display: none"
            @click="showLoadingImageUpload"
        />
    </div>
</template>

<style scoped>
.icon {
    color: white;
    background-color: black;
    padding: 10px;
    border-radius: 50%;
    margin-top: -150px;
    margin-left: 125px;
}
</style>

<script>
export default {
    name: "image-input",
    props: {
        editable: {
            type: Boolean,

            default: true,
        },
    },

    created() {
        this.devLog("Image Input Component created");
    },
    mounted() {
        this.devLog("Image Input Component mounted");
    },
    data: () => ({
        maxSize: 1024,
    }),
    props: {
        value: Object,
        editable: {
            type: Boolean,
            default: true,
        },
    },
    methods: {
        launchFilePicker() {
            this.$refs.file.click();
        },
        onFileChange(fieldName, file) {
            const { maxSize } = this;
            let imageFile = file[0];
            if (file.length > 0) {
                let size = imageFile.size / maxSize / maxSize;
                if (!imageFile.type.match("image.*")) {
                    this.alert("Failed", "Please choose an image file");
                } else if (size > 1) {
                    this.alert(
                        "Failed",
                        "Your file is too big! Please select an image under 1MB"
                    );
                } else {
                    let formData = new FormData();
                    let imageURL = URL.createObjectURL(imageFile);
                    let editable = true;
                    let imgFile = null;

                    let reader = new FileReader();
                    reader.onload = (e) => {
                        imgFile = file;
                        this.$emit("input", {
                            imageURL,
                            editable,
                            imgFile: file,
                        });
                    };
                    reader.readAsDataURL(imageFile);
                }
            }
        },

        showLoadingImageUpload() {
            document.body.onfocus = this.checkIfFileDialogClosed;
            this.LOADING(true);
            this.$refs.file.value = "";
        },
        checkIfFileDialogClosed() {
            if (!this.$refs.file.value.length) {
                this.LOADING(false);
            }
            document.body.onfocus = null;
        },
    },
};
</script>
