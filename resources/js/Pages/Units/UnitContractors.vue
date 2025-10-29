<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Assign Contractors & Consultants</h2>
        <a :href="backToUnits" class="px-3 py-1 border rounded">← Back to Units</a>
      </div>
      <UnitNav :unit-id="Number(unitId)" :cols="2" />

      <!-- Tabs -->
      <div class="flex border-b">
        <button
          v-for="t in tabs"
          :key="t"
          @click="activeTab = t"
          class="px-4 py-2 -mb-px border-b-2 font-medium"
          :class="activeTab === t
            ? 'border-blue-600 text-blue-600'
            : 'border-transparent text-gray-500 hover:text-gray-700'"
        >
          {{ t }}
        </button>
      </div>

      <!-- Contractors Tab -->
      <div v-if="activeTab === 'Contractors'" class="bg-white p-5 rounded-xl shadow-sm">
        <AssignmentsList
          :items="contractorsAssigned"
          :allOptions="contractorsAll"
          type="contractor"
          :unit-id="unitId"
          @refresh="fetchContractorsData"
        />
      </div>

      <!-- Consultants Tab -->
      <div v-else class="bg-white p-5 rounded-xl shadow-sm">
        <AssignmentsList
          :items="consultantsAssigned"
          :allOptions="consultantsAll"
          type="consultant"
          :unit-id="unitId"
          @refresh="fetchConsultantsData"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import UnitNav from '@/Components/UnitNav.vue'
import { ref, onMounted, computed } from 'vue'
import AssignmentsList from './Partials/AssignmentsList.vue'
import { UnitContractorsApi } from '@/Services/unitContractors'
import { UnitConsultantsApi } from '@/Services/unitConsultants'
import { ContractorsApi } from '@/Services/contractors'
import { ConsultantsApi } from '@/Services/consultants'

const props = defineProps({
  unitId: { type: [Number, String], required: true }
})

const backToUnits = computed(() => '/units-management')

const tabs = ['Contractors', 'Consultants']
const activeTab = ref('Contractors')

const contractorsAssigned = ref([])
const contractorsAll = ref([])

const consultantsAssigned = ref([])
const consultantsAll = ref([])

async function fetchContractorsData() {
  contractorsAssigned.value = await UnitContractorsApi.list(props.unitId)
  contractorsAll.value = await ContractorsApi.list()
}

async function fetchConsultantsData() {
  consultantsAssigned.value = await UnitConsultantsApi.list(props.unitId)
  consultantsAll.value = await ConsultantsApi.list()
}

onMounted(async () => {
  await fetchContractorsData()
  await fetchConsultantsData()
})
</script>
