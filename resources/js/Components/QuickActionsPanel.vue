<template>
    <div class="bg-white shadow rounded-lg p-6">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <ActionCard icon="mdi:file-plus-outline" label="Add New Tender" />
        <ActionCard icon="mdi:file-document-edit-outline" label="Manage Contracts" />
        <ActionCard icon="mdi:chart-box-outline" label="View Full Reports" />
        <ActionCard icon="mdi:currency-usd" label="Payout Control" />
        <ActionCard icon="mdi:clipboard-list-outline" label="View All Requests" />
      </div>
    </div>
  </template>
  
  <script setup>
  import ActionCard from './ActionCard.vue'
  </script>
  