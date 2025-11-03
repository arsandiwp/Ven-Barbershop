<template>
  <v-container grid-list-md pt-10>
    <v-row>
      <v-col class="mt-3 btn-back">
        <router-link
          to="/reservation"
          style="text-decoration: none; color: black"
        >
          <v-icon>mdi-arrow-left-thin</v-icon> Back
        </router-link>
      </v-col>
    </v-row>

    <v-sheet color="white" elevation="2" class="mt-4" rounded>
      <v-card class="pa-6">
        <!-- <h2 class="text-h5 font-weight-bold mb-4">✏️ Edit Reservation</h2> -->

        <v-form ref="editForm">
          <!-- Barber -->
          <v-select
            v-model="form.barber_id"
            :items="barbers"
            item-text="text"
            item-value="id"
            label="Select Barber"
            outlined
            dense
            rounded
          ></v-select>

          <!-- Services -->
          <v-select
            v-model="form.service_id"
            :items="services"
            item-text="text"
            item-value="id"
            label="Select Service"
            outlined
            dense
            rounded
            multiple
          ></v-select>

          <!-- Date -->
          <v-menu
            v-model="menuDate"
            :close-on-content-click="false"
            transition="scale-transition"
            offset-y
            min-width="290px"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="form.date"
                label="Reservation Date"
                prepend-icon="mdi-calendar"
                readonly
                outlined
                rounded
                dense
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="form.date"
              @input="menuDate = false"
              color="primary"
            ></v-date-picker>
          </v-menu>

          <!-- Time -->
          <v-text-field
            v-model="form.time"
            label="Reservation Time (HH:mm)"
            prepend-icon="mdi-clock-outline"
            outlined
            dense
            rounded
          ></v-text-field>

          <!-- Status -->
          <v-select
            v-model="form.status"
            :items="['Pending', 'Confirmed', 'Cancelled', 'Completed']"
            label="Reservation Status"
            outlined
            dense
            rounded
          ></v-select>

          <!-- Actions -->
          <div class="mt-6 d-flex justify-space-between">
            <v-btn text rounded @click="$router.push('/reservation')">
              Cancel
            </v-btn>
            <v-btn
              color="amber darken-2"
              style="color: white"
              rounded
              @click="updateReservation"
            >
              Save Changes
            </v-btn>
          </div>
        </v-form>
      </v-card>
    </v-sheet>
  </v-container>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      menuDate: false,
      barbers: [],
      services: [],
      form: {
        id: null,
        barber_id: "",
        service_id: [],
        date: "",
        time: "",
        status: "",
      },
    };
  },
  methods: {
    async fetchReservation() {
      const res = await axios.get(
        `http://127.0.0.1:8000/api/v1/reservations/${this.$route.params.id}`
      );
      const data = res.data.data;
      console.log("Reservation detail:", data); // 🔍 cek field aslinya

      let date = "";
      let time = "";

      // ✅ kalau backend kirim reservation_time
      if (data.reservation_time) {
        const parts = data.reservation_time.split(" ");
        date = parts[0];
        time = parts[1] ? parts[1].slice(0, 5) : "";
      }

      // ✅ kalau backend kirim date & time terpisah
      if (data.date) date = data.date;
      if (data.time) time = data.time;

      this.form = {
        id: data.id,
        barber_id: data.barber_id,
        service_id: data.service_id || [],
        date,
        time,
        status: data.status || "Pending",
      };
    },
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
    async updateReservation() {
      await axios.put(
        `http://127.0.0.1:8000/api/v1/reservations/${this.form.id}`,
        {
          barber_id: this.form.barber_id,
          service_id: this.form.service_id,
          // service_id: JSON.stringify(this.form.service_id),
          reservation_time: this.form.date + " " + this.form.time + ":00",
          status: this.form.status,
        },
        {
          headers: {
            // Authorization: `Bearer ${token}`,
            "Content-Type": "application/json", // ✅ penting agar Laravel baca sebagai JSON
          },
        }
      );

      this.$router.replace({
        name: "reservation",
        params: {
          snackbarOpt: {
            text: "Success! Reservation updated.",
            type: "success",
          },
        },
      });
    },
  },
  mounted() {
    this.fetchReservation();
    this.fetchBarbers();
    this.fetchServices();
  },
};
</script>
