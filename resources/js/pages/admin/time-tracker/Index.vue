<script setup>

import Timer from './Timer.vue';

const title = 'Time Tracker';

</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <div class="q-ma-md">
      <timer projects="projects"/>
      <div class="text-subtitle1 q-mt-lg">Minggu Ini</div>
      <div class="row q-my-sm">
        <q-card class="col" square flat bordered>
          <q-card-section class="row q-gutter-md">
            #TimeTrackerLog
            <!-- <q-select label="Proyek" :options="projects" map-options emit-value v-model="project"/>
            <q-input label="Judul" class="col" />
            <q-input label="Durasi" class="col-auto" readonly v-model="formattedDuration" />
            <q-btn icon="start" v-ripple/> -->
          </q-card-section>
        </q-card>
      </div>
    </div>
  </authenticated-layout>
</template>

<style>
.no-wrap-options .q-field__control  {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
</style>
