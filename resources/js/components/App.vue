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

<style scoped>
/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}
.header {
  margin-bottom: 2rem;
  text-align: center;
}
.header h1 {
  font-size: 2.5rem;
  color: #0ea5e9;
}

/* Open button */
.open-btn {
  background: linear-gradient(90deg, #0ea5e9, #3b82f6);
  color: white;
  font-weight: bold;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  transition: all 0.3s ease;
}
.open-btn:hover {
  transform: scale(1.05);
}

/* Dialog header */
.search-dialog .p-dialog-header {
  font-weight: bold;
  font-size: 1.25rem;
  color: #0ea5e9;
}

/* Input styling */
.input-style {
  padding: 0.75rem;
  border-radius: 10px;
  border: 1px solid #cbd5e1;
  box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
  width: 100%;
}
.input-style:focus {
  border-color: #0ea5e9;
  box-shadow: 0 0 8px rgba(14, 165, 233, 0.4);
}

/* Input wrapper */
.input-wrapper {
  position: relative;
}

/* Scrollable Results Box */
.results-container {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  max-height: 250px;
  overflow-y: auto;
  margin-top: 0.5rem;      /* space below input */
  padding: 0.5rem;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
  z-index: 50;
}
.result-btn {
  justify-content: flex-start;
  background: #f1f5f9;
  border-radius: 10px;
  border: none;
  padding: 0.5rem 1rem;
  width: 100%;
  transition: all 0.2s;
}
.result-btn:hover {
  background: #e0f2fe;
  transform: translateX(2px);
}

/* Selected country card */
/* Selected country card spacing */
.selected-country-card {
  background: #e0f2fe;
  padding: 1rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 0.75rem; /* <-- add this margin */
  margin-bottom: 1rem;
}

.country-name { font-weight: bold; font-size: 1.25rem; color: #0ea5e9; }
.country-code { color: #1e293b; font-size: 0.9rem; }

/* Footer buttons */
.footer-btn {
  border-radius: 10px;
  min-width: 100px;
}
</style>
