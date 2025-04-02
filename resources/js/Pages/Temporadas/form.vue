<script setup>
  import { router, useForm } from '@inertiajs/vue3';
  import axios from 'axios';
  import dayjs from 'dayjs';
  import { computed, defineProps, inject, onMounted, ref } from 'vue';

  import ButtonBack from '../../components/ButtonBack';
  import MainLayout from '../../components/Layout';
  import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';
  import { DATE, getSpecificDayOfWeek } from '../../utils/date';

  const validate = inject('$validation');

  const props = defineProps({
    action: String,
    temporada: { type: Object, default: {} },
    status: String,
  });

  onMounted(() => {
    console.log(props);
  });

  const loading = ref(false);
  const isDisabled = ref(props.action === CRUD.show);

  const inputForm = useForm({
    nombre: '',
    prefijo: '',
    titulo: '',
    fecha_inicio: '',
    fecha_cierre: '',
    fecha_extension: '',
    fecha_inicio_w: '',
    fecha_cierre_w: '',
    fecha_extension_w: '',
    inscripcion_inicio: '',
    inscripcion_cierre: '',
    extension: !!props.temporada.fecha_extension_w,
    ...props.temporada,
  });
  const form = ref(null);

  const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
  };

  const submit = async (form) => {
    loading.value = true;
    const action = props.action === CRUD.edit ? 'update' : 'store';
    const method = props.action === CRUD.edit ? 'put' : 'post';
    const routeName = `temporadas.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : null;

    try {
      const response = await axios[method](route(routeName, id), inputForm);
      if (response?.data?.message) {
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

        router.visit(route('temporadas.index'));
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
  const obtenerPrimerDiaSemanaISO = (yearWeek) => {
    const [year, week] = yearWeek.split('-W').map(Number);
    const date = dayjs().year(year).isoWeek(week).startOf('isoWeek');
    return date;
  };

  const getSemana = (fecha) => {
    if (!fecha) return { inicio: null, fin: null };
    fecha = obtenerPrimerDiaSemanaISO(fecha);
    return {
      inicio: fecha,
      fin: getSpecificDayOfWeek(fecha, 7),
    };
  };
  const semanaInicio = computed(() => getSemana(inputForm.fecha_inicio_w));
  const semanaFin = computed(() => getSemana(inputForm.fecha_cierre_w));
  const semanaExt = computed(() => getSemana(inputForm.fecha_extension_w));

  const semanasArray = computed(() => {
    const arr = [];
    if (!semanaInicio.value.inicio) return arr;
    let inicioTem = semanaInicio.value.inicio;
    const finTem = semanaFin.value.fin;
    const finExtTem = semanaExt.value.fin;
    const fin = !!finExtTem && finExtTem.isValid() ? finExtTem : finTem;
    let i = 0;
    while (inicioTem.isBefore(fin)) {
      arr.push({
        semana: `Semana ${i + 1}`,
        inicio: inicioTem.format(DATE.DD_MM_YYYY),
        fin: inicioTem.add(6, 'day').format(DATE.DD_MM_YYYY),
        ext: inicioTem.isAfter(finTem),
      });
      inicioTem = inicioTem.add(1, 'weeks');
      i++;
    }

    return arr;
  });
</script>

<template>
  <MainLayout>
    <v-container fluid>
      <v-card color="background" class="" :disabled="loading" :loading="loading">
        <template v-slot:loader="{ isActive }">
          <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
        </template>
        <v-card-title>
          <ButtonBack :href="route('temporadas.index')" /> TEMPORADAS
          {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
        </v-card-title>
        <v-card-subtitle> {{ ACCION[action] }} de Temporada</v-card-subtitle>
        <v-card-text>
          <v-form @submit="validateForm" ref="form" lazy-validation>
            <v-row class="row-gap-2">
              <v-col cols="12" sm="6">
                <v-text-field
                  id="prefijo"
                  name="prefijo"
                  label="Prefijo"
                  v-model="inputForm.prefijo"
                  :disabled="isDisabled"
                  :rules="validate('Nombre', 'required')"
                  :error-messages="inputForm.errors.prefijo"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="nombre"
                  name="nombre"
                  label="Nombre"
                  v-model="inputForm.nombre"
                  :disabled="isDisabled"
                  :rules="validate('Temporada', 'required')"
                  :error-messages="inputForm.errors.nombre"
                /> </v-col
              ><v-col cols="12" sm="6">
                <v-text-field
                  id="inscripcion_inicio"
                  name="inscripcion_inicio"
                  label="Fecha de inicio de inscripcion"
                  type="date"
                  v-model="inputForm.inscripcion_inicio"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.inscripcion_inicio"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="inscripcion_cierre"
                  name="inscripcion_cierre"
                  label="Fecha de cierre de inscripcion"
                  type="date"
                  v-model="inputForm.inscripcion_cierre"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.inscripcion_cierre"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="fecha_inicio"
                  name="fecha_inicio"
                  label="Fecha de inicio"
                  type="week"
                  v-model="inputForm.fecha_inicio_w"
                  :disabled="isDisabled"
                  :rules="validate('Fecha de inicio', 'required')"
                  :error-messages="inputForm.errors.fecha_inicio"
                  :max="inputForm.fecha_cierre_w"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  id="fecha_cierre"
                  name="fecha_cierre"
                  label="Fecha de cierre"
                  type="week"
                  v-model="inputForm.fecha_cierre_w"
                  :disabled="isDisabled"
                  :rules="validate('Fecha de cierre', 'required')"
                  :error-messages="inputForm.errors.fecha_cierre"
                  :min="inputForm.fecha_inicio_w"
                  :max="inputForm.fecha_extension_w"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-switch
                  id="extension"
                  name="extension"
                  v-model="inputForm.extension"
                  :label="inputForm.extension ? 'Con fecha para posible extension' : 'Normal'"
                  color="success"
                  class="px-2"
                />
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  v-if="inputForm.extension"
                  id="fecha_extension"
                  name="fecha_extension"
                  label="Fecha de extension de temporada"
                  type="week"
                  v-model="inputForm.fecha_extension_w"
                  :disabled="isDisabled"
                  :error-messages="inputForm.errors.fecha_extension"
                  :min="inputForm.fecha_cierre_w"
                />
              </v-col>
            </v-row>
            <v-row class="my-3" v-if="!isDisabled">
              <v-col cols="12" class="d-flex">
                <v-btn class="ms-auto" type="submit" color="primary" :loading="loading">
                  {{ TEXT_BUTTON[action] }}
                </v-btn>
              </v-col>
            </v-row>
          </v-form>

          <span v-if="inputForm.extension">
            De acuerdo con lo ingresado, el plazo es de
            <b>{{ semanasArray.filter((x) => !x.ext).length }}</b> semanas, con la posibilidad de
            una extensión de <b>{{ semanasArray.filter((x) => x.ext).length }}</b> semanas, lo que
            suma un total de <b>{{ semanasArray.length }}</b> semanas.</span
          >
          <span v-else>
            De acuerdo con lo ingresado, el plazo es de
            <b>{{ semanasArray.filter((x) => !x.ext).length }}</b> semanas</span
          >

          <v-card>
            <v-list class="my-2" scroll-y max-height="500">
              <v-list-item
                v-for="semana in semanasArray"
                :key="semana.inicio"
                :class="`${semana.ext ? 'bg-warning' : ''}`"
              >
                <v-row class="my-0">
                  <v-col class="py-0" lg="2" md="2" sm="3" cols="6">{{ semana.semana }}:</v-col>
                  <v-col class="py-0" lg="2" md="2" sm="3" cols="6">{{
                    semana.ext ? 'extensión' : 'regular'
                  }}</v-col>
                  <v-col class="py-0" lg="2" md="2" sm="3" cols="6">{{ semana.inicio }}</v-col>
                  <v-col class="py-0" lg="2" md="2" sm="3" cols="6"> {{ semana.fin }} </v-col>
                </v-row>
              </v-list-item>
            </v-list>
          </v-card>
        </v-card-text>
      </v-card>
    </v-container>
  </MainLayout>
</template>
