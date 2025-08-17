<template>
  <div>
    <div class="container">
      <v-row>
        <v-col cols="12">
          <div class="title-text">About Us</div>
          <div class="subtitle-text">
            More Than Just a Haircut. It’s a Statement
          </div>
        </v-col>
      </v-row>
    </div>

    <v-divider style="width: 90%" class="mx-auto"></v-divider>

    <div class="explore-container">
      <v-row>
        <v-col cols="12">
          <p class="description-text">
            At Ven Barbershop, we believe that a haircut is not just a style,
            but also an expression of oneself. Since our establishment, we have
            been committed to providing high-quality barbering services with a
            touch of professionalism and warmth.
          </p>
          <p class="description-text">
            Our team of barbers consists of experts who always keep up with the
            latest trends, while still prioritizing the comfort and preferences
            of each customer. From classic cuts to modern styles, every detail
            is taken care of to deliver the best results.
          </p>
          <p class="description-text">
            In addition to traditional haircuts and shaves, Ven Barbershop also
            offers comprehensive hair care and grooming services, turning your
            experience into more than just a hair care session—it’s a moment of
            relaxation and self-care.
          </p>
          <p class="description-text">
            We are proud to be a place where every customer can feel confident
            in their appearance while enjoying the warm and friendly atmosphere
            that makes every visit memorable.
          </p>
        </v-col>
      </v-row>
    </div>
  </div>
</template>

<script>
export default {};
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

.description-text {
  font-size: fluid(10, 20);
  font-weight: 500;
  line-height: 150%;
  letter-spacing: 0%;
  text-align: start;
  color: #696969;
}
</style>
