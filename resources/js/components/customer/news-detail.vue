<template>
  <div>
    <!-- <NavBar
      activeMenuGroup="news"
      :backgroundGradient="customGradient"
    ></NavBar> -->
    <div id="smooth-wrapper" style="background-color: white">
      <div id="smooth-content" class="page-setup">
        <!-- arrow back -->
        <div class="news-detail-top">
          <v-container fluid style="min-height: 300px" class="pa-0">
            <div class="top-spacer"></div>
            <v-layout row wrap class="pa-0 ma-0">
              <div class="left-spacer pa-0 ma-0"></div>
              <p class="news-navigation pa-0 ma-0 clickable" @click="goBack()">
                <v-icon>keyboard_arrow_left</v-icon> back
              </p>
            </v-layout>
          </v-container>
        </div>

        <!-- <Footer style="border: 1px solid red"></Footer> -->

        <Section1 v-if="ready" class="page-setup" :news="news" />

        <!-- <zk-footer
          v-if="ready"
          wrapperFooterColor="transparent"
          mt="-60px"
        ></zk-footer> -->
        <Footer style="border: 1px solid red"></Footer>
      </div>
    </div>

    <!-- <v-tooltip left>
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          v-scroll="onScroll"
          v-show="fab"
          v-bind="attrs"
          v-on="on"
          fab
          dark
          fixed
          bottom
          right
          color="primary"
          @click="toTop"
        >
          
          <span class="fas fa-chevron-up"></span>
        </v-btn>
      </template>
      <span>Scroll to top</span>
    </v-tooltip> -->
  </div>
</template>

<script>
// import { gsap } from "gsap";
// import { ScrollTrigger } from "gsap/ScrollTrigger";
// import { ScrollSmoother } from "gsap/ScrollSmoother";
// import NavBar from "../../navbar.vue";
import Section1 from "./detail-section.vue";
// import ZkFooter from "../../zk-footer.vue";
// import ZkFooter from "../../zk-footer-new.vue";
import Footer from "../footer.vue";

export default {
  components: {
    // NavBar,
    Section1,
    // ZkFooter
    Footer,
  },

  created() {
    this.initData();
  },

  data() {
    return {
      ready: false,
      id: null,
      news: {},
      //'rgba(0, 0, 0, 0.4)',
      customGradient:
        "linear-gradient(to bottom right, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.2))",

      fab: false,
    };
  },

  watch: {},

  methods: {
    onScroll(e) {
      if (typeof window === "undefined") return;
      const top = window.pageYOffset || e.target.scrollTop || 0;
      this.fab = top > 10;
    },
    toTop() {
      this.$vuetify.goTo(0);
    },

    initData() {
      this.id = this.$route.params.id;
      this.initAxios();
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
            this.news = response.data;
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
        });
    },

    goBack() {
      this.devLog(window.history);
      if (!window.history.state) {
        window.close();
      } else {
        this.$router.go(-1);
      }
      // this.$router.replace({ name: "news" });
    },
  },

  mounted() {
    // gsap.registerPlugin(ScrollTrigger, ScrollSmoother);
    // ScrollSmoother.create({
    //   smooth: 1,
    //   effects: true,
    //   smoothTouch: 0.1,
    // });
  },
};
</script>

<style scope>
.page-setup {
  font-family: "Proxima Nova", sans-serif;
}

.clickable:hover {
  cursor: pointer;
}

.top-spacer {
  /* height: calc(100px + (100 * (100vw / 1728))); */
  height: calc(105px + (105 * (100vw / 1728)));
  min-height: 100px;
}

.left-spacer {
  width: calc(150 * (100vw / 1728));
}

.news-navigation {
  color: #707070;
  font-weight: 400 !important;
  font-size: calc(8px + (18 * (100vw / 1728)));
  line-height: calc(10px + (20 * (100vw / 1728)));
}

.news-navigation .v-icon {
  color: #707070;
  font-weight: 400 !important;
  font-size: calc(9px + (17 * (100vw / 1728)));
  line-height: calc(10px + (20 * (100vw / 1728)));
  margin-bottom: calc(1px + (2 * (100vw / 1728)));
}

.news-detail-top {
  background-image: url("/images/top_left.png");
  background-size: calc(250px + (280 * (100vw / 1728)))
    calc(250px + (180 * (100vw / 1728)));
  background-position: top left;
  height: calc(150px + (180 * (100vw / 1728)));
}
</style>
