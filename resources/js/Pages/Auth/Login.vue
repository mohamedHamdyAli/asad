<script setup>
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const localErrors = reactive({
  email: '',
  password: '',
  general: '',
})

function validateEmail(v) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)
}

function validateClient() {
  localErrors.email = ''
  localErrors.password = ''
  localErrors.general = ''

  if (!form.email) {
    localErrors.email = 'Email is required.'
  } else if (!validateEmail(form.email)) {
    localErrors.email = 'Please enter a valid email address.'
  }

  if (!form.password) {
    localErrors.password = 'Password is required.'
  } else if (form.password.length < 8) {
    localErrors.password = 'Password must be at least 8 characters.'
  }

  return !localErrors.email && !localErrors.password
}

const hasServerGeneral = computed(() => {
  const msg = form.errors.email || form.errors.password || ''
  return typeof msg === 'string' && /credentials|match/i.test(msg)
})

const submit = () => {
  form.clearErrors()
  localErrors.general = ''

  if (!validateClient()) return

  form.post(route('login'), {
    preserveScroll: true,
    onError: () => {
      const serverMsg = form.errors.email || form.errors.password
      if (serverMsg) localErrors.general = serverMsg
    },
    onFinish: () => form.reset('password'),
    headers: { Accept: 'text/html, application/xhtml+xml' }, 
  })
}
</script>

<template>
  <GuestLayout>
    <Head title="Log in" />

    <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
      {{ status }}
    </div>

    <div
      v-if="localErrors.general || hasServerGeneral"
      class="mb-4 rounded-md border border-red-300 bg-red-50 px-3 py-2 text-sm text-red-700"
    >
      {{ localErrors.general || form.errors.email || form.errors.password }}
    </div>

<form @submit.prevent="submit" novalidate class="space-y-6">
  <h2 class="text-2xl font-bold text-gray-800 text-center">Sign in</h2>
  <p class="text-center text-gray-500 text-sm">
    Enter your credentials to access your account
  </p>

  <!-- Email -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Email</label>
    <div class="relative mt-1">
      <!-- <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">📧</span> -->
      <input
        v-model="form.email"
        type="email"
        placeholder="you@example.com"
        class="pl-10 form-input"
        required
      />
    </div>
    <InputError class="mt-2" :message="localErrors.email || form.errors.email" />
  </div>

  <!-- Password -->
  <div>
    <label class="block text-sm font-medium text-gray-700">Password</label>
    <div class="relative mt-1">
      <!-- <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">🔒</span> -->
      <input
        v-model="form.password"
        type="password"
        placeholder="••••••••"
        class="pl-10 form-input"
        required
        minlength="8"
      />
    </div>
    <InputError class="mt-2" :message="localErrors.password || form.errors.password" />
  </div>

  <!-- Remember + Forgot -->
  <div class="flex items-center justify-between">
    <label class="flex items-center text-sm text-gray-600">
      <Checkbox name="remember" v-model:checked="form.remember" class="mr-2" />
      Remember me
    </label>
    <Link
      v-if="canResetPassword"
      :href="route('password.request')"
      class="text-sm text-indigo-600 hover:text-indigo-800"
    >
      Forgot password?
    </Link>
  </div>

  <!-- Submit -->
  <button
    type="submit"
    :disabled="form.processing"
    class="w-full rounded-lg bg-black text-white px-3 py-2 rounded hover:bg-gray-700 disabled:opacity-50"
  >
    {{ form.processing ? 'Signing in…' : 'Log in' }}
  </button>
</form>


  </GuestLayout>
</template>

<style scoped>
/* in your global.css (or <style> in layout) */
.form-input {
  @apply w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
         focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
         placeholder-gray-400;
}

</style>