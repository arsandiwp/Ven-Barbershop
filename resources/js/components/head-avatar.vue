<template>
  <v-toolbar rounded elevation="1">
    <v-toolbar-title v-if="title" class="title">{{ title }}</v-toolbar-title>
    <v-spacer></v-spacer>
    <v-form v-if="search" @submit.prevent="search.submit()">
      <v-text-field
        class="pt-0"
        dense
        v-model="search.value"
        prepend-inner-icon="search"
        label="Search"
        hide-details
        outlined
      ></v-text-field>
    </v-form>
    <v-spacer></v-spacer>
    <v-avatar size="40">
      <img
        src="https://randomuser.me/api/portraits/women/81.jpg"
        alt="Avatar"
      />
    </v-avatar>
    <div class="username">Jane Smith</div>
  </v-toolbar>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      required: true,
    },
    search: {
      type: Object,
      required: false,
    },
  },
};
</script>

<style></style>
