# CountrySelector to GenericSelector Conversion Guide

This document shows the transformation from a country-specific component to a fully generic, reusable selector component.

---

## File Rename

**Before:**
```
resources/js/components/CountrySelector.vue
```

**After:**
```
resources/js/components/GenericSelector.vue
```

---

## 1. Component Props

### Before (CountrySelector.vue)
```javascript
const props = defineProps({
  modelValue: {
    type: Object,
    default: null
  },
  placeholder: {
    type: String,
    default: "Search for a country"  // ❌ Hardcoded for countries
  },
  apiUrl: {
    type: String,
    default: "/countries"  // ❌ Hardcoded default
  }
});
```

### After (GenericSelector.vue)
```javascript
const props = defineProps({
  modelValue: {
    type: Object,
    default: null
  },
  placeholder: {
    type: String,
    default: "Search..."  // ✅ Generic placeholder
  },
  apiUrl: {
    type: String,
    required: true  // ✅ No default - must be provided
  },
  optionLabel: {  // ✅ NEW: Configurable display field
    type: String,
    default: "name"
  },
  showIcon: {  // ✅ NEW: Toggle icon display
    type: Boolean,
    default: false
  },
  iconField: {  // ✅ NEW: Configurable icon field
    type: String,
    default: "code"
  }
});
```

**Changes:**
- ✅ Added `optionLabel` prop to make the display field configurable
- ✅ Added `showIcon` prop to optionally show icons (flags)
- ✅ Added `iconField` prop to specify which field contains icon codes
- ✅ Made `apiUrl` required instead of having a default
- ✅ Changed default placeholder to be generic

---

## 2. Select Component Binding

### Before (CountrySelector.vue)
```vue
<Select
  :key="selectKey"
  v-model="selectedCountry"
  :options="filteredCountries"
  filter
  optionLabel="name"  <!-- ❌ Hardcoded to "name" -->
  :placeholder="placeholder"
```

### After (GenericSelector.vue)
```vue
<Select
  :key="selectKey"
  v-model="selectedCountry"
  :options="filteredCountries"
  filter
  :optionLabel="optionLabel"  <!-- ✅ Dynamic, uses prop -->
  :placeholder="placeholder"
```

**Changes:**
- ✅ Changed from hardcoded `optionLabel="name"` to dynamic `:optionLabel="optionLabel"`

---

## 3. Template Slots - Value Display

### Before (CountrySelector.vue)
```vue
<template #value="slotProps">
  <div v-if="slotProps.value" class="flex items-center gap-2 sm:gap-3">
    <!-- ❌ Always shows flag, hardcoded to "code" field -->
    <span v-if="slotProps.value.code" :class="`fi fi-${slotProps.value.code.toLowerCase()} text-sm sm:text-base`"></span>

    <!-- ❌ Hardcoded to "name" field -->
    <div class="text-xs sm:text-sm" style="color: #374151 !important;">
      {{ slotProps.value.name }}
    </div>
  </div>
  <span v-else class="text-xs sm:text-sm" style="color: #9ca3af !important;">
    {{ slotProps.placeholder }}
  </span>
</template>
```

### After (GenericSelector.vue)
```vue
<template #value="slotProps">
  <div v-if="slotProps.value" class="flex items-center gap-2 sm:gap-3">
    <!-- ✅ Conditionally shows icon based on showIcon prop -->
    <!-- ✅ Uses configurable iconField prop -->
    <span
      v-if="showIcon && slotProps.value[iconField]"
      :class="`fi fi-${slotProps.value[iconField].toLowerCase()} text-sm sm:text-base`">
    </span>

    <!-- ✅ Uses configurable optionLabel prop -->
    <div class="text-xs sm:text-sm" style="color: #374151 !important;">
      {{ slotProps.value[optionLabel] }}
    </div>
  </div>
  <span v-else class="text-xs sm:text-sm" style="color: #9ca3af !important;">
    {{ slotProps.placeholder }}
  </span>
</template>
```

**Changes:**
- ✅ Icon now only shows if `showIcon` prop is true
- ✅ Icon field is now dynamic using `iconField` prop
- ✅ Display text now uses dynamic `optionLabel` prop instead of hardcoded "name"

---

## 4. Template Slots - Option Display

### Before (CountrySelector.vue)
```vue
<template #option="slotProps">
  <div class="flex items-center gap-2 sm:gap-3" style="color: #374151 !important;">
    <!-- ❌ Always shows flag, checks only "code" field -->
    <span v-if="slotProps.option.code" :class="`fi fi-${slotProps.option.code.toLowerCase()} text-sm sm:text-base`"></span>

    <!-- ❌ Hardcoded to "name" field -->
    <div class="text-xs sm:text-sm" style="color: #374151 !important;">
      {{ slotProps.option.name }}
    </div>
  </div>
</template>
```

### After (GenericSelector.vue)
```vue
<template #option="slotProps">
  <div class="flex items-center gap-2 sm:gap-3" style="color: #374151 !important;">
    <!-- ✅ Conditionally shows icon based on showIcon prop -->
    <!-- ✅ Uses configurable iconField prop -->
    <span
      v-if="showIcon && slotProps.option[iconField]"
      :class="`fi fi-${slotProps.option[iconField].toLowerCase()} text-sm sm:text-base`">
    </span>

    <!-- ✅ Uses configurable optionLabel prop -->
    <div class="text-xs sm:text-sm" style="color: #374151 !important;">
      {{ slotProps.option[optionLabel] }}
    </div>
  </div>
</template>
```

**Changes:**
- ✅ Icon now only shows if `showIcon` prop is true
- ✅ Icon field is now dynamic using `iconField` prop
- ✅ Display text now uses dynamic `optionLabel` prop instead of hardcoded "name"

---

## 5. Data Filtering Logic

### Before (CountrySelector.vue)
```javascript
// Filter on frontend to match countries that START WITH the search query
if (searchQuery.value && searchQuery.value.length > 0) {
  filteredCountries.value = response.data.filter(country =>
    country.name.toLowerCase().startsWith(searchQuery.value.toLowerCase())  // ❌ Hardcoded "name"
  );
} else {
  // Don't show any countries if search is empty
  filteredCountries.value = [];
}
```

### After (GenericSelector.vue)
```javascript
// Filter on frontend to match items that START WITH the search query
if (searchQuery.value && searchQuery.value.length > 0) {
  filteredCountries.value = response.data.filter(item =>
    item[props.optionLabel].toLowerCase().startsWith(searchQuery.value.toLowerCase())  // ✅ Dynamic field
  );
} else {
  // Don't show any items if search is empty
  filteredCountries.value = [];
}
```

**Changes:**
- ✅ Changed from `country.name` to `item[props.optionLabel]` for dynamic field filtering
- ✅ Updated comments from "countries" to generic "items"

---

## 6. Usage in App.vue

### Before (CountrySelector)
```vue
<template>
  <CountrySelector v-model="selectedCountry" />
</template>

<script setup>
import { ref } from "vue";
import CountrySelector from "./CountrySelector.vue";

const selectedCountry = ref(null);
</script>
```

### After (GenericSelector)
```vue
<template>
  <!-- For Cities (no icons) -->
  <GenericSelector
    v-model="selectedCity"
    api-url="/cities"
    placeholder="Search for a city"
  />

  <!-- For Countries (with flags) -->
  <GenericSelector
    v-model="selectedCountry"
    api-url="/countries"
    placeholder="Search for a country"
    :show-icon="true"
    icon-field="code"
  />

  <!-- For Cars (custom label field) -->
  <GenericSelector
    v-model="selectedCar"
    api-url="/cars"
    option-label="model"
    placeholder="Search for a car"
  />
</template>

<script setup>
import { ref } from "vue";
import GenericSelector from "./GenericSelector.vue";

const selectedCity = ref(null);
const selectedCountry = ref(null);
const selectedCar = ref(null);
</script>
```

---

## Summary of Changes

| Feature | CountrySelector (Before) | GenericSelector (After) |
|---------|-------------------------|------------------------|
| **Display Field** | Hardcoded to `"name"` | Configurable via `optionLabel` prop |
| **Icon Display** | Always shown if `code` exists | Optional via `showIcon` prop |
| **Icon Field** | Hardcoded to `"code"` | Configurable via `iconField` prop |
| **API URL** | Default: `"/countries"` | Required, no default |
| **Placeholder** | "Search for a country" | "Search..." (generic) |
| **Filtering** | Hardcoded to `country.name` | Dynamic: `item[optionLabel]` |
| **Use Cases** | Only countries | Any data: cities, cars, products, etc. |

---

## Real-World Usage Examples

### Example 1: Cities Selector
```vue
<GenericSelector
  v-model="selectedCity"
  api-url="/cities"
  placeholder="Search for a city"
/>
```
**API Response:**
```json
[
  { "id": 1, "name": "New York" },
  { "id": 2, "name": "London" }
]
```

---

### Example 2: Countries Selector (with flags)
```vue
<GenericSelector
  v-model="selectedCountry"
  api-url="/countries"
  placeholder="Search for a country"
  :show-icon="true"
  icon-field="code"
/>
```
**API Response:**
```json
[
  { "name": "United States", "code": "US" },
  { "name": "Canada", "code": "CA" }
]
```

---

### Example 3: Cars Selector (custom field)
```vue
<GenericSelector
  v-model="selectedCar"
  api-url="/cars"
  option-label="model"
  placeholder="Search for a car"
/>
```
**API Response:**
```json
[
  { "id": 1, "model": "Tesla Model 3", "year": 2024 },
  { "id": 2, "model": "BMW X5", "year": 2023 }
]
```

---

### Example 4: Products Selector
```vue
<GenericSelector
  v-model="selectedProduct"
  api-url="/products"
  option-label="title"
  placeholder="Search for a product"
/>
```
**API Response:**
```json
[
  { "id": 1, "title": "iPhone 15", "price": 999 },
  { "id": 2, "title": "MacBook Pro", "price": 2499 }
]
```

---

## Key Benefits of GenericSelector

✅ **Reusable** - Use for any type of data (countries, cities, cars, products, etc.)
✅ **Flexible** - Configurable display fields, icons, and placeholders
✅ **Maintainable** - Single component instead of multiple similar ones
✅ **Consistent** - Same UI/UX across all dropdowns in your app
✅ **Scalable** - Easy to add new dropdowns without duplicating code

---

*Generated: 2025-10-09*
*Project: Dropdown Country Selector → Generic Selector*
