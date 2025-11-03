<template>
  <v-container grid-list-md pt-10>
    <!-- <h1 class="mb-4">📊 Dashboard</h1> -->
    <HeaderBar title="Dashboard" />

    <!-- Summary Cards -->
    <v-row>
      <v-col
        cols="12"
        sm="6"
        md="3"
        v-for="card in summaryCards"
        :key="card.title"
      >
        <!-- <v-card class="pa-4 text-center">
          <h3>{{ card.title }}</h3>
          <h2>{{ card.value }}</h2>
        </v-card> -->
        <v-card class="pa-4 text-center">
          <h3>{{ card.title }}</h3>
          <h2>{{ card.value }}</h2>
          <v-sparkline
            :value="card.trend"
            color="primary"
            type="trend"
            smooth
          />
        </v-card>
      </v-col>
    </v-row>

    <!-- Charts -->
    <v-row class="mt-6">
      <v-col cols="12" md="6">
        <v-card class="pa-4">
          <h3 class="mb-2">Reservasi Berdasarkan Status</h3>
          <PieChart v-if="chartDataStatus" :chart-data="chartDataStatus" />
        </v-card>
      </v-col>

      <v-col cols="12" md="6">
        <v-card class="pa-4">
          <h3 class="mb-2">Summary Data</h3>
          <BarChart v-if="chartDataSummary" :chart-data="chartDataSummary" />
        </v-card>
      </v-col>
    </v-row>

    <!-- Line Chart -->
    <v-row class="mt-6">
      <v-col cols="12">
        <v-card class="pa-4">
          <h3 class="mb-2">Trend Reservasi 7 Hari Terakhir</h3>
          <LineChart v-if="chartDataTrend" :chart-data="chartDataTrend" />
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from "axios";
import { Pie, Bar, Line } from "vue-chartjs";

// Chart.js v2 style components
const PieChart = {
  extends: Pie,
  props: ["chartData"],
  mounted() {
    this.renderChart(this.chartData, {
      responsive: true,
      maintainAspectRatio: false,
    });
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.renderChart(val, { responsive: true, maintainAspectRatio: false });
      },
    },
  },
};

const BarChart = {
  extends: Bar,
  props: ["chartData"],
  mounted() {
    this.renderChart(this.chartData, {
      responsive: true,
      maintainAspectRatio: false,
    });
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.renderChart(val, { responsive: true, maintainAspectRatio: false });
      },
    },
  },
};

const LineChart = {
  extends: Line,
  props: ["chartData"],
  mounted() {
    this.renderChart(this.chartData, {
      responsive: true,
      maintainAspectRatio: false,
      scales: { yAxes: [{ ticks: { beginAtZero: true } }] },
    });
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.renderChart(val, {
          responsive: true,
          maintainAspectRatio: false,
          scales: { yAxes: [{ ticks: { beginAtZero: true } }] },
        });
      },
    },
  },
};

export default {
  name: "Dashboard",
  components: { PieChart, BarChart, LineChart },
  data() {
    return {
      summary: {},
      reservationsByStatus: {},
      trend: { dates: [], totals: [] },
    };
  },
  computed: {
    summaryCards() {
      return [
        {
          title: "Total Customers",
          value: this.summary.total_customers || 0,
          trend: [3, 5, 2, 8, 6, 7, 9],
        },
        {
          title: "Total Barbers",
          value: this.summary.total_barbers || 0,
          trend: [1, 1, 2, 2, 3, 3, 4],
        },
        {
          title: "Total Services",
          value: this.summary.total_services || 0,
          trend: [5, 6, 6, 7, 8, 9, 10],
        },
        {
          title: "Total Reservations",
          value: this.summary.total_reservations || 0,
          trend: this.trend.totals,
        },
        {
          title: "Today Reservations",
          value: this.summary.today_reservations || 0,
          trend: [1, 2, 3, 2, 4, 5, 6],
        },
      ];
    },

    chartDataStatus() {
      if (!this.reservationsByStatus) return null;
      return {
        labels: Object.keys(this.reservationsByStatus),
        datasets: [
          {
            label: "Reservasi",
            data: Object.values(this.reservationsByStatus),
            backgroundColor: ["#42A5F5", "#66BB6A", "#FFA726", "#EF5350"],
          },
        ],
      };
    },
    chartDataSummary() {
      if (!this.summary) return null;
      return {
        labels: ["Customers", "Barbers", "Services", "Reservations"],
        datasets: [
          {
            label: "Total",
            data: [
              this.summary.total_customers || 0,
              this.summary.total_barbers || 0,
              this.summary.total_services || 0,
              this.summary.total_reservations || 0,
            ],
            backgroundColor: ["#42A5F5", "#66BB6A", "#AB47BC", "#FFA726"],
          },
        ],
      };
    },
    chartDataTrend() {
      if (!this.trend.dates.length) return null;
      return {
        labels: this.trend.dates,
        datasets: [
          {
            label: "Reservasi",
            data: this.trend.totals,
            fill: true,
            borderColor: "#42A5F5",
            backgroundColor: "rgba(66,165,245,0.2)",
          },
        ],
      };
    },
  },
  mounted() {
    this.fetchSummary();
    this.fetchTrend();
  },
  methods: {
    async fetchSummary() {
      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/v1/dashboard/summary"
        );
        this.summary = res.data;
        this.reservationsByStatus = res.data.reservations_by_status || {};
      } catch (e) {
        console.error("Failed to fetch summary", e);
      }
    },
    async fetchTrend() {
      try {
        const res = await axios.get(
          "http://127.0.0.1:8000/api/v1/dashboard/trend"
        );
        this.trend = res.data;
      } catch (e) {
        console.error("Failed to fetch trend", e);
      }
    },
  },
};
</script>

<style scoped>
.v-card {
  min-height: 250px;
}
</style>
