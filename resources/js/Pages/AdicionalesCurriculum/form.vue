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
    curriculum: Object,
    curriculums: Array,
    types: Array,
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    nombre: '',
    adicionales: [],
    ...props.curriculum,
  });
  const form = ref(null);

  onMounted(() => {
    console.log(inputForm);
  });

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
  };

  const submit = async () => {
    loading.value = true;
    if (props.curriculum) inputForm.curriculum_id = inputForm.id;
    else inputForm.curriculum_id = inputForm.nombre.id;

    const action = 'store';
    const method = 'post';
    const routeName = `adicionales-curriculum.${action}`;

    try {
      const response = await axios[method](route(routeName), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('adicionales-curriculum.index'));
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

  const deleteItem = (e, index) => {
    e.preventDefault();
    const item = inputForm.adicionales[index];
    if (item.id) inputForm.adicionales[index].delete = true;
    else if (item) inputForm.adicionales.splice(index, 1);
  };
  const addItem = (e) => {
    e.preventDefault();
    inputForm.adicionales.push({ nombre: '', type_value: '' });
  };
  const onChange = (item) => {
    if (typeof item === 'object') inputForm.adicionales = item.adicionales;
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
          <ButtonBack :href="route('adicionales-curriculum.index')" /> ADICIONALES CURRICULUM
          {{ CRUD.create !== action ? `#${curriculum?.nombre}` : '' }}
        </v-card-title>
        <v-card-subtitle>{{ ACCION[action] }} de los Adicionales del Curriculum </v-card-subtitle>
        <v-card-text>
          <v-form @submit="validateForm" ref="form" lazy-validation>
            <v-row class="row-gap-2">
              <v-col cols="8" sm="6">
                <v-combobox
                  id="curriculum"
                  name="curriculum"
                  label="Curriculum"
                  v-model="inputForm.nombre"
                  :disabled="CRUD.create !== action"
                  :rules="validate('Curriculum', 'required')"
                  :error-messages="inputForm.errors.nombre"
                  :items="curriculums"
                  item-title="nombre"
                  item-value="id"
                  @update:modelValue="onChange"
                />
              </v-col>
              <v-col cols="4" sm="2" class="d-flex align-center justify-center">
                <v-btn
                  color="success"
                  small
                  @click="addItem"
                  :disabled="CRUD.edit !== action && typeof inputForm.nombre !== 'object'"
                >
                  <v-icon icon="mdi-plus-thick" />
                </v-btn>
              </v-col>
            </v-row>
            <v-row
              v-for="(adi, i) in inputForm.adicionales"
              :key="`${adi.id}_${i}`"
              :class="adi.delete ? 'd-none' : ''"
            >
              <v-col cols="12" sm="5">
                <v-text-field
                  id="nombre"
                  :name="`nombre_${adi.id}_${i}`"
                  label="Nombre Adicional"
                  v-model="adi.nombre"
                  :disabled="isDisabled"
                  :rules="validate('Nombre Adicional', 'required')"
                />
              </v-col>
              <v-col cols="8" sm="5">
                <v-select
                  id="type_value"
                  :name="`type_value_${adi.id}_${i}`"
                  label="Tipo del Valor"
                  v-model="adi.type_value"
                  :disabled="isDisabled"
                  :rules="validate('Tipo del Valor', 'required')"
                  :items="types"
                />
              </v-col>
              <v-col cols="4" sm="2" class="d-flex align-center justify-center">
                <v-btn
                  color="error"
                  small
                  @click="(e) => deleteItem(e, i)"
                  v-if="CRUD.show !== action"
                >
                  <v-icon icon="mdi-trash-can-outline" />
                </v-btn>
              </v-col>
            </v-row>
            <v-row class="my-3" v-if="!isDisabled">
              <v-col cols="12" class="d-flex">
                <v-btn
                  class="ms-auto"
                  type="submit"
                  color="primary"
                  :loading="loading"
                  :disabled="inputForm.adicionales.length == 0"
                >
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
