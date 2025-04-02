<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps } from 'vue';

defineProps({
  href: String,
});
// Función para manejar el retroceso
const goBack = () => {
  window.history.back();
};

</script>

<template>
  <Link v-if="href" :href="href">
  <v-btn icon="mdi-arrow-left" color="light" size="small" />
  </Link>

  <v-btn v-else icon="mdi-arrow-left" color="light" size="small" @click="goBack" />


</template>
<!-- <div>
  <Link v-if="href" :href="href">
  <v-btn icon="mdi-arrow-left" color="light" size="small" />
  </Link>

  <v-btn v-else icon="mdi-arrow-left" color="light" size="small" @click="window.history.back()" />
</div> -->
