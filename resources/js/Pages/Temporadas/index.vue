<script setup>
  import { Link } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { defineProps, onMounted, ref } from 'vue';

  import MainLayout from '../../components/Layout';
  import { FormatFecha } from '../../utils/date';
  import { truncarTexto } from '../../utils/string';

  dayjs.extend(isBetween);

  const props = defineProps({
    temporadas: Object,
    // status: Number,
  });

  onMounted(() => {
    // console.log(props);
  });
  const loading = ref(false);

  const isActive = (fecha_ini, fecha_fin) => {
    const fecha_iniJS = dayjs(fecha_ini);
    const fecha_finJS = dayjs(fecha_fin);
    const currentDate = dayjs();
    //   console.log('currentDate:', currentDate);
    return currentDate.isBetween(fecha_iniJS, fecha_finJS, 'day', '[]');
    //   return 'TRUE';
  };

  const headers = [
    { title: 'Prefijo', key: 'prefijo', fixed: true },
    { title: 'Nombre', key: 'nombre' },
    { title: 'Fecha Inicio', key: 'fecha_inicio', minWidth: '8rem' },
    { title: 'Fecha Fin', key: 'fecha_cierre', minWidth: '8rem' },
    { title: 'Fecha Inscripcion', key: 'inscripcion_inicio', minWidth: '8rem' },
    { title: 'Activo', key: 'activo', sortable: false },
    { title: 'Inscripciones', key: 'activo_inscripcion', sortable: false },
    { title: '', key: 'toggle', sortable: false },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const deleteAccion = async (item) => {
    try {
      const response = await axios.delete(route('temporadas.destroy', item.id));
      const index = props.temporadas.findIndex((x) => x.id === item.id);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
        props.temporadas.splice(index, 1);
      }
    } catch (err) {
      if (err?.response?.data?.server) {
        const { server: msg, message } = err.response.data;
        Swal.fire({ title: 'Error!', text: msg + '\n' + truncarTexto(message), icon: 'error' });
      }
    }
  };

  const onClickDelete = async (item) => {
    const { isConfirmed } = await Swal.fire({
      title: 'Eliminar Temporada',
      text: `Estas seguro de eliminar la temporada ${item.prefijo} ${item.nombre}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      const response = await axios.post(route('temporadas.checkDelete', item.id));
      const { data } = response;
      if (data?.isDelete) deleteAccion(item);
      else {
        const { isConfirmed: reConfirmed } = await Swal.fire({
          title: 'Temporada con información asociada',
          text: `Esta temporada ya cuenta con cursos ${data.withAlumnos ? 'y alumnos' : ''} asociados. Si decides eliminarla, también se eliminará toda la información relacionada, lo que podría generar grandes vacios de información. ¿Estás seguro de que deseas proceder con esta acción?
    `,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Aceptar',
          cancelButtonText: 'Cancelar',
        });
        if (reConfirmed) deleteAccion(item);
      }
    }
  };

  const onClickToggle = async (item, name) => {
    const index = props.temporadas.findIndex((x) => x.id === item.id);
    loading.value = true;

    if (props.temporadas[index].activo && name == 'toggleActivo') {
      const { isConfirmed } = await Swal.fire({
        title: 'Cerrar la temporada',
        text: `Vas cerrar la temporada ${item.prefijo} quieres calificar a sus alumnos?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
      });
      if (isConfirmed) calificarAlumnos(item.id);
    }
    try {
      const response = await axios.post(route(`temporadas.${name}`, item.id));
      const { temporada } = response.data;
      props.temporadas[index] = temporada;
    } catch (error) {
    } finally {
      loading.value = false;
    }
  };
  const calificarAlumnos = async (temporada_id) => {
    try {
      const response = await axios.post(route(`calificar`), { temporada_id });
      if (response?.data?.message) {
        const { message } = response.data;
        Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
      }
    } catch (err) {
      if (err?.response?.data?.server) {
        const { server: msg, message } = err.response.data;
        Swal.fire({
          title: 'Sin Temporada!',
          text: msg + '\n' + truncarTexto(message),
          icon: 'warning',
        });
      }
    }
  };
</script>
<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="px-4 py-2" :disabled="loading" :loading="loading">
        <template v-slot:loader="{ isActive }">
          <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
        </template>
        <v-card-title> TEMPORADAS </v-card-title>
        <div>
          <v-row class="">
            <v-col class="gridBtns" style="">
              <v-btn color="secondary" @click="() => calificarAlumnos()" class="ms-auto">
                Calificar Temporadas
              </v-btn>

              <Link :href="route('temporadas.create')">
                <v-btn :to="{ name: 'temporadas.create' }" color="success" class="ms-auto">
                  Crear Nueva Temporada
                </v-btn>
              </Link>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-data-table
                :headers="headers"
                :items="temporadas"
                :items-per-page="10"
                class="elevation-1 rounded"
                ><template v-slot:no-data>Información no encontrada</template>
                <template v-slot:[`item.fecha_inicio`]="{ item }">
                  {{ FormatFecha(item.fecha_inicio, 3) }}
                </template>
                <template v-slot:[`item.fecha_cierre`]="{ item }">
                  {{ FormatFecha(item.fecha_cierre, 3) }}
                </template>
                <template v-slot:[`item.inscripcion_inicio`]="{ item }">
                  {{ FormatFecha(item.inscripcion_inicio, 3) }} -
                  {{ FormatFecha(item.inscripcion_cierre, 3) }}
                </template>
                <template v-slot:[`item.activo`]="{ item }">
                  <v-chip v-if="item.activo" color="success" variant="flat">Activa</v-chip>
                  <v-chip v-else color="error" variant="flat">Inactiva</v-chip>
                </template>
                <template v-slot:[`item.activo_inscripcion`]="{ item }">
                  <v-chip v-if="item.activo_inscripcion" color="success" variant="flat"
                    >En inscripción</v-chip
                  >
                  <v-chip v-else color="error" variant="flat">Cerrada</v-chip>
                </template>
                <template v-slot:[`item.toggle`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <!-- <Link :href="route('temporadas.show', item)"> -->
                    <v-btn
                      v-if="item.activo"
                      color="error"
                      small
                      variant="outlined"
                      @click="onClickToggle(item, 'toggleActivo')"
                    >
                      cerrar
                    </v-btn>
                    <v-btn
                      v-else
                      color="success"
                      small
                      variant="outlined"
                      @click="onClickToggle(item, 'toggleActivo')"
                    >
                      activar
                    </v-btn>
                    <!-- </Link>
                    <Link :href="route('temporadas.edit', item)"> -->
                    <v-btn
                      v-if="item.activo_inscripcion"
                      color="error"
                      small
                      variant="outlined"
                      @click="onClickToggle(item, 'toggleInscripcion')"
                    >
                      cerrar inscripción
                    </v-btn>
                    <v-btn
                      v-else
                      color="success"
                      small
                      variant="outlined"
                      @click="onClickToggle(item, 'toggleInscripcion')"
                    >
                      activar inscripción
                    </v-btn>
                    <!-- </Link> -->
                  </div>
                </template>
                <template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <Link :href="route('temporadas.show', item)">
                      <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link>
                    <Link :href="route('temporadas.edit', item)">
                      <v-btn
                        :to="{ name: 'temporadas.edit', params: { id: item.idCrypt } }"
                        color="secondary"
                        small
                      >
                        Editar
                      </v-btn></Link
                    >
                    <v-btn color="error" small @click="onClickDelete(item)">Eliminar</v-btn>
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
