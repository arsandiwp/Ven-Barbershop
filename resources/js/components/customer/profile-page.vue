<template>
  <div>
    <!-- <navbar /> -->

    <v-container grid-list-md pt-10 min-height="100vh">
      <v-row>
        <v-col class="mt-3 btn-back">
          <a
            @click="$router.go(-1)"
            style="text-decoration: none; color: black"
          >
            <v-icon>mdi-arrow-left-thin</v-icon> Kembali
          </a>
        </v-col>
        <v-col class="text-right">
          <v-btn
            class="mt-2"
            color="primary"
            elevation-0
            small
            @click="editable = !editable"
            v-if="!editable"
            style="text-decoration: none; color: white"
          >
            <v-icon size="16">mdi-pencil-outline</v-icon>&nbsp;&nbsp;Edit
          </v-btn>
          <v-btn
            class="mt-2"
            color="error"
            elevation-0
            small
            @click="editable = !editable"
            v-else
            style="text-decoration: none; color: white"
          >
            <v-icon size="16">mdi-cancel</v-icon>&nbsp;&nbsp;Batal
          </v-btn>
        </v-col>
      </v-row>

      <!-- Profile Form -->
      <v-sheet color="white" elevation="2" class="mt-4" rounded>
        <v-form
          @submit.prevent="validateForm()"
          v-model="valid"
          ref="form"
          autofocus
          lazy-validation
        >
          <v-container>
            <image-input
              v-model="avatar"
              :editable="editable"
              name="photo"
              class="text-center"
            >
              <v-card-text slot="activator">
                <Avatar
                  id="profile"
                  :image="avatar.imageURL != null ? avatar.imageURL : ''"
                  :size="175"
                  @radius="175"
                  color="lightgrey"
                ></Avatar>
              </v-card-text>
            </image-input>

            <v-row>
              <v-col cols="12" md="6">
                <label for="name" class="text-label">Nama*</label>
                <v-text-field
                  id="name"
                  placeholder="Masukan Nama"
                  outlined
                  type="text"
                  v-model="model.name"
                  :rules="nameRules"
                  :readonly="!editable"
                  dense
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <label for="email" class="text-label">Email*</label>
                <v-text-field
                  id="email"
                  placeholder="Masukan Email"
                  outlined
                  type="email"
                  v-model="model.email"
                  :rules="emailRules"
                  :readonly="!editable"
                  dense
                ></v-text-field>
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <label for="phone" class="text-label">Telepon</label>
                <v-text-field
                  id="phone"
                  placeholder="Masukan Telepon"
                  outlined
                  v-model="model.phone"
                  :readonly="!editable"
                  dense
                />
              </v-col>
            </v-row>

            <v-row>
              <v-col cols="12">
                <label for="address" class="text-label">Alamat</label>
                <v-textarea
                  id="address"
                  placeholder="Masukan Alamat"
                  outlined
                  auto-grow
                  no-resize
                  rows="3"
                  v-model="model.address"
                  :readonly="!editable"
                  dense
                ></v-textarea>
              </v-col>
            </v-row>
            <div class="text-center mt-5" v-if="editable">
              <v-btn
                class="elevation-1"
                color="primary"
                type="submit"
                large
                :loading="loadingSave"
                >Simpan</v-btn
              >
            </div>
          </v-container>
        </v-form>
      </v-sheet>

      <!-- Booking History -->
      <v-sheet color="white" elevation="2" class="mt-8 pa-6" rounded>
        <h3 class="mb-4">📅 Riwayat Pemesanan</h3>

        <v-row v-if="loadingBookings">
          <v-col cols="12" class="text-center">
            <v-progress-circular
              indeterminate
              color="primary"
            ></v-progress-circular>
          </v-col>
        </v-row>

        <v-row v-else>
          <v-col v-for="(item, i) in bookings" :key="i" cols="12" md="6" lg="4">
            <v-card class="pa-4 rounded-xl" outlined elevation="2">
              <div class="d-flex justify-space-between align-center mb-2">
                <span class="font-weight-bold">{{ item.barber_name }}</span>
                <v-chip
                  :color="item.status === 'Completed' ? 'green' : 'blue'"
                  dark
                  small
                >
                  {{ item.status }}
                </v-chip>
              </div>
              <p>✂️ Layanan: {{ item.service_name }}</p>
              <p>📅 Tanggal: {{ item.date }}</p>
              <p>⏰ Waktu: {{ item.time }}</p>
            </v-card>
          </v-col>
        </v-row>
      </v-sheet>
    </v-container>

    <Footer />
  </div>
</template>

<script>
import Vue from "vue";
import Navbar from "../navbar.vue";
import Footer from "../footer.vue";
import Avatar from "vue-avatar-component";
import ImageInput from "../image-input.vue";
import Loading from "../loading-dialog";

export default {
  components: {
    Navbar,
    Footer,
    ImageInput,
    Avatar,
    Loading,
  },

  props: {
    api: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      id: null,
      valid: true,
      editable: false,
      loadingSave: false,
      loadingBookings: true,
      model: {
        name: "",
        email: "",
        phone: "",
        address: "",
      },
      avatar: {
        photo: null,
        editable: this.editable,
        imageURL: null,
      },
      bookings: [],
      nameRules: [(v) => !!v || "Nama perlu diisi"],
      emailRules: [
        (v) => !!v || "Email perlu diisi",
        (v) => /.+@.+/.test(v) || "Email tidak valid",
      ],
    };
  },

  created() {
    this.initAxio();
    this.fetchBookings();
  },

  watch: {
    "$route.path": function (path) {
      this.devLog("watching change");
      this.initData();
    },

    "model.phone": function (newValue, oldValue) {
      if (newValue !== null && newValue !== undefined) {
        const result = newValue.replace(/[^0-9+]/g, "");
        Vue.nextTick(() => (this.model.phone = result));
      }
    },
  },

  methods: {
    initAxio() {
      axios
        .get(this.api, {
          headers: { Authorization: localStorage.token },
        })
        .then((response) => {
          if (response.status == 200) {
            this.devLog(response.data.data);
            this.devLog("get data");
            this.model = response.data.data[0];
            this.initAvatar();
          } else {
            alert(response);
            this.showErr(response);
          }
        })
        .catch((err) => {
          if (!!err.response) {
            this.showErr(err.response, "Failed");
          } else {
            this.showErr({ status: "Code Error", statusText: err });
          }
        });
    },

    initAvatar() {
      this.model.editable = this.editable;
      this.avatar = {
        editable: this.editable,
        imageURL: this.model.photo,
        formData: null,
        imgFile: null,
      };
    },

    validateForm() {
      this.devLog("validating");
      if (this.$refs.form.validate()) {
        this.submitForm();
      } else {
        let snackbarOpt = {
          text: "Please check some Field!!",
          type: "error",
        };
        this.simpleSnackbar(snackbarOpt.text, snackbarOpt.type);
        window.scrollTo(0, 0);
      }
    },

    submitForm() {
      this.model.photo = this.avatar.imgFile;
      this.postData();
    },

    postData() {
      this.LOADING(true);

      let bodyPost = new FormData();
      bodyPost.append("name", this.model.name || "");
      bodyPost.append("email", this.model.email || "");
      if (this.model.photo) {
        bodyPost.append("photo", this.model.photo[0]);
      }
      bodyPost.append("phone", this.model.phone || "");
      bodyPost.append("address", this.model.address || "");

      this.devLog(this.model);

      axios
        .post(this.api, bodyPost, {
          headers: {
            Authorization: localStorage.token,
            "Content-Type": "multipart/form-data",
          },
        })
        .then((response) => {
          this.devLog(JSON.stringify(response));
          this.devLog(response.status);
          this.simpleSnackbar("Success! Data edited!!", "success");
        })
        .catch((err) => {
          if (!!err.response) {
            this.showErr(err.response, "Failed");
          } else {
            this.showErr({ status: "Code Error", statusText: err });
          }
        })
        .finally(() => {
          this.LOADING(false);
          this.editable = false;
        });
    },

    // async fetchProfile() {
    //   try {
    //     const res = await axios.get(this.api, {
    //       headers: { Authorization: localStorage.token },
    //     });
    //     // this.model = {
    //     //   name: res.data.name,
    //     //   email: res.data.email,
    //     //   phone: res.data.phone,
    //     //   address: res.data.address,
    //     // };
    //     // this.avatar.imageURL = res.data.avatar;
    //     this.devLog(res.data.data);
    //     // if(res.data.length > 0){
    //     this.devLog("get data");
    //     this.model = res.data.data[0];
    //     this.initAvatar();
    //   } catch (err) {
    //     console.error("Failed to fetch profile:", err);
    //   }
    // },
    async fetchBookings() {
      this.loadingBookings = true;
      try {
        const res = await axios.get(this.API + "/bookings", {
          headers: {
            Authorization: localStorage.token,
          },
        });
        this.bookings = res.data;
      } catch (err) {
        console.error("Failed to fetch bookings:", err);
      } finally {
        this.loadingBookings = false;
      }
    },
    // validateForm() {
    //   if (this.$refs.form.validate()) {
    //     this.updateProfile();
    //   }
    // },
    // async updateProfile() {
    //   this.loadingSave = true;
    //   try {
    //     const formData = new FormData();
    //     formData.append("name", this.model.name);
    //     formData.append("email", this.model.email);
    //     formData.append("phone", this.model.phone);
    //     formData.append("address", this.model.address);
    //     if (this.avatar.photo) {
    //       formData.append("avatar", this.avatar.photo);
    //     }

    //     await axios.post("/api/v1/customer/profile?_method=PUT", formData, {
    //       headers: { "Content-Type": "multipart/form-data" },
    //     });

    //     this.editable = false;
    //     this.fetchProfile();
    //   } catch (err) {
    //     console.error("Failed to update profile:", err);
    //   } finally {
    //     this.loadingSave = false;
    //   }
    // },
  },
};
</script>
