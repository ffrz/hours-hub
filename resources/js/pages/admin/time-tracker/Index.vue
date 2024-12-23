<script setup>
import Timer from "./Timer.vue";
import TimeEntries from "./TimeEntries.vue";
import { ref } from "vue";

const title = "Time Tracker";
const timeEntriesRef = ref(null);

const handleTimerSessionEnded = () => {
  console.log("Timer session ended");
  timeEntriesRef.value.fetchItems();
};

</script>

<template>
  <i-head :title="title" />
  <authenticated-layout>
    <template #title>{{ title }}</template>
    <timer class="q-pa-md" projects="projects" @timerSessionEnded="handleTimerSessionEnded" />
    <time-entries ref="timeEntriesRef" />
  </authenticated-layout>
</template>

