<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { defineProps, onMounted, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import MainLayout from '../../components/Layout';
  import { CRUD, TEXT_BUTTON } from '../../constants/form';

  // const validate = inject('$validation');

  const props = defineProps({
    action: String,
    grupoPequeno: { type: Object, default: {} },
    // estadosInscripcion: { type: Array, default: [] },
    status: String,
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    ...props.grupoPequeno,
  });
  const form = ref(null);

  onMounted(() => {
    console.log(props);
  });
</script>

<template>
  <MainLayout>
    <v-container>
      <v-card color="background" class="" :disabled="loading" :loading="loading">
        <template v-slot:loader="{ isActive }">
          <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
        </template>
        <v-card-title>
          <ButtonBack /> GRUPO PEQUEÑO -
          {{ `#${grupoPequeno.id}` }}
        </v-card-title>
        <v-card-text>
          <v-form ref="form" lazy-validation>
            <v-row class="row-gap-2">
              <v-col cols="12" sm="4">
                <v-text-field
                  id="Temporada"
                  name="Temporada"
                  label="Temporada"
                  v-model="grupoPequeno.temporada.prefijo"
                  disabled
                />
              </v-col>
              <v-col cols="12" sm="4">
                <v-text-field
                  id="Curriculum"
                  name="Curriculum"
                  label="Curriculum"
                  v-model="grupoPequeno.ciclo.curriculum.nombre"
                  disabled
                />
              </v-col>
              <v-col cols="12" sm="4">
                <v-text-field
                  id="Ciclo"
                  name="Ciclo"
                  label="Ciclo"
                  v-model="grupoPequeno.ciclo.nombre"
                  disabled
                />
              </v-col>
            </v-row>
            <v-row class="row-gap-2">
              <v-col cols="12">
                <span class="text-subtitle-2">Monitores</span>
              </v-col>
              <v-col cols="12" class="py-0">
                <v-list>
                  <v-list-item v-for="monitor in grupoPequeno.monitores" :key="monitor.id">
                    <div class="d-grid" style="grid-template-columns: 2fr 2fr 1fr">
                      <div>{{ `${monitor.persona.nombre} ${monitor.persona.apellido}` }}</div>
                      <div>{{ monitor.email }}</div>
                    </div>
                  </v-list-item>
                </v-list>
              </v-col>
            </v-row>
            <v-row class="row-gap-2">
              <v-col cols="12">
                <span class="text-subtitle-2">Lideres</span>
              </v-col>
              <v-col cols="12" class="py-0">
                <v-list>
                  <v-list-item v-for="lider in grupoPequeno.lideres" :key="lider.id">
                    <div class="d-grid" style="grid-template-columns: 2fr 2fr 1fr">
                      <div>{{ `${lider.persona.nombre} ${lider.persona.apellido}` }}</div>
                      <div>{{ lider.email }}</div>
                    </div>
                  </v-list-item>
                </v-list>
              </v-col>
            </v-row>
            <v-row class="row-gap-2">
              <v-col cols="12">
                <span class="text-subtitle-2">Alumnos</span>
              </v-col>
              <v-col cols="12" class="py-0">
                <v-list>
                  <v-list-item
                    v-for="inscripcion in grupoPequeno.inscripciones_alumnos"
                    :key="inscripcion.id"
                  >
                    <template v-slot:subtitle>
                      <div class="d-grid" style="grid-template-columns: 2fr 2fr 1fr">
                        <div>{{ inscripcion.usuario.nombreCompleto }}</div>
                        <div>{{ inscripcion.usuario.email }}</div>
                        <!-- <div>{{ inscripcion.usuario.persona.telefono }}</div> -->
                        <div class="text-right">
                          {{ inscripcion.estado_inscripcion.estado }}
                        </div>
                      </div>
                    </template>
                  </v-list-item>
                </v-list>
              </v-col>
            </v-row>

            <v-row class="my-3" v-if="!isDisabled">
              <v-col cols="12" class="d-flex">
                <v-btn class="ms-auto" type="submit" color="primary" :loading="loading">
                  {{ TEXT_BUTTON[action] }}
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
        </v-card-text>
      </v-card>
    </v-container>
  </MainLayout>
</template>
