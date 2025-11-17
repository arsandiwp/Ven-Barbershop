<template>
  <div class="bg">
    <div class="container">
      <v-row>
        <v-col cols="12">
          <div class="title-text">Our Services</div>
          <div class="subtitle-text">
            Our services are designed to provide you with the best grooming
            experience. From classic cuts to modern styles, we have something
            for everyone.
          </div>
        </v-col>
      </v-row>
    </div>

    <v-divider style="width: 90%" color="goldenrod" class="mx-auto"></v-divider>

    <div class="explore-container">
      <v-row align="center" justify="center">
        <v-col
          v-for="(service, index) in services"
          :key="index"
          cols="12"
          sm="6"
          md="4"
          class="d-flex flex-column align-center"
        >
          <!-- Circle Image -->
          <div class="circle-card">
            <v-img
              :src="service.photo"
              class="circle-img"
              height="150"
              width="150"
              contain
            />
          </div>

          <!-- Text -->
          <div class="service-text mt-4 text-center">
            <div class="service-title">{{ service.name }}</div>
            <div class="service-subtitle">{{ service.description }}</div>
          </div>
        </v-col>
      </v-row>

      <!-- Pagination -->
      <v-row justify="center" class="mt-6">
        <v-pagination
          v-model="page"
          :length="totalPages"
          @input="fetchServices"
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
      services: [],
      page: 1,
      perPage: 6,
      totalPages: 1,
    };
  },
  mounted() {
    this.fetchServices();
  },
  methods: {
    fetchServices() {
      axios
        .get(this.API + "/services/paginate", {
          params: {
            per_page: this.perPage,
            page: this.page,
            status: "Published",
          },
        })
        .then((response) => {
          this.services = response.data.data;
          this.totalPages = response.data.last_page || 1;
        })
        .catch((error) => {
          console.error("Failed to fetch services:", error);
        });
    },
  },
};
</script>

<style scoped lang="scss">
@import "../../../../sass/_calc.scss";

.bg {
  background: url("https://www.transparenttextures.com/patterns/white-wall-3.png");
}

.container {
  padding-top: fluid(30, 70);
  padding-bottom: fluid(30, 70);
  padding-left: fluid(24, 70);
  padding-right: fluid(24, 70);
}

.explore-container {
  padding-top: fluid(30, 70);
  padding-bottom: fluid(30, 70);
  padding-left: fluid(24, 50);
  padding-right: fluid(24, 50);
}

.title-text {
  font-size: fluid(24, 48);
  font-weight: 600;
  color: black;
  text-align: center;
}

.subtitle-text {
  font-size: fluid(14, 20);
  font-weight: 400;
  color: #444;
  text-align: center;
  max-width: 700px;
  margin: 0 auto;
}

.circle-card {
  width: 170px;
  height: 170px;
  border-radius: 50%;
  overflow: hidden;
  border: 4px solid #e0e0e0;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;

  &:hover {
    transform: scale(1.05);
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
  }
}

.circle-img {
  object-fit: cover;
  border-radius: 50%;
}

.service-text {
  max-width: 250px;
}

.service-title {
  font-size: fluid(14, 22);
  font-weight: 600;
  color: black;
}

.service-subtitle {
  font-size: fluid(10, 16);
  font-weight: 400;
  color: #666;
  margin-top: 4px;
}
</style>
