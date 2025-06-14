<template>
    <v-container grid-list-md pt-10>
        <HeaderBar />

        <v-row justify="center" v-if="loading">
            <v-progress-circular
                indeterminate
                color="primary"
                size="64"
            ></v-progress-circular>
        </v-row>

        <v-layout row wrap align-center v-else>
            <v-flex xs12 text-xs-center>
                <v-row>
                    <v-col>
                        <v-sheet color="white" elevation="2" rounded>
                            <v-container>
                                <v-row
                                    justify="space-between"
                                    style="padding: 10px"
                                >
                                    <v-col
                                        cols="2"
                                        class="pl-0 py-0"
                                        style="
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                        "
                                    >
                                        <span
                                            style="
                                                background-color: #f1f5fd;
                                                padding: 19px;
                                                border-radius: 50%;
                                            "
                                        >
                                            <v-icon
                                                color="primary"
                                                x-large
                                                v-if="
                                                    $vuetify.breakpoint.lgAndUp
                                                "
                                                >mdi-turbine</v-icon
                                            >
                                            <v-icon color="primary" large v-else
                                                >mdi-turbine</v-icon
                                            >
                                        </span>
                                    </v-col>
                                    <v-col
                                        cols="9"
                                        class="px-0 py-0"
                                        :class="{
                                            'pl-2': $vuetify.breakpoint
                                                .mdAndDown,
                                        }"
                                    >
                                        <p
                                            class="subheading"
                                            style="margin-bottom: 10px"
                                        >
                                            Model AI Setting
                                        </p>
                                        <v-autocomplete
                                            class="scrollable-autocomplete"
                                            menu-props="lazy"
                                            v-model="item.model_id"
                                            :hint="
                                                !item.isEditing
                                                    ? 'Click Pencil Icon to edit'
                                                    : 'Click Disk Icon to save'
                                            "
                                            :items="model_list"
                                            :readonly="!item.isEditing"
                                            persistent-hint
                                            outlined
                                            single-line
                                            item-value="value"
                                            item-text="text"
                                        >
                                        </v-autocomplete>
                                    </v-col>
                                    <v-col
                                        cols="1"
                                        class="pr-0 py-0"
                                        style="
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                        "
                                    >
                                        <v-slide-x-reverse-transition
                                            mode="out-in"
                                            pt-2
                                            xs3
                                        >
                                            <v-tooltip bottom>
                                                <template
                                                    v-slot:activator="{ on }"
                                                >
                                                    <button
                                                        style="min-width: 0"
                                                        v-on="on"
                                                        text
                                                        icon
                                                        color="green darken-1"
                                                        class="white--text ma-1"
                                                        @click="
                                                            toggleEdit(item)
                                                        "
                                                    >
                                                        <v-icon
                                                            medium
                                                            :color="'primary'"
                                                            v-text="
                                                                item.isEditing
                                                                    ? 'mdi-content-save-outline'
                                                                    : 'mdi-pencil-outline'
                                                            "
                                                        ></v-icon>
                                                    </button>
                                                </template>
                                                <span>
                                                    {{
                                                        item.isEditing
                                                            ? "Save Update"
                                                            : "Edit Model AI"
                                                    }}</span
                                                >
                                            </v-tooltip>
                                        </v-slide-x-reverse-transition>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-sheet>
                    </v-col>
                    <v-col>
                        <v-sheet color="white" elevation="2" rounded>
                            <v-container>
                                <v-row
                                    justify="space-between"
                                    style="padding: 10px"
                                >
                                    <v-col
                                        cols="2"
                                        class="pl-0 py-0"
                                        style="
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                        "
                                    >
                                        <span
                                            style="
                                                background-color: #f1f5fd;
                                                padding: 19px;
                                                border-radius: 50%;
                                            "
                                        >
                                            <v-icon
                                                color="primary"
                                                x-large
                                                v-if="
                                                    $vuetify.breakpoint.lgAndUp
                                                "
                                                >mdi-clock-time-eight-outline</v-icon
                                            >
                                            <v-icon color="primary" large v-else
                                                >mdi-clock-time-eight-outline</v-icon
                                            >
                                        </span>
                                    </v-col>
                                    <v-col
                                        cols="9"
                                        class="px-0 py-0"
                                        :class="{
                                            'pl-2': $vuetify.breakpoint
                                                .mdAndDown,
                                        }"
                                    >
                                        <p
                                            class="subheading"
                                            style="margin-bottom: 10px"
                                        >
                                            Expired Chat Setting
                                        </p>
                                        <!-- <v-autocomplete
                                            class="scrollable-autocomplete"
                                            menu-props="lazy"
                                            v-model="item.model_id"
                                            :hint="
                                                !item.isEditing
                                                    ? 'Click Pencil Icon to edit'
                                                    : 'Click Disk Icon to save'
                                            "
                                            :items="model_list"
                                            :readonly="!item.isEditing"
                                            persistent-hint
                                            outlined
                                            single-line
                                            item-value="value"
                                            item-text="text"
                                        >
                                        </v-autocomplete> -->
                                        <v-text-field
                                            v-model="item.expires_at"
                                            :readonly="!item.isEditingExpires"
                                            outlined
                                            :hint="
                                                !item.isEditingExpires
                                                    ? 'Click Pencil Icon to edit'
                                                    : 'Click Disk Icon to save'
                                            "
                                            persistent-hint
                                            single-line
                                        ></v-text-field>
                                    </v-col>
                                    <v-col
                                        cols="1"
                                        class="pr-0 py-0"
                                        style="
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                        "
                                    >
                                        <v-slide-x-reverse-transition
                                            mode="out-in"
                                            pt-2
                                            xs3
                                        >
                                            <v-tooltip bottom>
                                                <template
                                                    v-slot:activator="{ on }"
                                                >
                                                    <button
                                                        style="min-width: 0"
                                                        v-on="on"
                                                        text
                                                        icon
                                                        color="green darken-1"
                                                        class="white--text ma-1"
                                                        @click="
                                                            toggleEdit(item)
                                                        "
                                                    >
                                                        <v-icon
                                                            medium
                                                            :color="'primary'"
                                                            v-text="
                                                                item.isEditingExpires
                                                                    ? 'mdi-content-save-outline'
                                                                    : 'mdi-pencil-outline'
                                                            "
                                                        ></v-icon>
                                                    </button>
                                                </template>
                                                <span>
                                                    {{
                                                        item.isEditingExpires
                                                            ? "Save Update"
                                                            : "Edit Expired Chat"
                                                    }}</span
                                                >
                                            </v-tooltip>
                                        </v-slide-x-reverse-transition>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-sheet>
                    </v-col>
                </v-row>
            </v-flex>
        </v-layout>
        <ChatWidget />
    </v-container>
</template>

<script>
import ChatWidget from "../ChatWidget2.vue";
import axios from "axios";

export default {
    data() {
        return {
            // name_engine: "",
            loading: false,
            item: {
                isEditing: false,
                isEditingExpires: false,
                model_id: null,
            },
            model_list: [],
            original_model_id: null,
        };
    },
    components: {
        ChatWidget,
    },
    props: {
        api: {
            type: String,
            required: true,
        },
        apiCUD: {
            type: String,
            required: true,
        },
    },
    mounted() {
        this.initAxio();
    },
    methods: {
        initAxio() {
            this.loading = true;

            axios
                .get(this.api, {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    if (
                        response.data.settings &&
                        response.data.settings.length > 0
                    ) {
                        this.item = response.data.settings[0];
                        if (this.item.isEditing === undefined) {
                            this.$set(this.item, "isEditing", false);
                        }
                        if (this.item.isEditingExpires === undefined) {
                            this.$set(this.item, "isEditingExpires", false);
                        }
                        console.log("ini item", this.item.model_id);
                        // this.name_engine = response.data.settings[0].name;
                        this.original_model_id = this.item.model_id;

                        this.fetchModelList();
                    }
                })
                .catch((error) => {
                    console.error("Error fetching settings:", error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        fetchModelList() {
            this.loading = true;
            axios
                .get(this.API + "/models", {
                    headers: { Authorization: localStorage.token },
                })
                .then((response) => {
                    if (response.data && response.data.models) {
                        this.model_list = response.data.models.map((model) => ({
                            value: model.id,
                            text: model.id,
                            name: model.name,
                        }));
                        console.log(
                            "Model list loaded:",
                            this.model_list.length
                        );
                    } else {
                        console.error(
                            "Invalid model data format:",
                            response.data
                        );
                    }
                })
                .catch((error) => {
                    console.error("Error fetching models:", error);
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        toggleEdit(item) {
            if (item.isEditing) {
                this.saveChanges(item);
            } else {
                this.$set(item, "isEditing", true);
                this.original_model_id = item.model_id;
            }

            if (item.isEditingExpires) {
                this.saveChanges(item);
            } else {
                this.$set(item, "isEditingExpires", true);
                this.expires_at = item.expires_at;
            }
        },

        saveChanges(item) {
            const bodyReq = {
                name: this.item.model_id,
                model_id: this.item.model_id,
                expires_at: this.item.expires_at,
            };
            axios
                .put(this.api + "/" + 1, bodyReq, {
                    headers: { Authorization: localStorage.token },
                })
                // .put(
                //     this.apiCUD,
                //     {
                //         id: item.id,
                //         model_id: item.model_id,
                //     },
                //     {
                //         headers: { Authorization: localStorage.token },
                //     }
                // )
                .then((response) => {
                    window.location.reload();
                    // Jika berhasil, nonaktifkan mode editing
                    // this.$set(item, "isEditing", false);
                    // console.log("Changes saved successfully");

                    // Opsional: tampilkan notifikasi sukses
                    // if (this.$root.$emit) {
                    //     this.$root.$emit("show-snackbar", {
                    //         text: "Model engine updated successfully",
                    //         color: "success",
                    //     });
                    // }
                })
                .catch((error) => {
                    console.error("Error saving changes:", error);
                    // Kembalikan ke nilai awal jika gagal
                    item.model_id = this.original_model_id;

                    // Opsional: tampilkan notifikasi error
                    if (this.$root.$emit) {
                        this.$root.$emit("show-snackbar", {
                            text: "Failed to update model engine",
                            color: "error",
                        });
                    }
                });
        },
    },
};
</script>

<style scoped>
.scrollable-autocomplete {
    max-height: 300px;
    overflow-y: auto;
}
</style>
