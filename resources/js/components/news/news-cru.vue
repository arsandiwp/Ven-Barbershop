<template>
  <v-container grid-list-xs pt-10>
    <HeaderBar back :title="type + ' News'" />
    <ImagePreview
      :dialog="prev.dialog"
      :selectedImage="prev.image"
      @update:dialog="prev.dialog = $event"
    />
    <div ref="top"></div>
    <v-row v-if="ready">
      <v-col cols="12">
        <v-card class="pa-2 pt-6">
          <v-form
            ref="form"
            @submit.prevent="validate(model.status)"
            v-model="valid"
            lazy-validation
          >
            <v-container grid-list-xs>
              <v-row row wrap class="px-2 pt-2" align="start">
                <label
                  :class="{
                    'news-label-full': $vuetify.breakpoint.xs,
                    'news-label': $vuetify.breakpoint.smAndUp,
                  }"
                  for="title"
                  >Main Title:</label
                >
                <v-textarea
                  outlined
                  dense
                  :rows="1"
                  no-resize
                  auto-grow
                  placeholder="Main Title"
                  v-model="model.title"
                  :readonly="!editable"
                  :rules="not_null_rules"
                ></v-textarea>
              </v-row>

              <v-row row wrap class="px-2 pt-2" align="start">
                <!-- <v-col class="pa-0 ma-0" cols="12" lg="6">
                                <v-row class="pa-0 ma-0">
                                    <label :class="{'news-label-full': $vuetify.breakpoint.xs, 'news-label': $vuetify.breakpoint.smAndUp}" for="category_id">Category:</label>
                                    <v-select outlined dense
                                        placeholder="Category" v-model="model.category_id"
                                        :readonly="!editable"
                                        :rules="not_null_rules"
                                        :items="categories"
                                        :class="{'container-field-full': $vuetify.breakpoint.xs, 'container-field': $vuetify.breakpoint.smAndUp}"
                                    ></v-select>
                                </v-row>
                            </v-col> -->

                <v-col class="pa-0 ma-0" cols="12" lg="12">
                  <v-row class="pa-0 ma-0">
                    <label
                      :class="{
                        'news-label-full': $vuetify.breakpoint.xs,
                        'news-label': $vuetify.breakpoint.smAndUp,
                      }"
                      for="date"
                      >Date:</label
                    >
                    <v-menu
                      v-model="date_menu"
                      :close-on-content-click="false"
                      max-width="290"
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-text-field
                          outlined
                          dense
                          placeholder="Date"
                          :value="computedDateFormattedDatefns"
                          readonly
                          v-bind="attrs"
                          v-on="on"
                          :rules="not_null_rules"
                          append-outer-icon="mdi-calendar"
                          :class="{
                            'container-field-full': $vuetify.breakpoint.xs,
                            'container-field': $vuetify.breakpoint.smAndUp,
                          }"
                        ></v-text-field>
                      </template>
                      <v-date-picker
                        v-model="model.date"
                        @change="date_menu = false"
                      ></v-date-picker>
                    </v-menu>
                  </v-row>
                </v-col>
              </v-row>

              <!-- <v-row row wrap class="px-2 pt-2" align="start">
                            <label :class="{'news-label-full': $vuetify.breakpoint.xs, 'news-label': $vuetify.breakpoint.smAndUp}" for="location">Location:</label>
                            <v-textarea outlined dense
                                :rows="1" no-resize auto-grow
                                placeholder="Location" v-model="model.location"
                                :readonly="!editable"
                                :rules="not_null_rules"
                            >
                            </v-textarea>
                        </v-row> -->

              <v-row row wrap class="px-2 pt-2" align="start">
                <label
                  :class="{
                    'news-label-full': $vuetify.breakpoint.xs,
                    'news-label': $vuetify.breakpoint.smAndUp,
                  }"
                  for="title"
                  >Custom Url:</label
                >
                <v-text-field
                  outlined
                  dense
                  :placeholder="'' + (model.id || 'id')"
                  v-model="model.custom_url"
                  :readonly="!editable"
                  :prefix="getPrefix()"
                  :append-icon="
                    model.custom_url || model.id ? 'mdi-content-copy' : ''
                  "
                  @click:append="copyUrl()"
                  :class="{
                    'container-field-full': $vuetify.breakpoint.xs,
                    'container-field': $vuetify.breakpoint.smAndUp,
                  }"
                  :rules="not_null_rules"
                ></v-text-field>
              </v-row>

              <v-row row wrap class="px-2 pt-2" align="start">
                <label
                  :class="{
                    'news-label-full': $vuetify.breakpoint.xs,
                    'news-label': $vuetify.breakpoint.smAndUp,
                  }"
                  for="image_url"
                  >Main Picture:</label
                >
                <!-- :style="{'background-image': (model.image_url || '') }"  -->
                <div
                  id="container-img-uploader"
                  v-if="editable"
                  :class="{
                    'container-img-full': $vuetify.breakpoint.xs,
                    'container-img': $vuetify.breakpoint.smAndUp,
                    'red-border': !file_ok,
                  }"
                >
                  <v-img
                    v-if="model.image_url"
                    class="main-img"
                    width="100%"
                    contain
                    :src="model.image_url"
                  ></v-img>
                  <label for="img-uploader" class="label-img-uploader">
                    <v-img
                      class="overlay-img"
                      contain
                      src="/img/cloud-upload.png"
                      alt="Image Upload"
                    />
                    <b class="picker-label">Click to Input an Image</b>
                    <b class="picker-label">(Max 2MB)</b>
                  </label>
                  <input
                    type="file"
                    ref="imageUploader"
                    id="img-uploader"
                    name="image"
                    @click="showLoadingImageUpload"
                    @change="handleFile"
                    accept="image/*"
                    style="display: none"
                  />
                </div>
                <p
                  class="notif-danger"
                  :class="{
                    'container-message-full': $vuetify.breakpoint.xs,
                    'container-message': $vuetify.breakpoint.smAndUp,
                  }"
                  v-if="!model.image_url"
                >
                  <i class="error--text" style="font-size: 10pt"
                    >*Main Picture is required!</i
                  >
                </p>
                <p
                  class="notif-danger"
                  :class="{
                    'container-message-full': $vuetify.breakpoint.xs,
                    'container-message': $vuetify.breakpoint.smAndUp,
                  }"
                  v-if="model.main_file && model.main_file.size > 2000000"
                >
                  <i class="error--text" style="font-size: 10pt"
                    >*Max Upload Size is 2MB, your selected image size is :
                    {{ returnFileSize(model.main_file.size) }}</i
                  >
                </p>

                <p
                  class="notif-success"
                  :class="{
                    'container-message-full': $vuetify.breakpoint.xs,
                    'container-message': $vuetify.breakpoint.smAndUp,
                  }"
                  v-if="model.main_file && model.main_file.size <= 2000000"
                >
                  <i class="success--text" style="font-size: 10pt"
                    >*Your selected image size is :
                    {{ returnFileSize(model.main_file.size) }}</i
                  >
                </p>
              </v-row>

              <!-- <v-row row wrap class="px-2 pt-2" align="start">
                            <label :class="{'news-label-full': $vuetify.breakpoint.xs, 'news-label': $vuetify.breakpoint.smAndUp}" for="header">Header:</label>
                            <v-textarea outlined 
                                :rows="2" auto-grow no-resize
                                placeholder="Header" v-model="model.header"
                                :readonly="!editable"
                                :rules="not_null_rules"
                                dense
                            ></v-textarea>
                        </v-row> -->

              <v-row row wrap class="px-2 pt-2" align="start">
                <label
                  :class="{
                    'news-label-full': $vuetify.breakpoint.xs,
                    'news-label': $vuetify.breakpoint.smAndUp,
                  }"
                  for="description"
                  >Caption:</label
                >
                <v-textarea
                  outlined
                  :rows="10"
                  auto-grow
                  no-resize
                  placeholder="Caption"
                  v-model="model.description"
                  :readonly="!editable"
                  :rules="not_null_rules"
                  dense
                ></v-textarea>
              </v-row>
              <div ref="bottom"></div>
              <v-row row wrap class="pt-2 px-0" align="start">
                <label
                  class="px-2"
                  :class="{
                    'library-label-full': $vuetify.breakpoint.smAndDown,
                    'library-label': $vuetify.breakpoint.mdAndUp,
                  }"
                  for="image_urls"
                  >Other Pictures ({{ model.images.length }}):</label
                >
                <div
                  :class="{
                    'container-img-full': $vuetify.breakpoint.smAndDown,
                    'container-img': $vuetify.breakpoint.mdAndUp,
                  }"
                >
                  <v-container fluid class="ma-0 pa-0">
                    <v-row row wrap align="start" class="ma-0 pa-0">
                      <v-col
                        cols="12"
                        sm="6"
                        lg="4"
                        xl="3"
                        class="mb-4"
                        v-if="model.images.length < 18"
                      >
                        <div id="library-img-uploader" v-if="editable">
                          <label for="img-uploader2" class="label-img-uploader">
                            <v-img
                              class="overlay-img"
                              contain
                              src="/img/cloud-upload.png"
                              alt="Image Upload"
                            />
                            <b class="picker-label"
                              >Click to Input some Image</b
                            >
                            <b class="picker-label">(Max 18 @ 2MB)</b>
                          </label>
                          <input
                            type="file"
                            ref="imageUploader2"
                            id="img-uploader2"
                            name="image2"
                            multiple
                            @click="showLoadingImage2Upload"
                            @change="handleFiles"
                            accept="image/*"
                            style="display: none"
                          />
                        </div>
                      </v-col>

                      <v-col
                        cols="12"
                        sm="6"
                        lg="4"
                        xl="3"
                        class="mb-4"
                        v-for="(img, idx) in model.images"
                        :key="'library-' + idx"
                      >
                        <div
                          class="library-img"
                          v-if="editable"
                          :class="{
                            'red-border': img.file && img.file.size > 2000000,
                          }"
                        >
                          <v-img
                            :key="imgs[idx]"
                            class="news-library-image"
                            @click="preview(img)"
                            :src="img.image_url || '/images/no_image.png'"
                            height="196"
                            cover
                            @error="handleLibraryImageError(idx)"
                            :alt="'news-library-image-' + (idx + 1)"
                          ></v-img>
                          <!-- <div class="remove-button" style="border: 5px solid green;"> -->
                          <v-tooltip left>
                            <template v-slot:activator="{ on }">
                              <v-icon
                                size="30"
                                @click="removeImageLibrary(img)"
                                v-on="on"
                                class="remove-button"
                                color="primary-darken-2"
                                >mdi-close</v-icon
                              >
                            </template>
                            <span>Remove Image</span>
                          </v-tooltip>
                          <!-- </div> -->
                        </div>
                        <p class="text-center">
                          &nbsp;<span
                            :class="{
                              'error--text': img.file.size > 2000000,
                              'success--text': img.file.size <= 2000000,
                            }"
                            v-if="img.file"
                            >{{ img.file_name }} ({{ img.file_size }})</span
                          >
                        </p>
                      </v-col>
                    </v-row>
                  </v-container>
                </div>
              </v-row>

              <v-card-actions class="mt-6 pa-6" v-if="!submitting">
                <v-spacer></v-spacer>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      v-on="on"
                      elevation-0
                      width="100"
                      outlined
                      color="primary"
                      type="submit"
                      >{{
                        model.status == "Published" ? "Post" : "Save"
                      }}</v-btn
                    >
                  </template>
                  <span>{{
                    model.status == "Published" ? getTooltip(0) : getTooltip(1)
                  }}</span>
                </v-tooltip>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      v-on="on"
                      elevation-0
                      width="100"
                      color="primary"
                      @click="
                        validate(
                          model.status == 'Published' ? 'Draft' : 'Published'
                        )
                      "
                      >{{
                        model.status == "Published" ? "Save" : "Post"
                      }}</v-btn
                    >
                  </template>
                  <span>{{
                    model.status == "Published" ? getTooltip(1) : getTooltip(0)
                  }}</span>
                </v-tooltip>
              </v-card-actions>

              <v-card-actions class="mt-6 pa-6" v-else>
                <v-spacer></v-spacer>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      v-on="on"
                      elevation-0
                      width="100"
                      outlined
                      color="primary"
                    >
                      <v-progress-circular
                        indeterminate
                        color="primary"
                      ></v-progress-circular>
                    </v-btn>
                  </template>
                  <span></span>
                </v-tooltip>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn v-on="on" elevation-0 width="100" color="primary">
                      <v-progress-circular
                        indeterminate
                        color="white"
                      ></v-progress-circular>
                    </v-btn>
                  </template>
                  <span></span>
                </v-tooltip>
              </v-card-actions>
            </v-container>
          </v-form>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.red-border {
  border-color: red !important;
}
.picker-label {
  margin-top: 5px;
  padding: 0 5px;
  display: inline-block;
  border-radius: 5px;
  background-color: rgba(255, 255, 255, 0.6);
}
.overlay-img {
  height: 50px !important;
  width: 50px !important;
  flex: 0 0 auto !important;
  border-radius: 50%;
  padding: 5px;
  background-color: rgba(255, 255, 255, 0.6);
}

.overlay-img:deep(.v-image__image) {
  width: 40px !important;
  left: auto !important;
}
.news-label,
.library-label {
  width: 150px;
  padding-top: 6px !important;
  padding-bottom: 6px !important;
}
.news-label-full {
  width: 100%;
  padding-left: 2px !important;
}

.container-field-full {
  width: 100%;
}

.container-field {
  width: calc(100% - 155px);
}

.library-label-full {
  width: 100%;
}

.news-label-full,
.news-label,
.library-label,
.library-label-full {
  font-weight: 400;
  font-size: calc(14px + (6 * (100vw / 1728)));
}

.main-img {
  display: inline-flex;
  position: relative;
  flex-direction: column;
  justify-content: center;
  align-content: center;
  align-items: center;
}

#library-img-uploader {
  height: 200px;
  width: 100%;
  border: 2px dashed #b0b0b0;
  border-radius: 15px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-content: center;
  align-items: center;
}

#container-img-uploader {
  min-height: calc(100px + (200 * (100vw / 900)));
  border: 2px dashed #b0b0b0;
  /*background-image: url('/images/no_image.png');*/
  background-position: center;
  /*background-color: red;*/
  display: inline-flex;
  flex-direction: column;
  justify-content: center;
  align-content: center;
  align-items: center;
  position: relative;
}

.container-img-full,
.container-message,
.container-message-full {
  width: 100%;
}

.container-img {
  width: calc(100% - 150px);
}

.container-message {
  padding-left: 150px;
}

.label-img-uploader {
  position: absolute;
  display: inline-flex;
  flex-direction: column;
  justify-content: center;
  align-content: center;
  align-items: center;
  width: 100%;
  cursor: pointer;
  background-color: transparent;
}

.library-img {
  height: 200px;
  width: 100%;
  border: 2px dashed #b0b0b0;
  border-radius: 15px;
  display: flex;
  position: relative;
  flex-direction: column;
  justify-content: center;
  align-content: center;
  align-items: center;
}

.news-library-image {
  height: auto;
  width: 100%;
  display: block;
  border-radius: 15px;
  cursor: pointer;
}

.remove-button {
  position: absolute;
  top: 2px;
  right: 2px;
  border-radius: 50%;
  display: inline-flex;
  flex-direction: column;
  justify-content: center;
  align-content: center;
  align-items: center;
  width: 30px;
  height: 30px;
  cursor: pointer;
  background-color: rgba(255, 0, 0, 0.5);
}
</style>

<script>
import HeadAvatar from "../head-avatar";
import ImagePreview from "../image-preview.vue";
// import moment from 'moment';
import { format, parseISO } from "date-fns";

export default {
  name: "news-cru",
  components: {
    HeadAvatar,
    ImagePreview,
  },
  props: {
    editable: {
      type: Boolean,
      default: true,
    },
    type: {
      type: String,
      required: true,
    },
    apiCUD: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      id: null,
      ready: false,
      valid: true,
      date_menu: false,
      submitting: false,
      model: {
        // category_id: 1,
        image_url: null,
        main_file: null,
        images: [],
        date: format(parseISO(new Date().toISOString()), "yyyy-MM-dd"),
        status: "Draft",
      },
      not_null_rules: [(v) => !!v || "Field is required"],
      file_ok: false,
      files_ok: true,
      imgs: [],
      prev: {
        dialog: false,
        image: null,
      },
      categories: [],
    };
  },

  watch: {
    "$route.path": function (path) {
      this.devLog("watching change");
      this.initData();
    },
  },

  created() {
    this.devLog("Component News List Created");
    this.initData();
  },

  computed: {
    computedDateFormattedDatefns() {
      return this.model.date
        ? format(parseISO(this.model.date), "EEEE, MMMM do yyyy")
        : "";
    },
  },

  methods: {
    recheckOk() {
      this.files_ok = true;
      this.model.images.forEach((img) => {
        if (img.file) {
          if (img.file.size > 2000000) {
            this.files_ok = false;
          }
        }
      });
    },
    returnFileSize(number) {
      if (number < 1000) {
        return `${number} bytes`;
      } else if (number >= 1000 && number < 1000000) {
        return `${(number / 1000).toFixed(2)} KB`;
      } else if (number >= 1000000) {
        return `${(number / 1000000).toFixed(2)} MB`;
      }
    },
    getTooltip(nbr) {
      let act = [];
      act[0] = "Save & Publish News";
      act[1] = "Save News as Draft";
      if (this.type == "Edit") {
        act[0] = "Update & Publish News";
        act[1] = "Update News & Save as Draft";
      }

      return act[nbr];
    },
    copyUrl() {
      let url = this.getPrefix();

      if (this.model.custom_url) {
        navigator.clipboard.writeText(url + this.model.custom_url);
      } else {
        navigator.clipboard.writeText(url + this.model.id);
      }
      this.addNotification("URL Copied to Clipboard");
    },
    getPrefix() {
      return window.location.origin + "/news/";
    },
    initData() {
      this.id = this.$route.params.id;
      if (!this.id) {
        this.ready = true;
      } else {
        this.initAxios();
      }

      // this.getNewscategory().then((response) => {
      //     this.categories = response.data;
      // }).catch((err) => {
      //     if (err.response) {
      //         this.showErr(err.response, "Failed");
      //     } else {
      //         this.showErr({ status: "Code Error", statusText: err });
      //     }
      // }).finally(()=>{
      //     //category ready
      // });
    },

    removeImageLibrary(item) {
      const index = this.model.images.indexOf(item);
      this.model.images.splice(index, 1);
      this.recheckOk();
    },

    initAxios() {
      this.LOADING(true);
      axios
        .get(`${this.API}/news/` + this.id, {
          headers: { Authorization: localStorage.token },
        })
        .then((response) => {
          if (response.status == 200) {
            this.devLog(" Result Status: " + response.status);
            this.devLog(response.data);
            this.model = response.data;

            delete this.model.created_at;
            delete this.model.updated_at;
            delete this.model.deleted_at;

            if (this.model.image_url && this.model.image_url != "") {
              this.file_ok = true;
            }
          }
        })
        .catch((err) => {
          if (err.response) {
            this.showErr(err.response, "Failed");
          } else {
            this.showErr({ status: "Code Error", statusText: err });
          }
        })
        .finally(() => {
          this.LOADING(false);
          this.ready = true;
          if (this.model.images.length) {
            this.model.images.forEach((img, idx) => {
              this.imgs[idx] = 0;
            });
          }
        });
    },

    validate(status) {
      this.devLog("validating");
      if (this.$refs.form) {
        this.valid = this.$refs.form.validate();
        if (this.valid && this.file_ok) {
          if (!this.files_ok) {
            this.$refs.bottom.scrollIntoView({ behavior: "smooth" });
          } else {
            this.submitData(status);
          }
        } else {
          this.$refs.top.scrollIntoView({ behavior: "smooth" });
        }
      }
    },

    submitData(status) {
      this.model.status = status;
      this.postData();
    },

    postData() {
      this.LOADING(true);
      this.submitting = true;
      let formData = new FormData();
      let item = this.model;
      let arr = Object.keys(item);
      arr.forEach((a) => {
        if (a === "created_at" || a === "updated_at") {
          return; // JANGAN KIRIM KE BACKEND
        }

        if (
          item[a] &&
          (typeof item[a] == "string" || typeof item[a] == "number")
        ) {
          // this.devLog(a)
          // this.devLog(item[a])
          if (a != "image_url") {
            //image url may contain base64 string if have file
            formData.append(a, item[a]);
          } else {
            if (this.model.main_file) {
              formData.append("image_url", ""); //remove image_url if have file
            } else {
              formData.append(a, item[a]); //assign value if have no file
            }
          }
        } else {
          switch (a) {
            case "custom_url":
              formData.append("custom_url", "");
              break;
            case "image_url":
              formData.append("image_url", "");
              break;
            case "main_file":
              this.devLog("checking main_file");
              if (this.model.main_file) {
                this.devLog("main_file found");
                formData.append("main_file", this.model.main_file);
                this.devLog(this.model.main_file);
              }
              break;

            case "images":
              this.devLog("checking images");
              if (this.model.images && this.model.images.length > 0) {
                this.devLog("images found");
                this.model.images.forEach(function (img, idx) {
                  formData.append("arr_images[" + idx + "][id]", img.id || 0);
                  formData.append(
                    "arr_images[" + idx + "][image_url]",
                    img.image_url
                  );
                  if (img.file) {
                    formData.append("arr_images[" + idx + "][file]", img.file);
                    formData.append(
                      "arr_images[" + idx + "][file_name]",
                      img.file_name
                    );
                    formData.append(
                      "arr_images[" + idx + "][file_size]",
                      img.file_size
                    );
                  }
                });
                this.devLog(this.model.images);
              }
              break;

            default:
              break;
          }
        }
      });

      let ax;
      if (this.type == "Edit") {
        let url = this.apiCUD + "/" + this.model.id;
        ax = axios.post(url, formData, {
          headers: {
            Authorization: localStorage.token,
            "Content-Type": "multipart/form-data",
          },
        });
      } else if (this.type == "Create") {
        let url = this.apiCUD;
        ax = axios.post(url, formData, {
          headers: {
            Authorization: localStorage.token,
            "Content-Type": "multipart/form-data",
          },
        });
      }
      ax.then((response) => {
        this.devLog(response.status);
        this.devLog(response.data);
        if (response.status == 201) {
          // this.simpleSnackbar("Item has been created!", "success");
          this.$router.replace({
            name: "news",
            params: {
              snackbarOpt: {
                text: "Success! Item has been created!!!",
                type: "success",
              },
            },
          });
        } else if (response.status == 202) {
          // this.simpleSnackbar("Item has been updated!", "success");
          this.$router.replace({
            name: "news",
            params: {
              snackbarOpt: {
                text: "Success! Item has been updated!!!",
                type: "success",
              },
            },
          });
        } else {
          this.showErr(response, "Failed");
        }
      })
        .catch((err) => {
          if (err.response) {
            this.showErr(err.response, "Failed");
          } else {
            this.showErr({ status: "Code Error", statusText: err });
          }
        })
        .finally(() => {
          this.LOADING(false);
          this.submitting = false;
        });
    },

    handleFile(event) {
      const files = event.target.files;
      this.file_ok = false;
      if (files[0].type.match("image/*")) {
        const reader = new FileReader();
        const that = this;
        reader.onload = (e) => {
          that.model.image_url = e.target.result;
          this.model.main_file = files[0];
          this.devLog(this.model);
          if (this.model.main_file.size <= 2000000) {
            this.file_ok = true;
          }
        };
        reader.readAsDataURL(files[0]);
      }
    },

    handleFiles(event) {
      const files = event.target.files;

      for (let i = 0; i < files.length; i++) {
        if (files[i].type.match("image/*")) {
          const reader2 = new FileReader();
          const that = this;
          reader2.onload = (e) => {
            let img = {
              image_url: e.target.result,
              file: files[i],
              file_name: files[i].name,
              file_size: this.returnFileSize(files[i].size),
              error: false,
            };

            if (files[i].size > 2000000) {
              this.files_ok = false;
              img.error = true;
            }

            if (this.model.images.length < 18) {
              this.model.images.unshift(img);
            }
          };
          reader2.readAsDataURL(files[i]);
        }
      }
    },

    showLoadingImageUpload() {
      document.body.onfocus = this.checkIfFileDialogClosed;
      this.LOADING(true);
      this.$refs.imageUploader.value = "";
    },

    checkIfFileDialogClosed() {
      if (!this.$refs.imageUploader.value.length) {
        this.LOADING(false);
      }
      document.body.onfocus = null;
    },

    showLoadingImage2Upload() {
      document.body.onfocus = this.checkIfFileDialog2Closed;
      this.LOADING(true);
      this.$refs.imageUploader2.value = "";
    },

    checkIfFileDialog2Closed() {
      if (!this.$refs.imageUploader2.value.length) {
        this.LOADING(false);
      }
      document.body.onfocus = null;
    },

    preview(img) {
      this.prev.image = img.image_url;
      this.prev.dialog = true;
    },

    handleLibraryImageError(idx) {
      this.devLog(idx);
      this.$nextTick().then(() => {
        this.model.images[idx].image_url = "/images/image-unavailable.png";
        this.imgs[idx]++;
      });
    },
  },

  mounted() {},
};
</script>
