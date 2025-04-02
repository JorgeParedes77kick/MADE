<script setup>
  import { defineProps, inject, onMounted, ref } from 'vue';
  import MainLayout from '../../components/Layout.vue';
  import ModalAsistencia from '../Lider/modalAsistencia.vue';

  const validate = inject('$validation');

  const props = defineProps({
    curriculums: { type: Array, default: [] },
  });
  const loading = ref(false);
  const isDisabled = ref(false);

  const curriculum = ref(null);
  const ciclos = ref([]);
  const ciclo = ref(null);

  onMounted(() => {
    const { curriculums } = props;
    if (curriculums.length === 1) {
      curriculum.value = curriculums[0];
      ciclos.value = curriculums[0]?.ciclos || [];
    }
  });

  const onClickCurriculum = (item) => {
    curriculum.value = item;
    ciclos.value = item?.ciclos || [];
  };
  const onChangeCiclo = (idCiclo) => {
    const cicloSelect = ciclos.value.find((x) => x.id === idCiclo);
    if (cicloSelect) ciclo.value = cicloSelect;
    else ciclo.value = null;
  };

  const getAsistencia = () => {};
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2" title="BackOffices Coordinador">
        <div v-if="curriculums.length === 0" class="text-subtitle-2">
          No hay curriculum asignados a este coordinador
        </div>
        <template v-if="curriculums.length > 1">
          <v-row class="mt-3">
            <v-col
              cols="6"
              sm="4"
              md="3"
              xl="2"
              class="mb-1 pa-2"
              v-for="curriculum in curriculums"
              :key="curriculum.id"
            >
              <v-card class="hover-card" :onclick="() => onClickCurriculum(curriculum)">
                <v-img
                  :src="`/storage/img/curriculums/${curriculum.imagen}`"
                  :alt="curriculum.nombre"
                >
                  <template v-slot:error>
                    {{ curriculum.nombre }}
                  </template>
                </v-img>
              </v-card>
            </v-col>
          </v-row>
        </template>
        <template v-if="curriculum">
          <v-row>
            <v-col cols="12">
              {{ curriculum.nombre }}
            </v-col>

            <v-col cols="12" lg="3" md="4" sm="6">
              <v-select
                id="ciclo"
                name="ciclo"
                label="Selecione un Ciclo"
                v-model="ciclo"
                :items="ciclos"
                item-title="nombre"
                item-value="id"
                autocomplete="off"
                @update:modelValue="onChangeCiclo"
                clearable
              />
            </v-col>
          </v-row>
          <v-row v-if="ciclo">
            <v-col cols="12"> Seleccione un Líder: </v-col>
            <v-col
              v-for="grupospequenos in ciclo.grupospequenos"
              lg="3"
              md="4"
              sm="6"
              class="text-center"
              :key="grupospequenos.idCrypt"
            >
              <span>
                <strong>{{ grupospequenos.dia_curso }}, {{ grupospequenos.hora }}</strong>
              </span>

              <ModalAsistencia :edit="false" colorBtn="info" :idGrupo="grupospequenos.id">
                <span v-for="lider in grupospequenos.lideres" :key="lider.id">
                  {{ lider.nombreCompleto }}
                </span>
              </ModalAsistencia>
            </v-col>
          </v-row>
        </template>
      </v-card></v-container
    >
  </MainLayout>
</template>
<style></style>
