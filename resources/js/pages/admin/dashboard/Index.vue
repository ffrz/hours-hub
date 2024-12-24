<script setup>
import { create_options_v2, format_duration } from "@/helpers/utils";
import { usePage } from "@inertiajs/vue3";
import { onMounted, reactive, ref } from "vue";

const title = "Dashboard";
const page = usePage();
const projects = [
  { value: "all", label: "Semua" },
  ...create_options_v2(page.props.projects, "id", "name"),
];
const data = reactive({
  total_duration: 0,
  top_project: "Unknown Project",
});
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

onMounted(() => {
  fetchItems();
});

const fetchItems = async () => {
  const response = await axios.get(route("admin.dashboard.data", filter));
  Object.assign(data, response.data);
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
      <div class="row q-my-md q-gutter-sm">
        <div class="col bg-grey-2 text-center q-py-md border">
          <div class="full-height items-center flex">
            <div style="margin: 0 auto">
              <div class="text-subtitle2 text-grey-8">Total Waktu</div>
              <div class="text-h5">
                {{ format_duration(data.total_duration) }}
              </div>
            </div>
          </div>
        </div>
        <div class="col bg-grey-2 text-center q-py-md border">
          <div class="text-subtitle2 text-grey-8">Proyek Teratas</div>
          <div class="text-h6">
            {{
              data.top_project
                ? data.top_project.project_name
                  ? data.top_project.project_name
                  : "Non Proyek"
                : "Tidak Tersedia"
            }}
          </div>
          <div class="text-caption">
            {{
              data.top_project
                ? format_duration(data.top_project.total_duration)
                : ""
            }}
          </div>
        </div>
      </div>
      <div class="row q-my-md q-gutter-sm">
        <q-card class="col" square flat bordered>
          <div>#ca</div>
        </q-card>

        <q-card class="col-sm-3" square flat bordered>
          <div class="bg-grey-2 q-pa-sm">
            <div class="text-caption">Entri Teratas</div>
          </div>
          <q-list>
            <q-item
              v-for="item in data.top_entries"
              class="row items-center bordered"
              :key="item.id"
            >
              <div class="col">
                <div class="text-subtitle2">
                  {{ item.title ? item.title : "Untitled" }}
                </div>
                <div
                  class="text-caption text-grey-8"
                  style="overflow: hidden; white-space: no-wrap"
                >
                  {{ item.project_id ? item.project_name : "Non Proyek" }}
                </div>
              </div>
              <div class="col-auto">{{ format_duration(item.duration) }}</div>
            </q-item>
          </q-list>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>

<style>
.border {
  border: 1px solid #ddd;
}

.bordered {
  border-top: 1px solid #ddd;
}
</style>
