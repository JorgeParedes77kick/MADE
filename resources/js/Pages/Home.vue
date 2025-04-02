<template>
  <main-layout>
    <v-container>
      <v-card color="background">
        <v-card-title>Bienvenido a Mi Aplicación</v-card-title>
        <v-card-text> Este es el contenido de la página de ejemplo. </v-card-text>
        <div class="pa-4 ga-2" style="display: grid">
          <p v-for="ruta in rutas" :key="ruta.name" class="">
            <Link :href="route(ruta.name)">
            <a>{{ ruta.URI }}</a>
            </Link>
          </p>
        </div>
      </v-card>
    </v-container>
  </main-layout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import MainLayout from '../components/Layout.vue';

export default {
  props: ['rutas'],
  components: {
    MainLayout,
    Link,
  },
  mounted() {
    console.log(this.$props);
  },
};
</script>

<style scoped></style>
