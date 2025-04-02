<script setup>
import { defineProps, inject, onMounted, ref } from 'vue';

const validate = inject('validation');

const props = defineProps({});
const loading = ref(false);
const isDisabled = ref(false);

onMounted(() => {});
</script>

<template></template>
<style></style>
