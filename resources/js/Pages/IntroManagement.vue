<template>
    <AuthenticatedLayout>
      <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Intro Management</h2>
  
        <!-- Create New Intro -->
        <div class="bg-white p-4 rounded shadow mb-6">
          <h3 class="text-lg font-bold mb-4">Add New Intro</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input v-model="form.name" type="text" placeholder="Title" class="form-input" />
            <input v-model="form.description" type="text" placeholder="Description" class="form-input" />
            <input v-model="form.order" type="number" placeholder="Order" class="form-input" />
            <select v-model="form.status" class="form-input">
              <option value="active">Active</option>
              <option value="in_active">Inactive</option>
            </select>
            <input type="file" @change="handleFileChange" class="form-input" />
          </div>
          <button @click="createIntro" class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Add Intro</button>
        </div>
  
        <!-- List All Intros -->
        <div class="bg-white p-4 rounded shadow">
          <h3 class="text-lg font-bold mb-4">Intro Screens</h3>
          <div v-for="item in intros" :key="item.id" class="flex justify-between items-center border-b py-2">
            <div>
              <div class="font-semibold">{{ item.name }}</div>
              <div class="text-sm text-gray-600">{{ item.description }}</div>
              <div class="text-sm">Order: {{ item.order }} | Status: {{ item.status }}</div>
            </div>
            <div class="flex gap-2 items-center">
              <img :src="getImagePath(item.image)" class="w-16 h-10 object-cover rounded" v-if="item.image" />
              <button @click="editIntro(item.id)" class="text-blue-600 hover:underline">Edit</button>
              <button @click="deleteIntro(item.id)" class="text-red-600 hover:underline">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </template>
  
  <script setup>
  import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
  import { ref, onMounted } from 'vue'
  import axios from 'axios'
  
  const intros = ref([])
  const form = ref({
    name: '',
    description: '',
    image: null,
    order: '',
    status: 'active',
  })
  
  const fetchIntros = async () => {
    try {
      const res = await axios.get('/intro')
      intros.value = res.data.data
    } catch (err) {
      console.error('Failed to load intros:', err)
    }
  }
  
  const createIntro = async () => {
    const payload = new FormData()
    payload.append('name', form.value.name)
    payload.append('description', form.value.description)
    payload.append('image', form.value.image)
    payload.append('order', form.value.order)
    payload.append('status', form.value.status)
  
    try {
      const res = await axios.post('/intro/create', payload)
      alert(res.data.message)
      fetchIntros()
      form.value = { name: '', description: '', image: null, order: '', status: 'active' }
    } catch (err) {
      console.error('Create failed:', err)
    }
  }
  
  const deleteIntro = async (id) => {
    if (!confirm('Are you sure you want to delete this intro?')) return
    try {
      const res = await axios.delete(`/intro/delete/${id}`)
      alert(res.data.message)
      fetchIntros()
    } catch (err) {
      console.error('Delete failed:', err)
    }
  }
  
  const editIntro = async (id) => {
    try {
      const res = await axios.get(`/intro/edit/${id}`)
      const data = res.data.data
      form.value = {
        name: data.name,
        description: data.description,
        order: data.order,
        status: data.status,
        image: null,
      }
    } catch (err) {
      console.error('Edit fetch failed:', err)
    }
  }
  
  const handleFileChange = (e) => {
    form.value.image = e.target.files[0]
  }
  
  const getImagePath = (path) => {
    return path ? `/storage/${path}` : ''
  }
  
  onMounted(fetchIntros)
  </script>
  
  <style scoped>
  .form-input {
    @apply w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500;
  }
  </style>
  