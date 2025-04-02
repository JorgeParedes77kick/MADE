<script setup>
  import { Link } from '@inertiajs/vue3';
  import { computed, defineProps, onMounted, ref } from 'vue';
  import { useTheme } from 'vuetify';
  import MainLayout from '../../components/Layout.vue';

  const props = defineProps({
    curriculum: { type: Object, default: {} },
    grupospequenos: { type: Array, default: [] },
  });
  const theme = useTheme();

  const loading = ref(false);
  const isDisabled = ref(false);

  onMounted(() => {
    // console.log('pageProps:', pageProps);
    // console.log('Props:', props);
    console.log('vuetify:', theme);
  });
  const isDark = computed(() => theme.current.value.dark);
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <!-- <template v-if="status">
        <v-alert type="success" :text="status"></v-alert>
      </template> -->
      <v-card color="background" class="shadow-md px-4 py-2">
        <v-card-title class="text-center">GRUPO PEQUEÑO: {{ curriculum.nombre }} </v-card-title>
        <v-divider></v-divider>

        <v-row>
          <v-col cols="12" class="text-subtitle-1"> Selecciona tu ciclo: </v-col>
          <v-col v-for="grupo in grupospequenos" :key="grupo.id" cols="12" sm="6" md="4" lg="3">
            <v-hover v-slot:default="{ isHovering, props }">
              <Link
                :href="
                  route('mis-salones.grupo', {
                    idCryptCurriculum: curriculum.idCrypt,
                    id: grupo.id,
                  })
                "
                style="text-decoration: none; color: inherit"
                ><v-card
                  class="rounded-pill border-md w-100 h-auto px-3 py-1 text-center"
                  v-ripple
                  :elevation="isHovering ? 10 : 2"
                  color="info"
                  v-bind="props"
                >
                  <div :class="isHovering ? 'font-weight-medium' : ''">
                    <p>{{ curriculum.nombre }} {{ grupo.ciclo.nombre }}</p>
                    <p>{{ grupo.dia_curso }} {{ grupo.hora }}</p>
                  </div>
                </v-card>
              </Link>
            </v-hover>
            <!-- <v-radio class="rounded-pill border-lg border-success pa-3 w-100"
              :label="`${curriculum.nombre} ${ciclo.nombre}`" :value="ciclo" color="primary"></v-radio> -->
          </v-col>
        </v-row>
      </v-card>
    </v-container>
  </MainLayout>
</template>
<style></style>
