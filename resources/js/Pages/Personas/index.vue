<script setup>
  import { Link, router } from '@inertiajs/vue3';
  import { computed, defineProps, ref, watch } from 'vue';

  import MainLayout from '../../components/Layout.vue';
  import Pagination from '../../components/Pagination.vue';
  import { excelDescarga, excelError } from '../../utils/blob';

  const props = defineProps({
    genero: { type: Array, default: () => [] },
    estadoCivil: { type: Array, default: () => [] },
    pais: { type: Array, default: () => [] },
    nacionalidad: { type: Array, default: () => [] },
    usuarios: { type: Object, default: () => ({}) },
    form: { type: Object, default: () => ({}) },
  });

  const usuarios = ref(props.usuarios);
  const loading = ref(false);
  const openFilter = ref(false);

  const inputForm = ref({
    buscador: '',
    genero: null,
    estado_civil: null,
    nacionalidad: null,
    pais: null,
    region: null,
    ...props.form,
  });

  const headers = [
    { title: 'Nick', key: 'nick_name' },
    { title: 'Email', key: 'email' },
    { title: 'Nombre', key: 'nombreCompleto' },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];

  const options = ref({ page: 1, perPage: 20 });

  // Computed: Actualiza las regiones según el país seleccionado
  const region = computed(() => {
    const selectedPais = props.pais.find((p) => p.id === inputForm.value.pais);
    return selectedPais ? selectedPais.regiones : [];
  });

  // Actualiza `usuarios` cuando cambian las props
  watch(
    () => props.usuarios,
    (newUsuarios) => {
      usuarios.value = newUsuarios;
      loading.value = false;
    },
  );

  // Actualiza la tabla al cambiar `options` o realizar una búsqueda
  watch(
    () => options.value,
    () => fetchData(),
    { deep: true },
  );

  const fetchData = async () => {
    try {
      loading.value = true;
      console.log('inputForm.value:', inputForm.value);
      await router.get(
        route('personas.index'),
        { page: options.value.page, perPage: options.value.perPage, ...inputForm.value },
        { preserveState: true },
      );
      openFilter.value = false;
    } catch (error) {
      console.error('Error fetching data:', error);
    } finally {
      loading.value = false;
    }
  };

  const validateForm = async (e) => {
    e?.preventDefault?.();
    options.value.page = 1;
    fetchData();
  };

  const onChangePage = (page) => {
    options.value.page = page;
  };

  const onChangePerPage = (perPage) => {
    options.value.perPage = perPage;
    options.value.page = 1; // Reinicia la página al cambiar el tamaño
  };

  const downloadExcel = async (e) => {
    e?.preventDefault();
    loading.value = true;
    try {
      const response = await axios.get(route('exportar.usuarios'), {
        responseType: 'blob',
        params: {
          ...inputForm.value,
        },
      });

      // Llama a la función para manejar la descarga
      await excelDescarga(response.data, 'personas.xlsx');
    } catch (error) {
      // Llama a la función para manejar el error
      excelError(error);
    } finally {
      loading.value = false;
    }
  };
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2" :loading="loading">
        <v-card-title> Personas </v-card-title>

        <!-- Formulario de búsqueda -->
        <v-form @submit="validateForm" ref="formBuscar" lazy-validation>
          <v-row>
            <v-col cols="12">
              <v-text-field
                id="buscador"
                label="Buscador"
                v-model="inputForm.buscador"
                prepend-inner-icon="mdi-magnify"
                clearable
                placeholder="nombre | apellido | correo | dni | nick"
              />
            </v-col>
          </v-row>
          <v-expansion-panels v-model="openFilter" class="mb-2">
            <v-expansion-panel title="Filtros de búsqueda">
              <v-expansion-panel-text>
                <v-row>
                  <v-col cols="12" md="4" sm="6">
                    <v-select
                      id="genero"
                      label="Género"
                      v-model="inputForm.genero"
                      :items="props.genero"
                      item-title="nombre"
                      item-value="id"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" md="4" sm="6">
                    <v-select
                      id="estado_civil"
                      label="Estado Civil"
                      v-model="inputForm.estado_civil"
                      :items="props.estadoCivil"
                      item-title="estado"
                      item-value="id"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" md="4" sm="6">
                    <v-select
                      id="pais"
                      label="País"
                      v-model="inputForm.pais"
                      :items="props.pais"
                      item-title="nombre"
                      item-value="id"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" md="4" sm="6">
                    <v-select
                      id="region"
                      label="Región"
                      v-model="inputForm.region"
                      :items="region"
                      item-title="nombre"
                      item-value="id"
                      clearable
                    />
                  </v-col>
                </v-row>
              </v-expansion-panel-text>
            </v-expansion-panel>
          </v-expansion-panels>

          <v-row>
            <v-col class="d-flex justify-end ga-2">
              <v-btn class="" type="" color="surface" :loading="loading" @click="downloadExcel">
                Exportar
              </v-btn>
              <v-btn class="" type="submit" color="info" :loading="loading"> BUSCAR </v-btn>
            </v-col>
          </v-row>
        </v-form>

        <!-- Tabla -->
        <v-row>
          <v-col>
            <v-data-table
              :headers="headers"
              :items="usuarios.data"
              :loading="loading"
              :items-per-page="options.perPage"
              class="elevation-1 rounded"
            >
              <template #no-data> Información no encontrada </template>
              <template #bottom>
                <Pagination
                  v-bind="usuarios"
                  :onChangePage="onChangePage"
                  :onChangePerPage="onChangePerPage"
                />
              </template>
              <template #item.acciones="{ item }">
                <Link :href="route('personas.edit', item.idCrypt)">
                  <v-btn color="secondary" small> Editar </v-btn>
                </Link>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </MainLayout>
</template>
