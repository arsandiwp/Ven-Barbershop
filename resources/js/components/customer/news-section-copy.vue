<template>
  <div class="news-section">
    <!-- Section Title -->
    <div class="container">
      <v-row>
        <v-col cols="12">
          <div class="title-text">Our News</div>
          <div class="subtitle-text">
            Stay updated with the latest news and articles from our barbershop.
            From grooming tips to style trends, we have it all covered.
          </div>
        </v-col>
      </v-row>
    </div>

    <v-divider style="width: 80%" color="goldenrod" class="mx-auto"></v-divider>

    <!-- News Cards -->
    <div class="explore-container">
      <v-row>
        <v-col
          v-for="(item, index) in posts"
          :key="index"
          cols="12"
          sm="6"
          md="4"
          class="d-flex"
        >
          <v-hover v-slot="{ hover }">
            <v-card
              class="rounded-xl card"
              elevation="hover ? 12 : 4"
              :class="{ 'card-hover': hover }"
            >
              <v-img
                cover
                :src="item.image_url || defaultThumbnail"
                class="card-img"
                @click="goDetail(item)"
              >
                <div class="overlay-card">
                  <div class="title-card">
                    {{ item.title }}
                  </div>
                  <div class="subtitle-card">
                    {{ item.description }}
                  </div>
                </div>
              </v-img>
            </v-card>
          </v-hover>
        </v-col>
      </v-row>

      <!-- Pagination -->
      <v-row justify="center" class="mt-8">
        <v-pagination
          v-model="page"
          :length="totalPages"
          @input="fetchPosts"
          circle
          color="amber darken-2"
        ></v-pagination>
      </v-row>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      posts: [],
      page: 1,
      perPage: 6,
      totalPages: 1,
      defaultThumbnail: "/images/default-thumbnail.jpg",
    };
  },
  mounted() {
    this.fetchPosts();
  },
  methods: {
    goDetail(item) {
      this.$router.push({
        path: `/news/${item.custom_url || item.id}`,
      });
    },

    async fetchPosts() {
      try {
        const { data } = await axios.get(this.API + "/news/paginate", {
          params: {
            per_page: this.perPage,
            page: this.page,
            status: "Published",
          },
        });
        this.posts = data.data;
        this.totalPages = data.last_page || 1;
      } catch (error) {
        console.error("Failed to fetch blog/news:", error);
      }
    },
  },
};
</script>

<style scoped lang="scss">
@import "../../../../sass/_calc.scss";

.news-section {
  background: url("https://www.transparenttextures.com/patterns/white-wall-3.png");
  background-repeat: repeat;
  background-size: auto;
  padding: fluid(40, 80) 0;
}

.container {
  padding: fluid(40, 80) fluid(20, 60);
  text-align: center;
}

.explore-container {
  padding: fluid(40, 80) fluid(20, 100);
}

.title-text {
  font-size: fluid(26, 48);
  font-weight: 600;
  color: #111;
}

.subtitle-text {
  font-size: fluid(14, 20);
  font-weight: 400;
  max-width: 700px;
  margin: 0 auto;
  color: #555;
}

.card {
  width: 100%;
  height: fluid(200, 320);
  display: flex;
  flex-direction: column;
  border-radius: 16px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card-hover {
  transform: translateY(-8px);
}

.card-img {
  height: 100%;
}

.overlay-card {
  position: absolute;
  bottom: 0;
  left: 0;
  padding: 20px;
  width: 100%;
  background: linear-gradient(to top, rgba(0, 0, 0, 0.9), transparent);
}

.title-card {
  font-size: fluid(14, 20);
  font-weight: 700;
  color: #fff;
  margin-bottom: 4px;

  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.subtitle-card {
  font-size: fluid(10, 16);
  font-weight: 400;
  color: #ddd;

  display: -webkit-box;
  -webkit-line-clamp: 1;
  line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
