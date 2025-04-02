<script setup>
  import { defineProps, inject, onMounted, ref } from 'vue';

  import MainLayout from '../../components/Layout';
  const validate = inject('$validation');

  const props = defineProps({
    globales: Array,
    tipos_cast: Array,
  });

  const openShow = ref(false);
  const globalSelect = ref({});
  const loading = ref(false);

  onMounted(() => {});

  const headers = [
    { title: 'ID', key: 'id' },
    { title: 'Nombre', key: 'nombre' },
    { title: 'Tipo', key: 'tipo' },
    { title: 'Valor', key: 'valor' },
    { title: 'Acciones', key: 'acciones', sortable: false },
  ];
  const onClickVer = (global) => {
    globalSelect.value = {
      ...global,
    };
    openShow.value = true;
  };

  const form = ref(null);

  const submit = async () => {
    loading.value = true;
    try {
      openShow.value = false;
      const response = await axios.put(
        route('globals.update', globalSelect.value.id),
        globalSelect.value,
      );
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
      }
    } catch (err) {
      console.log('err:', err);
      console.log(err?.response);
      if (err?.response?.data?.server) {
        const { server: message } = err.response.data;
        await Swal.fire({ title: 'Error!', text: message, icon: 'error' });
        openShow.value = true;
      }
      // if (err?.response?.data?.errors) {
      //   const { errors } = err.response.data;
      //   inputForm.errors = errors;
      // }
    } finally {
      loading.value = false;
    }
  };
</script>
<template>
  <MainLayout>
    <v-container>
      <v-card color="background" class="px-4 py-2" title="Globales" :loading="loading">
        <v-row>
          <v-col>
            <v-data-table
              :loading="loading"
              :headers="headers"
              :items="globales"
              :items-per-page="10"
              class="elevation-1 rounded"
              hide-default-footer
            >
              <template v-slot:[`item.acciones`]="{ item }">
                <v-btn color="secondary" small @click="onClickVer(item)">Editar</v-btn> </template
              ><template v-slot:no-data>Información no encontrada</template>
            </v-data-table>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
    <v-dialog v-model="openShow" width="auto" min-width="25rem" persistent>
      <v-card :title="`#${globalSelect?.id} - ${globalSelect?.nombre}`" :loading="loading">
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="6" class="py-1 d-flex justify-start justify-sm-center align-center"
              ><v-label class="text-input">Valor: </v-label></v-col
            >
            <v-col cols="12" sm="6" class="py-1">
              <v-text-field
                id="valor"
                name="valor"
                v-model="globalSelect.valor"
                :rules="validate('valor', 'required')"
                color="input"
            /></v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="6" class="py-1 d-flex justify-start justify-sm-center align-center"
              ><v-label class="text-input">Tipo: </v-label></v-col
            >
            <v-col cols="12" sm="6" class="py-1">
              <v-select
                id="tipo"
                name="tipo"
                v-model="globalSelect.tipo"
                :rules="validate('tipo', 'required')"
                :items="tipos_cast"
                color="input"
            /></v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="submit" color="primary" small>Guardar</v-btn>
          <v-btn @click="openShow = false" color="secondary" small>Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </MainLayout>
</template>
