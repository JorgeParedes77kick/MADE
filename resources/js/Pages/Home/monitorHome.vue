<script setup>
  import { defineProps, inject, ref } from 'vue';
  import MainLayout from '../../components/Layout.vue';
  import ModalAsistencia from '../Lider/modalAsistencia.vue';

  const validate = inject('$validation');

  const props = defineProps({
    grupospequenos: { type: Array, default: [] },
  });
  const loading = ref(false);
  const isDisabled = ref(false);

  //   const curriculum = ref(null);
  //   const ciclos = ref([]);
  //   const ciclo = ref(null);

  //   onMounted(() => {
  //     const { curriculums } = props;
  //     if (curriculums.length === 1) {
  //       curriculum.value = curriculums[0];
  //       ciclos.value = curriculums[0]?.ciclos || [];
  //     }
  //   });

  //   const onClickCurriculum = (item) => {
  //     curriculum.value = item;
  //     ciclos.value = item?.ciclos || [];
  //   };
  //   const onChangeCiclo = (idCiclo) => {
  //     const cicloSelect = ciclos.value.find((x) => x.id === idCiclo);
  //     if (cicloSelect) ciclo.value = cicloSelect;
  //     else ciclo.value = null;
  //   };

  const getAsistencia = () => {};
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2" title="BackOffices Monitor">
        <div v-if="grupospequenos.length === 0" class="text-subtitle-2">
          No hay grupo pequeños asignados a este monitor
        </div>

        <v-row v-else>
          <v-col cols="12"> Seleccione un grupo pequeno: </v-col>
          <v-col
            v-for="grupospequeno in grupospequenos"
            md="4"
            sm="6"
            class="text-center"
            :key="grupospequeno.idCrypt"
          >
            <p>
              <strong>
                {{ grupospequeno.ciclo.curriculum.nombre }},
                {{ grupospequeno.ciclo.nombre }}
              </strong>
            </p>
            <p>
              <strong>{{ grupospequeno.dia_curso }}, {{ grupospequeno.hora }}</strong>
            </p>
            <ModalAsistencia :edit="false" colorBtn="info" :idGrupo="grupospequeno.id">
              <span v-for="lider in grupospequeno.lideres" :key="lider.id">
                {{ lider.nombreCompleto }}
              </span>
            </ModalAsistencia>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </MainLayout>
</template>
<style></style>
