<script setup>
import { useForm } from "@inertiajs/vue3";
import { validateEmail } from "@/helpers/validations";
import { scrollToFirstErrorField } from "@/helpers/utils";
import { handleSubmit } from "@/helpers/client-req-handler";

// TODO:
// 1. Lakukan validasi lanjutan untuk company_code, username dan password di local
// company_code: hanya boleh alpha num minimal m karakter, maksimal n karakter
// username: hanya boleh alpha num minimal m karakter, maksimal n karakter
// password: harus terdiri dari huruf dan angka, minimal 5 karakter
// sinkronisasi kriteria validasi dengan server side validation

let form = useForm({
  username: '',
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () =>
  handleSubmit({form, url: route('admin.auth.register')});

</script>

<template>
  <guest-layout>
    <i-head title="Register" />
    <q-page class="row justify-center items-center">
      <div class="column">
        <div class="row">
          <q-form class="q-gutter-md" @submit.prevent="submit" @validation-error="scrollToFirstErrorField">
            <q-card id="register-card" square bordered class="q-pa-md shadow-1">
              <q-card-section>
                <h6 class="q-my-none text-center">Daftar</h6>
              </q-card-section>
              <q-card-section>
                <h6 class="q-my-none text-body1">Informasi Akun</h6>
                <q-input v-model.trim="form.username" label="Username" lazy-rules autocomplete="username"
                  :error="!!form.errors.username" :error-message="form.errors.username" :disable="form.processing"
                  :rules="[(val) => (val && val.length > 0) || 'Username harus diisi']">
                </q-input>
                <q-input v-model.trim="form.name" label="Nama" lazy-rules :error="!!form.errors.name" autocomplete="name"
                  :error-message="form.errors.name" :disable="form.processing"
                  :rules="[(val) => (val && val.length > 0) || 'Nama harus diisi']">
                  <template v-slot:append>
                    <q-icon name="person" />
                  </template>
                </q-input>
                <q-input v-model.trim="form.email" label="Email" lazy-rules :error="!!form.errors.email" autocomplete="email"
                  :error-message="form.errors.email" :disable="form.processing"
                  :rules="[(val) => validateEmail(val) || 'Email tidak valid']">
                  <template v-slot:append>
                    <q-icon name="email" />
                  </template>
                </q-input>
                <q-input v-model="form.password" type="password" label="Kata Sandi" :error="!!form.errors.password" autocomplete="off"
                  :error-message="form.errors.password" lazy-rules :disable="form.processing"
                  :rules="[(val) => (val && val.length > 0) || 'Silahkan masukkan kata sandi.']">
                  <template v-slot:append>
                    <q-icon name="key" />
                  </template>
                </q-input>
                <q-input square v-model="form.password_confirmation" type="password" label="Konfirmasi Kata Sandi" autocomplete="off"
                  :disable="form.processing" :error="!!form.errors.password_confirmation"
                  :error-message="form.errors.password_confirmation" lazy-rules :rules="[
                    (val) => (val && val.length > 0) || 'Silahkan konfirmasi kata sandi anda.',
                    () => (form.password == form.password_confirmation) || 'Konfirmasi kata sandi tidak cocok.'
                  ]">
                  <template v-slot:append>
                    <q-icon name="key" />
                  </template>
                </q-input>
              </q-card-section>
              <q-card-actions>
                <q-btn icon="how_to_reg" type="submit" color="primary" class="full-width" label="Buat Akun"
                  :disable="form.processing" />
              </q-card-actions>
              <q-card-section class="text-center q-pa-none q-mt-md">
                <p class="q-my-xs text-grey-7">
                  Sudah terdaftar?
                  <i-link :href="route('admin.auth.login')">Masuk</i-link>
                </p>
              </q-card-section>
            </q-card>
          </q-form>
        </div>
      </div>
    </q-page>
  </guest-layout>
</template>

<style>
.q-card {
  width: 400pxpx;
}
</style>
