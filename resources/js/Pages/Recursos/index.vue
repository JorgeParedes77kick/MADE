<script setup>
  import { Link } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { defineProps } from 'vue';

  import MainLayout from '../../components/Layout';
  dayjs.extend(isBetween);

  const props = defineProps({
    recursos: Array,
  });

  const headers = [
    // { title: 'ID', key: 'id', fixed: true },
    { title: 'Curriculum', key: 'ciclo.curriculum.nombre' },
    { title: 'Ciclo', key: 'ciclo.nombre' },
    { title: 'Nombre (opc)', key: 'nombre' },
    { title: 'Clase', key: 'clase' },
    { title: 'Link Escritura', key: 'link_escritura' },
    { title: 'Link Lectura', key: 'link_lectura' },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const onClickDelete = async (item) => {
    console.log('item:', item);
    const { isConfirmed } = await SwalVuetify.fire({
      title: 'Eliminar Recursos',
      text: `Estas seguro de eliminar el recurso del ${item.ciclo.curriculum.nombre} del ${item.ciclo.nombre} de la clase ${item.clase}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      try {
        const response = await axios.delete(route('recursos.destroy', item.id));
        const index = props.recursos.findIndex((x) => x.id === item.id);
        if (response?.data?.message) {
          const { message } = response.data;
          Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
          props.recursos.splice(index, 1);
        }
      } catch (err) {
        console.log('err:', err);
        if (err?.response?.data?.server) {
          const { server: msg, message } = err.response.data;
          Swal.fire({ title: 'Error!', text: msg + '\n' + truncarTexto(message), icon: 'error' });
        }
      }
    }
  };
</script>
<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="px-4 py-2">
        <v-card-title> RECURSOS </v-card-title>
        <div>
          <v-row>
            <v-col class="d-flex justify-end">
              <Link :href="route('recursos.create')">
                <v-btn :to="{ name: 'recursos.create' }" color="success" class="ms-auto">
                  Crear Nuevo Recurso
                </v-btn>
              </Link>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-data-table
                :headers="headers"
                :items="recursos"
                :items-per-page="25"
                class="elevation-1 rounded"
              >
                <template v-slot:no-data>Información no encontrada</template>
                <template v-slot:[`item.link_escritura`]="{ item }">
                  <a v-if="item.link_escritura" :href="item.link_escritura" target="_blank"
                    >Link Escritura</a
                  >
                </template>
                <template v-slot:[`item.link_lectura`]="{ item }">
                  <a v-if="item.link_lectura" :href="item.link_lectura" target="_blank"
                    >Link Lectura</a
                  >
                </template>
                <template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <Link :href="route('recursos.show', item)">
                      <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link>
                    <Link :href="route('recursos.edit', item)">
                      <v-btn
                        :to="{ name: 'recursos.edit', params: { id: item.idCrypt } }"
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
        </div>
      </v-card>
    </v-container>
  </MainLayout>
</template>
