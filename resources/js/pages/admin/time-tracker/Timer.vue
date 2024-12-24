<script setup>
import { create_options_v2, format_duration } from "@/helpers/utils";
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, reactive } from "vue";
import { ref } from "vue";
import { Notify, Dialog } from "quasar";
import { defineEmits } from "vue";
import { onUnmounted } from "vue";

const emit = defineEmits(["timerSessionEnded"]);

const initialData = {
  id: 0,
  project_id: null,
  title: "",
  duration: 0,
};

const page = usePage();
const projects = create_options_v2(page.props.projects, "id", "name");
const showMenu = ref(false);
const data = reactive({ ...initialData });
let timerId = ref(null);
let syncTimerId = ref(null);

onMounted(async () => {
  const response = await axios
    .post(route("admin.time-tracker.check-last-session"))
    .catch(_catchError);

  if (!response.data || Object.keys(response.data).length === 0) return;

  _startTimer();
  Object.assign(data, response.data);
  Notify.create("Sesi telah dilanjutkan.");
});

onUnmounted(() => {
  clearInterval(timerId.value);
  clearInterval(syncTimerId.value);
});
// CALLBACKS //

const syncTimer = async () => {
  const response = await axios.post(route("admin.time-tracker.sync"), {
    id: data.id,
  });

  Object.assign(data, response.data);
};

const startTimer = async () => {
  const response = await axios.post(route("admin.time-tracker.start"), {
    project_id: data.project_id,
    title: data.title,
  });

  _startTimer();

  data.id = response.data.id;
  Notify.create("Sesi telah dimulai.");
};

const update = async () => {
  if (timerId.value === null) {
    // ignore karena timer tidak berjalan
    return;
  }

  const response = await axios
    .post(route("admin.time-tracker.update"), {
      id: data.id,
      project_id: data.project_id,
      title: data.title,
    })
    .catch(_catchError);

  Notify.create("Berhasil diperbarui.");
};

const stopTimer = async () => {
  Dialog.create({
    title: "Konfirmasi",
    icon: "question",
    message: "Anda yakin akan menyelesaikan sesi kali ini?",
    focus: "cancel",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    const response = await axios
      .post(route("admin.time-tracker.stop"), {
        id: data.id,
        project_id: data.project_id,
        title: data.title,
      })
      .catch(_catchError);
    _resetState();
    Notify.create("Sesi telah selesai.");
    emit("timerSessionEnded", data);
  });
};

const cancelTimer = () => {
  Dialog.create({
    title: "Konfirmasi",
    icon: "question",
    message: "Anda yakin akan membatalkan sesi ini?",
    focus: "cancel",
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    await axios
      .post(route("admin.time-tracker.cancel"), { id: data.id })
      .catch(_catchError);
    _resetState();
    Notify.create("Timer dibatalkan.");
  });
};

// FORMATTING //
const formattedDuration = computed(() => format_duration(data.duration));

// HELPERS //
const _startTimer = () => {
  timerId.value = setInterval(() => {
    data.duration += 1;
  }, 1000);

  syncTimerId.value = setInterval(syncTimer, 1000 * 60);
};

const _resetState = () => {
  Object.assign(data, initialData);
  clearInterval(timerId.value);
  timerId.value = null;
  clearInterval(syncTimerId.value);
  syncTimerId.value = null;
};

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
};
</script>
<template>
  <div class="row">
    <q-card class="col" square flat bordered>
      <q-card-section class="row q-col-gutter-xs">
        <q-input
          dense
          outlined
          label="Uraian Pekerjaan"
          class="col-12 col-md-6"
          v-model="data.title"
          @update:model-value="update"
          debounce="300"
          style="min-width: 150px"
        />
        <q-select
          dense
          label="Proyek"
          class="col-12 col-md-2 custom-select"
          outlined
          :options="projects"
          map-options
          emit-value
          v-model="data.project_id"
          clearable
          @update:model-value="update"
          style="min-width: 150px"
        />
        <q-input
          dense
          outlined
          label="Durasi"
          class="col-12 col-md-2"
          readonly
          v-model="formattedDuration"
        />
        <div class="col-12 col-md-2">
          <div class="row">
            <q-btn
              class="col"
              :label="!timerId ? 'MULAI' : 'SELESAI'"
              :color="!timerId ? 'primary' : 'negative'"
              @click="!timerId ? startTimer() : stopTimer()"
              style="width: 100px; height: 40px; border-top-right-radius: 0%; border-bottom-right-radius: 0%;"
              v-ripple
            />
            <q-btn
              class="col-auto"
              :class="showMenu ? 'menu-active' : ''"
              flat
              icon="more_vert"
              dense
              :disabled="!timerId"
              color="grey"
              style="
                height: 40px;
                width: 30px;
                border: 1px solid #ddd;
                border-top-left-radius: 0%;
                border-bottom-left-radius: 0%;
              "
            >
              <q-menu v-model="showMenu" anchor="bottom right" self="top right">
                <q-list style="width: 150px">
                  <q-item clickable v-ripple v-close-popup @click="cancelTimer">
                    <q-item-section>Batalkan</q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </div>
</template>

<style scoped>
.menu-active {
  background-color: #ddd;
}
</style>
