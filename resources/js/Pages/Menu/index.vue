<script setup>
  import { Link } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { defineProps, onMounted } from 'vue';
  import MainLayout from '../../components/Layout.vue';

  import { truncarTexto } from '../../utils/string';
  dayjs.extend(isBetween);

  const props = defineProps({
    menus: Array,
    menus_padres: Array,
  });
  onMounted(() => {});

  const headers = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Menu Padre', key: 'menu_padre_id', sortable: false },
    { title: 'Nombre', key: 'nombre' },
    { title: 'Ruta', key: 'url_ref', sortable: false },
    { title: 'Icono', key: 'icon', sortable: false },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const onClickDelete = async (item) => {
    console.log('item:', item);
    const { isConfirmed } = await Swal.fire({
      title: 'Eliminar Menu',
      text: `Estas seguro de eliminar el menu ${item.nombre}?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Aceptar',
      cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {
      try {
        const response = await axios.delete(route('menu.destroy', item.id));
        const index = props.menus.findIndex((x) => x.id === item.id);
        if (response?.data?.message) {
          const { message } = response.data;
          Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
          props.menus.splice(index, 1);
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
        <v-card-title> Menus </v-card-title>
        <div>
          <v-row>
            <v-col class="d-flex justify-end">
              <Link :href="route('menu.create')">
                <v-btn :to="{ name: 'menu.create' }" color="success" class="ms-auto">
                  Crear Nuevo Menu
                </v-btn>
              </Link>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-data-table
                :headers="headers"
                :items="menus"
                :items-per-page="10"
                class="elevation-1 rounded"
              >
                <template v-slot:no-data>Información no encontrada</template
                ><template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <Link :href="route('menu.show', item)">
                      <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link>
                    <Link :href="route('menu.edit', item)">
                      <v-btn
                        :to="{ name: 'menu.edit', params: { id: item.id } }"
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
