<template>
  <div class="explore-container">
    <hero-section></hero-section>
    <about-section></about-section>
    <barber-section></barber-section>
    <service-section></service-section>
    <news-section></news-section>
    <Footer></Footer>
  </div>
</template>

<script>
import heroSection from "./hero-section.vue";
import aboutSection from "./about-section.vue";
import BarberSection from "./barber-section.vue";
import serviceSection from "./service-section.vue";
import NewsSection from "./news-section.vue";
import Footer from "../footer.vue";

export default {
  components: {
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
<style scoped lang="scss">
// @import "../../../../sass/_calc.scss";

// .explore-container {
//   position: relative;
//   width: 100%;
//   height: 100vh;
//   overflow: hidden;
//   display: flex;
//   align-items: center;
//   justify-content: center;
// }

// .background-image {
//   position: absolute;
//   top: 0;
//   left: 0;
//   width: 100%;
//   height: 100%;
//   background-size: cover;
//   background-position: center;
//   z-index: 1;
//   filter: brightness(0.5);
// }

// .background-skeleton-wrapper,
// .background-skeleton {
//   position: absolute;
//   width: 100%;
//   height: 100%;
//   z-index: 1;
// }

// .content-wrapper {
//   position: relative;
//   z-index: 2;
//   padding: fluid(30, 100) fluid(24, 150);
//   width: 100%;
// }

// .hero-content {
//   max-width: 800px;
//   color: white;

//   h1.hero-title {
//     font-size: fluid(28, 64);
//     font-weight: bold;
//     margin-bottom: 16px;
//   }

//   p.hero-subtitle {
//     font-size: fluid(16, 24);
//     margin-bottom: 32px;
//     opacity: 0.9;
//   }

//   .hero-button {
//     font-weight: 600;
//     border-radius: 8px;
//   }
// }
//
</style>
