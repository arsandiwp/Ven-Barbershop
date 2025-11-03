<template>
  <div>
    <!-- <navbar :backgroundGradient="customGradient"></navbar> -->
    <!-- <v-container class="reservation-container" style="margin-top: 90px"> -->
    <div style="height: 90px">&nbsp;</div>
    <reservation></reservation>
    <Footer></Footer>
    <!-- </v-container> -->
  </div>
</template>

<script>
import Navbar from "../navbar.vue";
import Footer from "../footer.vue";
import Reservation from "./reservation-section.vue";

export default {
  data() {
    return {
      customGradient:
        "black",
    };
  },
  components: {
    Navbar,
    Footer,
    Reservation,
  },
};
</script>
