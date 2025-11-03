<template>
  <div>
    <navbar></navbar>
    <div>
      <hero-section id="home"></hero-section>
      <about-section id="aboutus"></about-section>
      <barber-section id="barber"></barber-section>
      <service-section id="services"></service-section>
      <news-section id="news"></news-section>
      <Footer id="contact"></Footer>
    </div>
  </div>
</template>

<script>
import Navbar from "../navbar.vue";
import heroSection from "./hero-section-copy.vue";
import aboutSection from "./about-section-copy.vue";
import BarberSection from "./barber-section-copy.vue";
import serviceSection from "./service-section-copy.vue";
import NewsSection from "./news-section-copy.vue";
import Footer from "../footer.vue";

export default {
  components: {
    Navbar,
    heroSection,
    aboutSection,
    BarberSection,
    serviceSection,
    NewsSection,
    Footer,
  },
};
</script>

//
<style scoped lang="scss"></style>
