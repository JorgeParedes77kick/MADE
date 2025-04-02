<script setup>
  import { Link } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { defineProps, onMounted, ref } from 'vue';

  import MainLayout from '../../components/Layout';

  import { excelDescarga, excelError } from '../../utils/blob';
  import { truncarTexto } from '../../utils/string';

  dayjs.extend(isBetween);

  const props = defineProps({
    curriculums: Array,
    status: Number,
  });

  onMounted(() => {
    console.log(props);
  });
  const loading = ref(false);

  const headers = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Nombre', key: 'nombre' },
    { title: 'Libro', key: 'libro' },
    { title: 'Descripcion', key: 'descripcion', minWidth: '20rem' },
    { title: 'Clases', key: 'cantidad_clases' },
    { title: 'Cupos', key: 'cantidad_cupos' },
    // { title: 'ID', key: 'imagen' },
    // { title: 'ID', key: 'imagen_landing' },
    { title: 'Status', key: 'activo' },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const onClickDelete = async (item) => {
    const { isConfirmed } = await Swal.fire({
      title: 'Eliminar Curriculum',
      text: `Estas seguro de eliminar el curriculum?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      try {
        const response = await axios.delete(route('curriculums.destroy', item.id));
        const index = props.curriculums.findIndex((x) => x.id === item.id);
        if (response?.data?.message) {
          const { message } = response.data;
          await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
          props.curriculums.splice(index, 1);
        }
      } catch (err) {
        if (err?.response?.data?.server) {
          const { server: msg, message } = err.response.data;
          Swal.fire({ title: 'Error!', text: msg + '\n' + truncarTexto(message), icon: 'error' });
        }
      }
    }
  };
  const downloadExcel = async (e) => {
    e?.preventDefault();
    loading.value = true;
    try {
      const response = await axios.get(route('exportar.curriculums'), {
        responseType: 'blob',
      });

      // Llama a la función para manejar la descarga
      await excelDescarga(response.data, 'curriculums.xlsx');
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
      <v-card color="background" class="px-4 py-2" :loading="loading">
        <v-card-title>ESTADOS DE CURRICULUM </v-card-title>

        <v-row>
          <v-col class="gridBtns">
            <v-btn class="" type="" color="surface" :loading="loading" @click="downloadExcel">
              Exportar
            </v-btn>
            <Link :href="route('curriculums.create')">
              <v-btn :to="{ name: 'curriculums.create' }" color="success" class="ms-auto">
                Crear Nuevo Curriculum
              </v-btn>
            </Link>
          </v-col>
        </v-row>
        <v-row justify="center">
          <v-col>
            <v-data-table
              :headers="headers"
              :items="curriculums"
              :items-per-page="10"
              class="elevation-1 rounded"
            >
              <template v-slot:no-data>Información no encontrada</template
              ><template v-slot:[`item.descripcion`]="{ item }">
                {{ truncarTexto(item.descripcion, 100) }}
              </template>
              <template v-slot:[`item.activo`]="{ item }">
                <v-chip v-if="item.activo" color="success">Activa</v-chip>
                <v-chip v-else color="error">Inactiva</v-chip>
              </template>
              <template v-slot:[`item.acciones`]="{ item }">
                <div class="d-flex inline-flex ga-2">
                  <Link :href="route('curriculums.show', item)">
                    <v-btn as="v-btn" color="info" small> Ver </v-btn>
                  </Link>
                  <Link :href="route('curriculums.edit', item)">
                    <v-btn
                      :to="{ name: 'curriculums.edit', params: { id: item.idCrypt } }"
                      color="secondary"
                      small
                    >
                      Editar
                    </v-btn>
                  </Link>
                  <v-btn color="error" small @click="onClickDelete(item)">Eliminar </v-btn>
                </div>
              </template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </MainLayout>
</template>
