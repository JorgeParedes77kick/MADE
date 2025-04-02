<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { router } from '@inertiajs/vue3';

  import { defineProps, inject, onMounted, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import axios from 'axios';
  import MainLayout from '../../components/Layout';
  import { CRUD, TEXT_BUTTON } from '../../constants/form';
  import { Truthty } from '../../utils/isType';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    grupoPequeno: { type: Object, default: {} },
    temporadas: { type: Array, default: [] },
    curriculums: { type: Array, default: [] },
    dias: { type: Array, default: [] },
    lideres: { type: Array, default: [] },
    monitores: { type: Array, default: [] },
    status: String,
  });

  const loading = ref(false);

  const inputForm = useForm({
    temporada_id: '',
    temporada: null,
    curriculum_id: '',
    curriculum: null,
    ciclo_id: '',
    ciclo: null,
    lideres: [],
    monitores: [],
    dia_curso: '',
    hora_inicio: '',
    hora_fin: '',
    activo_inscripcion: true,
    info_adicional: '',
    alumnos_contar: 0,
    ...props.grupoPequeno,
  });
  const form = ref(null);
  const ciclos = ref([]);

  const isDisabled = ref(inputForm.alumnos_contar > 0);

  const rules = {
    isValidTime: (value) => {
      if (!value) return true; // Permitir vacío
      const [hours, minutes] = value.split(':').map(Number);
      return minutes % 15 === 0 || 'Elige un múltiplo de 15 minutos';
    },
  };

  onMounted(() => {
    if (Truthty(props.grupoPequeno)) {
      const {
        grupoPequeno: { ciclo },
      } = props;
      inputForm.curriculum = ciclo.curriculum;
    }
    // const { usuario, roles } = props
  });

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid, ...rest } = await form.value.validate();
    if (valid) submit();
  };

  const submit = async () => {
    loading.value = true;
    const action = props.action === CRUD.edit ? 'update' : 'store';
    const method = props.action === CRUD.edit ? 'put' : 'post';
    const routeName = `grupos-pequenos.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : undefined;
    inputForm.ciclo_id = inputForm.ciclo.id;
    inputForm.temporada_id = inputForm.temporada.id;
    try {
      const response = await axios[method](route(routeName, id), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('grupos-pequenos.horarios'));
      }
    } catch (err) {
      console.log('err:', err);
      console.log(err?.response);
      if (err?.response?.data?.server) {
        const { server: message } = err.response.data;
        Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      }
      if (err?.response?.data?.errors) {
        const { errors } = err.response.data;
        inputForm.errors = errors;
      }
    } finally {
      loading.value = false;
    }
  };

  const focus = (state, name) => {
    if (state) return;
    const { curriculum, ciclo } = inputForm;
    if (!state && name == 'curriculum' && typeof curriculum == 'string') {
      inputForm.curriculum = null;
      inputForm.ciclo = null;
    } else if (!state && name == 'ciclo' && typeof ciclo == 'string') {
      inputForm.ciclo = null;
    } else if (!state && name == 'temporada' && typeof ciclo == 'string') {
      inputForm.temporada = null;
    }
  };

  const onChange = (item) => {
    if (typeof item === 'object' && Truthty(item)) {
      ciclos.value = item.ciclos;
      inputForm.ciclo = null;
    }
  };
  const onChangeUsuarios = (newValue) => {
    if (newValue.some((x) => typeof x === 'string')) {
      for (let i = newValue.length - 1; i >= 0; i--) {
        if (typeof newValue[i] !== 'object' || !newValue[i].id) {
          newValue.splice(i, 1); // Eliminar el elemento inválido
        }
      }
    }
  };
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="" :disabled="loading" :loading="loading">
        <template v-slot:loader="{ isActive }">
          <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
        </template>
        <v-card-title>
          <ButtonBack /> GRUPOS PEQUEÑOS - HORARIOS
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>

        <!-- <v-card-subtitle>{{ ACCION[action] }} del Rol</v-card-subtitle> -->
        <v-card-text>
          <v-alert color="error" v-if="inputForm.alumnos_contar > 0" class="mb-3">
            Algunas Propiedades no se pueden cambiar por que ya se inscribieron alumnos a esta
            clase.
          </v-alert>
          <v-form @submit="validateForm" ref="form" lazy-validation>
            <v-row class="row-gap-2">
              <v-col v-if="action !== CRUD.create" cols="12" sm="6">
                <v-text-field
                  id="id"
                  name="id"
                  label="ID"
                  v-model="inputForm.id"
                  disabled
                  :error-messages="inputForm.errors.id"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-combobox
                  id="temporada"
                  name="temporada"
                  label="Temporada"
                  v-model="inputForm.temporada"
                  :disabled="isDisabled"
                  :rules="validate('Temporada', 'required')"
                  :error-messages="inputForm.errors.temporada"
                  :items="temporadas"
                  item-title="prefijo"
                  item-value="id"
                  @update:focused="(s) => focus(s, 'temporada')"
                  autocomplete="off"
                />
              </v-col>
            </v-row>
            <v-row class="row-gap-2">
              <v-col cols="12" sm="6">
                <v-combobox
                  id="curriculum"
                  name="curriculum"
                  label="Curriculum"
                  v-model="inputForm.curriculum"
                  :disabled="isDisabled"
                  :rules="validate('Curriculum', 'required')"
                  @update:focused="(s) => focus(s, 'curriculum')"
                  :error-messages="inputForm.errors.curriculum"
                  :items="curriculums"
                  item-title="nombre"
                  item-value="id"
                  @update:modelValue="onChange"
                  autocomplete="off"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-combobox
                  id="ciclo"
                  name="ciclo"
                  label="Ciclo"
                  v-model="inputForm.ciclo"
                  :disabled="isDisabled"
                  :rules="validate('Ciclo', 'required')"
                  @update:focused="(s) => focus(s, 'ciclo')"
                  :error-messages="inputForm.errors.ciclo"
                  :items="ciclos"
                  item-title="nombre"
                  item-value="id"
                  autocomplete="off"
                />
              </v-col>
              <v-col cols="12">
                <v-combobox
                  id="monitores"
                  name="monitores"
                  label="Monitores"
                  v-model="inputForm.monitores"
                  @update:modelValue="onChangeUsuarios"
                  :rules="validate('Monitores', 'required')"
                  :error-messages="inputForm.errors.monitores"
                  :items="lideres"
                  item-title="fullNombre"
                  item-value="id"
                  chips
                  multiple
                  closable-chips
                  hide-selected
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props" :subtitle="item.raw.nick_name"></v-list-item>
                  </template>
                </v-combobox>
              </v-col>
              <v-col cols="12">
                <v-combobox
                  id="lideres"
                  name="lideres"
                  label="Lideres"
                  v-model="inputForm.lideres"
                  :rules="validate('Lideres', 'required')"
                  @update:modelValue="onChangeUsuarios"
                  :error-messages="inputForm.errors.lideres"
                  :items="monitores"
                  item-title="fullNombre"
                  item-value="id"
                  chips
                  multiple
                  closable-chips
                  hide-selected
                >
                  <template v-slot:item="{ props, item }">
                    <v-list-item v-bind="props" :subtitle="item.raw.nick_name"></v-list-item>
                  </template>
                </v-combobox>
              </v-col>
            </v-row>
            <v-row class="row-gap-2">
              <v-col cols="12" sm="6">
                <v-select
                  id="dia"
                  name="dia"
                  label="Día"
                  v-model="inputForm.dia_curso"
                  :disabled="isDisabled"
                  :rules="validate('Dia', 'required')"
                  :error-messages="inputForm.errors.dia_curso"
                  :items="dias"
                  autocomplete="off"
                /> </v-col
            ></v-row>
            <v-row class="row-gap-2">
              <v-col cols="12" sm="6">
                <v-text-field
                  id="hora_inicio"
                  name="hora_inicio"
                  label="Hora Inicio"
                  placeholder="HH:MM"
                  v-model="inputForm.hora_inicio"
                  :disabled="isDisabled"
                  type="time"
                  :step="900"
                  :rules="[...validate('Hora', 'required'), rules.isValidTime]"
                  :error-messages="inputForm.errors.hora_inicio"
                  clearable
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="hora_fin"
                  name="hora_fin"
                  label="Hora Fin"
                  placeholder="HH:MM"
                  v-model="inputForm.hora_fin"
                  :disabled="isDisabled"
                  type="time"
                  :step="900"
                  :rules="[...validate('Hora', 'required'), rules.isValidTime]"
                  :error-messages="inputForm.errors.hora_fin"
                  clearable
                />
              </v-col>
              <v-col cols="6" class="justify-end">
                <v-switch
                  id="activo_inscripcion"
                  name="activo_inscripcion"
                  v-model="inputForm.activo_inscripcion"
                  :label="
                    inputForm.activo_inscripcion
                      ? 'Activo para inscripciones'
                      : 'No más inscripciones'
                  "
                  color="success"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-textarea
                  id="info_adicional"
                  name="info_adicional"
                  label="Adicional"
                  v-model="inputForm.info_adicional"
                  autocomplete="off"
                />
              </v-col>
            </v-row>
            <v-row class="my-3">
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
