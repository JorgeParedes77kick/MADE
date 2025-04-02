<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';
import LeftMenuItemSub from './LeftMenuItemSub.vue';

const props = defineProps({
  menu: { type: [Array, Object], default: [] },
});

const activeGroup = ref(null);
</script>

<template>
  <template v-if="menu?.submenu?.length === 0">
    <Link :href="menu?.url_ref" as="div">
    <v-list-item :title="menu?.nombre" :prepend-icon="menu?.icon" link></v-list-item>
    </Link>

  </template>
  <template v-else>
    <v-list-group v-model="activeGroup" :value="menu?.id" class="my-v-list-group">
      <template v-slot:activator="{ props }">
        <v-list-item v-bind="props" :title="menu?.nombre" :prepend-icon="menu?.icon"></v-list-item>
      </template>
      <LeftMenuItemSub :subMenu="menu?.submenu"></LeftMenuItemSub>
    </v-list-group>
  </template>
</template>

<style scoped></style>
