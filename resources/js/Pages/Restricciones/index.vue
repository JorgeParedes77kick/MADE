<script setup>
  import { Link } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { defineProps, onMounted } from 'vue';

  import MainLayout from '../../components/Layout';
  import { truncarTexto } from '../../utils/string';
  dayjs.extend(isBetween);

  const props = defineProps({
    restricciones: Array,
  });
  onMounted(() => {
    // console.log(props.restricciones)
  });

  const headers = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Curriculum', key: 'curriculum.nombre' },
    { title: 'Restricción', key: 'tipo_restriccion.nombre' },
    { title: 'Valor', key: 'valor_restriccion', sortable: false },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const onClickDelete = async (item) => {
    const { isConfirmed } = await Swal.fire({
      title: 'Eliminar Rol',
      text: `Estas seguro de eliminar la restriccion para el curriculum ${item.curriculum.nombre} de ${item.tipo_restriccion.nombre} con valor ${item.valor_restriccion}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      try {
        const response = await axios.delete(route('restricciones.destroy', item.id));
        const index = props.restricciones.findIndex((x) => x.id === item.id);
        if (response?.data?.message) {
          const { message } = response.data;
          Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
          props.restricciones.splice(index, 1);
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
    <v-container>
      <v-card color="background" class="px-4 py-2">
        <v-card-title> RESTRICCIONES </v-card-title>
        <div>
          <v-row>
            <v-col class="d-flex justify-end">
              <Link :href="route('restricciones.create')">
                <v-btn :to="{ name: 'restricciones.create' }" color="success" class="ms-auto">
                  Crear Nueva Restricción
                </v-btn>
              </Link>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-data-table
                :headers="headers"
                :items="restricciones"
                :items-per-page="10"
                class="elevation-1 rounded"
              >
                <template v-slot:no-data>Información no encontrada</template
                ><template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <Link :href="route('restricciones.show', item)">
                      <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link>
                    <Link :href="route('restricciones.edit', item)">
                      <v-btn
                        :to="{ name: 'restricciones.edit', params: { id: item.idCrypt } }"
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
