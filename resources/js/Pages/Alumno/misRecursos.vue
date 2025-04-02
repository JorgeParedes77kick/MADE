<script setup>
import { Link } from '@inertiajs/vue3';
import { defineProps, onMounted, ref } from 'vue';
import MainLayout from '../../components/Layout.vue';
const props = defineProps({
  inscripciones: { type: Array, default: [] },
});
const loading = ref(false);
const isDisabled = ref(false);

onMounted(() => {
  console.log(props.inscripciones);
});

const headers = [
  { title: '#', key: 'id', sortable: false },
  { title: 'Temporada', key: 'grupo_pequeno.temporada.prefijo', sortable: false },
  { title: 'Grupo Pequeño', key: 'grupo_pequeno.ciclo.curriculum.nombre', sortable: false },
  { title: 'Ciclo', key: 'grupo_pequeno.ciclo.nombre', sortable: false },
  { title: 'Acciones', key: 'acciones', sortable: false },
];
</script>

<template>
  <main-layout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <v-card-title> MIS RECURSOS </v-card-title>
        <v-data-table :headers="headers" :header-props="{ class: 'bg-data-table-header' }" :items="inscripciones"
          class="elevation-1 rounded bg-data-table-body" hide-default-footer>
          <template v-slot:no-data>No tienes Inscripciones con Recursos disponibles </template>

          <template v-slot:[`item.id`]="{ index }">
            {{ index + 1 }}
          </template>
          <template v-slot:[`item.acciones`]="{ item }">
            <div class="d-flex inline-flex ga-2">
              <Link :href="route('mis-recursos.show', item)">
              <v-btn as="v-btn" color="info" small> Ver Recursos</v-btn>
              </Link>
            </div>
          </template>
        </v-data-table>
      </v-card>
    </v-container>
  </main-layout>
</template>
<style></style>
