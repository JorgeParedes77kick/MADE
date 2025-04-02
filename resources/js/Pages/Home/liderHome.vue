<script setup>
  import { defineProps, inject, onMounted, ref } from 'vue';
  import MainLayout from '../../components/Layout.vue';

  const validate = inject('$validation');

  const props = defineProps({});
  const loading = ref(false);
  const isDisabled = ref(false);

  onMounted(() => {});
</script>

<template>
  <MainLayout>
    <v-container fluid></v-container>
  </MainLayout>
</template>
<style></style>
