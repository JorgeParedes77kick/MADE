<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { defineProps, onMounted, ref } from 'vue';
import MainLayout from '../../components/Layout.vue';

// const { flash } = usePage().props.value;
const pageProps = usePage().props.value;

const props = defineProps({
  curriculums: { type: Array, default: [] },
  status: Boolean,
});

const loading = ref(false);
const isDisabled = ref(false);

onMounted(() => {
  console.log('pageProps:', pageProps);
  console.log('Props:', props);
});
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <v-card-title class="text-center">MIS SALONES </v-card-title>
        <p class="text-subtitle-1 text-center">
          Haz clic en un Grupo Pequeño para ver más detalles!
        </p>

        <template v-if="status">
          <v-alert type="success" :text="status"></v-alert>
        </template>

        <v-row class="mt-3">
          <v-col
            cols="6"
            sm="4"
            md="3"
            xl="2"
            class="mb-1"
            v-for="curriculum in curriculums"
            :key="curriculum.id"
          >
            <Link :href="route('mis-salones.curriculum', curriculum.idCrypt)">
              <v-card class="hover-card">
                <v-img
                  :src="`/storage/img/curriculums/${curriculum.imagen}`"
                  :alt="curriculum.nombre"
                >
                  <template v-slot:error>
                    {{ curriculum.nombre }}
                  </template>
                </v-img>
              </v-card>
            </Link>
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </MainLayout>
</template>
<style></style>
