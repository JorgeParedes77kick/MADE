<script setup>
  import { router, useForm } from '@inertiajs/vue3';
  import { defineProps, inject, onMounted, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';

  import axios from 'axios';
  import MainLayout from '../../components/Layout';
  import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    menu: { type: Object, default: {} },
    menus_padres: { type: Array },
    routes: { type: Array },
    status: String,
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    id: '',
    menu_padre_id: '',
    nombre: '',
    url_ref: '',
    icon: '',
    orden: '0',
    ...props.menu,
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
    const routeName = `menu.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : undefined;

    try {
      const response = await axios[method](route(routeName, id), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('menu.index'));
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

  let routesUri = [];

  onMounted(() => {
    /*console.log("routes ", JSON.stringify(props.routes));*/
    routesUri.push('#');
    for (let i = 0, length = props.routes.length; i < length; i++) {
      routesUri.push(props.routes[i].URI);
    }
    /*console.log("routesUri ", JSON.stringify(routesUri));*/
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
          <ButtonBack :href="route('menu.index')" /> Menu
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>
        <v-card-subtitle>{{ ACCION[action] }} del Menu</v-card-subtitle>
        <v-card-text>
          <v-form @submit="validateForm" ref="form" lazy-validation>
            <v-row>
              <v-col cols="4">
                <v-select
                  v-model="inputForm.menu_padre_id"
                  name="menu_padre_id"
                  label="Menu Padre"
                  :items="menus_padres"
                  item-title="nombre"
                  item-value="id"
                  variant="outlined"
                  class="rounded-l"
                  clearable
                  tabindex="1"
                ></v-select>
              </v-col>
              <v-col cols="4">
                <v-text-field
                  id="orden"
                  name="orden"
                  label="orden"
                  v-model="inputForm.orden"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.orden"
                  class="rounded-l"
                  variant="outlined"
                  type="number"
                  autocomplete="off"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="4">
                <v-text-field
                  id="nombre"
                  name="nombre"
                  label="Nombre"
                  v-model="inputForm.nombre"
                  :disabled="isDisabled"
                  :rules="validate('Nombre', 'required')"
                  :error-messages="inputForm.errors.nombre"
                  class="rounded-l"
                  variant="outlined"
                  clearable
                  tabindex="2"
                  autocomplete="off"
                />
              </v-col>
              <v-col cols="4">
                <v-autocomplete
                  id="url_ref"
                  name="url_ref"
                  label="Url"
                  v-model="inputForm.url_ref"
                  placeholder="Seleccione el simbolo # para Menus Principales"
                  :disabled="isDisabled"
                  :rules="validate('Url', 'required')"
                  :error-messages="inputForm.errors.url_ref"
                  class="rounded-l"
                  variant="outlined"
                  clearable
                  tabindex="3"
                  :items="routesUri"
                  autocomplete="off"
                ></v-autocomplete>
              </v-col>

              <v-col cols="4">
                <v-text-field
                  id="icon"
                  name="icon"
                  label="Icono"
                  v-model="inputForm.icon"
                  :error-messages="inputForm.errors.icon"
                  class="rounded-l"
                  variant="outlined"
                  clearable
                  tabindex="4"
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
