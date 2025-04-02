<script setup>
import { defineProps, onMounted, ref } from 'vue';
import MainLayout from '../../components/Layout.vue';
import ModalAsistencia from './modalAsistencia.vue';

// const validate = inject('validation');

const props = defineProps({
  grupopequeno: { type: Object, default: {} },
  inscripciones: { type: Array, default: [] },
  estados: { type: Array, default: [] },
});

const loading = ref(false);
const isDisabled = ref(false);
const openShow = ref(false);
const userSelect = ref(null);

onMounted(() => {
  console.log(props);
});

const headers = [
  { title: '#', key: 'id' },
  { title: 'Status', key: 'estado_inscripcion.estado' },
  // { title: 'Día', key: 'libro' },
  // { title: 'Hora', key: 'libro' },
  { title: 'DNI', key: 'usuario.persona.dni' },
  { title: 'Nombre', key: 'usuario.nombreCompleto' },
  { title: 'Correo', key: 'usuario.email' },
  { title: 'Teléfono', key: 'usuario.persona.telefono' },
  { title: 'Ubicación', key: 'usuario.persona.region.pais.nombre', sortable: false },
  { title: 'Opciones', key: 'opciones', sortable: false },
];

const onClickVer = (inscripcion) => {
  userSelect.value = {
    id: inscripcion.id,
    ...inscripcion.usuario,
  };
  openShow.value = true;
};
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <!-- <template v-if="status">
        <v-alert type="success" :text="status"></v-alert>
      </template> -->
      <v-card color="background" class="shadow-md px-4 py-2">
        <v-card-title class="text-center"
          >ALUMNOS {{ grupopequeno.ciclo.curriculum.nombre }} {{ grupopequeno.ciclo.nombre }} -
          {{ grupopequeno.temporada.prefijo }}
        </v-card-title>
        <v-divider></v-divider>
        <v-row>
          <v-col cols="12" class="text-subtitle-1">
            <b>Horario: {{ grupopequeno.dia_curso }} {{ grupopequeno.hora }}</b>
          </v-col>
          <v-col cols="12" class="text-subtitle-1">
            <ModalAsistencia :idGrupo="grupopequeno.id" />
          </v-col>
          <v-col cols="12">
            <v-data-table
              :headers="headers"
              :items="inscripciones"
              :items-per-page="20"
              class="elevation-1 rounded"
            >
              <template v-slot:no-data>Aún no tienes alumnos inscriptos</template>
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
              <template v-slot:[`item.opciones`]="{ item }">
                <v-btn color="info" @click="onClickVer(item)">Ver</v-btn>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
    <v-dialog v-model="openShow" width="auto">
      <v-card :title="`#${userSelect?.id} - ${userSelect?.nombreCompleto}`">
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="6" class="py-1"><strong>Fecha de nacimiento: </strong></v-col>
            <v-col cols="12" sm="6" class="py-1">{{ userSelect?.persona?.fecha_nacimiento }}</v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6" class="py-1"><strong>Email: </strong></v-col>
            <v-col cols="12" sm="6" class="py-1">{{ userSelect?.email }}</v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6" class="py-1"><strong>Teléfono: </strong></v-col>
            <v-col cols="12" sm="6" class="py-1">{{ userSelect?.persona?.telefono }}</v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6" class="py-1"><strong>Ubicación: </strong></v-col>
            <v-col cols="12" sm="6" class="py-1">{{
              userSelect?.persona?.region?.pais?.nombre
            }}</v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6" class="py-1"><strong>Nacionalidad: </strong></v-col>
            <v-col cols="12" sm="6" class="py-1">{{
              userSelect?.persona?.nacionalidad?.nombre
            }}</v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6" class="py-1"><strong>Estado Civil: </strong></v-col>
            <v-col cols="12" sm="6" class="py-1">{{
              userSelect?.persona?.estado_civil?.estado
            }}</v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6" class="py-1"><strong>Ocupación: </strong></v-col>
            <v-col cols="12" sm="6" class="py-1">{{ userSelect?.persona?.ocupacion }}</v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="openShow = false" color="secondary" small>Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </MainLayout>
</template>
<style></style>
