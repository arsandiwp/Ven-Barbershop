<template>
  <div>
    <header>
      <nav>
        <div
          :class="['nav-container custom-border-header']"
          :style="{ background: backgroundGradient }"
        >
          <!-- Logo -->
          <div class="left-section">
            <router-link :to="`/`">
              <img src="/img/logo-image.png" alt="logo" class="logo-button" />
            </router-link>
          </div>

          <!-- Desktop Menu -->
          <ul class="item-container d-none d-md-flex">
            <li
              v-for="(link, index) in links"
              :key="index"
              class="item"
              @mouseover="setHoveredIndex(index)"
              @mouseout="setHoveredIndex(-1)"
            >
              <a
                href="javascript:void(0)"
                class="item-link"
                :class="{
                  faded: hoveredIndex !== -1 && hoveredIndex !== index,
                }"
                @click="scrollToSection(link.key)"
              >
                <div :class="'text-with-icon ' + titleColor + '--text'">
                  <span
                    :class="{ 'active-menu': link.text == activeMenuGroup }"
                  >
                    {{ link.text }}
                  </span>
                </div>
              </a>
            </li>

            <li class="item ml-4">
              <v-btn
                color="red darken-2"
                dark
                rounded
                small
                @click="goToReservation"
              >
                Reservasi Sekarang
              </v-btn>
            </li>

            <!-- Login Button Desktop -->
            <li class="item ml-4" v-if="!isLoggedIn">
              <v-btn
                color="amber darken-2"
                dark
                rounded
                small
                @click="goToLogin"
              >
                Masuk
              </v-btn>
            </li>

            <!-- Jika sudah login -->
            <li class="item ml-4" v-else>
              <v-menu offset-y>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    color="amber darken-2"
                    dark
                    rounded
                    small
                    v-bind="attrs"
                    v-on="on"
                  >
                    {{ user?.name || "Account" }}
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item @click="goToProfile">
                    <v-list-item-title>Profile</v-list-item-title>
                  </v-list-item>
                  <v-list-item @click="logout">
                    <v-list-item-title>Keluar</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </li>
          </ul>

          <!-- Mobile Dropdown -->
          <div class="d-flex d-md-none">
            <v-menu v-model="menuShown" bottom left offset-y>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon v-bind="attrs" v-on="on">
                  <v-icon color="white">
                    {{ menuShown ? "mdi-close" : "mdi-menu" }}
                  </v-icon>
                </v-btn>
              </template>

              <!-- <v-list class="mobile-menu">
                <v-list-item
                  v-for="(item, index2) in links"
                  :key="'dd-' + index2"
                  dense
                  @click="
                    scrollToSection(item.key);
                    menuShown = false;
                  "
                >
                  <v-list-item-title>
                    <span class="mobile-link">{{ item.text }}</span>
                  </v-list-item-title>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item dense @click="goToReservation">
                  <v-list-item-title>
                    <v-btn color="red darken-2" dark rounded block>
                      Book Now
                    </v-btn>
                  </v-list-item-title>
                </v-list-item>

                <v-list-item
                  dense
                  @click="
                    goToLogin;
                    menuShown = false;
                  "
                >
                  <v-list-item-title>
                    <v-btn color="primary" dark rounded block> Login </v-btn>
                  </v-list-item-title>
                </v-list-item>
              </v-list> -->

              <v-list class="mobile-menu">
                <!-- Menu Links -->
                <v-list-item
                  v-for="(item, index2) in links"
                  :key="'dd-' + index2"
                  dense
                  @click="
                    scrollToSection(item.key);
                    menuShown = false;
                  "
                >
                  <v-list-item-title>
                    <span class="mobile-link">{{ item.text }}</span>
                  </v-list-item-title>
                </v-list-item>

                <v-divider></v-divider>

                <!-- Kalau belum login -->
                <v-list-item v-if="!isLoggedIn" dense @click="goToLogin">
                  <v-list-item-title>
                    <v-btn color="primary" dark rounded block> Login </v-btn>
                  </v-list-item-title>
                </v-list-item>

                <!-- Kalau sudah login -->
                <template v-else>
                  <!-- <v-list-item dense>
                    <v-list-item-title class="mobile-link">
                      {{ user?.name || "Account" }}
                    </v-list-item-title>
                  </v-list-item> -->

                  <v-list-item dense @click="goToProfile">
                    <v-list-item-title class="mobile-link"
                      >Profile</v-list-item-title
                    >
                  </v-list-item>

                  <v-list-item dense @click="logout">
                    <v-list-item-title class="mobile-link"
                      >Logout</v-list-item-title
                    >
                  </v-list-item>
                </template>

                <v-divider></v-divider>

                <!-- Book Now button -->
                <v-list-item dense @click="goToReservation">
                  <v-list-item-title>
                    <v-btn color="red darken-2" dark rounded block>
                      Book Now
                    </v-btn>
                  </v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
        </div>
      </nav>
    </header>
  </div>
</template>

<script>
export default {
  props: {
    backgroundGradient: String,
    logoImage: {
      type: String,
      default: "/img/images.png",
    },
    activeMenuGroup: {
      type: String,
    },
    titleColor: {
      type: String,
      default: "#f5f5f5",
    },
  },
  data() {
    return {
      hoveredIndex: -1,
      isMenuMobileClicked: false, // tambahkan ini
      links: [
        { text: "Beranda", key: "home" },
        { text: "Tentang Kami", key: "aboutus" },
        { text: "Tukang Cukur Kami", key: "barber" },
        { text: "Layanan", key: "services" },
        { text: "Berita", key: "news" },
        { text: "Kontak", key: "contact" },
      ],
      menuShown: false,
      user: null,
    };
  },
  computed: {
    isLoggedIn() {
      return !!localStorage.getItem("customerLogin");
    },
  },
  watch: {
    menuShown(val) {
      this.isMenuMobileClicked = val;
    },
  },
  mounted() {
    if (this.isLoggedIn) {
      this.user = JSON.parse(localStorage.getItem("customerLogin"));
    }
  },
  methods: {
    setHoveredIndex(index) {
      this.hoveredIndex = index;
    },
    scrollToSection(sectionId) {
      const section = document.getElementById(sectionId);
      if (section) {
        section.scrollIntoView({ behavior: "smooth" });
      }
    },
    goToLogin() {
      this.$router.push("/login");
    },
    goToProfile() {
      this.$router.push("/profile");
    },
    goToReservation() {
      if (!localStorage.getItem("customerLogin")) {
        this.$router.push("/login"); // belum login → ke login dulu
      } else {
        this.$router.push("/reservation"); // sudah login → ke reservasi
      }
    },
    logout() {
      localStorage.removeItem("customerLogin");
      localStorage.removeItem("token");
      localStorage.removeItem("active_until");
      this.$router.replace({ name: "home" });
      window.location.reload();
    },
  },
};
</script>

<style scoped>
/* Global */
header {
  width: 100%;
  position: absolute;
  z-index: 9999;
  top: 0;
  left: 0;
}
.nav-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 15px 4vw;
}
.left-section {
  display: flex;
  align-items: center;
}
.logo-button {
  width: 70px;
  max-height: 70px;
  object-fit: contain;
}

/* Desktop */
.item-container {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
  list-style: none;
  margin: 0;
  padding: 0;
}
.item {
  margin: 0 12px;
}
.item-link {
  text-decoration: none;
  color: #f5f5f5d2;
  font-weight: 600;
  font-size: calc(12px + 0.3vw);
}
.text-with-icon {
  display: flex;
  align-items: center;
}
.active-menu {
  font-weight: 700 !important;
  color: white !important;
}

/* Mobile Dropdown */
.mobile-menu {
  background: #222 !important;
}
.mobile-link {
  color: #fff;
  font-weight: 600;
  font-size: 16px;
}
</style>
