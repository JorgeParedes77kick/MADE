<script setup>
  import { defineProps, onMounted, ref } from 'vue';
  import MainLayout from '../../components/Layout.vue';
  import { Truthty } from '../../utils/isType';

  const props = defineProps({
    temporada: { type: Object, default: {} },
    data: { type: Object, default: {} },
  });
  const loading = ref(false);
  const isDisabled = ref(false);
  const chips = ref({
    cursos: { color: '', icon: '', value: '' },
    inscripciones: { color: '', icon: '', value: '' },
    usuariosNuevos: { color: '', icon: '', value: '' },
    usuariosInscritos: { color: '', icon: '', value: '' },
  });

  onMounted(() => {
    console.log(props.data);

    const { cursos, inscripciones, usuariosInscritos, usuariosNuevos } = props.data;
    if (cursos?.percentage) {
      chips.value.cursos = getBadgePercentage(cursos.percentage);
    }
    if (inscripciones?.percentage) {
      chips.value.inscripciones = getBadgePercentage(inscripciones.percentage);
    }
    if (usuariosNuevos?.percentage) {
      chips.value.usuariosNuevos = getBadgePercentage(usuariosNuevos.percentage);
    }
    if (usuariosInscritos?.percentage) {
      chips.value.usuariosInscritos = getBadgePercentage(usuariosInscritos.percentage);
    }
  });

  /**
   * @param {number|string} percentage
   */
  const getBadgePercentage = (percentage) => {
    percentage = parseFloat(percentage);
    const sing = Math.sign(percentage);
    const value = Math.abs(percentage).toFixed(2) + '%';
    let icon = ''; // Cambiado a let para permitir la reasignación
    let color = ''; // Cambiado a let para permitir la reasignación
    if (sing > 0) {
      color = 'success';
      icon = 'mdi-arrow-up-thin';
    } else if (sing < 0) {
      color = 'error';
      icon = 'mdi-arrow-down-thin';
    }

    return { color, icon, value };
  };
</script>

<template>
  <main-layout>
    <v-container fluid>
      <template v-if="Truthty(!temporada)">
        <v-card color="background" class="shadow-md px-4 py-2">
          <v-card-title class="text-center" style="font-size: 30px"
            >No existe ninguna temporada activa en este momento !</v-card-title
          >
          <!-- <img src="@/assets/celebracion.png" width="10%" height="10%" /> -->
        </v-card>
      </template>
      <template v-else>
        <v-row>
          <v-col cols="12" lg="3" md="4" sm="6">
            <v-card name="totalUsuarios">
              <v-card-item>
                <v-card-subtitle>Total de Usuarios Registrados</v-card-subtitle>
                <v-card-title>{{ data.totalUsuarios.total }}</v-card-title>

                <v-card-text class="py-0">
                  <template
                    v-for="(item, index) in data.totalUsuarios.generos"
                    :key="'genero-' + index"
                  >
                    <div class="d-flex justify-space-between">
                      <text>{{ item.nombre }} :</text>
                      <text>{{ item.total }}</text>
                    </div>
                  </template>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-text class="pt-0">
                  <template v-for="(item, index) in data.totalUsuarios.roles" :key="'rol-' + index">
                    <div class="d-flex justify-space-between">
                      <text>{{ item.nombre }} :</text>
                      <text>{{ item.total }}</text>
                    </div>
                  </template>
                </v-card-text>
              </v-card-item>
            </v-card>
          </v-col>
          <v-col cols="12" lg="3" md="4" sm="6">
            <v-card name="registrosNuevos">
              <v-card-item>
                <v-card-subtitle>Registros nuevos {{ temporada.prefijo }}</v-card-subtitle>
                <v-card-title class="d-flex justify-space-between">
                  <div>{{ data.usuariosNuevos.total }}</div>
                  <v-chip
                    :prepend-icon="chips.usuariosNuevos.icon"
                    :color="chips.usuariosNuevos.color"
                    variant="tonal"
                    class="text-caption"
                  >
                    {{ chips.usuariosNuevos.value }}</v-chip
                  >
                </v-card-title>
                <v-card-text class="">
                  <template
                    v-for="(item, index) in data.usuariosNuevos.generos"
                    :key="'genero-' + index"
                  >
                    <div class="d-flex justify-space-between">
                      <text>{{ item.nombre }} :</text>
                      <text>{{ item.total }}</text>
                    </div>
                  </template>
                </v-card-text>
              </v-card-item>
            </v-card>
          </v-col>
          <v-col cols="12" lg="3" md="4" sm="6">
            <v-card name="inscritos">
              <v-card-item>
                <v-card-subtitle>Usuarios inscritos {{ temporada.prefijo }}</v-card-subtitle>
                <v-card-title class="d-flex justify-space-between">
                  <div>{{ data.usuariosInscritos.total }}</div>
                  <v-chip
                    :prepend-icon="chips.usuariosInscritos.icon"
                    :color="chips.usuariosInscritos.color"
                    variant="tonal"
                    class="text-caption"
                  >
                    {{ chips.usuariosInscritos.value }}</v-chip
                  >
                </v-card-title>
                <v-card-text class="py-0">
                  <template
                    v-for="(item, index) in data.usuariosInscritos.generos"
                    :key="'genero-' + index"
                  >
                    <div class="d-flex justify-space-between">
                      <text>{{ item.nombre }} :</text>
                      <text>{{ item.total }}</text>
                    </div>
                  </template>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-text class="pt-0">
                  <template
                    v-for="(item, index) in data.usuariosInscritos.roles"
                    :key="'rol-' + index"
                  >
                    <div class="d-flex justify-space-between">
                      <text>{{ item.nombre }} :</text>
                      <text>{{ item.total }}</text>
                    </div>
                  </template>
                </v-card-text>
              </v-card-item>
            </v-card>
          </v-col>
          <v-col cols="12" lg="3" md="4" sm="6" class="d-grid gr-6">
            <v-card>
              <v-card-item>
                <v-card-subtitle>Cursos</v-card-subtitle>
                <v-card-title class="d-flex justify-space-between">
                  <div>{{ data.cursos.actual }}</div>
                  <v-chip
                    :prepend-icon="chips.cursos.icon"
                    :color="chips.cursos.color"
                    variant="tonal"
                    class="text-caption"
                  >
                    {{ chips.cursos.value }}</v-chip
                  >
                </v-card-title>
                <!-- <v-card-text>Registrados</v-card-text> -->
              </v-card-item>
            </v-card>
            <v-card>
              <v-card-item>
                <v-card-subtitle>inscripciones</v-card-subtitle>
                <v-card-title class="d-flex justify-space-between">
                  <div>{{ data.inscripciones.actual }}</div>
                  <v-chip
                    :prepend-icon="chips.inscripciones.icon"
                    :color="chips.inscripciones.color"
                    variant="tonal"
                    class="text-caption"
                  >
                    {{ chips.inscripciones.value }}</v-chip
                  >
                </v-card-title>
                <!-- <v-card-text>Registrados</v-card-text> -->
              </v-card-item>
            </v-card>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" lg="4" md="6" sm="6">
            <v-card>
              <v-card-title>Registros por Curriculum {{ temporada.prefijo }}</v-card-title>
              <v-list max-height="25rem">
                <v-list-item
                  v-for="(inscripcion, index) in data.inscripcionesCurriculum"
                  :key="index"
                  class="min-h-auto"
                >
                  <v-list-item-content class="d-flex">
                    <v-list-item-title class="white-space-normal w-70">{{
                      inscripcion.curriculum_nombre
                    }}</v-list-item-title>
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ inscripcion.cantidad }}</v-list-item-subtitle
                    >
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ getBadgePercentage(inscripcion.percentage).value }}</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
          <v-col cols="12" lg="4" md="6" sm="6">
            <v-card>
              <v-card-title>Registros por Ciclo {{ temporada.prefijo }}</v-card-title>
              <v-list max-height="25rem">
                <v-list-item
                  v-for="(inscripcion, index) in data.inscripcionesCiclo"
                  :key="index"
                  class="min-h-auto"
                >
                  <v-list-item-content class="d-flex">
                    <v-list-item-title class="white-space-normal w-70"
                      >{{ inscripcion.curriculum_nombre }} -
                      {{ inscripcion.ciclo_nombre }}</v-list-item-title
                    >
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ inscripcion.cantidad }}</v-list-item-subtitle
                    >
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ getBadgePercentage(inscripcion.percentage).value }}</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
          <v-col cols="12" lg="4" md="6" sm="6">
            <v-card>
              <v-card-title>Usuarios inscritos por País {{ temporada.prefijo }}</v-card-title>
              <v-list max-height="25rem">
                <v-list-item
                  v-for="(inscripcion, index) in data.inscripcionesPais"
                  :key="index"
                  class="min-h-auto"
                >
                  <v-list-item-content class="d-flex">
                    <v-list-item-title class="white-space-normal w-70">{{
                      inscripcion.pais_nombre
                    }}</v-list-item-title>
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ inscripcion.cantidad }}</v-list-item-subtitle
                    >
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ getBadgePercentage(inscripcion.percentage).value }}</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
          <v-col cols="12" lg="4" md="6" sm="6">
            <v-card>
              <v-card-title>Usuarios inscritos por Región {{ temporada.prefijo }}</v-card-title>
              <v-list max-height="25rem">
                <v-list-item
                  v-for="(inscripcion, index) in data.inscripcionesRegion"
                  :key="index"
                  class="min-h-auto"
                >
                  <v-list-item-content class="d-flex">
                    <v-list-item-title class="white-space-normal w-70"
                      >{{ inscripcion.pais_nombre }} -
                      {{ inscripcion.region_nombre }}</v-list-item-title
                    >
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ inscripcion.cantidad }}</v-list-item-subtitle
                    >
                    <v-list-item-subtitle
                      class="overflow-visible d-flex align-center justify-end w-15"
                      >{{ getBadgePercentage(inscripcion.percentage).value }}</v-list-item-subtitle
                    >
                  </v-list-item-content>
                </v-list-item>
              </v-list>
            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-container>
  </main-layout>
</template>
<style></style>
