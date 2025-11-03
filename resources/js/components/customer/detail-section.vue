<template>
  <v-container fluid class="pa-0">
    <ImagePreview
      :dialog="prev.dialog"
      :selectedImage="prev.image"
      @update:dialog="prev.dialog = $event"
    />
    <v-row class="pa-0 ma-0">
      <v-col class="pa-0 ma-0">
        <!-- <div style="height: auto;" class="float-left left-spacer">&nbsp;</div> -->
        <div class="d-flex right-spacer pa-0 ma-0">
          <article>
            <header>
              <h1 class="news-title-main" v-html="news.title"></h1>
              <p
                class="news-subtitle-main m43 pa-0 mx-0 mb-0"
                v-html="news.readable_date"
              ></p>
              <!-- <v-row align="center" justify="center">
                <v-col cols="10">
                  <p class="news-subtitle-main m15 pa-0 ma-0">
                    <v-icon class="material-icons-outlined">location_on</v-icon>
                    {{ news.location || "-" }}
                  </p>
                </v-col>
                <v-col cols="2">
                  <social-share-dialog
                    :sharing="sharingData"
                  ></social-share-dialog>
                </v-col>
              </v-row> -->
              <!-- <p class="news-subtitle-main m15 pa-0 ma-0">
                                <v-icon class="material-icons-outlined"
                                    >location_on</v-icon
                                >
                                {{ news.location || "-" }}
                            </p> -->
            </header>
            <p>
              <v-img
                class="news-main-image"
                :src="
                  news.image_url ||
                  (news.image && news.image.image_url
                    ? news.image.image_url
                    : '/images/no_image.png')
                "
                contain
                @click="preview(news)"
                @error="handleMainImageError()"
                alt="news-main-image"
              ></v-img>
            </p>
            <h1
              v-if="news.header"
              class="news-header"
              v-html="news.header"
            ></h1>
            <p
              style="white-space: pre-line"
              class="news-content"
              v-html="news.description"
            ></p>
          </article>
        </div>
      </v-col>
    </v-row>

    <div class="news-detail-bottom">
      <v-row class="right-spacer mbc" v-if="news.images && news.images.length">
        <v-col cols="12" class="news-navigation">Image Library</v-col>
        <v-col
          cols="6"
          md="4"
          xl="3"
          v-for="(img, idx) in news.images"
          :key="'img-lib-' + idx"
          class="p-1"
        >
          <v-img
            :key="imgs[idx]"
            class="news-library-image"
            @click="preview(img)"
            :src="img.image_url || '/images/no_image.png'"
            height="22.5vmin"
            cover
            @error="handleLibraryImageError(idx)"
            :alt="'news-library-image-' + (idx + 1)"
          ></v-img>
        </v-col>
      </v-row>
    </div>
  </v-container>
</template>

<style scoped>
.bgreen {
  border: 2px solid green;
}
.right-spacer {
  padding-right: calc(20px + (220 * (100vw / 1728))) !important;
  padding-left: calc(20px + (220 * (100vw / 1728))) !important;
}
.news-title-main {
  overflow-wrap: break-word;
  color: black;
  text-align: left;
  font-size: calc(72 * (100vw / 1728));
  font-weight: 700;
  line-height: calc(102.6 * (100vw / 1728));
}

.m15 {
  margin-top: calc(5px + (10 * 100vw / 1728)) !important;
}

.m43 {
  margin-top: calc(10px + (33 * 100vw / 1728)) !important;
}

.news-subtitle-main {
  overflow-wrap: break-word;
  color: black;
  text-align: left;
  font-size: calc(8px + (14.4 * (100vw / 1728)));
  font-weight: 400;
  line-height: calc(10px + (20 * (100vw / 1728)));
}

.news-subtitle-main .v-icon {
  color: black;
  font-weight: 400 !important;
  font-size: calc(10px + (17.6 * (100vw / 1728)));
  line-height: calc(10px + (26 * (100vw / 1728)));
  margin-bottom: calc(2px + (2 * (100vw / 1728)));
  margin-left: -5px;
}

.news-main-image {
  margin-top: calc(10px + (34 * 100vw / 1728));
  height: auto;
  width: calc(100vw - 2 * (20px + (220 * (100vw / 1728))));
  display: block;
  /* border-radius: 15px; */
  border-radius: calc(5px + (10 * 100vw / 1728));
}

.news-header {
  margin-top: calc(10px + (50 * 100vw / 1728));
  font-size: calc(14px + (25.6 * (100vw / 1728)));
  font-weight: 300;
  line-height: calc(20.5px + (32 * (100vw / 1728)));
  font-style: italic;
}

.news-content {
  margin-top: calc(6px + (26 * 100vw / 1728));
  font-size: calc(10px + (16 * (100vw / 1728)));
  font-weight: 400;
  line-height: calc(14.2px + (20 * (100vw / 1728)));
}

.mbc {
  margin-bottom: calc(10px + (40 * 100vw / 1728));
}

.news-library-image {
  margin-top: calc(5px + (10 * 100vw / 1728));
  height: auto;
  width: 100%;
  display: block;
  /* border-radius: 15px; */
  border-radius: calc(5px + (10 * 100vw / 1728));
}

.news-detail-bottom {
  background-image: url("/images/bottom_right.png");
  background-size: calc(200px + (280 * (100vw / 1728)))
    calc(200px + (180 * (100vw / 1728)));
  background-position: bottom right;
  padding-top: calc(10px + (40 * 100vw / 1728));
  padding-bottom: 60px;
  min-height: calc(200px + (180 * (100vw / 1728)));
}
</style>

<script>
// import VueSocialSharing from "vue-social-sharing";
import ImagePreview from "../image-preview.vue";
// import SocialShareDialog from "../../social-share-dialog.vue";

export default {
  components: {
    ImagePreview,
    // SocialShareDialog,
  },
  props: {
    news: {
      type: Object,
      required: true,
    },
  },

  created() {
    this.initData();

    this.sharingData.title = this.news.title;
    this.sharingData.description = this.news.description;
    this.sharingData.quote = this.news.header;

    this.sharingData.url = window.location.href;
    // this.sharingData.url = encodeURIComponent(window.location.href);

    // const { title, description, header } = this.news;
    // this.sharingData = {
    //     ...this.sharingData,
    //     title: title || "",
    //     description: description || "",
    //     quote: header || "",
    // };
  },

  data: () => ({
    show: false,
    prev: {
      dialog: false,
      image: null,
    },
    main_img: 0,
    imgs: [],

    sharingData: {
      url: "",
      title: "",
      description: "",
      quote: "",
      hashtags: "zk-digimax",
    },
  }),

  methods: {
    initData() {
      if (this.news.images.length) {
        this.news.images.forEach((img, idx) => {
          this.imgs[idx] = 0;
        });
      }
    },
    preview(img) {
      this.prev.image = img.image_url;
      this.prev.dialog = true;
    },
    handleMainImageError() {
      this.$nextTick().then(() => {
        this.news.image_url = "/images/image-unavailable.png";
        this.main_img++;
      });
    },

    handleLibraryImageError(idx) {
      this.devLog(idx);
      this.$nextTick().then(() => {
        this.news.images[idx].image_url = "/images/image-unavailable.png";
        this.imgs[idx]++;
      });
    },
  },
};
</script>
