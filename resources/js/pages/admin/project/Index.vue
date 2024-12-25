<script setup>
import { onMounted, reactive, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { router, usePage } from "@inertiajs/vue3";
import { handleFetchItems, handleDelete } from "@/helpers/client-req-handler";
import { create_options_from_clients } from "@/helpers/utils";

const page = usePage();
const currentUser = page.props.auth.user;
const title = "Proyek";
const $q = useQuasar();
const tableRef = ref(null);
const rows = ref([]);
const loading = ref(true);
const filter = reactive({
  status: "all",
  client: "all",
  search: "",
});

const statuses = [
  { value: "all", label: "Semua" },
  { value: "active", label: "Aktif" },
  { value: "inactive", label: "Tidak Aktif" },
];

const clients = [
  { value: "all", label: "Semua Klien" },
  ...create_options_from_clients(page.props.clients),
];

const pagination = ref({
  page: 1,
  rowsPerPage: 10,
  rowsNumber: 10,
  sortBy: "name",
  descending: false,
});

const columns = [
  {
    name: "name",
    label: "Nama Proyek",
    field: "name",
    align: "left",
    sortable: true,
  },
  {
    name: "client",
    label: "Klien",
    field: "client",
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
  const savedFilter = localStorage.getItem("fixsync.project.filter");
  if (savedFilter) {
    // ini akan mentrigger fetchItems
    Object.assign(filter, JSON.parse(savedFilter));
    // return; // kadang mengakibatkan gagal fetch
  }
  fetchItems();
});

watch(
  filter,
  (newValue) => {
    localStorage.setItem("fixsync.project.filter", JSON.stringify(newValue));
  },
  { deep: true }
);

const onFilterChange = () => fetchItems();

const fetchItems = (props = null) =>
  handleFetchItems({
    pagination,
    props,
    rows,
    loading,
    filter,
    url: route("admin.project.data"),
  });

const deleteItem = (row) =>
  handleDelete({
    url: route("admin.project.delete", row.id),
    title: `Hapus proyek ${row.name}?`,
    fetchItemsCallback: fetchItems,
    loading,
  });
</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="q-pa-md">
      <q-table
        ref="tableRef"
        flat
        bordered
        square
        :dense="true || $q.screen.lt.md"
        color="primary"
        row-key="id"
        virtual-scroll
        title="Proyek"
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
            <div class="row q-mt-xs q-mb-md q-col-gutter-xs items-center">
              <div class="col-auto">
                <q-btn
                  color="primary"
                  icon="add"
                  @click="router.get(route('admin.project.add'))"
                  label="Baru"
                >
                  <q-tooltip>Proyek Baru</q-tooltip>
                </q-btn>
              </div>
              <q-space v-show="$q.screen.gt.xs" />
              <q-select
                class="col-12 col-sm-2 custom-select"
                v-model="filter.status"
                :options="statuses"
                label="Status"
                dense
                map-options
                emit-value
                outlined
                flat
                @update:model-value="onFilterChange"
              />
              <q-select
                class="col-12 col-sm-2 custom-select"
                v-model="filter.client_id"
                :options="clients"
                label="Klien"
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
          <q-tr :props="props" :class="!props.row.active ? 'bg-red-1' : ''">
            <q-td key="name" :props="props">
              {{ props.row.name }}
            </q-td>
            <q-td key="client" :props="props">
              {{ props.row.client ? props.row.client.name : "" }}
            </q-td>
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
                @click="router.get(route('admin.project.edit', props.row.id))"
                icon="edit"
              >
                <q-tooltip>Edit Proyek</q-tooltip>
              </q-btn>
              <q-btn
                rounded
                dense
                flat
                icon="delete"
                @click="deleteItem(props.row)"
              >
                <q-tooltip>Hapus Proyek</q-tooltip>
              </q-btn>
            </q-td>
          </q-tr>
        </template>
      </q-table>
    </div>
  </authenticated-layout>
</template>
