<template>
  <v-footer padless class="footer">
    <v-container>
      <v-row justify="center" class="text-center">
        <v-col cols="12">
          <div class="footer-title">Contact Us</div>
          <div class="footer-subtitle">
            We are here to help you with any questions or concerns you may have.
          </div>
        </v-col>
      </v-row>

      <v-row justify="center" class="text-center mt-6">
        <v-col cols="12" sm="3">
          <v-icon color="goldenrod" size="28">mdi-map-marker</v-icon>
          <div class="footer-label">ADDRESS</div>
          <div class="footer-info">
            Jl. Ringin Raya No.121, Ngringin, Condongcatur, Depok, Sleman, DIY
          </div>
        </v-col>

        <v-col cols="12" sm="3">
          <v-icon color="goldenrod" size="28">mdi-email-outline</v-icon>
          <div class="footer-label">EMAIL</div>
          <div class="footer-info">info@company.com</div>
        </v-col>

        <v-col cols="12" sm="3">
          <v-icon color="goldenrod" size="28">mdi-phone</v-icon>
          <div class="footer-label">PHONE</div>
          <div class="footer-info">085967279857</div>
        </v-col>

        <v-col cols="12" sm="3">
          <v-icon color="goldenrod" size="28">mdi-clock-outline</v-icon>
          <div class="footer-label">WORKING HOURS</div>
          <div class="footer-info">10:00am - 10:00pm</div>
        </v-col>
      </v-row>

      <v-divider class="mt-6" color="goldenrod"></v-divider>

      <v-row justify="center" class="mt-4">
        <v-col cols="12" class="text-center">
          © Copyright Barbershop 2025
        </v-col>
      </v-row>
    </v-container>
  </v-footer>
</template>

<script>
export default {
  name: "AppFooter",
};
</script>

<style lang="scss" scoped>
@import "../../../sass/calc.scss";

.footer {
  background-color: #fff;
  padding-top: 40px;
  padding-bottom: 20px;
}

.footer-title {
  font-size: fluid(14, 24);
  font-weight: 500;
  line-height: 150%;
  letter-spacing: 0%;
  color: black;
  text-align: center;
}

.footer-subtitle {
  font-size: fluid(10, 20);
  font-weight: 400;
  line-height: 150%;
  letter-spacing: 0%;
  color: black;
  text-align: center;
}

.footer-label {
  font-weight: 700;
  margin-top: 8px;
}

.footer-info {
  font-size: 14px;
  margin-top: 4px;
  color: #333;
}
</style>
