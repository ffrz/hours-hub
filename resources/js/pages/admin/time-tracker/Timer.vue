<script setup>
import { create_options_v2 } from '@/helpers/utils';
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, reactive } from 'vue';
import { ref } from 'vue';
import { Notify, Dialog } from "quasar";

const initialData = {
  id: 0,
  project_id: null,
  title: '',
  duration: 0,
};

const page = usePage();
const projects = create_options_v2(page.props.projects, 'id', 'name');
const showMenu = ref(false);
const data = reactive({ ...initialData });
let timerId = ref(null);
let syncTimerId = ref(null);

onMounted(() => {
  // TODO: deteksi jika sudah ada session yang sedang berjalan, jangan perbolehkan memulai session baru
  // karena aplikasi tidak memperbolehkan user memulai lebih dari satu sesi
});

// CALLBACKS //

const syncTimer = async () => {
  const response = await axios.post(route('admin.time-tracker.sync'), {
    id: data.id,
  });

  Object.assign(data, response.data);
}

const startTimer = async () => {
  const response = await axios.post(route('admin.time-tracker.start'), {
    project_id: data.project_id,
    title: data.title,
  });

  timerId.value = setInterval(() => {
    data.duration += 1;
  }, 1000);

  syncTimerId.value = setInterval(syncTimer, 1000 * 60);

  data.id = response.data.id;
  Notify.create('Sesi Timer telah dimulai.');
}

const update = async () => {
  if (timerId.value === null) {
    // ignore karena timer tidak berjalan
    return;
  }

  const response = await axios.post(route('admin.time-tracker.update'), {
    id: data.id,
    project_id: data.project_id,
    title: data.title,
  })
    .catch(_catchError);

  Notify.create('Berhasil diperbarui.');
}

const stopTimer = async () => {
  const response = await axios.post(route('admin.time-tracker.stop'), {
    id: data.id,
    project_id: data.project_id,
    title: data.title,
  })
    .catch(_catchError);

  _resetState();

  Notify.create('Sesi Timer telah selesai.');
}

const cancelTimer = () => {
  Dialog.create({
    title: "Konfirmasi",
    icon: "question",
    message: "Anda yakin akan membatalkan sesi ini?",
    focus: "cancel",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await axios.post(route('admin.time-tracker.cancel'), { id: data.id })
      .catch(_catchError);
    Notify.create('Timer dibatalkan.');
  });
}

// FORMATTING //

const formattedDuration = computed(() => {
  const hours = Math.floor(data.duration / 3600);
  const minutes = Math.floor((data.duration % 3600) / 60);
  const seconds = data.duration % 60;
  return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
});

// HELPERS //
const _resetState = () => {
  Object.assign(data, initialData);
  clearInterval(timerId.value);
  timerId.value = null;
  clearInterval(syncTimerId.value);
  syncTimerId.value = null;
}

const _catchError = (error) => {
  let message = "";
  if (error.response.data && error.response.data.message) {
    message = error.response.data.message;
  } else if (error.message) {
    message = error.message;
  }

  if (message.length > 0) {
    Notify.create({ message: message, color: "red" });
  }
  console.log(error);
}

</script>
<template>
  <div class="row q-my-sm">
    <q-card class="col" square flat bordered>
      <q-card-section class="row q-gutter-xs">
        <q-select label="Proyek" :options="projects" map-options emit-value v-model="data.project_id" outlined clearable
          @update:model-value="update" style="width: 150px;" square />
        <q-input label="Judul" class="col" v-model="data.title" outlined square @update:model-value="update"
          debounce="300" />
        <q-input label="Durasi" class="col-auto" readonly v-model="formattedDuration" outlined square />
        <q-btn :label="!timerId ? 'MULAI' : 'SELESAI'" class="col-auto" :color="!timerId ? 'primary' : 'negative'"
          @click="!timerId ? startTimer() : stopTimer()" style="width:100px" v-ripple square />
        <q-btn class="col-auto" :class="showMenu ? 'menu-active' : ''" flat icon="more_vert" dense :disabled="!timerId"
          square>
          <q-menu square v-model="showMenu" anchor="bottom right" self="top right">
            <q-list style="min-width: 150px;">
              <q-item clickable v-ripple v-close-popup @click="cancelTimer">
                <q-item-section>Batalkan</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
      </q-card-section>
    </q-card>
  </div>
</template>

<style scoped>
.menu-active {
  background-color: #ddd;
}
</style>
