<template>
  <div class="max-w-[1200px] mx-auto p-4 sm:p-6 md:p-8 min-h-screen flex flex-col items-center bg-gray-50">
    <!-- Header -->
    <header class="mb-6 sm:mb-8 text-center px-4">
      <h1 class="!text-2xl sm:!text-3xl md:!text-[2.5rem] !font-bold !text-[#0ea5e9]">Live Search Countries</h1>
    </header>

    <!-- Open Modal Button -->
    <div class="mb-6 px-4">
      <Button
        label="Open Search Modal"
        @click="showFirstModal = true"
        :pt="{
          root: { class: '!bg-gradient-to-r !from-[#0ea5e9] !to-[#3b82f6] !text-white !font-bold !px-4 sm:!px-6 !py-2.5 sm:!py-3 !rounded-[12px] !shadow hover:!brightness-110 hover:!scale-105 !transition-all !duration-300 !text-sm sm:!text-base' }
        }"
      />
    </div>

    <!-- Search Modal -->
    <Dialog
      v-model:visible="showFirstModal"
      header="Search Country"
      :style="{ width: '95vw', maxWidth: '500px' }"
      :modal="true"
      :pt="{
        header: { class: '!font-bold !text-lg sm:!text-[1.25rem] !text-[#0ea5e9] !bg-transparent' },
        content: { class: '!p-4 sm:!p-6' },
        root: { class: '!mx-4' }
      }"
      class="!rounded-[10px]"
    >
      <p class="mb-3 sm:mb-4 text-sm sm:text-base text-gray-700">Click to select a country:</p>

      <!-- Input + Dropdown -->
      <div class="relative w-full" :style="{ marginBottom: showList ? '380px' : '0px', transition: 'margin-bottom 0.2s ease' }">
        <!-- Selected Country Input -->
        <div class="relative w-full mb-2">
          <span
            v-if="selectedCountry"
            :class="`fi fi-${selectedCountry.code.toLowerCase()}`"
            style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-size: 16px; pointer-events: none; z-index: 1;"
          ></span>
          <InputText
            :value="selectedCountry ? selectedCountry.name : ''"
            @click="toggleList"
            placeholder="Click to select country..."
            readonly
            class="input-style"
            :pt="{
              root: { class: '!w-full !pr-3 !py-2.5 sm:!py-3 !rounded-[10px] !border !border-[#0ea5e9] !bg-black !text-white !shadow-inner !cursor-pointer placeholder:!text-white/60 focus:!outline-none !text-sm sm:!text-base' }
            }"
            style="padding-left: 48px !important;"
          />
        </div>

        <!-- Dropdown List Container -->
        <div
          v-if="showList"
          style="position: absolute; z-index: 9999; width: 100%; top: 100%; margin-top: 4px;"
          @mousedown.prevent
        >
          <div style="background: black; border: 1px solid #0ea5e9; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; max-height: 400px;">
            
            <!-- Search Input - Fixed at top -->
            <div style="padding: 10px; background: black; border-bottom: 1px solid #0ea5e9; border-radius: 10px 10px 0 0; flex-shrink: 0;">
              <div style="position: relative; display: flex; align-items: center;">
                <i class="pi pi-search" style="position: absolute; left: 12px; color: #0ea5e9; font-size: 14px; pointer-events: none;"></i>
                <InputText
                  v-model="searchQuery"
                  @input="searchCountries"
                  @keydown.enter.prevent="handleEnter"
                  @keydown.down.prevent="handleArrowDown"
                  @keydown.up.prevent="handleArrowUp"
                  @keydown.escape="closeList"
                  placeholder="Search countries..."
                  ref="searchInput"
                  :pt="{
                    root: { class: '!w-full !py-2 !rounded-[8px] !border !border-[#0ea5e9] !text-sm focus:!border-[#0ea5e9] focus:!shadow-[0_0_4px_rgba(14,165,233,0.3)] focus:!outline-none !bg-black !text-white placeholder:!text-gray-400' }
                  }"
                  style="padding-left: 36px !important; padding-right: 36px !important;"
                />
                <i
                  v-if="searchQuery"
                  @click="clearSearchInput"
                  class="pi pi-times"
                  style="position: absolute; right: 12px; color: #94a3b8; font-size: 14px; cursor: pointer; transition: color 0.2s;"
                  @mouseenter="$event.target.style.color = '#0ea5e9'"
                  @mouseleave="$event.target.style.color = '#94a3b8'"
                ></i>
              </div>
            </div>

            <!-- Scrollable Country List ONLY -->
            <div style="display: flex; flex-direction: column; gap: 6px; padding: 8px; overflow-y: auto; max-height: 300px; overflow-x: hidden; background: black;">
              <Button
                v-for="(country, index) in filteredCountries"
                :key="country.code"
                @click="selectCountry(country)"
                @mouseenter="highlightedIndex = index"
                :class="[
                  'country-list-item',
                  { 'highlighted': index === highlightedIndex }
                ]"
                :pt="{
                  root: {
                    class: [
                      '!w-full !flex !items-center !text-left !rounded-none !transition-all !duration-200 !justify-start !border-none !text-base sm:!text-lg',
                      {
                        '!bg-[#60a5fa] !text-white !translate-x-[2px]': index === highlightedIndex,
                        '!bg-[#3b82f6] !text-white': selectedCountry && selectedCountry.code === country.code,
                        '!bg-[#1a1a1a] !text-white hover:!bg-[#2a2a2a] hover:!text-white': !(index === highlightedIndex || (selectedCountry && selectedCountry.code === country.code))
                      }
                    ]
                  }
                }"
                style="padding: 10px 12px !important;"
              >
                <span :class="`fi fi-${country.code.toLowerCase()}`" style="font-size: 18px; margin-right: 12px;"></span>
                <span style="font-size: 16px; color: white;">{{ country.name }}</span>
              </Button>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <template #footer>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px;" class="sm:!gap-4 sm:!mt-4">
          <Button
            label="Clear"
            @click="clearSearch"
            :pt="{
              root: { class: '!bg-[#0ea5e9] !text-white !rounded-[10px] !min-w-[80px] sm:!min-w-[100px] hover:!bg-[#3b82f6] !transition-colors !text-sm sm:!text-base' }
            }"
          />
          <Button
            label="Close"
            @click="showFirstModal = false"
            :pt="{
              root: { class: '!bg-[#0ea5e9] !text-white !rounded-[10px] !min-w-[80px] sm:!min-w-[100px] hover:!bg-[#3b82f6] !transition-colors !text-sm sm:!text-base' }
            }"
          />
        </div>
      </template>
    </Dialog>
  </div>
</template>


<script setup>
import { ref, nextTick } from "vue";
import axios from "axios";
import Dialog from "primevue/dialog";
import Button from "primevue/button";
import InputText from "primevue/inputtext";

const showFirstModal = ref(false);
const showList = ref(false);
const selectedCountry = ref(null);
const filteredCountries = ref([]);
const searchQuery = ref("");
const highlightedIndex = ref(0);
const searchInput = ref(null);

const toggleList = async () => {
  showList.value = !showList.value;
  if (showList.value) {
    await fetchCountries();
    await nextTick();
    if (searchInput.value) searchInput.value.$el.focus();
  }
};

const fetchCountries = async () => {
  try {
    const response = await axios.get("/countries");
    
    // Filter on frontend to match countries that START WITH the search query
    if (searchQuery.value) {
      filteredCountries.value = response.data.filter(country =>
        country.name.toLowerCase().startsWith(searchQuery.value.toLowerCase())
      );
    } else {
      filteredCountries.value = response.data;
    }
    
    highlightedIndex.value = 0;
  } catch (err) {
    filteredCountries.value = [];
  }
};

const selectCountry = (country) => {
  selectedCountry.value = country;
  showList.value = false;
  highlightedIndex.value = filteredCountries.value.findIndex(
    (c) => c.code === country.code
  );
};

const clearSearchInput = () => {
  searchQuery.value = "";
  fetchCountries();
};

const clearSearch = () => {
  selectedCountry.value = null;
  searchQuery.value = "";
  filteredCountries.value = [];
};

const closeList = () => {
  showList.value = false;
};

const handleEnter = () => {
  if (filteredCountries.value.length > 0)
    selectCountry(filteredCountries.value[highlightedIndex.value]);
};

const handleArrowDown = () => {
  if (filteredCountries.value.length > 0) {
    highlightedIndex.value =
      (highlightedIndex.value + 1) % filteredCountries.value.length;
    scrollToHighlighted();
  }
};

const handleArrowUp = () => {
  if (filteredCountries.value.length > 0) {
    highlightedIndex.value =
      highlightedIndex.value === 0
        ? filteredCountries.value.length - 1
        : highlightedIndex.value - 1;
    scrollToHighlighted();
  }
};

const scrollToHighlighted = () => {
  nextTick(() => {
    const highlightedElement = document.querySelector('.country-list-item.highlighted');
    if (highlightedElement) {
      highlightedElement.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      });
    }
  });
};

const searchCountries = () => {
  fetchCountries();
};
</script>