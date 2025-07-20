<template>
  <AuthenticatedLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold text-gray-800">Manage Entity: {{ entity.name }}</h2>
          <p class="text-sm text-gray-500">Code: {{ entity.code }} | Status: {{ entity.active ? 'Active' : 'Inactive' }}</p>
        </div>
      </div>

      <!-- Tabs -->
      <div>
        <div class="border-b border-gray-200 mb-4">
          <nav class="-mb-px flex space-x-6 text-sm font-medium">
            <button
              v-for="tab in tabs"
              :key="tab"
              @click="activeTab = tab"
              :class="[
                'pb-2 px-1 border-b-2',
                activeTab === tab
                  ? 'border-primary-600 text-primary-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              {{ tab }}
            </button>
          </nav>
        </div>

        <!-- Tab Panels -->
        <div v-if="activeTab === 'Team'">
          <EntityTeam :entity="entity" :members="members" />
        </div>

        <div v-if="activeTab === 'Offerings'">
          <EntityOfferings :entity="entity" :offerings="offerings" />
        </div>

        <div v-if="activeTab === 'Requests'">
          <EntityRequests :entity="entity" />
        </div>
        
        <div v-if="activeTab === 'Analytics'">
          <EntityAnalytics :entity="entity" />
        </div>

        <div v-if="activeTab === 'Settings'">
          <EntitySettings :entity="entity" />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  entity: Object,
  members: Array,
  offerings: Array
})

const tabs = ['Team', 'Offerings', 'Requests', 'Analytics', 'Settings']
const activeTab = ref('Team')

import EntityTeam from '@/Components/Entity/EntityTeam.vue'
import EntityOfferings from '@/Components/Entity/EntityOfferings.vue'
import EntityRequests from '@/Components/Entity/EntityRequests.vue'
import EntitySettings from '@/Components/Entity/EntitySettings.vue'
import EntityAnalytics from '@/Components/Entity/EntityAnalytics.vue'
</script>
