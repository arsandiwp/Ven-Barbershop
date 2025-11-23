<template>
  <v-footer padless class="footer">
    <v-container>
      <!-- Contact Us -->
      <v-row justify="center" class="text-center mb-8">
        <v-col cols="12">
          <div class="footer-title">Hubungi kami</div>
          <div class="footer-subtitle">
            Kami hadir untuk membantu Anda dengan pertanyaan atau masalah apa
            pun yang mungkin Anda miliki.
          </div>
        </v-col>
      </v-row>

      <!-- Info Grid -->
      <v-row justify="center" class="text-center">
        <v-col
          v-for="(item, index) in footerItems"
          :key="index"
          cols="12"
          sm="6"
          md="3"
          class="mb-6"
        >
          <div class="footer-item">
            <v-icon color="amber darken-2" size="32" class="mb-2">{{
              item.icon
            }}</v-icon>
            <div class="footer-label">{{ item.label }}</div>
            <div class="footer-info">{{ item.info }}</div>
          </div>
        </v-col>
      </v-row>

      <!-- Divider -->
      <v-divider class="my-6" color="goldenrod"></v-divider>

      <!-- Copyright -->
      <v-row justify="center">
        <v-col cols="12" class="text-center copyright">
          © {{ new Date().getFullYear() }} Pangkas Rambut. Hak Cipta Dilindungi
          Undang-Undang.
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script>
export default {
  name: "AppFooter",
  data() {
    return {
      footerItems: [
        {
          icon: "mdi-map-marker",
          label: "ALAMAT",
          info: "Jl. Ringin Raya No.121, Ngringin, Condongcatur, Depok, Sleman, DIY",
        },
        {
          icon: "mdi-email-outline",
          label: "EMAIL",
          info: "info@company.com",
        },
        {
          icon: "mdi-phone",
          label: "TELEPON",
          info: "085967279857",
        },
        {
          icon: "mdi-clock-outline",
          label: "JAM KERJA",
          info: "10:00 - 22:00",
        },
      ],
    };
  },
};
</script>

<style lang="scss" scoped>
@import "../../../sass/calc.scss";

.footer {
  background-color: #fff;
  padding: 60px 20px 30px;
  box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
}

.footer-title {
  font-size: fluid(18, 28);
  font-weight: 600;
  color: #222;
  margin-bottom: 8px;
}

.footer-subtitle {
  font-size: fluid(12, 18);
  font-weight: 400;
  color: #555;
  max-width: 600px;
  margin: 0 auto;
}

.footer-item {
  transition: transform 0.3s ease, color 0.3s ease;
  padding: 12px;
  border-radius: 12px;

  &:hover {
    transform: translateY(-5px);
    background: rgba(218, 165, 32, 0.05);
  }
}

.footer-label {
  font-weight: 700;
  font-size: 14px;
  color: #222;
}

.footer-info {
  font-size: 13px;
  margin-top: 4px;
  color: #555;
  line-height: 1.5;
}

.copyright {
  font-size: 12px;
  color: #777;
}
</style>
