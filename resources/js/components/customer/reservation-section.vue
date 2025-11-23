<template>
  <v-container fluid class="py-6 px-4 reservation-bg">
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <!-- Hero -->
        <div class="reservation-hero text-center py-8 px-4 mb-6">
          <h2 class="text-h4 font-weight-bold mb-2">✂️ Buat Reservasi</h2>
          <p class="grey--text">
            Pilih tukang cukur, layanan, dan waktu yang sesuai dengan Anda
          </p>
        </div>

        <v-stepper v-model="step" class="rounded-2xl elevation-3 glass-effect">
          <v-stepper-header>
            <v-stepper-step :complete="step > 1" color="amber darken-2" step="1"
              >Pilih Tukang Cukur</v-stepper-step
            >
            <v-divider></v-divider>
            <v-stepper-step :complete="step > 2" color="amber darken-2" step="2"
              >Pilih Layanan</v-stepper-step
            >
            <v-divider></v-divider>
            <v-stepper-step :complete="step > 3" color="amber darken-2" step="3"
              >Pilih Tanggal</v-stepper-step
            >
            <v-divider></v-divider>
            <v-stepper-step :complete="step > 4" color="amber darken-2" step="4"
              >Pilih Waktu</v-stepper-step
            >
          </v-stepper-header>

          <!-- Step Contents -->
          <v-stepper-items>
            <!-- Step 1: Barber -->
            <v-stepper-content step="1">
              <h3 class="mb-4 font-weight-medium">
                Siapa tukang cukur pilihan Anda?
              </h3>
              <v-row dense>
                <v-col
                  v-for="barber in barbers"
                  :key="barber.id"
                  cols="12"
                  sm="6"
                  md="4"
                >
                  <v-card
                    class="pa-5 rounded-xl text-center transition-ease"
                    outlined
                    :class="{ 'selected-card': selectedBarber === barber.id }"
                    @click="selectedBarber = barber.id"
                  >
                    <v-avatar size="80" class="mb-3">
                      <img
                        :src="
                          barber.avatar ||
                          'https://cdn-icons-png.flaticon.com/512/921/921079.png'
                        "
                        alt="barber"
                      />
                    </v-avatar>
                    <div class="font-weight-bold text-subtitle-1">
                      {{ barber.text }}
                    </div>
                    <div class="grey--text caption mt-1">
                      {{ barber.email }}
                    </div>
                  </v-card>
                </v-col>
              </v-row>

              <div class="mt-6 d-flex justify-end">
                <v-btn
                  color="amber darken-2"
                  style="color: white"
                  rounded
                  :disabled="!selectedBarber"
                  @click="step = 2"
                  >Lanjut</v-btn
                >
              </div>
            </v-stepper-content>

            <!-- Step 2: Services -->
            <v-stepper-content step="2">
              <h3 class="mb-4 font-weight-medium">
                Pilih layanan yang Anda inginkan?
              </h3>
              <v-row dense>
                <v-col
                  v-for="service in services"
                  :key="service.id"
                  cols="12"
                  sm="6"
                  md="4"
                >
                  <v-card
                    class="pa-5 rounded-xl text-center transition-ease"
                    outlined
                    :class="{
                      'selected-card': selectedServices.includes(service.id),
                    }"
                    @click="toggleService(service.id)"
                  >
                    <v-avatar size="80" class="mb-3">
                      <img
                        :src="
                          service.avatar ||
                          'https://cdn-icons-png.flaticon.com/512/921/921079.png'
                        "
                        alt="barber"
                      />
                    </v-avatar>
                    <div class="font-weight-bold text-subtitle-1">
                      {{ service.text }}
                    </div>
                    <div class="grey--text caption mt-1">
                      {{ service.desc }}
                    </div>
                    <div class="grey--text caption mt-1">
                      Rp
                      {{
                        service.price
                          ? Number(service.price).toLocaleString("id-ID")
                          : "50.000"
                      }}
                    </div>
                  </v-card>
                </v-col>
              </v-row>

              <div class="mt-6 d-flex justify-space-between">
                <v-btn text rounded @click="step = 1">Kembali</v-btn>
                <v-btn
                  color="primary"
                  rounded
                  :disabled="!selectedServices.length"
                  @click="step = 3"
                  >Lanjut</v-btn
                >
              </div>
            </v-stepper-content>

            <!-- Step 3: Date -->
            <v-stepper-content step="3">
              <h3 class="mb-4 font-weight-medium">
                Kapan Anda ingin potong rambut?
              </h3>
              <v-menu
                v-model="menuDate"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                min-width="290px"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    v-model="selectedDate"
                    label="Pilih Tanggal"
                    prepend-icon="mdi-calendar"
                    readonly
                    outlined
                    rounded
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="selectedDate"
                  @input="menuDate = false"
                  color="primary"
                ></v-date-picker>
              </v-menu>

              <div class="mt-6 d-flex justify-space-between">
                <v-btn text rounded @click="step = 2">Kembali</v-btn>
                <v-btn
                  color="primary"
                  rounded
                  :disabled="!selectedDate"
                  @click="fetchAvailableSlots"
                  >Lanjut</v-btn
                >
              </div>
            </v-stepper-content>

            <!-- Step 4: Time -->
            <v-stepper-content step="4">
              <h3 class="mb-4 font-weight-medium">
                Pilih waktu yang tersedia?
              </h3>
              <v-row dense>
                <v-col
                  v-for="time in availableSlots"
                  :key="time"
                  cols="6"
                  sm="4"
                  md="3"
                >
                  <v-card
                    class="pa-4 rounded-xl text-center transition-ease"
                    outlined
                    :class="{ 'selected-card': selectedTime === time }"
                    @click="selectedTime = time"
                  >
                    <div class="font-weight-bold">{{ time }}</div>
                  </v-card>
                </v-col>
              </v-row>

              <div class="mt-6 d-flex justify-space-between">
                <v-btn text rounded @click="step = 3">Kembali</v-btn>
                <v-btn
                  color="primary"
                  rounded
                  :disabled="!selectedTime"
                  @click="submitReservation"
                  >Buat Reservasi</v-btn
                >
              </div>
            </v-stepper-content>
          </v-stepper-items>
        </v-stepper>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      step: 1,
      menuDate: false,
      barbers: [],
      services: [],
      availableSlots: [],
      selectedBarber: null,
      selectedServices: [],
      selectedDate: null,
      selectedTime: null,
    };
  },
  methods: {
    async fetchBarbers() {
      const res = await axios.get("http://127.0.0.1:8000/api/v1/barbers");
      this.barbers = res.data;
    },
    async fetchServices() {
      const res = await axios.get(
        "http://127.0.0.1:8000/api/v1/services/data-list"
      );
      this.services = res.data;
    },
    toggleService(serviceId) {
      if (this.selectedServices.includes(serviceId)) {
        this.selectedServices = this.selectedServices.filter(
          (id) => id !== serviceId
        );
      } else {
        this.selectedServices.push(serviceId);
      }
    },
    async fetchAvailableSlots() {
      const res = await axios.post(
        "http://127.0.0.1:8000/api/v1/reservations/available-slots",
        {
          barber_id: this.selectedBarber,
          date: this.selectedDate,
          service_id: this.selectedServices,
        },
        {
          headers: { Authorization: localStorage.token },
        }
      );
      this.availableSlots = res.data.available_slots;
      this.step = 4;
    },
    async submitReservation() {
      await axios.post(
        "http://127.0.0.1:8000/api/v1/reservations",
        {
          barber_id: this.selectedBarber,
          service_id: this.selectedServices,
          reservation_time: this.selectedDate + " " + this.selectedTime + ":00",
        },
        {
          headers: { Authorization: localStorage.token },
        }
      );
      // alert("Reservasi berhasil dibuat!");
      this.$router.push("/");
    },
  },
  mounted() {
    this.fetchBarbers();
    this.fetchServices();
  },
};
</script>

<style scoped>
.reservation-bg {
  /* min-height: 100vh; */
  display: flex;
  justify-content: center;
  align-items: flex-start;
  padding-bottom: 50px;
}

.glass-effect {
  background: rgba(255, 255, 255, 0.92) !important;
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255, 255, 255, 0.4);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
  padding: 16px;
}

.reservation-hero {
  /* background: linear-gradient(135deg, #2196f3, #6ec6ff); */
  color: black;
  border-radius: 16px;
  box-shadow: 0 6px 20px rgba(112, 113, 113, 0.3);
}

.selected-card {
  border: 2px solid #ffa000 !important;
  /* background: linear-gradient(135deg, #e3f2fd, #bbdefb) !important; */
  /* box-shadow: 0 6px 18px rgba(33, 150, 243, 0.25); */
  transform: translateY(-3px);
}

.v-card {
  background: #ffffff;
  border-radius: 16px;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.v-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.v-stepper {
  border-radius: 24px !important;
}

/* 📱 mobile */
@media (max-width: 600px) {
  .reservation-hero {
    padding: 24px 16px;
    margin-right: 0; /* buang fixed margin */
    text-align: center;
  }
  .v-stepper__header {
    flex-direction: column;
    align-items: stretch; /* biar full width */
  }
  .v-stepper {
    padding: 8px;
  }
}
</style>
