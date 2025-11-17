<template>
  <div class="barber-section">
    <div class="container">
      <v-row>
        <v-col cols="12">
          <div class="title-text">Our Barbers</div>
          <div class="subtitle-text">
            Skilled professionals dedicated to giving you the best grooming
            experience.
          </div>
        </v-col>
      </v-row>
    </div>

    <v-divider
      style="width: 90%"
      color="goldenrod"
      class="mx-auto mb-8"
    ></v-divider>

    <div class="explore-container">
      <v-row justify="center">
        <v-col
          v-for="(barber, index) in barbers"
          :key="index"
          cols="12"
          sm="6"
          md="4"
          class="d-flex justify-center"
        >
          <div class="barber-card text-center">
            <v-avatar size="200" class="barber-avatar">
              <v-img :src="barber.photo || 'https://cdn-icons-png.flaticon.com/512/921/921079.png'" cover></v-img>
            </v-avatar>
            <div class="barber-info mt-4">
              <h3 class="barber-name">{{ barber.name }}</h3>
              <p class="barber-email">{{ barber.email }}</p>
              <p class="barber-address">{{ barber.address }}</p>
              <v-btn
                small
                color="amber darken-2"
                class="mt-2 rounded-lg"
                style="color: white"
                elevation="3"
                @click="goToReservation"
              >
                Reservation Now
              </v-btn>
            </div>
          </div>
        </v-col>
      </v-row>

      <!-- Pagination -->
      <v-row justify="center" class="mt-10">
        <v-pagination
          v-model="page"
          :length="totalPages"
          @input="fetchBarbers"
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
            status: "Active",
          },
        });

        this.barbers = data.data;
        this.totalPages = data.last_page || 1;
      } catch (error) {
        console.error("Failed to fetch barbers:", error);
      }
    },

    goToReservation() {
      if (!localStorage.getItem("customerLogin")) {
        this.$router.push("/login"); // belum login → ke login dulu
      } else {
        this.$router.push("/reservation"); // sudah login → ke reservasi
      }
    },
  },
};
</script>

<style scoped lang="scss">
@import "../../../../sass/_calc.scss";

.barber-section {
  padding-bottom: 60px;
  background: url("https://www.transparenttextures.com/patterns/white-wall-3.png");
}

.container {
  padding-top: fluid(40, 80);
  padding-bottom: fluid(20, 40);
  text-align: center;
}

.explore-container {
  padding: fluid(20, 50) fluid(24, 100);
}

.title-text {
  font-size: fluid(24, 48);
  font-weight: 700;
  color: #222;
}

.subtitle-text {
  font-size: fluid(14, 20);
  font-weight: 400;
  color: #555;
  max-width: 650px;
  margin: 0 auto;
}

.barber-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  padding: 20px;

  &:hover {
    transform: translateY(-6px);
  }
}

.barber-avatar {
  border: 5px solid #fff;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

.barber-info {
  .barber-name {
    font-size: fluid(18, 24);
    font-weight: 600;
    margin-bottom: 4px;
    color: #222;
  }
  .barber-email,
  .barber-address {
    font-size: fluid(12, 16);
    color: #666;
    margin: 0;
  }
}
</style>
