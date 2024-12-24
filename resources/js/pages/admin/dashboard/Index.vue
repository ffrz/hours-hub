<script setup>
import { create_options_v2 } from "@/helpers/utils";
import { usePage } from "@inertiajs/vue3";
import { reactive, ref } from "vue";
const title = "Dashboard";
const page = usePage();
const projects = [
  { value: "all", label: "Semua" },
  ...create_options_v2(page.props.projects, "id", "name"),
];
const filter = reactive({
  project_id: "all",
  period: "today",
});

const periods = ref([
  { value: "today", label: "Hari Ini" },
  { value: "yesterday", label: "Kemarin" },
  { value: "this_week", label: "Minggu Ini" },
  { value: "prev_week", label: "Minggu Kemarin" },
  { value: "this_month", label: "Bulan Ini" },
  { value: "prev_month", label: "Bulan Kemarin" },
]);

const onFilterChange = () => fetchItems();

const fetchItems = (props = null) => {
  // axios.get(route("admin.project.list")).then((response) => {
  //   projects.value = [
  //     { value: "all", label: "Semua" },
  //     ...create_options_v2(response.data, "id", "name"),
  //   ];
  // });
};
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="q-ma-md">
      <div class="row q-my-md">
        <q-card class="col" square flat bordered>
          <div class="row bg-grey-2 q-pa-sm">
            <div class="text-caption">Filter</div>
          </div>
          <q-card-section class="row q-col-gutter-sm">
            <q-select
              v-model="filter.project_id"
              class="col-12 col-sm-6"
              :options="projects"
              label="Proyek"
              dense
              map-options
              emit-value
              outlined
              flat
              @update:model-value="onFilterChange"
            />
            <q-select
              v-model="filter.period"
              class="col-12 col-sm-6"
              :options="periods"
              label="Periode"
              dense
              map-options
              emit-value
              outlined
              flat
              @update:model-value="onFilterChange"
            />
          </q-card-section>
        </q-card>
      </div>
      <div class="row q-my-md">
        <div class="col bg-blue-3 text-center q-py-md">
          <div class="text-subtitle2">Total Waktu</div>
          <div class="text-h6">10:12:47</div>
        </div>
        <div class="col bg-green-3 text-center q-py-md">
          <div class="text-subtitle2">Proyek Teratas</div>
          <div class="text-h6">Hours Hub</div>
        </div>
      </div>
      <div class="row q-my-md">
        <q-card class="col" square flat bordered>
          <div class="bg-grey-2 q-pa-sm">
            <div class="text-caption">Aktivitas Teratas</div>
          </div>
          <div class="q-pa-md">
            <div v-for="item in [1, 2, 3, 4]" class="row items-center">
              <div class="col">
                <div>Activity {{ item }}</div>
                <div>Project {{ item }}</div>
              </div>
              <div class="col-auto">00:00:00</div>
            </div>
          </div>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>
