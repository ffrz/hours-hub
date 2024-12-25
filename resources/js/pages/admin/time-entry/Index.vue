<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { handleFetchItems, handleDelete } from "@/helpers/client-req-handler";
import { create_options_v2, format_duration } from "@/helpers/utils";
import { router, usePage } from "@inertiajs/vue3";

const page = usePage();
const title = "Time Entries";
const $q = useQuasar();
const tableRef = ref(null);
const rows = ref([]);
const loading = ref(true);
const projects = [
  { value: "all", label: "Semua" },
  ...create_options_v2(page.props.projects, "id", "name"),
];
const users = [
  { value: "all", label: "Semua" },
  ...create_options_v2(page.props.users, "id", "name"),
];
const filter = reactive({
  search: "",
  project_id: "all",
  user_id: "all",
  period: "today",
});

const periods = ref([
  { value: "all", label: "Semua" },
  { value: "today", label: "Hari Ini" },
  { value: "yesterday", label: "Kemarin" },
  { value: "this_week", label: "Minggu Ini" },
  { value: "prev_week", label: "Minggu Kemarin" },
  { value: "this_month", label: "Bulan Ini" },
  { value: "prev_month", label: "Bulan Kemarin" },
]);

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "start_time",
  descending: true,
});

const columns = [
  {
    name: "user",
    label: "Pengguna",
    field: "user",
    align: "left",
    sortable: true,
  },
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
    // ini mungkin akan mentrigger fetchItems, tapi kadang tidak
    Object.assign(filter, JSON.parse(savedFilter));
    // return;
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
    url: route("admin.time-entry.data"),
  });
};

const deleteItem = (row) =>
  handleDelete({
    url: route("admin.time-entry.delete", row.id),
    message: `Hapus Entri ${row.title}?`,
    fetchItemsCallback: fetchItems,
    loading,
  });
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
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
            <div class="row q-mt-sm q-mb-md q-col-gutter-xs items-center">
              <div class="col-auto">
                <q-btn
                  color="primary"
                  icon="add"
                  @click="router.get(route('admin.time-entry.add'))"
                  label="Manual Entry"
                >
                  <q-tooltip>Manual Entry</q-tooltip>
                </q-btn>
              </div>
              <q-space v-show="$q.screen.gt.xs" />
              <q-select
                v-model="filter.user_id"
                class="col-12 col-sm-2 custom-select"
                :options="users"
                label="Pengguna"
                dense
                map-options
                emit-value
                outlined
                flat
                @update:model-value="onFilterChange"
              />
              <q-select
                v-model="filter.project_id"
                class="col-12 col-sm-2 custom-select"
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
                class="col-12 col-sm-2 custom-select"
                :options="periods"
                label="Periode"
                dense
                map-options
                emit-value
                outlined
                flat
                @update:model-value="onFilterChange"
              />
              <q-input
                class="col-12 col-sm-2"
                dense
                debounce="300"
                v-model="filter.search"
                placeholder="Cari"
                clearable
                outlined
              >
                <template v-slot:append>
                  <q-icon name="search" />
                </template>
              </q-input>
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
            <q-td key="user" :props="props">
              {{ props.row.user.name }}
            </q-td>
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
              {{ props.row.end_time ?? "Sedang berjalan" }}
            </q-td>
            <q-td key="duration" :props="props">
              {{
                props.row.duration
                  ? format_duration(props.row.duration)
                  : "??:??:??"
              }}
            </q-td>
            <q-td
              key="action"
              class="q-gutter-x-sm"
              :props="props"
              align="center"
            >
              <q-td
                key="action"
                class="q-gutter-x-sm"
                :props="props"
                align="center"
              >
                <q-btn
                  rounded
                  dense
                  flat
                  @click="
                    router.get(route('admin.time-entry.edit', props.row.id))
                  "
                  icon="edit"
                >
                  <q-tooltip>Edit Entry</q-tooltip>
                </q-btn>
                <q-btn
                  rounded
                  dense
                  flat
                  icon="delete"
                  @click="deleteItem(props.row)"
                >
                  <q-tooltip>Hapus Entry</q-tooltip>
                </q-btn>
              </q-td>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </authenticated-layout>
</template>
