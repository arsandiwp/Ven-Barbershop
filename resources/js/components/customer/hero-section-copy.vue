<template>
  <v-container fluid class="pa-0 hero-section">
    <v-img
      src="/img/background-hero.jpg"
      height="100vh"
      gradient="to bottom, rgba(0,0,0,0.5), rgba(0,0,0,0.85)"
      class="d-flex align-center justify-center"
    >
      <v-container class="text-center white--text">
        <!-- Small tagline -->
        <div class="overline mb-2">Selamat Datang di</div>

        <!-- Main Headline -->
        <h1 class="hero-title mb-4">Ven Barbershop</h1>

        <!-- Subheadline -->
        <p class="hero-subtitle mb-8">
          Potongan dan perawatan premium untuk pria modern. Hadirkan gaya
          terbaik, kenyamanan maksimal, dan kepercayaan diri yang selalu terbawa
          pulang di setiap kunjungan.
        </p>

        <!-- Highlight Features -->
        <v-row justify="center" class="mb-10">
          <v-col cols="12" sm="4" class="text-center mb-6 mb-sm-0">
            <v-icon large color="amber lighten-2">mdi-content-cut</v-icon>
            <div class="mt-2 font-weight-medium">Tukang Cukur Profesional</div>
          </v-col>

          <v-col cols="12" sm="4" class="text-center mb-6 mb-sm-0">
            <v-icon large color="amber lighten-2">mdi-bottle-tonic</v-icon>
            <div class="mt-2 font-weight-medium">Produk Premium</div>
          </v-col>

          <v-col cols="12" sm="4" class="text-center">
            <v-icon large color="amber lighten-2">mdi-sofa</v-icon>
            <div class="mt-2 font-weight-medium">Suasana Nyaman</div>
          </v-col>
        </v-row>

        <!-- CTA Buttons -->
        <div>
          <v-btn
            large
            color="amber darken-2"
            class="rounded-lg px-6 py-3 mr-4 hero-btn"
            dark
            @click="goToReservation"
          >
            Reservasi Sekarang
          </v-btn>

          <v-btn
            large
            outlined
            color="white"
            class="rounded-lg px-6 py-3 hero-btn"
            @click="scrollToSection('services')"
          >
            Lihat Layanan
          </v-btn>
        </div>

        <!-- Bottom tagline -->
        <div class="mt-12 caption font-italic">
          Membangun Kepercayaan Diri Sejak 2025
        </div>
      </v-container>
    </v-img>
  </v-container>
</template>

<script>
export default {
  name: "HeroSection",
  methods: {
    goToReservation() {
      if (!localStorage.getItem("customerLogin")) {
        this.$router.push("/login"); // belum login → ke login dulu
      } else {
        this.$router.push("/reservation"); // sudah login → ke reservasi
      }
    },
    scrollToSection(sectionId) {
      const section = document.getElementById(sectionId);
      if (section) {
        section.scrollIntoView({ behavior: "smooth" });
      }
    },
  },
};
</script>

<style scoped>
.hero-section {
  font-family: "Poppins", sans-serif;
}

/* Headline responsive */
.hero-title {
  font-weight: 700;
  letter-spacing: 3px;
  text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.9);
  font-size: 3.5rem;
}

@media (max-width: 960px) {
  .hero-title {
    font-size: 2.5rem;
  }
}

@media (max-width: 600px) {
  .hero-title {
    font-size: 2rem;
  }
}

/* Subtitle responsive */
.hero-subtitle {
  width: 80%;
  font-size: 1.2rem;
  line-height: 1.6;
  margin: 0 auto;
}

@media (max-width: 600px) {
  .hero-subtitle {
    font-size: 1rem;
    padding: 0 10px;
  }
}

/* Buttons */
.hero-btn {
  font-size: 16px;
  text-transform: none;
  border-radius: 30px !important;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.35);
  transition: 0.3s;
}

.hero-btn:hover {
  transform: translateY(-3px);
}
</style>
