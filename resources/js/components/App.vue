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
          <!-- Display Selected Country Input with Flag -->
          <div class="selected-country-wrapper">
            <span v-if="selectedCountry" :class="`fi fi-${selectedCountry.code.toLowerCase()} selected-flag`"></span>
            <InputText
              :value="selectedCountry ? selectedCountry.name : ''"
              @click="toggleList"
              placeholder="Click to select country..."
              class="w-full input-style"
              readonly
            />
          </div>

          <!-- Dropdown List with Search Inside -->
          <div v-if="showList" class="results-container" @mousedown.prevent>
            <!-- Search Input Inside List -->
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

            <!-- Scrollable Country List -->
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

// Debounced search
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
      } catch (error) {
        filteredCountries.value = [];
      } finally {
        loading.value = false;
      }
    }, 150);
    return;
  }

  searchTimeout = setTimeout(async () => {
    loading.value = true;
    try {
      const response = await axios.get('/countries', { params: { search: query } });
      if (query === currentQuery) {
        filteredCountries.value = response.data.filter(c =>
          c.name.toLowerCase().startsWith(query.toLowerCase())
        );
        highlightedIndex.value = 0;
      }
    } catch (error) {
      filteredCountries.value = [];
    } finally {
      loading.value = false;
    }
  }, 150);
};

// Click outside to close dropdown
const handleClickOutside = (event) => {
  const dropdown = document.querySelector('.results-container');
  const mainInput = document.querySelector('.input-style');
  
  if (dropdown && !dropdown.contains(event.target) && !mainInput.contains(event.target)) {
    showList.value = false;
    searchQuery.value = '';
  }
};

// Toggle list
const toggleList = async () => {
  if (showList.value) {
    showList.value = false;
    return;
  }

  loading.value = true;
  try {
    const response = await axios.get('/countries');
    filteredCountries.value = response.data;
    highlightedIndex.value = selectedCountry.value
      ? filteredCountries.value.findIndex(c => c.code === selectedCountry.value.code)
      : 0;
    showList.value = true;
    searchQuery.value = '';
    await nextTick();
    if (searchInput.value) searchInput.value.$el.focus();
  } catch (error) {}
  finally { loading.value = false; }
};

const closeList = () => {
  showList.value = false;
  searchQuery.value = '';
};

const clearSearchInput = () => {
  searchQuery.value = '';
  searchCountries();
};

// Arrow navigation
const handleArrowDown = () => {
  if (filteredCountries.value.length > 0) {
    highlightedIndex.value = (highlightedIndex.value + 1) % filteredCountries.value.length;
    scrollToHighlighted();
  }
};
const handleArrowUp = () => {
  if (filteredCountries.value.length > 0) {
    highlightedIndex.value = highlightedIndex.value === 0
      ? filteredCountries.value.length - 1
      : highlightedIndex.value - 1;
    scrollToHighlighted();
  }
};
const scrollToHighlighted = () => {
  setTimeout(() => {
    const list = document.querySelector('.country-list');
    const highlighted = document.querySelector('.result-btn.highlighted');
    if (list && highlighted) {
      const listRect = list.getBoundingClientRect();
      const buttonRect = highlighted.getBoundingClientRect();
      if (buttonRect.bottom > listRect.bottom || buttonRect.top < listRect.top) {
        highlighted.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }
    }
  }, 0);
};
const handleEnter = () => {
  if (filteredCountries.value.length > 0) selectCountry(filteredCountries.value[highlightedIndex.value]);
};
const selectCountry = (country) => {
  selectedCountry.value = country;
  showList.value = false;
  searchQuery.value = '';
  highlightedIndex.value = filteredCountries.value.findIndex(c => c.code === country.code);
};
const clearSearch = () => {
  selectedCountry.value = null;
  searchQuery.value = '';
  filteredCountries.value = [];
  highlightedIndex.value = 0;
  showList.value = false;
};


// Click outside listener
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});
onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
