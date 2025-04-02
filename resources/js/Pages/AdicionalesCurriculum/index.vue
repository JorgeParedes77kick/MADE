<script setup>
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import isBetween from 'dayjs/plugin/isBetween';
import { defineProps, onMounted } from 'vue';

import MainLayout from '../../components/Layout';
dayjs.extend(isBetween);

const props = defineProps({
  adicionales: Array,
  curriculums: Array,
});
onMounted(() => {
  console.log(props);
});

const headers = [
  { title: '', key: 'data-table-expand' },
  { title: 'Nombre', key: 'nombre' },
  { title: 'Con Adicional', key: 'adicionales.length' },
  { title: 'Acciones', key: 'acciones', sortable: false },
];
</script>
<template>
  <MainLayout>
    <v-container>
      <v-card color="background" class="px-4 py-2">
        <v-card-title> ADICIONALES CURRICULUM </v-card-title>
        <div>
          <!-- <v-row>
            <v-col class="d-flex justify-end">
              <Link :href="route('adicionales-curriculum.create')">
              <v-btn :to="{ name: 'adicionales-curriculum.create' }" color="success" class="ms-auto">
                Crear Nuevas Adicionales
              </v-btn>
              </Link>
            </v-col>
          </v-row> -->
          <v-row>
            <v-col>
              <v-data-table :headers="headers" :items="curriculums" :items-per-page="15" class="elevation-1 rounded"
                show-expand>
                <template v-slot:no-data>Información no encontrada</template>

                <template v-slot:expanded-row="{ item }">
                  <tr v-for="adi in item.adicionales" :key="adi.id">
                    <td :colspan="2"></td>
                    <td :colspan="1">{{ adi.nombre }}</td>
                    <td :colspan="1">{{ adi.type_value }}</td>
                  </tr>
                </template>
                <template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <Link :href="route('adicionales-curriculum.show', item)">
                    <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link>
                    <Link :href="route('adicionales-curriculum.edit', item)">
                    <v-btn :to="{ name: 'adicionales-curriculum.edit', params: { id: item.idCrypt } }" color="secondary"
                      small>
                      Editar
                    </v-btn>
                    </Link>
                    <!-- <v-btn color="error" small @click="onClickDelete(item)">Eliminar
                    </v-btn> -->
                  </div>
                </template>
              </v-data-table>
            </v-col>
          </v-row>
        </div>
      </v-card>
    </v-container>
  </MainLayout>
</template>
