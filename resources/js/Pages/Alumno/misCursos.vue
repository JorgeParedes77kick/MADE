<script setup>
  // import { Link, usePage } from '@inertiajs/vue3';
  import { defineProps, onMounted, ref } from 'vue';
  // import AppLayout from '@/Layouts/AppLayout.vue'; // Importamos el layout
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
    { title: 'Dia', key: 'grupo_pequeno.dia_curso', sortable: false },
    { title: 'Hora', key: 'grupo_pequeno.hora', sortable: false },
    { title: 'Status', key: 'estado_inscripcion.estado', sortable: false },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const onClickDelete = async (item) => {
    const { isConfirmed } = await Swal.fire({
      title: 'Eliminar Inscripción',
      text: `Estas seguro de eliminar tu inscripción al grupo pequeño ${item.grupo_pequeno.ciclo.curriculum.nombre}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      try {
        const response = await axios.delete(route('horario.inscripcion.delete', item.idCrypt));
        const index = props.inscripciones.findIndex((x) => x.id === item.id);
        if (response?.data?.message) {
          const { message } = response.data;
          Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
          props.inscripciones.splice(index, 1);
        }
      } catch (err) {
        console.log('err:', err);
        if (err?.response?.data?.server) {
          const { server: msg, message } = err.response.data;
          Swal.fire({ title: 'Error!', text: msg + '\n' + truncarTexto(message), icon: 'error' });
        }
      }
    }
  };
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <v-card-title> MIS GRUPOS PEQUEÑOS </v-card-title>
        <v-data-table
          :headers="headers"
          :header-props="{ class: 'bg-data-table-header' }"
          :items="inscripciones"
          class="elevation-1 rounded bg-data-table-body"
          hide-default-footer
        >
          <template v-slot:no-data> No tienes inscripciones</template>
          <template v-slot:[`item.id`]="{ index }">
            {{ index + 1 }}
          </template>
          <template v-slot:[`item.estado_inscripcion.estado`]="{ item }">
            <v-chip v-if="item.estado_inscripcion.id == 1" color="primary" variant="flat">
              {{ item.estado_inscripcion.estado }}</v-chip
            >
            <v-chip v-else-if="item.estado_inscripcion.id == 2" color="success" variant="flat">
              {{ item.estado_inscripcion.estado }}</v-chip
            >
            <v-chip v-else-if="[3, 4].includes(item.estado_inscripcion.id)" variant="flat">
              {{ item.estado_inscripcion.estado }}</v-chip
            >
            <v-chip v-else color="info" variant="flat">
              {{ item.estado_inscripcion.estado }}
            </v-chip>
          </template>
          <template v-slot:[`item.acciones`]="{ item }">
            <div class="d-flex inline-flex ga-2">
              <v-btn color="error" small @click="onClickDelete(item)">Darse de Baja</v-btn>
            </div>
          </template>
        </v-data-table>
      </v-card>
    </v-container>
  </MainLayout>
</template>
<style></style>
