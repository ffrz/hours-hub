<script setup>
import { handleSubmit } from "@/helpers/client-req-handler";
import { validateUsername, validateEmail } from "@/helpers/validations";
import { router, useForm, usePage } from "@inertiajs/vue3";

const page = usePage();
const title = !!page.props.data.id ? 'Edit Klien' : 'Tambah Klien';
const form = useForm({
  id: page.props.data.id,
  name: page.props.data.name,
  phone: page.props.data.phone,
  email: page.props.data.email,
  address: page.props.data.address,
  notes: page.props.data.notes,
  active: !!page.props.data.active,
});

const submit = () =>
  handleSubmit({ form, url: route('admin.client.save') });

</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="row justify-center">
      <div class="col col-lg-6 q-pa-md">
        <q-form class="row" @submit.prevent="submit">
          <q-card square flat bordered class="col q-pa-sm">
            <q-card-section class="q-pt-none">
              <input type="hidden" name="id" v-model="form.id" />
              <q-input autofocus v-model.trim="form.name" label="Nama Klien" lazy-rules :error="!!form.errors.name"
                :disable="form.processing" :error-message="form.errors.name" :rules="[
                  (val) => (val && val.length > 0) || 'Nama harus diisi.',
                ]" />
              <q-input v-model.trim="form.phone" label="Telepon" lazy-rules :disable="form.processing"
                :error="!!form.errors.phone" :error-message="form.errors.phone" />
              <q-input v-model.trim="form.email" type="email" label="Email" lazy-rules :disable="form.processing"
                :error="!!form.errors.email" :error-message="form.errors.email"
                :rules="[(val) => validateEmail(val) || 'Email harus valid.']" />
              <q-input v-model.trim="form.address" type="textarea" autogrow counter maxlength="1000" label="Alamat"
                lazy-rules :disable="form.processing" :error="!!form.errors.address"
                :error-message="form.errors.address" />
              <q-input v-model.trim="form.notes" type="textarea" label="Catatan" autogrow counter maxlength="1000"
                lazy-rules :disable="form.processing" :error="!!form.errors.notes" :error-message="form.errors.notes" />
              <div style="margin-left:-10px;">
                <q-checkbox class="full-width" v-model="form.active" :disable="form.processing" label="Aktif" />
              </div>
            </q-card-section>
            <q-card-actions>
              <q-btn icon="save" type="submit" label="Simpan" color="primary" :disable="form.processing"
                @click="submit" />
              <q-btn icon="cancel" label="Batal" class="text-black" :disable="form.processing"
                @click="router.get(route('admin.client.index'))" />
            </q-card-actions>
          </q-card>
        </q-form>
      </div>
    </div>
  </authenticated-layout>
</template>
