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

    <form @submit.prevent="submit" novalidate>
      <div>
        <InputLabel for="email" value="Email" />
        <TextInput
          id="email"
          type="email"
          class="mt-1 block w-full"
          v-model="form.email"
          required
          autocomplete="username"
          @blur="validateClient"
          :aria-invalid="!!(localErrors.email || form.errors.email)"
          :aria-describedby="'email-error'"
        />
        <InputError id="email-error" class="mt-2" :message="localErrors.email || form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Password" />
        <TextInput
          id="password"
          type="password"
          class="mt-1 block w-full"
          v-model="form.password"
          required
          autocomplete="current-password"
          minlength="8"
          @blur="validateClient"
          :aria-invalid="!!(localErrors.password || form.errors.password)"
          :aria-describedby="'password-error'"
        />
        <InputError id="password-error" class="mt-2" :message="localErrors.password || form.errors.password" />
      </div>

      <div class="mt-4 block">
        <label class="flex items-center">
          <Checkbox name="remember" v-model:checked="form.remember" />
          <span class="ms-2 text-sm text-gray-600">Remember me</span>
        </label>
      </div>

      <div class="mt-4 flex items-center justify-end">
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
          Forgot your password?
        </Link>

        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Log in
        </PrimaryButton>
      </div>
    </form>
  </GuestLayout>
</template>
