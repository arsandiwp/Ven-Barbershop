<template>
    <v-container grid-list-md pt-10>
        <HeaderBar />
        <v-layout row wrap align-center>
            <v-flex xs12 text-xs-center>
                <v-sheet color="white" elevation="2" rounded>
                    <v-container>
                        <v-file-input
                            v-model="files"
                            color="primary"
                            counter
                            label="Upload File Excel"
                            placeholder="Pilih file Excel"
                            prepend-icon="mdi-paperclip"
                            outlined
                            accept=".xls,.xlsx"
                            @change="validateFile"
                        ></v-file-input>

                        <p>
                            <i
                                >Sampel Document :
                                <span
                                    style="
                                        color: blue;
                                        text-decoration: underline;
                                        cursor: pointer;
                                    "
                                    @click="downlaodTempalte"
                                    >Download here!</span
                                ></i
                            >
                        </p>

                        <v-btn color="primary" right dark @click="uploadFile">
                            Upload File
                        </v-btn>
                    </v-container>
                </v-sheet>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import axios from "axios";

export default {
    data() {
        return {
            files: null, // Harus singular karena hanya satu file
        };
    },
    methods: {
        validateFile() {
            if (!this.files) return;

            const allowedExtensions = ["xls", "xlsx"];
            const fileExtension = this.files.name
                .split(".")
                .pop()
                .toLowerCase();

            if (!allowedExtensions.includes(fileExtension)) {
                alert("Hanya file Excel (.xls, .xlsx) yang diperbolehkan!");
                this.files = null;
            }
        },

        // uploadFile() {
        //     if (!this.files) {
        //         alert("Pilih file terlebih dahulu!");
        //         return;
        //     }

        //     let formData = new FormData();
        //     formData.append("file", this.files);

        //     axios
        //         .post(`${this.API}/users-import`, formData, {
        //             headers: {
        //                 "Content-Type": "multipart/form-data",
        //             },
        //         })
        //         .then((response) => {
        //             this.$router.push("/users");
        //         })
        //         .catch((err) => {
        //             if (!!err.response) {
        //                 this.showErr(err.response, "Failed");
        //             } else {
        //                 this.showErr({ status: "Code Error", statusText: err });
        //             }
        //         });
        // },

        uploadFile() {
            if (!this.files) {
                alert("Pilih file terlebih dahulu!");
                return;
            }

            let formData = new FormData();
            formData.append("file", this.files);

            axios
                .post(`${this.API}/users-import`, formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                })
                .then((response) => {
                    this.$router.push("/users");
                })
                .catch((err) => {
                    if (err.response) {
                        const errors = err.response.data.errors;
                        if (errors && errors.length > 0) {
                            let errorMessage =
                                "Terjadi kesalahan pada import data: \n";
                            errors.forEach((error) => {
                                errorMessage += `Baris: ${error.row}, ${
                                    error.attribute
                                }: ${error.errors.join(", ")}\n`;
                            });
                            alert(errorMessage);
                        } else {
                            this.showErr(err.response, "Failed");
                        }
                    } else {
                        this.showErr({ status: "Code Error", statusText: err });
                    }
                });
        },

        downlaodTempalte() {
            window.open(`${this.API + "/users-template"}`, "_blank");
        },
    },
};
</script>
