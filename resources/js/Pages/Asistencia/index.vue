<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { defineProps, onMounted, ref } from 'vue';
import MainLayout from '../../components/Layout.vue';

// const { flash } = usePage().props.value;
const pageProps = usePage().props.value;

const props = defineProps({
  temporadas: { type: Array, default: [] },
  curriculums: { type: Array, default: [] },
  status: Boolean,
});

const loading = ref(false);
const isDisabled = ref(false);

onMounted(() => {
  console.log(props.curriculums);
});
</script>
<template>
  <main-layout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <template v-slot:title>
          <div class="text-center">
            <template v-if="temporadas.length == 0">
              >No existe ninguna temporada activa en este momento!</template
            >
            <template v-else-if="temporadas.curriculums == 0">
              No existe ningun curriculum activo en este momento!
            </template>
            <template v-else> Seleccione el curriculum que quiere revisar la asistencia! </template>
          </div>
        </template>

        <template v-if="status">
          <v-alert type="success" :text="status"></v-alert>
        </template>

        <template v-if="curriculums.length > 0">
          <v-row class="mt-3">
            <v-col
              cols="6"
              sm="4"
              md="3"
              xl="2"
              class="mb-1"
              v-for="curriculum in curriculums"
              :key="curriculum.id"
            >
              <Link
                :href="
                  route('asistencias.show', {
                    asistencia: curriculum.idCrypt,
                  })
                "
              >
                <v-card class="hover-card">
                  <v-img
                    :src="`/storage/img/curriculums/${curriculum.imagen}`"
                    :alt="curriculum.nombre"
                  >
                    <template v-slot:error>
                      {{ curriculum.nombre }}
                    </template>
                  </v-img>
                </v-card>
              </Link>
            </v-col>
          </v-row>
        </template>
      </v-card>
    </v-container>
  </main-layout>
</template>
