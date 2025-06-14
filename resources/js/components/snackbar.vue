<template>
    <v-snackbar
        v-model="snackbar.notification"
        right
        top
        :color="snackbar.type"
        :timeout="snackbar.timeout"
        small
    >
        <i
            ><b>{{ snackbar.notificationText }}</b></i
        >

        <v-btn
            v-if="snackbar.hasButton"
            text
            depressed
            small
            :color="snackbar.buttonColor"
            @click="snackbar.onClick"
            class="ml-4"
        >
            {{ snackbar.buttonText }}
        </v-btn>
    </v-snackbar>
</template>

<script>
export default {
    name: "snackbar",
    props: {
        snackbar: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {};
    },

    watch: {},
};
</script>
