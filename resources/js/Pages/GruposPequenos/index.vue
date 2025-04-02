<script setup>
  import { Link, router, usePage } from '@inertiajs/vue3';

  import dayjs from 'dayjs';
  import customParseFormat from 'dayjs/plugin/customParseFormat';
  import isBetween from 'dayjs/plugin/isBetween';

  import { defineProps, onMounted, ref, watch } from 'vue';
  import { excelDescarga, excelError } from '../../utils/blob';

  import MainLayout from '../../components/Layout';
  import Pagination from '../../components/Pagination.vue';

  dayjs.extend(isBetween);
  dayjs.extend(customParseFormat);
  const page = usePage().props;

  const props = defineProps({
    temporadas: { type: Array, default: [] },
    curriculums: { type: Array, default: [] },
    ciclos: { type: Array, default: [] },
    dias: { type: Array, default: [] },
    action: String,
    gruposPequenos: { type: Object, default: () => ({}) },
    form: { type: Object, default: () => ({}) },
  });

  const loading = ref(false);
  const gruposPequenos = ref(props.gruposPequenos);

  watch(
    () => props.gruposPequenos,
    (newGruposPequenos) => {
      gruposPequenos.value = newGruposPequenos;
      loading.value = false;
    },
  );
  const headers = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Temporada', key: 'temporada.prefijo' },
    { title: 'Curriculum', key: 'ciclo.curriculum.nombre' },
    { title: 'Ciclo', key: 'ciclo.nombre' },
    { title: 'Monitores', key: 'monitores', minWidth: '15rem' },
    { title: 'Lideres', key: 'lideres', minWidth: '15rem' },
    { title: 'Día', key: 'dia_curso' },
    { title: 'Hora', key: 'hora', minWidth: '6rem' },
    { title: 'Obs', key: 'info_adicional', minWidth: '10rem', sortable: false },
    // { title: 'Roles', key: 'roles', width: '20rem', sortable: false },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];

  const openFilter = ref(false);
  const searchForm = ref({
    temporadas: [],
    curriculums: [],
    ciclos: [],
    dias: [],
    nombre: '',
    ...props.form,
  });

  //   const filteredItems = computed(() => {
  //     return props.gruposPequenos.filter((item) => {
  //       const { temporadas, curriculums, ciclos, dias, nombre } = searchForm.value;
  //       const nombreCast = nombre.toLowerCase();

  //       if (temporadas.length > 0 && !temporadas.includes(item.temporada.prefijo)) return false;
  //       if (curriculums.length > 0 && !curriculums.includes(item.ciclo.curriculum.nombre))
  //         return false;
  //       if (ciclos.length > 0 && !ciclos.includes(item.ciclo.nombre)) return false;
  //       if (dias.length > 0 && !dias.includes(item.dia_curso)) return false;

  //       const inscripciones = [...item.monitores, ...item.lideres].map((x) => {
  //         return {
  //           nombre: x.persona.nombre.toLowerCase(),
  //           apellido: x.persona.apellido.toLowerCase(),
  //           email: x.email.toLowerCase(),
  //           fullName: `${x.persona.nombre} ${x.persona.apellido}`.toLowerCase(),
  //         };
  //       });

  //       const matchNombre =
  //         nombreCast.length === 0 ||
  //         inscripciones.some((x) =>
  //           [x.nombre, x.apellido, x.email, x.fullName].some((y) => {
  //             return y.includes(nombreCast);
  //           }),
  //         );

  //       return matchNombre;
  //     });
  //   });
  //   watch(searchForm, () => {}, { deep: true });

  const onClickClean = (e) => {
    e.preventDefault();
    searchForm.value = { temporadas: [], curriculums: [], ciclos: [], dias: [], nombre: '' };
    openFilter.value = false;
  };

  // Actualiza la tabla al cambiar `options` o realizar una búsqueda

  const fetchData = async () => {
    try {
      loading.value = true;
      const { page, perPage } = options.value;
      const routeEnd = props.action === 'horarios' ? 'horarios' : 'index';
      await router.get(
        route(`grupos-pequenos.${routeEnd}`),
        { page, perPage, ...searchForm.value },
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

  const onClickDelete = async (item) => {
    console.log('item:', item);
    const { isConfirmed } = await Swal.fire({
      title: 'Eliminar Horario Grupo Pequeño',
      text: `Estas seguro de eliminar el horario #${item.id}.\n Día ${item.dia_curso} a las ${dayjs(item.hora_inicio, 'HH:mm:ss').format('HH:mm [hrs]')} a
    ${dayjs(item.hora_fin, 'HH:mm:ss').format('HH:mm [hrs]')}, para el grupo pequeno ${item.ciclo.curriculum.nombre}
     en el ciclo ${item.ciclo.nombre}.`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      try {
        const response = await axios.delete(route('grupos-pequenos.destroy', item.id));
        const index = props.gruposPequenos.findIndex((x) => x.id === item.id);
        if (response?.data?.message) {
          const { message } = response.data;
          await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
          props.gruposPequenos.splice(index, 1);
        }
      } catch (err) {
        if (err?.response?.data?.server) {
          const { server: msg, message } = err.response.data;
          Swal.fire({ title: 'Error!', text: msg + '\n' + truncarTexto(message), icon: 'error' });
        }
      }
    }
  };

  const options = ref({ page: 1, perPage: 20 });

  watch(
    () => options.value,
    () => fetchData(),
    { deep: true },
  );
  const onChangePage = (page) => {
    options.value.page = page;
  };
  const onChangePerPage = (perPage) => {
    options.value.perPage = perPage;
    options.value.page = 1; // Reinicia la página al cambiar el tamaño
  };

  const coorNodata = ref(false);
  onMounted(() => {
    const { rol_selected } = page?.auth;
    const { curriculums } = props;
    if (rol_selected == 2 && curriculums.length === 0) coorNodata.value = true;
  });

  const downloadExcel = async (e) => {
    e?.preventDefault();
    loading.value = true;

    try {
      const response = await axios.get(route('exportar.grupos-pequenos'), {
        responseType: 'blob',
        params: {
          ...searchForm.value,
          temporadasActivas: props.action === 'horarios',
        },
      });

      // Llama a la función para manejar la descarga
      await excelDescarga(response.data, 'grupos-pequenos.xlsx');
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
    <v-container fluid :loading="loading">
      <v-card color="background" class="px-4 py-2">
        <v-card-title>
          GRUPOS PEQUEÑOS - {{ action === 'horarios' ? 'HORARIOS' : 'HISTÓRICO' }}</v-card-title
        >
        <div>
          <v-row class="pb-2">
            <v-col class="gridBtns">
              <v-btn class="" type="" color="surface" :loading="loading" @click="downloadExcel">
                Exportar
              </v-btn>
              <Link
                v-if="action === 'horarios' && !coorNodata"
                :href="route('grupos-pequenos.create')"
              >
                <v-btn color="success" class="ms-auto"> Nuevo Horario </v-btn>
              </Link>
            </v-col>
          </v-row>
        </div>
        <v-expansion-panels v-model="openFilter">
          <v-expansion-panel>
            <v-expansion-panel-title> Filtros de búsqueda </v-expansion-panel-title>
            <v-expansion-panel-text>
              <v-form @submit="validateForm" lazy-validation>
                <v-row class="row-gap-2">
                  <v-col cols="12">
                    <!-- <v-text-field
                      v-model="searchForm.nombre"
                      label="Ingrese información a buscar"
                      hide-details
                    ></v-text-field> -->
                  </v-col>

                  <v-col cols="12" sm="3">
                    <v-select
                      id="Temporada"
                      name="Temporada"
                      label="Temporada"
                      chips
                      multiple
                      closable-chips
                      v-model="searchForm.temporadas"
                      :items="temporadas"
                      item-title="prefijo"
                      item-value="id"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" sm="3">
                    <v-select
                      id="Curriculum"
                      name="Curriculum"
                      label="Curriculum"
                      chips
                      multiple
                      closable-chips
                      v-model="searchForm.curriculums"
                      :items="curriculums"
                      item-title="nombre"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" sm="3">
                    <v-select
                      id="Ciclo"
                      name="Ciclo"
                      label="Ciclo"
                      chips
                      multiple
                      closable-chips
                      v-model="searchForm.ciclos"
                      :items="ciclos"
                      item-title="nombre"
                      clearable
                    />
                  </v-col>
                  <v-col cols="12" sm="3">
                    <v-select
                      id="Dia"
                      name="Dia"
                      label="Día"
                      chips
                      multiple
                      closable-chips
                      v-model="searchForm.dias"
                      :items="dias"
                      clearable
                    />
                  </v-col>
                  <v-col class="d-flex justify-end ga-2">
                    <v-btn size="small" @click="onClickClean">Limpiar</v-btn>
                    <v-btn size="small" type="submit" color="info" :loading="loading">
                      BUSCAR
                    </v-btn>
                  </v-col>
                </v-row>
              </v-form>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>

        <div>
          <v-row>
            <v-col>
              <v-data-table
                :headers="headers"
                :items="gruposPequenos.data"
                :loading="loading"
                :items-per-page="options.perPage"
                class="elevation-1 rounded"
              >
                <template #no-data>
                  {{
                    coorNodata
                      ? 'Ha este coordinador no se le asignado ningún curriculum'
                      : 'Información no encontrada'
                  }}
                </template>
                <template #bottom>
                  <Pagination
                    v-bind="gruposPequenos"
                    :onChangePage="onChangePage"
                    :onChangePerPage="onChangePerPage"
                  />
                </template>
                <template v-slot:[`item.monitores`]="{ item }">
                  <p v-for="monitor in item.monitores" :key="monitor.id">
                    {{ monitor?.nombreCompleto }}
                  </p>
                </template>
                <template v-slot:[`item.lideres`]="{ item }">
                  <p v-for="lider in item.lideres" :key="lider.id">
                    {{ lider?.nombreCompleto }}
                  </p>
                </template>
                <template v-slot:[`item.hora`]="{ item }">
                  <p v-if="item.dia_curso == 'none'">none</p>
                  <p v-else>
                    {{ dayjs(item.hora_inicio, 'HH:mm:ss').format('HH:mm [hrs]') }}
                    {{ dayjs(item.hora_fin, 'HH:mm:ss').format('HH:mm [hrs]') }}
                  </p>
                </template>
                <template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <Link :href="route('grupos-pequenos.show', item)">
                      <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link>
                    <Link v-if="item.temporada.activo" :href="route('grupos-pequenos.edit', item)">
                      <v-btn color="secondary" small> Editar </v-btn>
                    </Link>
                    <v-btn
                      v-if="item.alumnos_count === 0"
                      color="error"
                      small
                      @click="onClickDelete(item)"
                      v-tooltip:top="'Solo se pueden eliminar los que no tienen inscripciones'"
                      >Eliminar
                    </v-btn>
                  </div>
                </template>
              </v-data-table>
            </v-col>
          </v-row>
        </div>
      </v-card>
    </v-container>
  </MainLayout>
</template>
