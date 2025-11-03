<template>
  <v-layout row wrap align-center>
    <!-- <ImagePreview
            :dialog="prev.dialog"
            :selectedImage="prev.image"
            @update:dialog="prev.dialog = $event"
        /> -->

    <v-flex xs4 sm4 md4 text-xs-right>
      <td v-if="list.headnote" :class="list.headnote.class">
        <span>
          <i> : {{ list.headnote.content }}</i>
        </span>
      </td>
    </v-flex>

    <v-flex xs12 text-xs-center>
      <v-sheet color="white" elevation="2" rounded>
        <v-container>
          <v-row>
            <v-col
              cols="12"
              sm="6"
              order-sm="2"
              class="text-right"
              align-self="center"
            >
              <!-- <v-btn
                                color="primary"
                                dark
                                @click="list.print(list)"
                                v-if="
                                    !!list.selected && list.selected.length > 0
                                "
                                >Print Selected</v-btn
                            >
                            <v-btn
                                color="primary"
                                dark
                                @click="list.sync()"
                                v-if="list.sync"
                                >Sync Doc</v-btn
                            > -->

              <v-btn
                class="elevation-1 button-add"
                color="primary"
                dark
                @click="list.add()"
                v-if="list.add"
                >New Data +</v-btn
              >
              <v-btn
                color="primary"
                dark
                @click="list.import()"
                v-if="list.import"
                >Import Data</v-btn
              >
              <v-btn
                color="primary"
                dark
                @click="list.export()"
                v-if="list.export"
                >Export Data</v-btn
              >
            </v-col>
            <v-col cols="12" sm="6">
              <v-row>
                <v-col cols="12" sm="6">
                  <v-form @submit.prevent="checkSearch()">
                    <v-text-field
                      :placeholder="
                        'Search: (Type at least ' +
                        (list.min_key != null ? list.min_key : '2') +
                        ' character then hit `Enter`)'
                      "
                      class="pt-0"
                      dense
                      v-model="list.search"
                      append-icon="mdi-magnify"
                      label="Search"
                      hide-details
                      outlined
                    ></v-text-field>
                  </v-form>
                </v-col>
              </v-row>
            </v-col>
          </v-row>

          <v-data-table
            :headers="list.headers"
            :items="list.datas"
            :loading="list.loading"
            :options.sync="list.options"
            rounded
            hide-default-footer
            class="mt-5"
            :items-per-page="itemsPerPage"
            :key="tableKey"
          >
            <template v-slot:item.index="{ index }">
              {{
                index + 1 + (list.options.page - 1) * list.options.itemsPerPage
              }}.
            </template>

            <template v-slot:item.parent="{ header, item }">
              {{
                item[header.parent] ? item[header.parent][header.field] : "-"
              }}
            </template>

            <template v-slot:[`item.total_user`]="{ item }">
              {{ item.total_user }} Users
            </template>

            <template v-slot:item.status="{ item }">
              <label
                :class="{
                  'error--text':
                    item.status == 'Inactive' || item.status == 'Cancelled',
                  'success--text':
                    item.status == 'Active' || item.status == 'Completed',
                  'warning--text': item.status == 'Pending',
                  'indigo--text':
                    item.status == 'Published' || item.status == 'Confirmed',
                  'teal--text': item.status == 'Draft',
                }"
                >{{ item.status }}</label
              >
            </template>

            <template v-slot:item.image="{ header, item, index }">
              <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                  <v-avatar
                    size="30"
                    style="cursor: pointer"
                    @click="
                      header.preview
                        ? preview(item[header.field])
                        : list.detail(item)
                    "
                    v-bind="attrs"
                    v-on="on"
                  >
                    <img
                      :src="
                        defaultImg[index] ||
                        (item[header.field] != null
                          ? item[header.field]
                          : '/img/default-img.jpg')
                      "
                      @error="handleImageError(index)"
                    />
                  </v-avatar>
                </template>
                <span>{{ header.preview ? "Preview" : "Detail" }}</span>
              </v-tooltip>
            </template>

            <template v-slot:item.actions="{ item }">
              <v-layout row wrap>
                <v-flex xs12 class="text-xs-center align-center">
                  <template v-for="(btn, idx) in list.actions">
                    <v-tooltip
                      bottom
                      v-if="btn.fx && checkRule(btn.rules, item)"
                      :key="'action_btn-' + idx"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-icon
                          small
                          :class="
                            'ma-1 ' +
                            (btn.color || 'primary') +
                            '--text border-debug ' +
                            (btn.class || '')
                          "
                          v-bind="attrs"
                          v-on="on"
                          @click.stop="btn.fx(item)"
                          >{{ btn.icon }}</v-icon
                        >
                      </template>
                      <span>{{ btn.label }}</span>
                    </v-tooltip>
                  </template>
                </v-flex>
              </v-layout>
            </template>
          </v-data-table>
          <v-divider class="my-0"></v-divider>
          <template>
            <v-row class="mt-0">
              <v-col cols="12" sm="6" class="mt-0">
                <v-row class="pagination">
                  <v-col cols="8" sm="8" align-self="center">
                    <p class="text-center my-auto">Showing Result Page</p>
                  </v-col>
                  <v-col cols="4" sm="4">
                    <v-select
                      dense
                      outlined
                      hide-details
                      :value="itemsPerPage"
                      @change="updateItemsPerPage"
                      :items="perPageChoices"
                    ></v-select>
                  </v-col>
                </v-row>
              </v-col>
              <v-col
                cols="12"
                sm="6"
                class="d-flex justify-end number-pagination"
              >
                <v-pagination
                  v-model="pagination.current"
                  :length="pagination.total"
                  :options.sync="list.options"
                  @input="onPageChange"
                ></v-pagination>
              </v-col>
            </v-row>
          </template>
        </v-container>
      </v-sheet>
    </v-flex>
  </v-layout>
</template>

<script>
// import ImagePreview from "./image-preview.vue";

export default {
  name: "DatatableComponent",
  components: {
    // ImagePreview,
  },
  data() {
    return {
      prev: {
        dialog: false,
        image: null,
      },
      page: 1,
      itemsPerPage: 10,
      perPageChoices: [5, 10, 20, 25, 50, 75],
      pagination: {
        current: 1,
        total: 0,
      },
      defaultImg: [],
      defaultIcn: [],
      tableKey: 0,
    };
  },

  props: {
    api: {
      type: String,
      required: true,
    },
    list: {
      type: Object,
      required: true,
    },
  },
  watch: {
    "list.search"(val) {
      if (val.length == 0) {
        this.readDataFromAPI();
      }
    },
  },
  methods: {
    preview(image_url) {
      if (!image_url) image_url = "/img/no_image.png";
      this.prev.image = image_url;
      this.prev.dialog = true;
    },

    handleImageError(idx) {
      this.$nextTick().then(() => {
        this.defaultImg[idx] = "/img/broken-image.png";
        this.tableKey++;
      });
    },

    updateItemsPerPage(value) {
      this.itemsPerPage = parseInt(value, 10);
      this.list.options.itemsPerPage = this.itemsPerPage;
      this.pagination.current = 1; // Reset to first page when changing items per page
      this.readDataFromAPI(); // Fetch data for the new page size
    },
    checkSearch() {
      if (this.list.search.length >= (this.list.min_key || 2)) {
        this.pagination.current = 1;
        this.readDataFromAPI();
      } else {
        this.alert(
          "Warning",
          "Please type atleast " +
            (this.list.min_key || 2) +
            " keyword to search!",
          { color: "warning", width: 500 }
        );
      }
    },
    //Reading data from API method.
    readDataFromAPI() {
      this.list.loading = true;
      const { page, itemsPerPage } = this.list.options;
      // let pageNumber = page - 1;
      let url =
        this.api +
        "per_page=" +
        itemsPerPage +
        "&page=" +
        this.pagination.current;
      if (this.list.search.length >= (this.list.min_key || 2)) {
        url = url + "&keyword=" + this.list.search;
      }

      if (this.list.selectRegion && this.list.selectRegion != "All Regions") {
        url = url + "&region=" + this.list.selectRegion;
      }

      if (this.list.filter_status && this.list.filter_status != "All") {
        url = url + "&status=" + this.list.filter_status;
      }

      axios
        .get(url, { headers: { Authorization: localStorage.token } })
        .then((response) => {
          this.devLog(response);
          //Then injecting the result to datatable parameters.
          this.list.loading = false;
          this.list.datas = response.data.data;
          this.pagination.current = response.data.current_page;
          this.pagination.total = response.data.last_page;
        })
        .catch((err) => {
          this.list.loading = false;
          if (!!err.response) {
            this.showErr(err.response, "Failed");
          } else {
            this.showErr({ status: "Code Error", statusText: err });
          }
        });
    },
    onPageChange() {
      this.readDataFromAPI();
    },
    //Check Actions Rule
    checkRule(rules, item) {
      let check_result = true;
      if (rules && rules.length > 0) {
        rules.forEach((rule) => {
          if (rule.negation) {
            //If rule negation true, than check result will false if both same
            if (item[rule.key] == rule.value) check_result = false;
          } else {
            //If rule negation false, than check result will false if both not same
            if (item[rule.key] != rule.value) check_result = false;
          }
        });
      }
      return check_result;
    },
  },
  //this will trigger in the onReady State
  mounted() {
    this.readDataFromAPI();
  },
};
</script>

<style scoped>
::v-deep .v-data-table-header {
  background-color: #f6f7fb;
}
</style>
