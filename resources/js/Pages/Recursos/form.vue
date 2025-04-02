<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { router } from '@inertiajs/vue3';
  import { defineProps, inject, onMounted, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import MainLayout from '../../components/Layout';
  import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    recurso: { type: Object, default: {} },
    curriculums: { type: Array, default: [] },
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    nombre: '',
    link_lectura: '',
    link_escritura: '',
    clase: '',
    ciclo: '',
    ciclo_id: '',
    curriculum: {
      nombre: '',
      ciclo: {},
      ciclos: [],
    },
    ...props.recurso,
  });

  onMounted(() => {
    if (inputForm?.ciclo?.curriculum?.id) {
      const { id } = inputForm?.ciclo?.curriculum;
      const item = props.curriculums.find((x) => x.id == id);
      if (item) inputForm.curriculum = { ...item };
    }
  });
  const form = ref(null);

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
    const routeName = `recursos.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : undefined;
    inputForm.ciclo_id = inputForm.ciclo.id;
    console.log('inputForm:', inputForm);
    try {
      const response = await axios[method](route(routeName, id), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('recursos.index'));
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
  const focus = (state, name) => {
    if (!state && name == 'curriculum' && typeof inputForm.curriculum == 'string') {
      inputForm.curriculum = '';
    } else if (!state && name == 'ciclo' && typeof inputForm.ciclo == 'string') {
      inputForm.ciclo = '';
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
          <ButtonBack :href="route('recursos.index')" /> RECURSOS
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>
        <v-card-subtitle>{{ ACCION[action] }} del Recurso</v-card-subtitle>
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
                /> </v-col
            ></v-row>
            <v-row class="row-gap-2">
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
                  autocomplete="off"
                  @update:focused="(s) => focus(s, 'curriculum')"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-combobox
                  id="ciclo"
                  name="ciclo"
                  label="Ciclo"
                  v-model="inputForm.ciclo"
                  :disabled="CRUD.create !== action || !inputForm.curriculum?.id"
                  :rules="validate('Ciclo', 'required')"
                  :error-messages="inputForm.errors.curriculum?.ciclo"
                  :items="inputForm.curriculum?.ciclos || []"
                  item-title="nombre"
                  item-value="id"
                  autocomplete="off"
                  @update:focused="(s) => focus(s, 'ciclo')"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="nombre"
                  name="nombre"
                  label="Nombre"
                  v-model="inputForm.nombre"
                  :disabled="isDisabled || !inputForm.curriculum?.id"
                  :rules="validate('Nombre', 'required')"
                  :error-messages="inputForm.errors.nombre"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="clase"
                  name="clase"
                  label="Clase"
                  v-model="inputForm.clase"
                  :disabled="isDisabled || !inputForm.curriculum?.id"
                  :rules="validate('Clase', 'required')"
                  :error-messages="inputForm.errors.clase"
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  id="link_lectura"
                  name="link_lectura"
                  label="Link de Lectura"
                  v-model="inputForm.link_lectura"
                  :disabled="isDisabled || !inputForm.curriculum?.id"
                  :rules="validate('Link de Lectura', 'required')"
                  :error-messages="inputForm.errors.link_lectura"
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  id="link_escritura"
                  name="link_escritura"
                  label="Link de Escritura"
                  v-model="inputForm.link_escritura"
                  :disabled="isDisabled || !inputForm.curriculum?.id"
                  :rules="validate('Link de Escritura', 'required')"
                  :error-messages="inputForm.errors.link_escritura"
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
