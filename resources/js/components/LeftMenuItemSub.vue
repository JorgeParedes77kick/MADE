<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps, ref } from 'vue';

const props = defineProps({
  subMenu: { type: [Array, Object], default: [] },

});

const activeGroup = ref(null);
</script>

<template>
  <template v-for="(subM) in subMenu" :key="subM?.id + 'group'">
    <template v-if="subM?.submenu?.length === 0">
      <Link :href="subM?.url_ref" as="div">
      <v-list-item :title="subM?.nombre" :prepend-icon="subM?.icon" link></v-list-item>
      </Link>

    </template>
    <template v-else>
      <v-list-group v-model="activeGroup" :value="subM?.id + 'subItem'" class="my-v-list-group">
        <template v-slot:activator="{ props }">
          <v-list-item v-bind="props" :title="subM?.nombre" href="#" :prepend-icon="subM?.icon"></v-list-item>
        </template>
        <LeftMenuItemSub :subMenu="subM?.submenu"></LeftMenuItemSub>
      </v-list-group>
    </template>
  </template>
</template>

<style scoped></style>
