<script setup>
  import { defineProps, onMounted, ref } from 'vue';
  import MainLayout from '../../components/Layout.vue';

  // const validate = inject('validation');

  const props = defineProps({
    curriculumAsistencias: { type: Array, default: [] },
    ciclosAsistencias: { type: Array, default: [] },
    gruposAsistencias: { type: Array, default: [] },
    semanas: { type: Array, default: [] },
    curriculum: { type: Object, default: {} },
    estados: { type: Array, default: [] },
  });

  const headers = ref({
    curriculum: [],
    ciclo: [],
    grupo: [],
  });

  const alumnos = ref([]);

  const loading = ref(false);
  const isDisabled = ref(false);

  const generateSemanaHeaders = (semanas) => {
    return semanas.map((_, i) => ({
      title: `Semana ${i + 1}`,
      key: `semana_${i + 1}`,
      sortable: false,
      minWidth: '1rem',
      align: 'center',
    }));
  };

  onMounted(() => {
    console.log(props);
    const { semanas } = props;
    const semanaHeaders = generateSemanaHeaders(semanas);

    const baseHeader = { title: 'Total Alumnos', key: 'total_inscritos', maxWidth: '3rem' };
    headers.value.curriculum = [baseHeader, ...semanaHeaders];
    headers.value.ciclo = [{ title: 'Ciclo', key: 'nombre_ciclo' }, baseHeader, ...semanaHeaders];
    headers.value.grupo = [
      { title: 'Lideres', key: 'lideres' },
      { title: 'Ciclo', key: 'nombre_ciclo', maxWidth: '5rem' },
      baseHeader,
      ...semanaHeaders,
    ];
  });
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <template v-slot:title>
          <div class="text-center">Asistencias {{ curriculum?.nombre?.toUpperCase() }}</div>
        </template>
        <template v-slot:subtitle>
          <div class="text-center font-weight-medium">
            {{
              estados
                .map((x) => {
                  return `${x.estado}: ${x.key}`;
                })
                .join('&emsp;')
            }}
          </div>
        </template>
        <v-row
          ><v-col>
            <text class="text-h6">Asistencias por Curriculum</text>
            <!-- <hr /> -->
            <v-data-table
              :headers="headers.curriculum"
              :items="curriculumAsistencias"
              class="elevation-1 rounded"
              :items-per-page="5"
              :hide-default-footer="curriculumAsistencias.length <= 5"
            >
              <template v-slot:no-data> No hay datos disponibles </template>
              <template
                v-for="n in semanas.length"
                :key="`semana_${n}`"
                v-slot:[`item.semana_${n}`]="{ item, index }"
              >
                <p v-for="estado in estados" :key="estado.id">
                  {{ estado.key }} - {{ item.semanas[n - 1]?.[estado.estado] }}
                </p>
              </template>
            </v-data-table>
          </v-col></v-row
        >
        <v-row
          ><v-col>
            <text class="text-h6">Asistencias por Ciclo</text>
            <!-- <hr /> -->
            <v-data-table
              :headers="headers.ciclo"
              :items="ciclosAsistencias"
              class="elevation-1 rounded"
              :items-per-page="5"
              :hide-default-footer="ciclosAsistencias.length <= 5"
            >
              <template v-slot:no-data> No hay datos disponibles </template>
              <template
                v-for="n in semanas.length"
                :key="`semana_${n}`"
                v-slot:[`item.semana_${n}`]="{ item, index }"
              >
                <p v-for="estado in estados" :key="estado.id">
                  {{ estado.key }} - {{ item.semanas[n - 1]?.[estado.estado] }}
                </p>
              </template>
            </v-data-table>
          </v-col></v-row
        >
        <v-row
          ><v-col>
            <text class="text-h6">Asistencias por Grupo</text>
            <!-- <hr /> -->
            <v-data-table
              :headers="headers.grupo"
              :items="gruposAsistencias"
              class="elevation-1 rounded"
              :items-per-page="5"
              :hide-default-footer="gruposAsistencias.length <= 5"
            >
              <template v-slot:no-data> No hay datos disponibles </template>
              <template v-slot:[`item.lideres`]="{ item }">
                <p v-for="lider in item.lideres" :key="lider.id" class="text-capitalize">
                  {{ lider?.nombreCompleto?.toLowerCase() }}
                </p>
              </template>
              <template
                v-for="n in semanas.length"
                :key="`semana_${n}`"
                v-slot:[`item.semana_${n}`]="{ item, index }"
              >
                <p v-for="estado in estados" :key="estado.id">
                  {{ estado.key }} - {{ item.semanas[n - 1]?.[estado.estado] }}
                </p>
              </template>
            </v-data-table>
          </v-col></v-row
        >
      </v-card>
    </v-container>
  </MainLayout>
</template>

<style></style>
