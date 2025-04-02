<script setup>
// import { Link, usePage } from '@inertiajs/vue3';
import { defineProps, onMounted, ref } from 'vue';
import ButtonBack from '../../components/ButtonBack.vue';
import MainLayout from '../../components/Layout.vue';

const props = defineProps({
  recursos: { type: Array, default: [] },
  ciclo: { type: Object, default: {} },
});
const loading = ref(false);
const isDisabled = ref(false);

onMounted(() => {
  console.log(props.recursos);
});

const headers = [
  { title: 'N° DE RECURSO', key: 'clase', sortable: false, align: 'center' },
  { title: 'LINK LECTURA', key: 'link_lectura', sortable: false, align: 'center' },
  { title: 'LINK ESCRITURA', key: 'link_escritura', sortable: false, align: 'center' },
];
</script>

<template>
  <main-layout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <v-card-title>
          <ButtonBack /> RECURSOS - {{ ciclo.curriculum.nombre }} {{ ciclo.nombre }}
        </v-card-title>

        <v-data-table :headers="headers" :header-props="{ class: 'bg-data-table-header' }" :items="recursos"
          class="elevation-1 rounded bg-data-table-body" hide-default-footer>
          <template v-slot:no-data>No tienes Inscripciones con Recursos disponibles </template>
          <template v-slot:[`item.link_lectura`]="{ item }">
            <a v-if="item.link_lectura" :href="item.link_lectura" target="_blank">
              <v-chip color="info" variant="flat"> Ver </v-chip>
            </a>
            <i v-else>No disponible</i>
          </template>
          <template v-slot:[`item.link_escritura`]="{ item }">
            <a v-if="item.link_escritura" :href="item.link_escritura" target="_blank">
              <v-chip color="info" variant="flat"> Ver </v-chip>
            </a>
            <i v-else>No disponible</i>
          </template>
        </v-data-table>
      </v-card>
    </v-container>
  </main-layout>
</template>
<style></style>
