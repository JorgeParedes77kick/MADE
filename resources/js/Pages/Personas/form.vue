<script setup>
  import { useForm, usePage } from '@inertiajs/vue3';

  import { defineProps, inject, onMounted, ref, watch } from 'vue';

  import ButtonBack from '../../components/ButtonBack';
  import MainLayout from '../../components/Layout.vue';
  import { previewImage } from '../../utils/image';

  const validate = inject('$validation');
  const pageProps = usePage().props;

  const props = defineProps({
    usuario: { type: Object, default: {} },
    generos: { type: Array, default: [] },
    estadosCivil: { type: Array, default: [] },
    paises: { type: Array, default: [] },
    nacionalidades: { type: Array, default: [] },
    action: { type: String, default: '' },
  });
  const loading = ref(false);
  const isDisabled = ref(true);
  const Editar = ref(false);
  const miPerfil = ref(false);
  const regiones = ref([]);

  onMounted(() => {
    const { usuario } = props;
    miPerfil.value = pageProps.auth.user.id === usuario.id;
    if (usuario?.persona?.pais) {
      regiones.value = usuario.persona.pais.regiones;
    }
  });
  const inputForm = useForm({
    email: '',
    nick_name: '',
    newContrasena: '',
    repitaContrasena: '',
    ...props.usuario?.persona,
    ...props.usuario,
  });

  const form = ref(null);

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
  };
  const onChangeFile = () => {
    inputForm.imagen = '';
  };

  const submit = async () => {
    delete inputForm?.persona;
    try {
      const response = await axios.put(
        route('personas.update', { persona: props.usuario.idCrypt }),
        inputForm,
      );
      if (response?.data?.message) {
        const { message, persona } = response.data;
        inputForm.defaults({ ...persona });
        inputForm.reset();
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
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

  watch(
    () => Editar.value,
    (newPage, oldPage) => {
      isDisabled.value = !newPage;
      if (!newPage) {
        inputForm.reset();
        regiones.value = props.usuario?.persona?.pais?.regiones || [];
      }
    },
  );

  const onChangePais = (idPais) => {
    if (idPais) {
      regiones.value = props.paises.find((x) => x.id === idPais)?.regiones || [];
    } else regiones.value = [];
    inputForm.region_id = null;
  };
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <template v-slot:loader="{ isActive }">
          <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
        </template>
        <v-card-title>
          <ButtonBack :href="miPerfil ? route('home') : undefined" />
          {{
            miPerfil
              ? 'Mi Perfil'
              : `Nick:
                    ${props.usuario.nick_name}`
          }}
        </v-card-title>
        <v-card-subtitle>
          <v-switch
            id="isDisabled"
            name="isDisabled"
            v-model="Editar"
            :label="Editar ? 'Cancelar Edición' : 'Editar Perfil'"
            color="primary"
          />
        </v-card-subtitle>
        <v-card-text>
          <!-- <v-expand-x-transition> -->
          <v-form @submit="validateForm" ref="form" lazy-validation>
            <v-row class="justify-center">
              <v-col cols="12" sm="8" md="6">
                <v-img
                  v-if="inputForm.imagenFile"
                  :src="previewImage(inputForm.imagenFile)"
                  max-width="150"
                  max-height="150"
                  class="d-block mx-auto"
                />
                <v-img
                  v-if="typeof inputForm.imagen == 'string' && inputForm.imagen.length > 0"
                  :src="`/storage/img/perfil/${inputForm.imagen}`"
                  max-width="150"
                  max-height="150"
                  class="d-block mx-auto"
                />
              </v-col>
            </v-row>
            <v-row class="justify-center">
              <v-col cols="12" sm="8" md="6">
                <v-file-input
                  id="imagen"
                  name="imagen"
                  label="Imagen"
                  v-model="inputForm.imagenFile"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.imagen"
                  accept="image/*"
                  prepend-inner-icon="mdi-camera"
                  @update:modelValue="onChangeFile"
                  :rules="[
                    (value) =>
                      value ||
                      !value.length ||
                      value[0].size < 1000000 ||
                      'Tamaño debe ser menor a 1 MB!',
                  ]"
                />
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col>
                <v-label class="text-input">
                  <v-icon icon="mdi-card-account-details-star-outline" class="mr-2 mb-2" />
                  Datos del Usuario
                </v-label>
                <v-divider />
              </v-col>
            </v-row>
            <v-row class="row-gap-2">
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="Numero de Documento"
                  name="Numero de Documento"
                  label="Numero de Documento"
                  v-model="inputForm.dni"
                  color="input"
                  disabled
                  :rules="validate('dni', 'required')"
                  :error-messages="inputForm.errors.dni"
                  prepend-inner-icon="mdi-lock-outline"
              /></v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="email"
                  name="email"
                  label="email"
                  v-model="inputForm.email"
                  color="input"
                  disabled
                  :rules="validate('email', 'required')"
                  :error-messages="inputForm.errors.email"
                  prepend-inner-icon="mdi-lock-outline"
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="telefono"
                  name="telefono"
                  label="Telefono"
                  v-model="inputForm.telefono"
                  color="input"
                  :disabled="isDisabled"
                  :rules="validate('Telefono', 'required')"
                  :error-messages="inputForm.errors.telefono"
                />
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col>
                <v-label class="text-input">
                  <v-icon icon="mdi-notebook-edit-outline" class="mr-2 mb-2" />
                  Datos Personales
                </v-label>
                <v-divider />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="nombre"
                  name="nombre"
                  label="Nombre"
                  v-model="inputForm.nombre"
                  color="input"
                  :disabled="isDisabled"
                  :rules="validate('Nombre', 'required')"
                  :error-messages="inputForm.errors.nombre"
                  clearable
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="apellido"
                  name="apellido"
                  label="Apellido"
                  v-model="inputForm.apellido"
                  color="input"
                  :disabled="isDisabled"
                  :rules="validate('Apellido', 'required')"
                  :error-messages="inputForm.errors.apellido"
                  clearable
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="fecha_nacimiento"
                  name="fecha_nacimiento"
                  label="Fecha de nacimiento"
                  v-model="inputForm.fecha_nacimiento"
                  color="input"
                  type="date"
                  :disabled="isDisabled"
                  :rules="validate('Fecha de nacimiento', 'required')"
                  :error-messages="inputForm.errors.fecha_nacimiento"
                  clearable
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-select
                  id="genero_id"
                  name="genero_id"
                  label="Genero"
                  v-model="inputForm.genero_id"
                  color="input"
                  :disabled="isDisabled"
                  :rules="validate('genero', 'required')"
                  :error-messages="inputForm.errors.genero_id"
                  :items="generos"
                  item-title="nombre"
                  item-value="id"
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-select
                  id="estado_civil_id"
                  name="estado_civil_id"
                  label="Estado Civil"
                  v-model="inputForm.estado_civil_id"
                  color="input"
                  :disabled="isDisabled"
                  :rules="validate('estado civil', 'required')"
                  :error-messages="inputForm.errors.estado_civil_id"
                  :items="estadosCivil"
                  item-title="estado"
                  item-value="id"
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="ocupacion"
                  name="ocupacion"
                  label="Ocupación"
                  v-model="inputForm.ocupacion"
                  color="input"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.ocupacion"
                />
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col>
                <v-label class="text-input">
                  <v-icon icon="mdi-map-marker-radius" class="mr-2 mb-2" />
                  Ubicacíon
                </v-label>
                <v-divider />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="4" sm="6">
                <v-select
                  id="nacionalidad_id"
                  name="nacionalidad_id"
                  label="Nacionalidad"
                  v-model="inputForm.nacionalidad_id"
                  color="input"
                  :disabled="isDisabled"
                  :rules="validate('Nacionalidad', 'required')"
                  :error-messages="inputForm.errors.nacionalidad_id"
                  :items="nacionalidades"
                  item-title="nombre"
                  item-value="id"
                  clearable
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-select
                  id="pais"
                  name="pais"
                  label="País donde te encuentras"
                  v-model="inputForm.pais"
                  color="input"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.pais"
                  :items="paises"
                  item-title="nombre"
                  item-value="id"
                  clearable
                  @update:modelValue="onChangePais"
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-select
                  id="region_id"
                  name="region_id"
                  label="Región donde te encuentras"
                  v-model="inputForm.region_id"
                  color="input"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.region"
                  :items="regiones"
                  item-title="nombre"
                  item-value="id"
                  clearable
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="ciudad"
                  name="ciudad"
                  label="Ciudad/Comuna"
                  v-model="inputForm.ciudad"
                  color="input"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.ciudad"
                  clearable
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="direccion"
                  name="direccion"
                  label="Dirección"
                  v-model="inputForm.direccion"
                  color="input"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.direccion"
                  clearable
                />
              </v-col>
            </v-row>
            <v-row no-gutters>
              <v-col>
                <v-label class="text-input">
                  <v-icon icon="mdi-key-outline" class="mr-2 mb-2" />
                  Contraseña
                </v-label>
                <v-divider />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="newContrasena"
                  name="newContrasena"
                  label="Nueva Contraseña"
                  placeholder="Nueva Contraseña"
                  v-model="inputForm.newContrasena"
                  color="input"
                  type="password"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.newContrasena"
                  clearable
                  autocomplete="off"
                />
              </v-col>
              <v-col cols="12" md="4" sm="6">
                <v-text-field
                  id="repitaContrasena"
                  name="repitaContrasena"
                  label="Repita Contraseña"
                  placeholder="Repita Nueva Contraseña"
                  v-model="inputForm.repitaContrasena"
                  color="input"
                  type="password"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.repitaContrasena"
                  clearable
                  autocomplete="off"
                />
              </v-col> </v-row
            ><v-row class="my-3" v-if="!isDisabled">
              <v-col cols="12" class="d-flex">
                <v-btn class="ms-auto" type="submit" color="primary" :loading="loading">
                  Actualizar
                </v-btn>
              </v-col>
            </v-row>
          </v-form>
          <!-- </v-expand-x-transition> -->
        </v-card-text>
      </v-card>
    </v-container>
  </MainLayout>
</template>
<style></style>
