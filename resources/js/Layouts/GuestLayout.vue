<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
  <div class="flex min-h-screen bg-gradient-to-br from-dash-yellow via-white to-yellow-10">
    <!-- Left Section: Branding / Illustration -->
    <div class="hidden lg:flex w-1/2 items-center justify-center p-10 relative overflow-hidden">
      <!-- subtle dots background -->
      <!-- <div
        class="absolute inset-0 bg-[url('https://www.toptal.com/designers/subtlepatterns/uploads/p6.png')] opacity-10"
      ></div> -->

      <div class="relative z-10 text-center max-w-md">
        <!-- Logo -->
        <Link href="/" class="inline-block">
          <ApplicationLogo class="h-24 w-24 text-indigo-600 mx-auto" />
        </Link>

        <h2 class="mt-6 text-3xl font-extrabold text-gray-800">Welcome Back</h2>
        <p class="mt-2 text-gray-600">
          your app slogan here
        </p>

        <!-- External Illustration -->
        <!-- <img
          src="https://undraw.co/api/illustrations/secure_login_pdn4.svg"
          alt="Login Illustration"
          class="mt-8 w-full max-w-sm mx-auto"
        /> -->
      </div>
    </div>

    <!-- Right Section: Login Form -->
    <div class="flex flex-1 items-center justify-center px-6 sm:px-12">
      <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-lg">
        <slot />
      </div>
    </div>
  </div>
</template>
