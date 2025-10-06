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
        <p class="mb-3">Type to search countries from API:</p>

        <!-- Input + Results wrapper -->
        <div class="input-wrapper mb-4">
          <InputText
            v-model="searchQuery"
            @input="searchCountries"
            @keydown.enter="handleEnter"
            placeholder="Start typing country name..."
            class="w-full input-style"
          />

          <!-- Scrollable Results Box -->
          <div v-if="filteredCountries.length > 0 && !selectedCountry" class="results-container">
            <Button
              v-for="country in filteredCountries"
              :key="country.code"
              @click="selectCountry(country)"
              class="result-btn"
            >
              <span :class="`fi fi-${country.code.toLowerCase()} mr-2`"></span>
              {{ country.name }}
            </Button>
          </div>
        </div>

        <!-- Selected Country -->
        <div v-if="selectedCountry" class="selected-country-card">
          <div class="flex items-center gap-4">
            <span :class="`fi fi-${selectedCountry.code.toLowerCase()}`" style="font-size: 2.5rem;"></span>
            <div>
              <p class="country-name">{{ selectedCountry.name }}</p>
              <p class="country-code">Code: {{ selectedCountry.code }}</p>
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
import { ref } from "vue";
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
let searchTimeout = null;

// Debounced search
const searchCountries = () => {
  if (searchTimeout) clearTimeout(searchTimeout);

  const query = searchQuery.value.trim();
  if (!query) {
    filteredCountries.value = [];
    selectedCountry.value = null;
    return;
  }

  searchTimeout = setTimeout(async () => {
    loading.value = true;
    try {
      const response = await axios.get('/countries', { params: { search: query } });
      // Filter countries starting with the typed letters
      filteredCountries.value = response.data.filter(country =>
        country.name.toLowerCase().startsWith(query.toLowerCase())
      );
    } catch (error) {
      console.error('Error searching countries:', error);
      filteredCountries.value = [];
    } finally {
      loading.value = false;
    }
  }, 150);
};

// Select a country
const selectCountry = (country) => {
  selectedCountry.value = country;
  searchQuery.value = country.name;
  filteredCountries.value = [country];
};

// Clear search
const clearSearch = () => {
  selectedCountry.value = null;
  searchQuery.value = '';
  filteredCountries.value = [];
};

// Enter key selects first country
const handleEnter = (event) => {
  if (filteredCountries.value.length > 0) {
    selectCountry(filteredCountries.value[0]);
    event.preventDefault();
  }
};
</script>


