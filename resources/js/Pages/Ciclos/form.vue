<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { router } from '@inertiajs/vue3';
  import { inject, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import axios from 'axios';
  import { defineProps, onMounted } from 'vue';
  import MainLayout from '../../components/Layout';
  import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    ciclo: { type: Object, default: {} },
    curriculums: { type: Array, default: [] },
  });
  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    nombre: '',
    descripcion: '',
    previos: props.ciclo?.requisitos?.length > 0,
    requisitos: [],
    ...props.ciclo,
  });
  const form = ref(null);
  onMounted(() => {
    // console.log(props)
  });

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
  };

  const submit = async () => {
    loading.value = true;
    const action = props.action === CRUD.edit ? 'update' : 'store';
    const method = props.action === CRUD.edit ? 'put' : 'post';
    const routeName = `ciclos.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : undefined;

    inputForm.curriculum_id = inputForm.curriculum.id;

    try {
      const response = await axios[method](route(routeName, id), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('ciclos.index'));
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
  const addItem = (e) => {
    e.preventDefault();
    inputForm.requisitos.push({
      ciclo_id: '',
      ciclo_pre_id: '',
      curriculum: { id: '', nombre: '' },
    });
  };
  const onChange = (item) => {
    if (typeof item === 'object') inputForm.curriculum = item;
  };
  const onChangeRequisito = (item, i) => {
    inputForm.requisitos[i].ciclo_pre_id = '';
  };
  const deleteItem = (e, index) => {
    e.preventDefault();
    const item = inputForm.requisitos[index];
    if (item.id) inputForm.requisitos[index].delete = true;
    else if (item) inputForm.requisitos.splice(index, 1);
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
          <ButtonBack :href="route('ciclos.index')" /> CICLO
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>
        <v-card-subtitle>{{ ACCION[action] }} del Ciclo</v-card-subtitle>
        <v-card-text>
          <v-form @submit="validateForm" ref="form" lazy-validation>
            <v-row class="row-gap-2">
              <!-- <v-col v-if="action !== CRUD.create" cols="12" sm="6">
                <v-text-field id="id" name="id" label="ID" v-model="inputForm.id" disabled
                  :error-messages="inputForm.errors.id" />
              </v-col> -->
              <v-col cols="12" sm="6">
                <v-combobox
                  id="curriculum"
                  name="curriculum"
                  label="Curriculum"
                  v-model="inputForm.curriculum"
                  :disabled="CRUD.create !== action"
                  :rules="validate('Curriculum', 'required')"
                  :error-messages="inputForm.errors.curriculum"
                  :items="curriculums"
                  item-title="nombre"
                  item-value="id"
                  @update:modelValue="onChange"
                />
              </v-col>
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
              <v-col cols="12">
                <v-textarea
                  id="descripcion"
                  name="descripcion"
                  label="Descripción"
                  v-model="inputForm.descripcion"
                  :disabled="isDisabled"
                />
              </v-col>
              <v-col>
                <v-switch
                  id="previos"
                  name="previos"
                  v-model="inputForm.previos"
                  :disabled="isDisabled"
                  :label="inputForm.previos ? 'Con Ciclos Previos' : 'Sin Requisitos'"
                  color="primary"
                />
              </v-col>
              <v-col
                v-if="CRUD.show !== action && inputForm.previos"
                class="d-flex align-center justify-center"
              >
                <v-btn color="success" small @click="addItem">
                  <v-icon icon="mdi-plus-thick" />
                </v-btn>
              </v-col>
            </v-row>
            <v-row
              v-for="(requi, i) in inputForm.requisitos"
              :key="`${requi.id}_${i}`"
              :class="requi.delete ? 'd-none' : ''"
            >
              <v-col cols="12" sm="5">
                <v-combobox
                  id="curriculum_requi"
                  :name="'curriculum_requi_' + i"
                  label="Curriculum"
                  v-model="requi.curriculum"
                  :rules="validate('Curriculum', 'required')"
                  :error-messages="inputForm.errors.curriculum"
                  :items="curriculums"
                  item-title="nombre"
                  item-value="id"
                  @update:modelValue="(item) => onChangeRequisito(item, i)"
                  :disabled="isDisabled"
                />
              </v-col>
              <v-col cols="8" sm="5">
                <v-select
                  id="ciclo_requi"
                  :name="'ciclo_requi_' + i"
                  label="Ciclo"
                  v-model="requi.ciclo_pre_id"
                  :rules="validate('Ciclo', 'required')"
                  :error-messages="inputForm.errors.ciclo_pre_id"
                  :items="requi.curriculum.ciclos"
                  item-title="nombre"
                  item-value="id"
                  :disabled="isDisabled"
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
