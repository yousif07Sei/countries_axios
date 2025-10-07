<template>
  <div class="container">
    <div class="header">
      <h1>Live Search Countries</h1>
    </div>

    <div class="content">
      <div class="card flex justify-center">
        <Button label="Open Search Modal" @click="showFirstModal = true" class="open-btn" />
      </div>

      <!-- Search Modal -->
      <Dialog v-model:visible="showFirstModal" header="Search Country" :style="{ width: '500px' }" :modal="true" class="search-dialog">
        <p class="mb-3">Click to select a country:</p>

        <!-- Input + Results wrapper -->
        <div class="input-wrapper mb-4">
          <!-- Selected Country Input with Flag -->
          <InputText
            :value="selectedCountry ? selectedCountry.name : ''"
            @click="toggleList"
            placeholder="Click to select country..."
            class="w-full input-style"
            readonly
          >
            <template #prepend>
              <span v-if="selectedCountry" :class="`fi fi-${selectedCountry.code.toLowerCase()} mr-2`"></span>
            </template>
          </InputText>

          <!-- Dropdown List -->
          <div v-if="showList" class="results-container" @mousedown.prevent>
            <div class="search-input-wrapper-inside">
              <i class="pi pi-search search-icon-inside"></i>
              <InputText
                v-model="searchQuery"
                @input="searchCountries"
                @keydown.enter.prevent="handleEnter"
                @keydown.down.prevent="handleArrowDown"
                @keydown.up.prevent="handleArrowUp"
                @keydown.escape="closeList"
                placeholder="Search countries..."
                class="w-full search-input-inside"
                ref="searchInput"
              />
              <i v-if="searchQuery" @click="clearSearchInput" class="pi pi-times clear-icon-inside"></i>
            </div>

            <div class="country-list">
              <Button
                v-for="(country, index) in filteredCountries"
                :key="country.code"
                @click="selectCountry(country)"
                :class="['result-btn', { 'highlighted': index === highlightedIndex, 'selected': selectedCountry && selectedCountry.code === country.code }]"
                @mouseenter="highlightedIndex = index"
              >
                <span :class="`fi fi-${country.code.toLowerCase()} mr-2`"></span>
                {{ country.name }}
              </Button>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <template #footer>
          <Button label="Clear" @click="clearSearch" severity="secondary" outlined class="footer-btn" />
          <Button label="Close" @click="showFirstModal = false" severity="secondary" class="footer-btn" />
        </template>
      </Dialog>
    </div>
  </div>
</template>

<script setup>
import { ref, nextTick, onMounted, onUnmounted } from "vue";
import axios from "axios";
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import './LiveSearchModal.css';

const showFirstModal = ref(false);
const selectedCountry = ref(null);
const filteredCountries = ref([]);
const searchQuery = ref('');
const loading = ref(false);
const highlightedIndex = ref(0);
const showList = ref(false);
const searchInput = ref(null);
let searchTimeout = null;
let currentQuery = '';

// Search countries with debounce
const searchCountries = () => {
  if (searchTimeout) clearTimeout(searchTimeout);
  const query = searchQuery.value.trim();
  currentQuery = query;

  if (!query) {
    searchTimeout = setTimeout(async () => {
      loading.value = true;
      try {
        const response = await axios.get('/countries');
        filteredCountries.value = response.data;
        highlightedIndex.value = 0;
      } finally { loading.value = false; }
    }, 150);
    return;
  }

  searchTimeout = setTimeout(async () => {
    loading.value = true;
    try {
      const response = await axios.get('/countries', { params: { search: query } });
      if (query === currentQuery) {
        filteredCountries.value = response.data.filter(c => c.name.toLowerCase().startsWith(query.toLowerCase()));
        highlightedIndex.value = 0;
      }
    } finally { loading.value = false; }
  }, 150);
};

// Toggle dropdown and focus selected
const toggleList = async () => {
  if (showList.value) { showList.value = false; return; }
  loading.value = true;
  try {
    const response = await axios.get('/countries');
    filteredCountries.value = response.data;
    showList.value = true;
    searchQuery.value = '';
    highlightedIndex.value = selectedCountry.value
      ? filteredCountries.value.findIndex(c => c.code === selectedCountry.value.code)
      : 0;

    await nextTick();
    if (searchInput.value) searchInput.value.$el.focus();
  } finally { loading.value = false; }
};

const closeList = () => { showList.value = false; searchQuery.value = ''; };
const clearSearchInput = () => { searchQuery.value = ''; searchCountries(); };
const handleArrowDown = () => { highlightedIndex.value = (highlightedIndex.value + 1) % filteredCountries.value.length; scrollToHighlighted(); };
const handleArrowUp = () => { highlightedIndex.value = highlightedIndex.value === 0 ? filteredCountries.value.length - 1 : highlightedIndex.value - 1; scrollToHighlighted(); };
const handleEnter = () => { if (filteredCountries.value.length > 0) selectCountry(filteredCountries.value[highlightedIndex.value]); };

const scrollToHighlighted = () => {
  setTimeout(() => {
    const countryList = document.querySelector('.country-list');
    const highlightedButton = document.querySelector('.result-btn.highlighted');
    if (countryList && highlightedButton) {
      const containerRect = countryList.getBoundingClientRect();
      const buttonRect = highlightedButton.getBoundingClientRect();
      if (buttonRect.bottom > containerRect.bottom || buttonRect.top < containerRect.top) {
        highlightedButton.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }
    }
  }, 0);
};

const selectCountry = (country) => {
  selectedCountry.value = country;
  showList.value = false;
  searchQuery.value = '';
  highlightedIndex.value = 0;
};

const clearSearch = () => {
  selectedCountry.value = null;
  searchQuery.value = '';
  filteredCountries.value = [];
  highlightedIndex.value = 0;
  showList.value = false;
};

// Click outside closes list
const handleClickOutside = (event) => {
  const dropdown = document.querySelector('.results-container');
  const mainInput = document.querySelector('.input-style');
  if (dropdown && !dropdown.contains(event.target) && !mainInput.contains(event.target)) {
    showList.value = false;
    searchQuery.value = '';
  }
};

onMounted(() => { document.addEventListener('click', handleClickOutside); });
onUnmounted(() => { document.removeEventListener('click', handleClickOutside); });
</script>
