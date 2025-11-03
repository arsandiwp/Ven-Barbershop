<template>
  <section class="about-section">
    <v-container>
      <!-- Title -->
      <v-row justify="center" class="text-center">
        <v-col cols="12" md="8">
          <h2 class="about-title">About Us</h2>
          <p class="about-subtitle">
            More Than Just a Haircut. It’s a Statement
          </p>
          <!-- <v-divider class="about-divider mx-auto my-6"></v-divider> -->
        </v-col>
      </v-row>

      <v-divider style="width: 90%" color="goldenrod" class="mx-auto mb-8"></v-divider>

      <!-- Content with Image -->
      <v-row align="center" justify="center">
        <!-- Left: Image -->
        <v-col cols="12" md="5" class="mb-6 mb-md-0 text-center">
          <v-img
            src="/img/about-image.jpg"
            aspect-ratio="1"
            class="about-image mx-auto"
            cover
          />
        </v-col>

        <!-- Right: Text -->
        <v-col cols="12" md="7">
          <p class="about-text">
            At <strong>Ven Barbershop</strong>, we believe that a haircut is not
            just a style, but also an expression of oneself. Since our
            establishment, we have been committed to providing high-quality
            barbering services with a touch of professionalism and warmth.
          </p>
          <p class="about-text">
            Our team of barbers consists of experts who always keep up with the
            latest trends, while still prioritizing the comfort and preferences
            of each customer. From classic cuts to modern styles, every detail
            is taken care of to deliver the best results.
          </p>

          <!-- Quote Box -->
          <div class="quote-box my-6">
            ✂️ <em>"A good haircut is the foundation of confidence."</em>
          </div>

          <p class="about-text">
            In addition to traditional haircuts and shaves, Ven Barbershop also
            offers comprehensive hair care and grooming services, turning your
            experience into more than just a hair care session—it’s a moment of
            relaxation and self-care.
          </p>
          <p class="about-text">
            We are proud to be a place where every customer can feel confident
            in their appearance while enjoying the warm and friendly atmosphere
            that makes every visit memorable.
          </p>
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>

<script>
export default {
  name: "AboutSection",
};
</script>

<style scoped lang="scss">
.about-section {
  padding: 60px 20px;
  background-color: #fafafa;
  position: relative;
  background: url("https://www.transparenttextures.com/patterns/white-wall-3.png");

}

/* Decorative subtle pattern background */
// .about-section::before {
//   content: "";
//   background: url("https://www.transparenttextures.com/patterns/white-wall-3.png");
//   opacity: 0.08;
//   top: 0;
//   left: 0;
//   right: 0;
//   bottom: 0;
//   position: absolute;
// }

.about-title {
  font-size: 2.5rem;
  font-weight: 700;
  letter-spacing: 2px;
  color: #222;
  position: relative;
}

.about-subtitle {
  font-size: 1.2rem;
  font-weight: 400;
  color: #555;
  margin-top: 8px;
}

.about-divider {
  width: 80px;
  height: 3px;
  background-color: #d4a017;
  border-radius: 4px;
  position: relative;
}

.about-image {
  max-width: 350px;
  border-radius: 20px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  transition: transform 0.3s ease;
}
.about-image:hover {
  transform: scale(1.05);
}

.about-text {
  font-size: 1.05rem;
  line-height: 1.8;
  color: #555;
  margin-bottom: 18px;
  text-align: justify;
}

/* Quote Box */
.quote-box {
  background: #fff;
  border-left: 5px solid #d4a017;
  padding: 16px 20px;
  font-size: 1.1rem;
  color: #333;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Responsive */
@media (max-width: 960px) {
  .about-title {
    font-size: 2rem;
  }
  .about-subtitle {
    font-size: 1rem;
  }
  .about-text {
    font-size: 1rem;
    line-height: 1.6;
  }
}

@media (max-width: 600px) {
  .about-section {
    padding: 40px 16px;
  }
  .about-title {
    font-size: 1.8rem;
  }
  .about-subtitle {
    font-size: 0.95rem;
  }
  .about-text {
    font-size: 0.95rem;
  }
}
</style>
