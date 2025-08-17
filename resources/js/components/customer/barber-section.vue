<template>
  <div>
    <div class="container">
      <v-row>
        <v-col cols="12">
          <div class="title-text">Our Barbers</div>
          <div class="subtitle-text">
            Our barbers are skilled professionals dedicated to providing you
            with the best grooming experience. Meet our team and book your
            appointment today.
          </div>
        </v-col>
      </v-row>
    </div>

    <v-divider style="width: 90%" class="mx-auto"></v-divider>

    <div class="explore-container">
      <v-row>
        <v-col
          v-for="(barber, index) in barbers"
          :key="index"
          cols="12"
          sm="6"
          md="4"
          class="d-flex"
        >
          <v-card class="rounded-xl card">
            <v-img
              cover
              :src="barber.photo"
              class="card-img"
              height="100%"
              width="100%"
            >
              <div class="overlay-card">
                <div class="title-card">
                  {{ barber.name }}
                </div>
                <div class="subtitle-card">
                  {{ barber.email }}
                </div>
                <div class="subtitle-card">
                  {{ barber.address }}
                </div>
              </div>
            </v-img>
          </v-card>
        </v-col>
      </v-row>

      <!-- Pagination -->
      <v-row justify="center" class="mt-6">
        <v-pagination
          v-model="page"
          :length="totalPages"
          @input="fetchBarbers"
          circle
        ></v-pagination>
      </v-row>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      barbers: [],
      page: 1,
      perPage: 6,
      totalPages: 1,
    };
  },
  mounted() {
    this.fetchBarbers();
  },
  methods: {
    async fetchBarbers() {
      try {
        const { data } = await axios.get(this.API + "/barbers/paginate", {
          params: {
            per_page: this.perPage,
            page: this.page,
          },
        });

        this.barbers = data.data;
        this.totalPages = data.last_page || 1; // fallback jika backend tidak mengirim last_page
      } catch (error) {
        console.error("Failed to fetch barbers:", error);
      }
    },
  },
};
</script>

<style scoped lang="scss">
@import "../../../../sass/_calc.scss";

.container {
  position: relative;

  padding-top: fluid(30, 70);
  padding-bottom: fluid(30, 70);
  padding-left: fluid(24, 70);
  padding-right: fluid(24, 70);
}

.explore-container {
  position: relative;

  padding-top: fluid(30, 70);
  padding-bottom: fluid(30, 70);
  padding-left: fluid(24, 150);
  padding-right: fluid(24, 150);
}

.title-text {
  font-size: fluid(24, 48);
  font-weight: 500;
  line-height: 150%;
  letter-spacing: 0%;
  color: black;
  text-align: center;
}

.subtitle-text {
  font-size: fluid(14, 20);
  font-weight: 400;
  line-height: 150%;
  letter-spacing: 0%;
  color: black;
  text-align: center;
}

.card {
  width: 100%;
  height: fluid(166, 274);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.overlay-card {
  position: absolute;
  bottom: 0;
  left: 0;
  padding: 16px;
  width: 100%;
  background: linear-gradient(to top, rgba(0, 0, 0, 1), transparent);
}

.title-card {
  font-size: fluid(12, 20);
  font-weight: 700;
  line-height: 150%;
  letter-spacing: 0%;
  color: white;

  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.subtitle-card {
  font-size: fluid(8, 16);
  font-weight: 400;
  line-height: 150%;
  letter-spacing: 0%;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
