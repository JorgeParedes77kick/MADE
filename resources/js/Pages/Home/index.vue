<script setup>
  import { Link, usePage } from '@inertiajs/vue3';
  import { defineProps, onMounted } from 'vue';
  import MainLayout from '../../components/Layout.vue';

  // const { flash } = usePage().props.value;
  const pageProps = usePage().props;

  const props = defineProps({
    temporadas: { type: Array, default: [] },
    curriculums: { type: Array, default: [] },
    status: Boolean,
  });

  onMounted(() => {
    console.log('pageProps:', pageProps);
    console.log('Props:', props);
  });
</script>

<template>
  <main-layout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <template #title>
          <div class="text-center">
            <template v-if="temporadas.some((x) => x.activo_inscripcion) && curriculums.length > 0">
              GRUPOS PEQUEÑOS ABIERTOS ESTA TEMPORADA
            </template>
            <template v-else-if="temporadas.length > 0">
              ¡Ya comenzó la temporada {{ temporadas[0].prefijo }} de grupos pequeños!
            </template>
            <template v-else> No existe ninguna temporada activa en este momento! </template>
          </div>
        </template>
        <template
          #subtitle
          v-if="temporadas.some((x) => x.activo_inscripcion) && curriculums.length > 0"
        >
          Haz clic en el grupo pequeño de tu interes!
        </template>
        <div>
          <template v-if="status">
            <v-alert type="success" :text="status"></v-alert>
          </template>

          <template v-if="temporadas.some((x) => x.activo_inscripcion) && curriculums.length > 0">
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
                <Link :href="route('horario.curriculum', curriculum.idCrypt)">
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
          </template>

          <template v-else>
            <v-row id="aviso">
              <v-col cols="12">
                <v-alert color="info">
                  <p v-if="temporadas.length > 0">
                    Qué bueno que seas parte de esta familia sin fronteras. Esperamos que estés
                    disfrutando de todo lo que estás viviendo este tiempo. Recuerda que aquí podrás
                    tener
                    <Link :href="route('mi-perfil')"><strong>acceso a tu perfil</strong></Link> y
                    revisar
                    <Link :href="route('mis-cursos')"><strong>tus grupos inscritos</strong></Link>
                    <br />
                    Muy pronto tendremos nuevas funciones que harán mejor tu experiencia de Grupos
                    Pequeños Sin Fronteras.
                  </p>
                  <p v-else>
                    Recuerda que aquí podrás tener
                    <Link :href="route('mi-perfil')"><strong>acceso a tu perfil</strong></Link>
                    <br />
                    Muy pronto tendremos nuevas funciones que harán mejor tu experiencia de Grupos
                    Pequeños Sin Fronteras.
                  </p>

                  <p style="text-align: center" v-if="temporadas.length > 0">
                    <strong>¡Que tengas una bella temporada!</strong>
                  </p>
                </v-alert>
              </v-col>
            </v-row>
          </template>
        </div>
      </v-card></v-container
    ></main-layout
  >
</template>
<style></style>
