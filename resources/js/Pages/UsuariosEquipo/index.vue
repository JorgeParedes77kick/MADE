<script setup>
  import { Link } from '@inertiajs/vue3';
  import dayjs from 'dayjs';
  import isBetween from 'dayjs/plugin/isBetween';
  import { computed, defineProps, onMounted, ref } from 'vue';

  import MainLayout from '../../components/Layout';
  import { excelDescarga, excelError } from '../../utils/blob';
  dayjs.extend(isBetween);

  const props = defineProps({
    usuarios: Array,
    roles: Array,
  });
  onMounted(() => {
    // console.log(props.usuarios)
  });
  const loading = ref(false);

  const headers = [
    // { title: 'ID', key: 'id' },
    // { title: 'Nick', key: 'nick_name' },
    { title: 'Nombre', key: 'persona' },
    { title: 'Correo', key: 'email' },
    { title: 'Roles', key: 'roles', width: '20rem', sortable: false },
    { title: 'Tiene curriculums', key: 'curriculums', sortable: false },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const search = ref('');
  const nameFilter = ref([]);

  const filteredItems = computed(() => {
    return props.usuarios.filter((item) => {
      // Filtro por nombre
      const roles = item.roles.map((x) => x.id);
      const nameMatch =
        nameFilter.value.length === 0 || roles.some((x) => nameFilter.value.includes(x));
      // const roles = item.roles.map(x => x.nombre.toLowerCase())
      // const nameMatch = roles.some(x => x.includes(nameFilter.value.toLowerCase()));

      const searchL = search.value.toLowerCase();
      const fullName = `${item.persona.nombre} ${item.persona.apellido}`.toLowerCase();
      // Filtro global
      const globalMatch = search.value
        ? item.nick_name.toLowerCase().includes(searchL) ||
          item.email.toLowerCase().includes(searchL) ||
          fullName.includes(searchL)
        : true;

      // // Retornar elementos que coincidan con ambos filtros
      return nameMatch && globalMatch;
    });
  });
  // const onClickDelete = async (item) => {
  //   console.log('item:', item);
  //   const { isConfirmed } = await Swal.fire({
  //     title: 'Eliminar Rol',
  //     text: `Estas seguro de eliminar el rol ${item.nombre}?`,
  //     icon: 'question',
  //     showCancelButton: true,
  //     confirmButtonText: 'Aceptar',
  //     cancelButtonText: 'Cancelar',
  //   });
  //   if (isConfirmed) {
  //     try {
  //       const response = await axios.delete(route('usuarios-equipo.destroy', item.id));
  //       const index = props.usuarios - equipo.findIndex(x => x.id === item.id)
  //       if (response?.data?.message) {
  //         const { message } = response.data;
  //         Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
  //         props.usuarios - equipo.splice(index, 1)
  //       }
  //     } catch (err) {
  //       console.log("err:", err)
  //       if (err?.response?.data?.server) {
  //         const { server: msg, message } = err.response.data;
  //         Swal.fire({ title: 'Error!', text: msg + '\n' + truncarTexto(message), icon: 'error' });
  //       }
  //     }
  //   }
  // };
  const onChange = (item) => {
    console.log('item:', item);
    return {};
  };

  const downloadExcel = async (e) => {
    e?.preventDefault();
    loading.value = true;
    try {
      const response = await axios.get(route('exportar.usuarios-roles'), {
        responseType: 'blob',
      });

      // Llama a la función para manejar la descarga
      await excelDescarga(response.data, 'usuariosRoles.xlsx');
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
    <v-container>
      <v-card color="background" class="px-4 py-2" :loading="loading">
        <v-card-title> ASIGNACIÓN DE ROLES A USUARIOS </v-card-title>
        <div>
          <v-row>
            <v-col class="gridBtns">
              <v-btn class="" type="" color="surface" :loading="loading" @click="downloadExcel">
                Exportar
              </v-btn>
              <Link :href="route('usuarios-equipo.create')">
                <v-btn color="success" class="ms-auto"> Asignar Roles a alumno </v-btn>
              </Link>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <v-data-table
                :headers="headers"
                :items="filteredItems"
                :items-per-page="20"
                class="elevation-1 rounded"
              >
                <template v-slot:no-data>Información no encontrada</template
                ><template v-slot:top>
                  <v-row
                    ><v-col cols="12" sm="6" md="5">
                      <v-text-field
                        v-model="search"
                        label="Filtrar"
                        class="mx-3 mt-2"
                        append-icon="mdi-magnify"
                        variant="underlined"
                        hide-details
                      ></v-text-field> </v-col
                  ></v-row>
                </template>
                <template v-slot:header.roles="{ props }">
                  <!-- <span class="mt-2">Rol</span> -->
                  <v-select
                    v-model="nameFilter"
                    label="Roles"
                    persistent-placeholder
                    hide-details
                    variant="underlined"
                    :items="roles"
                    multiple
                    item-title="nombre"
                    item-value="id"
                    @update:modelValue="onChange"
                  />
                </template>
                <template v-slot:[`item.roles`]="{ item }">
                  <span v-for="(rol, i) in item.roles" :key="rol.id">
                    {{ rol.nombre }}{{ i + 1 == item.roles.length ? ' ' : ' - ' }}
                  </span>
                </template>
                <template v-slot:[`item.persona`]="{ item }">
                  {{ item.persona.nombre }} {{ item.persona.apellido }}
                </template>
                <template v-slot:[`item.curriculums`]="{ item }">
                  {{ item.tiene_curriculums ? `SI (${item.tiene_curriculums})` : '' }}
                </template>
                <template v-slot:[`item.acciones`]="{ item }">
                  <div class="d-flex inline-flex ga-2">
                    <!-- <Link :href="route('usuarios-equipo.show', item)">
                    <v-btn as="v-btn" color="info" small> Ver </v-btn>
                    </Link> -->
                    <Link :href="route('usuarios-equipo.edit', item)">
                      <v-btn color="secondary" small> Editar </v-btn>
                    </Link>
                    <!-- <v-btn color="error" small @click="onClickDelete(item)">Eliminar
                    </v-btn> -->
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
