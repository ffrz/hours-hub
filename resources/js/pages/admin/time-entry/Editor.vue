<script setup>
import { handleSubmit } from "@/helpers/client-req-handler";
import { create_options_v2, format_duration } from "@/helpers/utils";
import { router, useForm, usePage } from "@inertiajs/vue3";
import DateTimePicker from "@/components/DateTimePicker.vue";
import dayjs from "dayjs";

const page = usePage();
const title = !!page.props.data.id ? "Edit Entri" : "Tambah Entri";
const projects = [
  { value: null, label: "Tidak dipilih" },
  ...create_options_v2(page.props.projects, "id", "name"),
];
const users = [...create_options_v2(page.props.users, "id", "name")];
const form = useForm({
  id: page.props.data.id,
  title: page.props.data.title,
  user_id: page.props.data.user_id,
  project_id: page.props.data.project_id ?? null,
  start_time: page.props.data.start_time ?? null,
  end_time: page.props.data.end_time,
  duration:
    page.props.data.duration > 0
      ? format_duration(page.props.data.duration)
      : "--:--:--",
  notes: page.props.data.notes,
});

const submit = () =>
  handleSubmit({ form, url: route("admin.time-entry.save") });

const updateDuration = () => {
  let duration = 0;

  if (form.start_time && form.end_time) {
    const date1 = dayjs(form.start_time);
    const date2 = dayjs(form.end_time);
    duration = date2.diff(date1, "second");
    if (duration < 0) {
      duration = 0;
    }
  }

  form.duration = format_duration(duration);
};
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
              <q-input
                v-model.trim="form.title"
                label="Uraian Pekerjaan"
                lazy-rules
                :error="!!form.errors.title"
                :disable="form.processing"
                :error-message="form.errors.title"
              />
              <q-select
                v-model="form.user_id"
                :options="users"
                label="Pengguna"
                map-options
                emit-value
                flat
                :error="!!form.errors.user_id"
                :disable="form.processing"
                :error-message="form.errors.user_id"
              />
              <q-select
                v-model="form.project_id"
                :options="projects"
                label="Proyek"
                map-options
                emit-value
                flat
                :error="!!form.errors.project_id"
                :disable="form.processing"
                :error-message="form.errors.project_id"
              />
              <date-time-picker
                v-model="form.start_time"
                label="Waktu Mulai"
                :error="!!form.errors.start_time"
                :disable="form.processing"
                @update:model-value="updateDuration"
              />
              <date-time-picker
                v-model="form.end_time"
                label="Waktu Selesai"
                :error="!!form.errors.end_time"
                :disable="form.processing"
                @update:model-value="updateDuration"
              />
              <q-input
                v-model.trim="form.duration"
                label="Durasi"
                readonly
                lazy-rules
                :error="!!form.errors.duration"
                :disable="form.processing"
                :error-message="form.errors.duration"
              />
              <q-input
                v-model.trim="form.notes"
                type="textarea"
                label="Catatan"
                autogrow
                counter
                maxlength="1000"
                lazy-rules
                :disable="form.processing"
                :error="!!form.errors.notes"
                :error-message="form.errors.notes"
              />
            </q-card-section>
            <q-card-actions>
              <q-btn
                icon="save"
                type="submit"
                label="Simpan"
                color="primary"
                :disable="form.processing"
                @click="submit"
              />
              <q-btn
                icon="cancel"
                label="Batal"
                class="text-black"
                :disable="form.processing"
                @click="router.get(route('admin.time-entry.index'))"
              />
            </q-card-actions>
          </q-card>
        </q-form>
      </div>
    </div>
  </authenticated-layout>
</template>
