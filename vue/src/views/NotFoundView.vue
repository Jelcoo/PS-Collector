<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faHouse } from '@fortawesome/free-solid-svg-icons'
</script>

<template>
  <div class="text-center">
    <h1 class="text-9xl font-extrabold">404</h1>
    <p class="text-2xl font-semibold text-gray-400 mt-4">
      Oops! The page you're looking for doesn't exist.
    </p>
    <RouterLink
      to="/"
      class="bg-cyan-600 mt-4 p-2 inline-block rounded-lg text-white font-semibold hover:bg-cyan-700 border-2 border-cyan-600"
      ><FontAwesomeIcon :icon="faHouse" class="mr-2" />Go back to the homepage</RouterLink
    >
  </div>
</template>
