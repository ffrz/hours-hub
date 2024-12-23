<script setup>
import { ref } from 'vue';

const title = 'Dashboard';

const buttonRef = ref(null);
const showMenu = ref(false);

const showContextMenu = () => {

}

</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="q-ma-md">
      <div class="row q-my-sm">
        <q-card class="col" square flat bordered>
          <q-card-section>
            <div class="q-mb-md text-h6">Hi, {{ $page.props.auth.user.name }}</div>
            <p>Selamat datang di dashboard {{ $config.APP_NAME }}.
              Anda saat ini login sebagai {{ $CONSTANTS.USER_ROLES[$page.props.auth.user.role] }}</p>

            <q-btn flat icon="more_vert" dense square @click="showContextMenu">
              <q-menu v-model="showMenu" anchor="bottom left" self="top left">
                <q-list style="min-width: 150px;">
                  <q-item clickable v-ripple v-close-popup>
                    <q-item-section>Option 1</q-item-section>
                  </q-item>
                  <q-item clickable v-ripple v-close-popup>
                    <q-item-section>Option 2</q-item-section>
                  </q-item>
                  <q-item clickable v-ripple v-close-popup>
                    <q-item-section>Option 3</q-item-section>
                  </q-item>
                </q-list>
              </q-menu>
            </q-btn>

          </q-card-section>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>
