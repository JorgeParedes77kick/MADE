<script setup>
  import { Link } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { defineProps, onMounted } from 'vue';

  import MainLayout from '../../components/Layout';
  import { truncarTexto } from '../../utils/string';
  dayjs.extend(isBetween);

  const props = defineProps({
    roles: Array,
  });
  onMounted(() => {});

  const headers = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Nombre', key: 'nombre' },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const onClickDelete = async (item) => {
    console.log('item:', item);
    const { isConfirmed } = await Swal.fire({
      title: 'Eliminar Rol',
      text: `Estas seguro de eliminar el rol ${item.nombre}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      try {
        const response = await axios.delete(route('roles.destroy', item.id));
        const index = props.roles.findIndex((x) => x.id === item.id);
        if (response?.data?.message) {
          const { message } = response.data;
          Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
          props.roles.splice(index, 1);
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
        <v-card-title> ROLES </v-card-title>
        <div>
          <v-row>
            <v-col class="d-flex justify-end">
              <Link :href="route('roles.create')">
                <v-btn :to="{ name: 'roles.create' }" color="success" class="ms-auto">
                  Crear Nuevo Rol
                </v-btn>
              </Link>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-data-table
                :headers="headers"
                :items="roles"
                :items-per-page="10"
                class="elevation-1 rounded"
              >
                <template v-slot:no-data>Información no encontrada</template
                ><template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <Link :href="route('roles.show', item)">
                      <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link>
                    <Link :href="route('roles.edit', item)">
                      <v-btn
                        :to="{ name: 'roles.edit', params: { id: item.idCrypt } }"
                        color="secondary"
                        small
                      >
                        Editar
                      </v-btn>
                    </Link>
                    <v-btn v-if="item.id > 5" color="error" small @click="onClickDelete(item)"
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
