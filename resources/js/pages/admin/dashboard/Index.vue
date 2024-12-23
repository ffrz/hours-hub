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
              Anda saat ini login sebagai {{ $CONSTANTS.USER_ROLES[$page.props.auth.user.role] }}.</p>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>
