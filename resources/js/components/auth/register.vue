<template>
  <v-container fluid>
    <v-row id="row-login">
      <v-col
        cols="12"
        sm="12"
        md="6"
        lg="6"
        xl="6"
        class="container-column-login"
        id="container-image-banner-login"
      >
        <v-img src="/img/bannerhero.jpeg" cover id="banner-login"></v-img>
      </v-col>
      <v-col
        sm="12"
        md="6"
        lg="6"
        xl="6"
        cols="12"
        class="container-column-login"
        id="container-main-register"
      >
        <v-container fluid id="container-box-main-login">
          <v-container fluid id="container-header-login">
            <h1>Buat Akun Baru</h1>
            <p>Dapatkan akun Ven Barbershop Anda sekarang!</p>
          </v-container>
          <v-form @submit.prevent="verifyRecaptcha" ref="form" lazy-validation>
            <v-container fluid id="container-input-area-login">
              <v-container class="container-text-field-login">
                <label for="register-fullname" class="text-bolder"
                  >Nama Lengkap*</label
                >
                <v-text-field
                  id="register-fullname"
                  placeholder="Masukan Nama Lengkap"
                  outlined
                  type="text"
                  :rules="fullname_rules"
                  :success="name_ok"
                  :error="name_ok"
                  v-model="customer.name"
                  :maxlength="fieldMaxLengths.name"
                  @input="checkMaxLength(customer.name, 'name')"
                  required
                ></v-text-field>
              </v-container>
              <v-container class="container-text-field-login">
                <label for="register-email" class="text-bolder">Email*</label>
                <v-text-field
                  id="register-email"
                  placeholder="Masukan Email"
                  outlined
                  type="email"
                  v-model="customer.email"
                  :success="email_ok"
                  :error="email_ok"
                  :rules="email_rules"
                  :maxlength="fieldMaxLengths.email"
                  @input="checkMaxLength(customer.email, 'email')"
                  required
                  autocomplete="username"
                ></v-text-field>
              </v-container>
              <v-container class="container-text-field-login">
                <label for="register-password" class="text-bolder"
                  >Password*</label
                >
                <v-text-field
                  id="register-password"
                  placeholder="Masukan Password"
                  :append-icon="
                    password.visible_main ? 'mdi-eye' : 'mdi-eye-off'
                  "
                  @click:append="
                    () => (password.visible_main = !password.visible_main)
                  "
                  :type="password.visible_main ? 'text' : 'password'"
                  outlined
                  :rules="password_rules"
                  :success="password.main_ok"
                  :error="password.main_ok"
                  v-model="customer.password"
                  required
                  autocomplete="new-password"
                ></v-text-field>
              </v-container>
              <v-container class="container-text-field-login">
                <label for="register-password-validation" class="text-bolder"
                  >Re-Enter Password*</label
                >
                <v-text-field
                  id="register-password-validation"
                  placeholder="Re-Enter Password"
                  :append-icon="
                    password.visible_validation ? 'mdi-eye' : 'mdi-eye-off'
                  "
                  @click:append="
                    () =>
                      (password.visible_validation =
                        !password.visible_validation)
                  "
                  :type="password.visible_validation ? 'text' : 'password'"
                  :rules="password_validation"
                  :success="password.retype_ok"
                  :error="password.retype_ok"
                  v-model="customer.password_validation"
                  required
                  outlined
                  autocomplete="new-password"
                ></v-text-field>
              </v-container>
              <v-container id="container-remember-and-forget-pw">
                <p>
                  <i> Dengan mendaftar Anda setuju dengan Ven Barbershop </i>
                  <span id="TOS">Ketentuan Penggunaan</span>
                </p>
              </v-container>
            </v-container>
            <v-container id="container-button-login">
              <v-btn
                block
                elevation="2"
                id="btn-login"
                type="submit"
                color="primary"
                >Daftar</v-btn
              >
            </v-container>
          </v-form>
          <v-container id="container-footer-login">
            <p>
              Sudah punya akun?
              <span @click="redirectToSignIn" id="sign-up" class="pointer">
                Masuk
              </span>
            </p>
          </v-container>
        </v-container>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: "register",
  data() {
    return {
      password: {
        visible_main: false,
        visible_validation: false,
        main_ok: false,
        retype_ok: false,
      },
      name_ok: false,
      email_ok: false,
      customer: {},
      email_rules: [
        (v) => !!v || "Harus diisi",
        (v) =>
          /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
            v
          ) || "Email tidak valid",
      ],
      fullname_rules: [
        (v) => !!v || "Harus diisi",
        (v) => /^[^\d]+$/.test(v) || "Nama Lengkap tidak boleh mengandung angka",
      ],
      password_rules: [
        (v) => !!v || "Harus diisi",
        (v) => (v && v.length >= 6) || "Minimal 6 karakter",
        (v) => /\d/.test(v) || "Harus mengandung angka",
      ],
      password_validation: [
        (v) => !!v || "Harus diisi",
        (v) => v == this.customer.password || "Password tidak sesuai",
      ],
      fieldMaxLengths: {
        name: 50,
        email: 60,
      },
    };
  },
  methods: {
    checkMaxLength(value, field) {
      const maxLength = this.fieldMaxLengths[field];
      if (value.length >= maxLength) {
        this.alert("Warning", "You have reached the maximum length!", {
          color: "warning",
        });
      }
    },

    async verifyRecaptcha() {
      // await this.$recaptchaLoaded();
      const isValid = this.$refs.form.validate();
      if (isValid) {
        const bodyReq = {
          name: this.customer.name,
          email: this.customer.email,
          password: this.customer.password,
          role_id: 2,
          status: "Pending",
        };

        this.LOADING(true);

        axios
          .post(`${this.API}/register`, bodyReq)
          .then((response) => {
            this.LOADING(false);
            if (response.status == 201) {
              this.alert(
                "Registration success",
                "Please contact Template Project admin to process your admin status"
              );

              this.redirectToSignIn();
            }
          })
          .catch((error) => {
            const responseErr = error.response;
            this.LOADING(false);

            if (responseErr.status == 500) {
              this.alert(
                "Registration Failed (Error Code : 500)",
                "Please contact Template Project technical support to report this issue",
                { color: "#d9534f" }
              );
              return;
            }

            if (responseErr.status == 422) {
              const dataError = responseErr.data.message;
              this.alert("Registration Failed (Error Code : 422)", dataError, {
                color: "#d9534f",
              });
              return;
            }
          });
      }
    },
    redirectToSignIn() {
      window.location = "admin#/login";
    },
  },
};
</script>
