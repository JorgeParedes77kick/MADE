<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { router } from '@inertiajs/vue3';
  import { defineProps, inject, onMounted, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import axios from 'axios';
  import MainLayout from '../../components/Layout';
  import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    status: String,
    restriccion: { type: Object, default: {} },
    tipos: { type: Array, default: [] },
    curriculums: { type: Array, default: [] },
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    nombre: '',
    valor_restriccion: '',
    tipo_restriccion_id: '',
    curriculum_id: '',
    tipo_restriccion: null,
    curriculum: null,
    ...props.restriccion,
  });
  const form = ref(null);

  onMounted(() => {
    console.log(props.restriccion, props.curriculums);
  });

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
  };

  const submit = async () => {
    loading.value = true;
    //
    inputForm.curriculum_id = inputForm.curriculum.id;
    inputForm.tipo_restriccion_id = inputForm.tipo_restriccion.id;
    //
    const action = props.action === CRUD.edit ? 'update' : 'store';
    const method = props.action === CRUD.edit ? 'put' : 'post';
    const routeName = `restricciones.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : undefined;

    try {
      const response = await axios[method](route(routeName, id), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('restricciones.index'));
      }
    } catch (err) {
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
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="" :disabled="loading" :loading="loading">
        <template v-slot:loader="{ isActive }">
          <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
        </template>
        <v-card-title>
          <ButtonBack :href="route('restricciones.index')" /> RESTRICCIONES
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>
        <v-card-subtitle>{{ ACCION[action] }} de la Restricción</v-card-subtitle>
        <v-card-text>
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
                <v-text-field
                  id="nombre"
                  name="nombre"
                  label="Nombre"
                  v-model="inputForm.nombre"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.nombre"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-combobox
                  id="curriculum"
                  name="curriculum"
                  label="Curriculum"
                  v-model="inputForm.curriculum"
                  :disabled="isDisabled"
                  :rules="validate('Curriculum', 'required')"
                  :error-messages="inputForm.errors.curriculum_id"
                  :items="curriculums"
                  item-title="nombre"
                  item-value="id"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-combobox
                  id="tipo_restriccion_id"
                  name="tipo_restriccion_id"
                  label="Tipo de Restricción"
                  v-model="inputForm.tipo_restriccion"
                  :disabled="isDisabled"
                  :rules="validate('Tipo de Restricción', 'required')"
                  :error-messages="inputForm.errors.tipo_restriccion_id"
                  :items="tipos"
                  item-title="nombre"
                  item-value="id"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="valor_restriccion"
                  name="valor_restriccion"
                  label="Valor Restricción"
                  v-model="inputForm.valor_restriccion"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.valor_restriccion"
                />
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
