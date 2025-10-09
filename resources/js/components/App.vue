<template>
  <div class="w-full max-w-[1400px] mx-auto px-4 py-6 sm:px-6 sm:py-8 md:px-8 md:py-10 min-h-screen" style="background: white !important;">
    <!-- Header -->
    <header class="mb-4 sm:mb-6 md:mb-8">
      <h1 class="text-xl sm:text-2xl md:text-3xl font-semibold" style="color: #374151 !important;">Country Selector</h1>
    </header>

    <!-- Country Select Component -->
    <CountrySelector v-model="selectedCountry" />
  </div>
</template>

<script setup>
import { ref } from "vue";
import CountrySelector from "./CountrySelector.vue";

// ============ Reactive State ============
const selectedCountry = ref(null);
</script>
