<script setup>
  import { router, useForm } from '@inertiajs/vue3';
  import { defineProps, inject, onMounted, ref } from 'vue';
  import MainLayout from '../../components/Layout';

  const validate = inject('$validation');
  const props = defineProps({
    inscripcion: { type: Object, default: {} },
    message: { type: String, default: '' },
    grupos_pequenos: { type: Array, default: [] },
    action: String,
  });
  const loading = ref(false);
  const form = ref(null);

  const inputInscripcion = useForm({
    usuario_id: -1,
    grupo_pequeno_id: '',
    estado_inscripcion_id: '',
    ...props.inscripcion,
    rol_id: 5,
    info_adicional: 'Inscripción vía administración',
  });

  const headersGrupos = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Día', key: 'dia_curso' },
    { title: 'Hora', key: 'hora', minWidth: '6rem' },
    { title: 'Lideres', key: 'lideres' },
  ];
  const headersIns = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Día', key: 'grupo_pequeno.dia_curso' },
    { title: 'Hora', key: 'hora', minWidth: '6rem' },
    { title: 'Lideres', key: 'lideres' },
    { title: 'monitores', key: 'monitores' },
  ];
  const grupo_pequeno = ref([]);
  const onChangeGrupo = (item) => {
    inputInscripcion.grupo_pequeno_id = item.length ? item[0].id : '';
  };
  onMounted(() => {
    console.log(props);
  });

  onMounted(() => {});

  const submit = async (e) => {
    e?.preventDefault();
    loading.value = true;
    try {
      const response = await axios.put(
        route('inscripcion.update', props.inscripcion),
        inputInscripcion,
      );
      console.log('response:', response);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
        router.visit(route('inscripcion.index'));
      }
    } catch (err) {
      console.log(err);
      if (err?.response?.data?.server) {
        const { server: message } = err.response.data;
        Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      }
      if (err?.response?.data?.errors) {
        const { errors } = err.response.data;
        inputInscripcion.errors = errors;
      }
    } finally {
      loading.value = false;
    }
  };
</script>

<template>
  <MainLayout>
    <v-container>
      <v-card color="background" class="px-4 py-2">
        <v-card-title>REASIGNACION DE INSCRIPCIÓN #{{ inscripcion.id }}</v-card-title>
        <v-form @submit="submit" ref="form">
          <v-row>
            <v-col cols="12" sm="6">
              <v-text-field
                id="usuario"
                name="usuario"
                disabled
                v-model="inscripcion.usuario.fullNombre"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col sm="6" md="4">
              <v-text-field
                id="temporada"
                name="temporada"
                label="Temporada"
                v-model="inscripcion.grupo_pequeno.temporada.prefijo"
                disabled
              />
            </v-col>
            <v-col sm="6" md="4">
              <v-text-field
                id="curriculum"
                name="curriculum"
                label="Curriculum"
                v-model="inscripcion.grupo_pequeno.ciclo.curriculum.nombre"
                disabled
              />
            </v-col>
            <v-col sm="6" md="4">
              <v-text-field
                id="ciclo"
                name="ciclo"
                label="Ciclo"
                v-model="inscripcion.grupo_pequeno.ciclo.nombre"
                disabled
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" class="text-subtitle-2"> Inscripcion Actual </v-col>
            <v-col>
              <v-data-table
                :headers="headersIns"
                :items="[inscripcion]"
                class="elevation-1 rounded"
                hide-default-footer
                hide-default-header
                ><template v-slot:no-data>Información no encontrada</template>
                <template v-slot:[`item.id`]="{ item }"> #{{ item.grupo_pequeno.id }} </template>
                <template v-slot:[`item.monitores`]="{ item }">
                  <p v-for="monitor in item.grupo_pequeno.monitores" :key="monitor.id">
                    {{ monitor?.nombreCompleto }}
                  </p>
                </template>
                <template v-slot:[`item.lideres`]="{ item }">
                  <p v-for="lider in item.grupo_pequeno.lideres" :key="lider.id">
                    {{ lider?.nombreCompleto }}
                  </p>
                </template>
                <template v-slot:[`item.hora`]="{ item }">
                  <p v-if="item.grupo_pequeno.ia_curso == 'none'">none</p>
                  <p v-else>{{ item.grupo_pequeno.hora }}</p>
                </template>
              </v-data-table>
            </v-col>
          </v-row>

          <v-row>
            <v-col cols="12" class="text-subtitle-2"> Reasignar a </v-col>

            <v-col>
              <v-data-table
                :headers="headersGrupos"
                :items="grupos_pequenos"
                class="elevation-1 rounded"
                hide-default-footer
                hide-default-header
                show-select
                select-strategy="single"
                v-model="grupo_pequeno"
                @update:modelValue="onChangeGrupo"
                :rules="validate('Grupo Pequeño', 'required')"
                return-object
              >
                <template v-slot:no-data>
                  No Hay más grupos pequeños para realizar la reasignación
                </template>
                <template
                  v-slot:item.data-table-select="{ internalItem, isSelected, toggleSelect }"
                >
                  <v-checkbox-btn
                    :model-value="isSelected(internalItem)"
                    color="primary"
                    @update:model-value="toggleSelect(internalItem)"
                  ></v-checkbox-btn>
                </template>
                <template v-slot:[`item.id`]="{ item }"> #{{ item.id }} </template>
                <template v-slot:[`item.monitores`]="{ item }">
                  <p v-for="monitor in item.monitores" :key="monitor.id">
                    {{ monitor?.nombreCompleto }}
                  </p>
                </template>
                <template v-slot:[`item.lideres`]="{ item }">
                  <p v-for="lider in item.lideres" :key="lider.id">{{ lider?.nombreCompleto }}</p>
                </template>
                <template v-slot:[`item.hora`]="{ item }">
                  <p v-if="item.dia_curso == 'none'">none</p>
                  <p v-else>{{ item.hora }}</p>
                </template>
              </v-data-table>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" class="d-flex">
              <v-btn class="ms-auto" type="submit" color="primary" :loading="loading">
                Actualizar
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card>
    </v-container>
  </MainLayout>
</template>
<style></style>
