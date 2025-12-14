<template>
  <Head title="Contact Us Management" />

  <AuthenticatedLayout>
    <div class="p-6 space-y-6 max-w-4xl">

      <h2 class="text-2xl font-semibold text-dash-title">
        Contact Us Management
      </h2>

      <div v-if="loading" class="text-sm text-gray-500">
        Loading…
      </div>

      <div v-else class="bg-white p-6 rounded-2xl shadow-sm border space-y-6">

        <!-- COUNTRY -->
        <div>
          <label class="block text-xs text-gray-500 mb-1">Country</label>
          <select
            class="form-input"
            v-model="form.country"
            @change="onCountryChange"
          >
            <option value="">—</option>
            <option value="Egypt">Egypt</option>
            <option value="Kuwait">Kuwait</option>
          </select>

          <p v-if="err('country')" class="text-red-600 text-xs">
            {{ err('country') }}
          </p>
        </div>

        <!-- TELEPHONE -->
        <div>
          <label class="block text-xs text-gray-500 mb-1">Telephone</label>
          <input
            class="form-input"
            v-model="form.telephone"
            @blur="validateField('telephone')"
          />
          <p v-if="err('telephone')" class="text-red-600 text-xs">
            {{ err('telephone') }}
          </p>
        </div>

        <!-- BANK INFO -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs text-gray-500 mb-1">Account Name</label>
            <input
              class="form-input"
              v-model="form.account_name"
              @blur="validateField('account_name')"
            />
            <p v-if="err('account_name')" class="text-red-600 text-xs">
              {{ err('account_name') }}
            </p>
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1">Bank Name</label>
            <input
              class="form-input"
              v-model="form.bank_name"
              @blur="validateField('bank_name')"
            />
            <p v-if="err('bank_name')" class="text-red-600 text-xs">
              {{ err('bank_name') }}
            </p>
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1">IBAN</label>
            <input
              class="form-input"
              v-model="form.iban"
              @blur="validateField('iban')"
            />
            <p v-if="err('iban')" class="text-red-600 text-xs">
              {{ err('iban') }}
            </p>
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1">Currency</label>
            <input
              class="form-input"
              v-model="form.currency"
              @blur="validateField('currency')"
            />
            <p v-if="err('currency')" class="text-red-600 text-xs">
              {{ err('currency') }}
            </p>
          </div>

          <div>
            <label class="block text-xs text-gray-500 mb-1">Swift Code</label>
            <input
              class="form-input"
              v-model="form.swift_code"
              @blur="validateField('swift_code')"
            />
            <p v-if="err('swift_code')" class="text-red-600 text-xs">
              {{ err('swift_code') }}
            </p>
          </div>
        </div>

        <!-- LOCATION -->
        <div class="space-y-2">
          <label class="block text-xs text-gray-500 mb-1">Location</label>

          <MapPicker
            :lat="form.lat"
            :lng="form.long"
            :address="form.location"
            @update:lat="form.lat = $event"
            @update:lng="form.long = $event"
            @update:address="form.location = $event"
          />

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs text-gray-500 mb-1">Latitude</label>
              <input
                class="form-input bg-gray-100 cursor-not-allowed"
                :value="form.lat"
                disabled
              />
            </div>

            <div>
              <label class="block text-xs text-gray-500 mb-1">Longitude</label>
              <input
                class="form-input bg-gray-100 cursor-not-allowed"
                :value="form.long"
                disabled
              />
            </div>
          </div>

          <p v-if="err('location')" class="text-red-600 text-xs">
            {{ err('location') }}
          </p>
        </div>

        <!-- SAVE -->
        <div class="flex justify-end">
          <button
            class="px-4 py-2 bg-green-600 text-white rounded disabled:opacity-50"
            :disabled="saving"
            @click="save"
          >
            {{ saving ? 'Saving…' : 'Save' }}
          </button>
        </div>

        <!-- SERVER MESSAGES -->
        <div v-if="successMsg" class="p-3 bg-green-100 text-green-700 border rounded">
          {{ successMsg }}
        </div>

        <div v-if="errorMsg" class="p-3 bg-red-100 text-red-700 border rounded">
          {{ errorMsg }}
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue"
import { Head } from "@inertiajs/vue3"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import axios from "axios"
import MapPicker from "@/Components/MapPicker.vue"

const loading = ref(false)
const saving = ref(false)
const successMsg = ref("")
const errorMsg = ref("")
const errors = reactive({})

const form = reactive({
  country: "",
  telephone: "",
  account_name: "",
  bank_name: "",
  iban: "",
  currency: "",
  swift_code: "",
  location: "",
  lat: "",
  long: "",
})

/* Country change */
async function onCountryChange() {
  resetMessages()
  clearErrors()

  if (!form.country) return

  try {
    const { data } = await axios.get(`/api/contact-infos?country=${form.country}`)

    const found = data.data?.find(
      (item) => item.country?.toLowerCase() === form.country.toLowerCase()
    ) || null

    if (!found) {
      clearForm()
      form.country = form.country
      errorMsg.value = "No contact info found for this country."
      return
    }

    Object.assign(form, {
      country: form.country,
      telephone: found.telephone,
      account_name: found.account_name,
      bank_name: found.bank_name,
      iban: found.iban,
      currency: found.currency,
      swift_code: found.swift_code,
      location: found.location,
      lat: found.lat,
      long: found.long,
    })
  } catch (e) {
    console.error(e)
    clearForm()
    form.country = form.country
  }
}



/* Validation */
function validateField(field) {
  if (!form[field]) {
    errors[field] = `${field.replace("_", " ")} is required`
  } else {
    delete errors[field]
  }
}

function validateAll() {
  [
    "country",
    "telephone",
    "account_name",
    "bank_name",
    "iban",
    "currency",
    "swift_code",
    "location",
  ].forEach(validateField)

  return Object.keys(errors).length === 0
}

/* Save */
async function save() {
  resetMessages()

  if (!validateAll()) {
    errorMsg.value = "Please fill in all required fields."
    return
  }

  saving.value = true

  try {
    await axios.post("/api/contact-infos/update", form)
    successMsg.value = "Saved successfully!"
  } catch (e) {
    errorMsg.value =
      e?.response?.data?.message || "Save failed. Try again."
  } finally {
    saving.value = false
  }
}

/* Helpers */
function clearForm() {
  Object.assign(form, {
    telephone: "",
    account_name: "",
    bank_name: "",
    iban: "",
    currency: "",
    swift_code: "",
    location: "",
    lat: "",
    long: "",
  })
}

function resetMessages() {
  successMsg.value = ""
  errorMsg.value = ""
}

function clearErrors() {
  Object.keys(errors).forEach(k => delete errors[k])
}

function err(field) {
  return errors[field] || ""
}
</script>

<style scoped>
.form-input {
  @apply w-full border border-gray-300 rounded-md px-3 py-2
  focus:outline-none focus:ring-2 focus:ring-blue-500;
}
</style>
