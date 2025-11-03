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
        <h2 class="text-h5 font-weight-bold mb-4">
          ✏️ Edit Reservation Status
        </h2>

        <v-form ref="editForm">
          <!-- Barber (Read Only) -->
          <v-text-field
            :value="selectedBarber"
            label="Barber"
            outlined
            dense
            rounded
            readonly
            prepend-icon="mdi-account"
          ></v-text-field>

          <!-- Services (Read Only) -->
          <v-text-field
            :value="selectedServices"
            label="Services"
            outlined
            dense
            rounded
            readonly
            prepend-icon="mdi-content-cut"
          ></v-text-field>

          <!-- Date & Time (Read Only) -->
          <v-text-field
            :value="reservationDateTime"
            label="Reservation Date & Time"
            outlined
            dense
            rounded
            readonly
            prepend-icon="mdi-calendar-clock"
          ></v-text-field>

          <!-- Status (Editable) -->
          <v-select
            v-model="form.status"
            :items="statusOptions"
            label="Reservation Status"
            outlined
            dense
            rounded
            prepend-icon="mdi-information"
            :rules="[(v) => !!v || 'Status is required']"
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
              @click="updateReservationStatus"
              :loading="loading"
            >
              Update Status
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
      loading: false,
      barbers: [],
      services: [],
      reservation: {},
      form: {
        status: "",
      },
      statusOptions: [
        { text: "Pending", value: "Pending" },
        { text: "Confirmed", value: "Confirmed" },
        { text: "Cancelled", value: "Cancelled" },
        { text: "Completed", value: "Completed" },
      ],
    };
  },
  computed: {
    selectedBarber() {
      if (!this.reservation.barber_id || !this.barbers.length) return "";
      const barber = this.barbers.find(
        (b) => b.id === this.reservation.barber_id
      );
      return barber ? barber.text : "";
    },
    selectedServices() {
      if (!this.reservation.service_id || !this.services.length) return "";

      let serviceIds = [];
      // Handle jika service_id adalah string JSON atau array
      if (typeof this.reservation.service_id === "string") {
        try {
          serviceIds = JSON.parse(this.reservation.service_id);
        } catch (e) {
          serviceIds = [this.reservation.service_id];
        }
      } else if (Array.isArray(this.reservation.service_id)) {
        serviceIds = this.reservation.service_id;
      }

      const selectedServices = this.services.filter((s) =>
        serviceIds.includes(s.id)
      );
      return selectedServices.map((s) => s.text).join(", ");
    },
    reservationDateTime() {
      if (this.reservation.reservation_time) {
        return new Date(this.reservation.reservation_time).toLocaleString();
      }
      return "";
    },
  },
  methods: {
    async fetchReservation() {
      try {
        const res = await axios.get(
          `http://127.0.0.1:8000/api/v1/reservations/${this.$route.params.id}`
        );
        this.reservation = res.data.data;
        this.form.status = this.reservation.status || "Pending";

        console.log("Reservation detail:", this.reservation);
      } catch (error) {
        console.error("Error fetching reservation:", error);
        this.$router.push("/reservation");
      }
    },
    async fetchBarbers() {
      try {
        const res = await axios.get("http://127.0.0.1:8000/api/v1/barbers");
        this.barbers = res.data;
      } catch (error) {
        console.error("Error fetching barbers:", error);
      }
    },
    async fetchServices() {
      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/v1/services/data-list"
        );
        this.services = res.data;
      } catch (error) {
        console.error("Error fetching services:", error);
      }
    },
    async updateReservationStatus() {
      if (!this.form.status) {
        alert("Please select a status");
        return;
      }

      this.loading = true;

      try {
        await axios.put(
          `http://127.0.0.1:8000/api/v1/reservations/${this.$route.params.id}/status`,
          {
            status: this.form.status,
          },
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        );

        this.$router.replace({
          name: "reservation",
          params: {
            snackbarOpt: {
              text: "Success! Reservation status updated.",
              type: "success",
            },
          },
        });
      } catch (error) {
        console.error("Error updating status:", error);
        alert("Failed to update reservation status. Please try again.");
      } finally {
        this.loading = false;
      }
    },
  },
  mounted() {
    this.fetchReservation();
    this.fetchBarbers();
    this.fetchServices();
  },
};
</script>
