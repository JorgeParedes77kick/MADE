<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { router } from '@inertiajs/vue3';
  import axios from 'axios';
  import { defineProps, inject, onMounted, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import MainLayout from '../../components/Layout';
  import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';
  import { previewImage } from '../../utils/image';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    curriculum: { type: Object, default: {} },
    status: String,
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    nombre: '',
    libro: '',
    descripcion: '',
    cantidad_clases: '',
    cantidad_cupos: '',
    imagen: '',
    imagen_landing: '',
    activo: '',
    ...props.curriculum,
    imagenFile: null,
  });
  const form = ref(null);

  onMounted(() => {
    if (props.curriculum.id) inputForm.imagenFile = new File([''], props.curriculum.imagen);
  });

  const onChangeFile = () => {
    inputForm.imagen = '';
    inputForm.errors.imagenFile = null;
  };

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
  };

  const submit = async () => {
    loading.value = true;
    const action = props.action === CRUD.edit ? 'update' : 'store';
    const routeName = `curriculums.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : null;
    const config = { headers: { 'Content-Type': 'multipart/form-data' } };
    const formData = new FormData();
    const keys = Object.keys(inputForm);
    for (let i = 0; i < keys.length; i++) {
      const key = keys[i];
      formData.append(key, inputForm[key]);
      if (key === 'imagenFile') break;
    }

    try {
      const response = await axios.post(route(routeName, id), formData, config);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('curriculums.index'));
      }
    } catch (err) {
      console.log(err?.response);
      if (err?.response?.data?.server) {
        const { server: message } = err.response.data;
        Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      }
      console.log('err?.response?.data?.errors:', err?.response?.data?.errors);
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
          <ButtonBack :href="route('curriculums.index')" /> CURRICULUM
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>
        <v-card-subtitle>{{ ACCION[action] }} de Curriculum</v-card-subtitle>
        <v-card-text>
          <v-form @submit="validateForm" ref="form" lazy-validation>
            <v-row class="row-gap-2">
              <v-col cols="12" sm="6">
                <v-text-field
                  id="nombre"
                  name="nombre"
                  label="Nombre"
                  v-model="inputForm.nombre"
                  :disabled="isDisabled"
                  :rules="validate('Nombre', 'required')"
                  :error-messages="inputForm.errors.nombre"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="libro"
                  name="libro"
                  label="Libro"
                  v-model="inputForm.libro"
                  :disabled="isDisabled"
                  :rules="validate('Libro', 'required')"
                  :error-messages="inputForm.errors.libro"
                />
              </v-col>
              <v-col cols="12" sm="12">
                <v-textarea
                  id="descripcion"
                  name="descripcion"
                  label="Descripción"
                  v-model="inputForm.descripcion"
                  :disabled="isDisabled"
                  :rules="validate('Descripción', 'required')"
                  :error-messages="inputForm.errors.descripcion"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="cantidad_clases"
                  name="cantidad_clases"
                  label="Cantidad de clases"
                  type="number"
                  v-model="inputForm.cantidad_clases"
                  :disabled="isDisabled"
                  :rules="validate('Cantidad de clases', 'required')"
                  :error-messages="inputForm.errors.cantidad_clases"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="cantidad_cupos"
                  name="cantidad_cupos"
                  label="Cantidad de cupos"
                  type="number"
                  v-model="inputForm.cantidad_cupos"
                  :disabled="isDisabled"
                  :rules="validate('Cantidad de cupos', 'required')"
                  :error-messages="inputForm.errors.cantidad_cupos"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-file-input
                  id="imagen"
                  name="imagen"
                  label="Imagen"
                  v-model="inputForm.imagenFile"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.imagenFile || inputForm.errors.imagen"
                  accept="image/*"
                  prepend-icon="mdi-camera"
                  @update:modelValue="onChangeFile"
                />
                <v-img
                  v-if="inputForm.imagenFile"
                  :src="previewImage(inputForm.imagenFile)"
                  max-width="500"
                  max-height="500"
                  class="d-block mx-auto"
                />
                <v-img
                  v-if="typeof inputForm.imagen == 'string' && inputForm.imagen.length > 0"
                  :src="`/storage/img/curriculums/${inputForm.imagen}`"
                  max-width="500"
                  max-height="500"
                  class="d-block mx-auto"
                />
              </v-col>
              <!-- <v-col cols="12" sm="6">
                <v-file-input id="imagen_landing" name="imagen_landing" label="Imagen de landing"
                  v-model="inputForm.imagen_landing" :disabled="isDisabled"
                  :error-messages="inputForm.errors.imagen_landing" accept="image/*" prepend-icon="mdi-camera" />
                <v-img v-if="inputForm.imagen_landing" :src="previewImage(inputForm.imagen_landing)" max-width="500"
                  max-height="500" class="d-block mx-auto" />
                <v-img v-if="typeof inputForm.imagen_landing == 'string' && inputForm.imagen_landing.length > 0"
                  :src="`/img/curriculums/${inputForm.imagen_landing}`" max-width="500" max-height="500"
                  class="d-block mx-auto" />

              </v-col> -->
              <v-col cols="6" class="justify-end">
                <v-switch
                  id="activo"
                  name="activo"
                  v-model="inputForm.activo"
                  :disabled="isDisabled"
                  :label="inputForm.activo ? 'Activo' : 'Inactivo'"
                  color="primary"
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
