<template>
  <div class="w-full max-w-[1400px] mx-auto px-4 py-6 sm:px-6 sm:py-8 md:px-8 md:py-10 min-h-screen" style="background: white !important;">
    <!-- Header -->
    <header class="mb-4 sm:mb-6 md:mb-8">
      <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold" style="color: #374151 !important;">Ajax Remote Data Selector</h1>
    </header>

    <!-- Generic Selector Component -->
    <GenericSelector
      v-model="selectedCity"
      api-url="https://fake-json-api.mock.beeceptor.com/companies"
      placeholder="Search for a city"
    />
      
  </div>
</template>

<script setup>
import { ref } from "vue";
import GenericSelector from "./GenericSelector.vue";

// ============ Reactive State ============
console.log('App.vue loaded');
const selectedCity = ref(null);
</script>
