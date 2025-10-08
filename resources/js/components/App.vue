<template>
  <div class="max-w-[1400px] mx-auto p-4 sm:p-6 md:p-8 min-h-screen" style="background: white !important;">
    <!-- Header -->
    <header class="mb-6 sm:mb-8">
      <h1 class="text-2xl sm:text-3xl font-semibold" style="color: #374151 !important;">Country Selector</h1>
    </header>

    <!-- Country Select -->
    <div class="w-full max-w-full">
      <Select
        :key="selectKey"
        v-model="selectedCountry"
        :options="filteredCountries"
        filter
        optionLabel="name"
        placeholder="Search for a country"
        @filter="searchCountries"
        @show="handleDropdownShow"
        @hide="handleDropdownHide"
        @keydown="handleKeyDown"
        class="country-select"
        emptyMessage="Please enter 1 or more characters"
        :filterFocus="true"
        :pt="{
          root: { 
            class: '!w-full !bg-white !relative',
            style: isDropdownOpen 
              ? 'border: 1px solid #d1d5db !important; border-bottom: 1px solid #d1d5db !important; border-radius: 6px 6px 0 0 !important; background: white !important; z-index: 1001 !important; position: relative !important;'
              : 'border: 1px solid #d1d5db !important; border-radius: 6px !important; background: white !important;'
          },
          input: { 
            class: '!py-2.5 !px-3 !text-sm',
            style: 'color: #374151 !important; background: white !important;'
          },
          inputFocus: {
            style: 'border-color: #3b82f6 !important; box-shadow: 0 0 0 1px #3b82f6 !important; outline: none !important;'
          },
          trigger: { 
            class: '!w-10',
            style: 'color: #6b7280 !important; background: white !important;'
          },
          panel: {
            class: '!mt-0',
            style: 'border: 1px solid #d1d5db !important; border-top: 1px solid #d1d5db !important; border-radius: 0 0 6px 6px !important; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; background: white !important; z-index: 1000 !important; margin-top: -1px !important; position: absolute !important; top: 100% !important; left: 0 !important; right: 0 !important;'
          },
          list: { 
            class: '!p-0',
            style: 'background: white !important;'
          },
          item: {
            class: '!py-2.5 !px-4 !text-sm !border-b last:!border-b-0',
            style: 'color: #374151 !important; background: white !important; border-color: #f3f4f6 !important; border-radius: 0 !important;'
          },
          option: {
            style: ({ context }) => ({
              background: context.focused ? 'var(--p-primary-200) !important' : 'white !important',
              color: context.focused ? 'var(--p-primary-800) !important' : '#374151 !important'
            })
          },
          itemHighlight: {
            style: 'background: var(--p-primary-200) !important; color: var(--p-primary-800) !important;'
          },
          itemSelected: {
            style: 'background: var(--p-primary-500) !important; color: white !important;'
          },
          filterContainer: { 
            class: '!p-3 !border-b',
            style: 'background: white !important; border-color: #e5e7eb !important;'
          },
          filterInput: { 
            class: '!w-full !py-2 !px-3 !text-sm',
            style: 'border: 1px solid #d1d5db !important; border-radius: 6px !important; background: white !important; color: #374151 !important;'
          },
          filterInputFocus: {
            style: 'border-color: #3b82f6 !important; box-shadow: 0 0 0 1px #3b82f6 !important; outline: none !important;'
          },
          emptyMessage: { 
            class: '!py-3 !px-4 !text-sm',
            style: 'color: #6b7280 !important; background: white !important;'
          }
        }"
      >
        <template #value="slotProps">
          <div v-if="slotProps.value" class="flex items-center gap-3">
            <span :class="`fi fi-${slotProps.value.code.toLowerCase()} text-base`"></span>
            <div class="text-sm" style="color: #374151 !important;">{{ slotProps.value.name }}</div>
          </div>
          <span v-else class="text-sm" style="color: #9ca3af !important;">
            {{ slotProps.placeholder }}
          </span>
        </template>
        <template #option="slotProps">
          <div class="flex items-center gap-3" style="color: #374151 !important;">
            <span :class="`fi fi-${slotProps.option.code.toLowerCase()} text-base`"></span>
            <div class="text-sm" style="color: #374151 !important;">{{ slotProps.option.name }}</div>
          </div>
        </template>
        <template #filtericon>
          <i class="pi pi-search text-xs" style="color: #9ca3af !important;"></i>
        </template>
      </Select>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick } from "vue";
import axios from "axios";
import Select from "primevue/select";

// ============ Reactive State ============
const selectedCountry = ref(null);
const filteredCountries = ref([]);
const allCountries = ref([]);
const isDropdownOpen = ref(false);
const searchQuery = ref("");
const highlightedIndex = ref(0);
const selectKey = ref(0); // Key to force component re-render

// ============ Fetch Countries ============
const fetchCountries = async () => {
  try {
    const response = await axios.get("/countries");
    allCountries.value = response.data;

    // Filter on frontend to match countries that START WITH the search query
    if (searchQuery.value && searchQuery.value.length > 0) {
      filteredCountries.value = response.data.filter(country =>
        country.name.toLowerCase().startsWith(searchQuery.value.toLowerCase())
      );
    } else {
      // Don't show any countries if search is empty
      filteredCountries.value = [];
    }

    highlightedIndex.value = 0;
  } catch (err) {
    console.error("Error fetching countries:", err);
    filteredCountries.value = [];
  }
};

// ============ Handle Dropdown Show ============
const handleDropdownShow = async () => {
  isDropdownOpen.value = true;

  // Focus the filter input after dropdown opens
  await nextTick();
  const filterInput = document.querySelector('.p-select-filter');
  if (filterInput) {
    filterInput.focus();
  }
};

// ============ Handle Keyboard Events ============
const handleKeyDown = (event) => {
  // If Enter is pressed and dropdown is closed, open it
  if (event.key === 'Enter' && !isDropdownOpen.value) {
    event.preventDefault();
    // Trigger the dropdown to open
    const trigger = document.querySelector('.p-select-dropdown');
    if (trigger) {
      trigger.click();
    }
  }
};

// ============ Handle Dropdown Hide ============
const handleDropdownHide = () => {
  isDropdownOpen.value = false;
  // Clear search input and force component re-render to reset filter
  setTimeout(() => {
    searchQuery.value = "";
    filteredCountries.value = [];
    selectKey.value++; // Force component to re-render and clear internal filter state
  }, 100);
};

// ============ Search Countries ============
const searchCountries = async (event) => {
  searchQuery.value = event.value;
  await fetchCountries();

  // Highlight and focus first item in dropdown so Enter key works
  await nextTick();
  if (filteredCountries.value.length > 0) {
    // Simulate arrow down key press to focus first item
    await nextTick();
    const filterInput = document.querySelector('.p-select-filter');
    if (filterInput) {
      // Dispatch arrow down event to trigger PrimeVue's internal focus logic
      const arrowDownEvent = new KeyboardEvent('keydown', {
        key: 'ArrowDown',
        code: 'ArrowDown',
        keyCode: 40,
        bubbles: true,
        cancelable: true
      });
      filterInput.dispatchEvent(arrowDownEvent);
    }
  }
};

// ============ Select Country ============
const selectCountry = (country) => {
  selectedCountry.value = country;
  isDropdownOpen.value = false;
  highlightedIndex.value = filteredCountries.value.findIndex(
    (c) => c.code === country.code
  );
};

// ============ Clear Functions ============
const clearSearch = () => {
  selectedCountry.value = null;
  searchQuery.value = "";
  filteredCountries.value = [];
};

const clearSearchInput = () => {
  searchQuery.value = "";
  fetchCountries();
};

// ============ Keyboard Handlers ============
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

// Fetch countries when component mounts
fetchCountries();
</script>