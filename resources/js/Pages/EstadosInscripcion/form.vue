<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { router } from '@inertiajs/vue3';
  import { defineProps, inject, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import axios from 'axios';
  import MainLayout from '../../components/Layout';
  import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    estInscripcion: { type: Object, default: {} },
    status: String,
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    estado: '',
    ...props.estInscripcion,
  });
  const form = ref(null);

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
  };

  const submit = async (form) => {
    loading.value = true;
    const action = props.action === CRUD.edit ? 'update' : 'store';
    const method = props.action === CRUD.edit ? 'put' : 'post';
    const routeName = `estados-inscripcion.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : null;

    try {
      const response = await axios[method](route(routeName, id), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('estados-inscripcion.index'));
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
    <v-container>
      <v-card color="background" class="" :disabled="loading" :loading="loading">
        <template v-slot:loader="{ isActive }">
          <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
        </template>
        <v-card-title>
          <ButtonBack :href="route('estados-inscripcion.index')" /> ESTADOS DE INSCRIPCIÓN
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>
        <v-card-subtitle>{{ ACCION[action] }} de Estado de Inscripcion</v-card-subtitle>
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
                  id="estado"
                  name="estado"
                  label="Estado"
                  v-model="inputForm.estado"
                  :disabled="isDisabled"
                  :rules="validate('Estado', 'required')"
                  :error-messages="inputForm.errors.estado"
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
