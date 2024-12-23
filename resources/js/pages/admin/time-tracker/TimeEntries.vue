<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { handleFetchItems, handleDelete } from "@/helpers/client-req-handler";
import {
  create_options_v2,
  format_duration,
} from "@/helpers/utils";
import axios from "axios";

const $q = useQuasar();
const tableRef = ref(null);
const rows = ref([]);
const loading = ref(true);
const projects = ref([]);
const filter = reactive({
  search: "",
  project_id: "all",
});

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "start_time",
  descending: true,
});

const columns = [
  {
    name: "title",
    label: "Uraian Pekerjaan",
    field: "title",
    align: "left",
    sortable: true,
  },
  {
    name: "project",
    label: "Proyek",
    field: "project",
    align: "left",
    sortable: true,
  },
  {
    name: "start_time",
    label: "Waktu Mulai",
    field: "start_time",
    align: "left",
    sortable: true,
  },
  {
    name: "end_time",
    label: "Waktu Selesai",
    field: "end_time",
    align: "left",
    sortable: true,
  },
  {
    name: "duration",
    label: "Durasi",
    field: "duration",
    align: "left",
    sortable: true,
  },
  {
    name: "action",
    label: "Aksi",
    align: "center",
  },
];

onMounted(() => {
  const savedFilter = localStorage.getItem("fixsync.time-entries.filter");
  if (savedFilter) {
    Object.assign(filter, JSON.parse(savedFilter));
  }
  fetchItems();
});

watch(
  filter,
  (newValue) => {
    localStorage.setItem(
      "fixsync.time-entries.filter",
      JSON.stringify(newValue)
    );
  },
  { deep: true }
);

const onFilterChange = () => fetchItems();

const fetchItems = (props = null) => {
  handleFetchItems({
    pagination,
    props,
    rows,
    loading,
    filter,
    url: route("admin.time-tracker.data"),
  });
  axios.get(route("admin.project.list")).then((response) => {
    projects.value = [
      { value: 'all', label: 'Semua' },
      ...create_options_v2(response.data, "id", "name")
      ];
  });
};

const deleteItem = (row) =>
  handleDelete({
    url: route("admin.client.delete", row.id),
    title: `Hapus client ${row.name}?`,
    fetchItemsCallback: fetchItems,
    loading,
  });

defineExpose({
  fetchItems,
});
</script>

<template>
  <div class="q-px-md">
    <q-table
      ref="tableRef"
      flat
      bordered
      square
      :dense="true || $q.screen.lt.md"
      color="primary"
      row-key="id"
      virtual-scroll
      title="Time Entries"
      v-model:pagination="pagination"
      :filter="filter.search"
      :loading="loading"
      :columns="columns"
      :rows="rows"
      :rows-per-page-options="[10, 25, 50]"
      @request="fetchItems"
      binary-state-sort
    >
      <template v-slot:loading>
        <q-inner-loading showing color="red" />
      </template>
      <template #top>
        <div class="col">
          <div class="row q-my-sm items-center">
            <div class="text-subtitle1">Time Entries</div>
            <q-space />
            <q-input
              dense
              debounce="300"
              v-model="filter.search"
              placeholder="Cari"
              clearable
            >
              <template v-slot:append>
                <q-icon name="search" />
              </template>
            </q-input>
          </div>
          <div class="row q-my-sm q-gutter-sm items-center">
            <span>Filter: tidak tersedia</span>
            <q-select
              v-model="filter.project_id"
              class="custom-select"
              :options="projects"
              label="Proyek"
              dense
              map-options
              emit-value
              outlined
              flat
              style="min-width: 150px"
              @update:model-value="onFilterChange"
            />
          </div>
        </div>
      </template>

      <template v-slot:no-data="{ icon, message, term }">
        <div class="full-width row flex-center text-grey-8 q-gutter-sm">
          <span>{{ message }} {{ term ? " with term " + term : "" }}</span>
        </div>
      </template>

      <template v-slot:body="props">
        <q-tr :props="props">
          <q-td key="title" :props="props">
            {{ props.row.title }}
          </q-td>
          <q-td key="project" :props="props">
            {{ props.row.project ? props.row.project.name : "" }}
          </q-td>
          <q-td key="start_time" :props="props">
            {{ props.row.start_time }}
          </q-td>
          <q-td key="end_time" :props="props">
            {{ props.row.end_time }}
          </q-td>
          <q-td key="duration" :props="props">
            {{ format_duration(props.row.duration) }}
          </q-td>
          <q-td
            key="action"
            class="q-gutter-x-sm"
            :props="props"
            align="center"
          >
          </q-td>
        </q-tr>
      </template>
    </q-table>
  </div>
</template>
