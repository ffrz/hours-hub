<script setup>
import { defineComponent, onMounted, onUnmounted, ref, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { check_role } from "@/helpers/utils";
import { useQuasar } from "quasar";

defineComponent({
  name: "AuthenticatedLayout",
});

const $q = useQuasar();
const page = usePage();
const leftDrawerOpen = ref(
  JSON.parse(localStorage.getItem("hourshub.layout.left-drawer-open"))
);
const isDropdownOpen = ref(false);
const isScrolled = ref(false);

const toggleLeftDrawer = () => (leftDrawerOpen.value = !leftDrawerOpen.value);

function handleScroll() {
  isScrolled.value = window.scrollY > 0;
}

watch(leftDrawerOpen, (newValue) => {
  localStorage.setItem("hourshub.layout.left-drawer-open", newValue);
});

onMounted(() => {
  leftDrawerOpen.value = JSON.parse(
    localStorage.getItem("hourshub.layout.left-drawer-open")
  );

  // tutup aja drawernya, kan layar kecil kasian user
  if ($q.screen.lt.md) {
    leftDrawerOpen.value = false;
  };
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
  <q-layout view="lHh LpR lFf">
    <q-header>
      <q-toolbar
        class="bg-grey-1 text-black"
        :class="{ 'toolbar-scrolled': isScrolled }"
      >
        <q-btn
          v-if="!leftDrawerOpen"
          flat
          dense
          aria-label="Menu"
          @click="toggleLeftDrawer"
        >
          <q-icon class="material-symbols-outlined">dock_to_right</q-icon>
        </q-btn>
        <q-toolbar-title
          :class="{ 'q-ml-sm': leftDrawerOpen }"
          style="font-size: 18px"
        >
          <slot name="title">{{ $config.APP_NAME }}</slot>
        </q-toolbar-title>
      </q-toolbar>
    </q-header>
    <q-drawer

      v-model="leftDrawerOpen"
      bordered
      class="bg-grey-2"
      style="color: #444"
    >
      <div
        class="absolute-top"
        style="
          height: 50px;
          border-bottom: 1px solid #ddd;
          align-items: center;
          justify-content: center;
        "
      >
        <div
          style="
            width: 100%;
            padding: 8px;
            display: flex;
            justify-content: space-between;
          "
        >
          <q-btn-dropdown
            v-model="isDropdownOpen"
            class="profile-btn text-bold"
            flat
            :label="page.props.auth.user.name"
            style="
              justify-content: space-between;
              flex-grow: 1;
              overflow: hidden;
            "
            :class="{ 'profile-btn-active': isDropdownOpen }"
          >
            <q-list id="profile-btn-popup" style="color: #444">
              <q-item>
                <q-avatar style="margin-left: -15px"
                  ><q-icon class="material-symbols-outlined"
                    >person</q-icon
                  ></q-avatar
                >
                <q-item-section>
                  <q-item-label>
                    <div class="text-bold">{{ page.props.auth.user.name }}</div>
                    <div class="text-grey-8 text-caption">
                      {{ $CONSTANTS.USER_ROLES[page.props.auth.user.role] }}
                    </div>
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-separator />
              <q-item
                dense
                v-close-popup
                class="subnav"
                clickable
                v-ripple
                :active="$page.url.startsWith('/admin/settings/profile')"
                @click="router.get(route('admin.profile.edit'))"
              >
                <q-item-section>
                  <q-item-label>
                    <q-icon class="material-symbols-outlined q-mr-sm"
                      >account_circle</q-icon
                    >
                    Profil Saya
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item
                dense
                clickable
                v-close-popup
                v-ripple
                @click="router.post(route('admin.auth.logout'))"
              >
                <q-item-section>
                  <q-item-label
                    ><q-icon name="logout" class="q-mr-sm" />
                    Logout</q-item-label
                  >
                </q-item-section>
              </q-item>
            </q-list>
          </q-btn-dropdown>
          <q-btn
            v-if="leftDrawerOpen"
            flat
            dense
            aria-label="Menu"
            @click="toggleLeftDrawer"
          >
            <q-icon class="material-symbols-outlined">dock_to_right</q-icon>
          </q-btn>
        </div>
      </div>
      <q-scroll-area style="height: calc(100% - 50px); margin-top: 50px">
        <q-list id="main-nav" style="margin-bottom: 50px">
          <q-item
            clickable
            v-ripple
            :active="$page.url == '/admin/time-tracker'"
            @click="router.get(route('admin.time-tracker.index'))"
          >
            <q-item-section avatar>
              <q-icon class="material-icons-outlined">timer</q-icon>
            </q-item-section>
            <q-item-section>
              <q-item-label>Time Tracker</q-item-label>
            </q-item-section>
          </q-item>
          <q-item
            clickable
            v-ripple
            :active="$page.url == '/admin/dashboard'"
            @click="router.get(route('admin.dashboard'))"
          >
            <q-item-section avatar>
              <q-icon class="material-icons-outlined">dashboard</q-icon>
            </q-item-section>
            <q-item-section>
              <q-item-label>Dashboard</q-item-label>
            </q-item-section>
          </q-item>
          <q-item
            clickable
            v-ripple
            :active="$page.url == '/admin/reports'"
            @click="router.get(route('admin.report.index'))"
          >
            <q-item-section avatar>
              <q-icon class="material-symbols-outlined">monitoring</q-icon>
            </q-item-section>
            <q-item-section>
              <q-item-label>Laporan</q-item-label>
            </q-item-section>
          </q-item>
          <q-item
            clickable
            v-ripple
            :active="$page.url.startsWith('/admin/settings/profile')"
            @click="router.get(route('admin.profile.edit'))"
          >
            <q-item-section avatar>
              <q-icon class="material-symbols-outlined">account_circle</q-icon>
            </q-item-section>
            <q-item-section>
              <q-item-label>Profil Saya</q-item-label>
            </q-item-section>
          </q-item>
          <template v-if="check_role('admin')">
            <q-separator />
            <q-item>
              <q-item-label
                header
                class="text-caption"
                style="margin: 8px 0 0; padding: 0; text-transform: uppercase"
              >
                Administrator Area
              </q-item-label>
            </q-item>
            <q-item
              clickable
              v-ripple
              :active="$page.url == '/admin/time-entries'"
              @click="router.get(route('admin.time-entry.index'))"
            >
              <q-item-section avatar>
                <q-icon class="material-symbols-outlined">work_history</q-icon>
              </q-item-section>
              <q-item-section>
                <q-item-label>Time Entry</q-item-label>
              </q-item-section>
            </q-item>
            <q-item
              clickable
              v-ripple
              :active="$page.url.startsWith('/admin/projects')"
              @click="router.get(route('admin.project.index'))"
            >
              <q-item-section avatar>
                <q-icon class="material-symbols-outlined">task</q-icon>
              </q-item-section>
              <q-item-section>
                <q-item-label>Proyek</q-item-label>
              </q-item-section>
            </q-item>
            <q-item
              clickable
              v-ripple
              :active="$page.url.startsWith('/admin/clients')"
              @click="router.get(route('admin.client.index'))"
            >
              <q-item-section avatar>
                <q-icon class="material-symbols-outlined">apartment</q-icon>
              </q-item-section>
              <q-item-section>
                <q-item-label>Klien</q-item-label>
              </q-item-section>
            </q-item>
            <q-item
              clickable
              v-ripple
              :active="$page.url.startsWith('/admin/settings/users')"
              @click="router.get(route('admin.user.index'))"
            >
              <q-item-section avatar>
                <q-icon class="material-symbols-outlined">group</q-icon>
              </q-item-section>
              <q-item-section>
                <q-item-label>Pengguna</q-item-label>
              </q-item-section>
            </q-item>
          </template>

          <div class="absolute-bottom text-grey-6 q-pa-md">
            &copy; 2024 -
            {{ $config.APP_NAME + " v" + $config.APP_VERSION_STR }}
          </div>
        </q-list>
      </q-scroll-area>
    </q-drawer>
    <q-page-container class="bg-grey-1">
      <q-page>
        <slot></slot>
      </q-page>
    </q-page-container>
    <slot name="footer"></slot>
  </q-layout>
</template>

<style>
.profile-btn span.block {
  text-align: left !important;
  width: 100% !important;
  margin-left: 10px !important;
}
</style>
<style scoped>
.q-toolbar {
  border-bottom: 1px solid transparent;
  /* Optional border line */
}

.toolbar-scrolled {
  box-shadow: 0px 1px 2px rgba(0, 0, 0, 0.05);
  /* Add shadow */
  border-bottom: 1px solid #ddd;
  /* Optional border line */
}

.profile-btn-active {
  background-color: #ddd !important;
}

#profile-btn-popup .q-item--active {
  color: inherit !important;
}
</style>
