<script setup>
  import { router, usePage } from '@inertiajs/vue3';
  import { defineProps, inject, onMounted, ref, watch } from 'vue';
  import MainLayout from '../../components/Layout.vue';

  const validate = inject('validation');

  // const { flash } = usePage().props.value;
  const pageProps = usePage();

  const props = defineProps({
    curriculum: { type: Object, default: {} },
    temporadaras: {},
  });

  const ciclo = ref(null);
  const grupoForm = ref(null);

  const loading = ref(false);
  const isDisabled = ref(true);

  onMounted(() => {
    // console.log('pageProps:', pageProps.props);
    // console.log('router:', router.visit);
  });
  const onChangeCiclo = () => (grupoForm.value = null);

  const formIns = ref(null);

  const submit = async (e) => {
    e.preventDefault();
    if (!grupoForm) return;
    try {
      const response = await axios.post(route('horario.inscripcion.store'), grupoForm.value);
      console.log('response?.data:', response?.data);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('home'));
      }
    } catch (err) {
      console.log(err?.response);
      if (err?.response?.data?.server) {
        const { server: message } = err.response.data;
        Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      }
      if (err?.response?.data?.errors) {
        const { errors } = err.response.data;
        inputForm.errors = errors;
      }
    } finally {
      loading.value = false;
    }
  };
  watch(grupoForm, (newValue) => {
    isDisabled.value = !newValue;
  });
</script>
<template>
  <main-layout>
    <v-container fluid>
      <v-card color="background" class="shadow-md px-4 py-2">
        <v-card-title> GRUPO PEQUEÑO: {{ curriculum.nombre }} </v-card-title>
        <v-form @submit="submit" lazy-validation ref="formIns">
          <v-row class="row-gap-1">
            <v-col cols="12">
              <v-alert color="yellow" style="white-space: pre-wrap">
                {{ curriculum.descripcion }}
              </v-alert>
            </v-col>
            <v-col cols="12">
              <v-alert color="info">
                Recuerda todos nuestros <strong>horarios son hora Chile</strong>.
              </v-alert>
            </v-col>
            <v-col cols="12" class="text-subtitle-1">
              <b><i>Selecciona tu ciclo:</i></b>
            </v-col>
            <v-radio-group inline v-model="ciclo" @update:modelValue="onChangeCiclo">
              <template v-for="ciclo in curriculum.ciclos" :key="ciclo.id">
                <v-col cols="12" sm="6" md="3" xl="3">
                  <v-radio
                    class="rounded-pill border-md border-success pa-3 w-100"
                    :label="`${curriculum.nombre} ${ciclo.nombre}`"
                    :value="ciclo"
                    color="primary"
                  ></v-radio>
                </v-col> </template
            ></v-radio-group>
          </v-row>
          <v-row class="row-gap-1">
            <v-radio-group v-if="ciclo?.id" inline v-model="grupoForm">
              <v-col cols="12" class="text-subtitle-1">
                <b><i>Selecciona un Horario Disponible:</i></b>
              </v-col>
              <template
                v-for="grupo in ciclo.grupos_pequenos"
                :key="`${grupo.dia_curso} ${grupo.hora}`"
              >
                <v-col cols="12" sm="6" md="3" xl="3">
                  <v-radio
                    class="rounded-pill border-md border-success pa-3 w-100"
                    :label="grupo.hora"
                    :value="grupo"
                    color="primary"
                  ></v-radio>
                </v-col> </template
            ></v-radio-group>
          </v-row>
          <v-row class="my-3" v-if="!isDisabled">
            <v-col cols="12" class="d-flex">
              <v-btn class="ms-auto" type="submit" color="primary" :loading="loading">
                Inscribirme
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </v-card>
    </v-container>
  </main-layout>
</template>

<style></style>
