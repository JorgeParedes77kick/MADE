<script setup>
  import { defineProps, inject, onMounted, ref } from 'vue';

  import { useForm, usePage } from '@inertiajs/inertia-vue3';
  import { Link } from '@inertiajs/vue3';
  import MainLayout from '../../components/Layout';
  import { Truthty } from '../../utils/isType';
  import FormInscripcion from './formInscripcion';

  const validate = inject('$validation');
  const { flash } = usePage().props;

  const props = defineProps({
    temporadas: { type: Array, default: [] },
    curriculums: { type: Array, default: [] },
    dias: { type: Array, default: [] },
    estados: { type: Array, default: [] },
    error: String,
    // action: String,
  });
  const loading = ref(false);
  const isDisabled = ref(false);

  onMounted(() => {
    // console.log(props);
  });
  const formEmail = ref(null);
  const formGrupo = ref(null);
  const formInscripcion = ref(null);

  const inputEmail = useForm({ email: '' });
  const inputFormGrupo = useForm({
    temporada: null,
    curriculum: null,
    ciclo: null,
    grupo_pequeno_id: '',
  });
  const inputInscripcion = useForm({
    usuario_id: -1,
    grupo_pequeno_id: '',
    estado_inscripcion_id: '',
    rol_id: 5,
    info_adicional: 'Inscripción vía administración',
  });

  const ciclos = ref([]);
  const inscripciones = ref([]);
  const usuario = ref({});
  const grupo_pequeno = ref([]);
  const grupos_pequenos = ref([]);

  const headers = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Temporada', key: 'grupo_pequeno.temporada.prefijo' },
    { title: 'Curriculum', key: 'grupo_pequeno.ciclo.curriculum.nombre' },
    { title: 'Ciclo', key: 'grupo_pequeno.ciclo.nombre' },
    { title: 'Lideres', key: 'lideres' },
    { title: 'Día', key: 'grupo_pequeno.dia_curso' },
    { title: 'Hora', key: 'hora', minWidth: '6rem' },
    { title: 'Status', key: 'estado_inscripcion.estado' },
    { title: 'Reasignar', key: 'reasignar' },
  ];
  const headersGrupos = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Día', key: 'dia_curso' },
    { title: 'Hora', key: 'hora', minWidth: '6rem' },
    { title: 'Lideres', key: 'lideres' },
  ];

  const buscarPorEmail = async (e) => {
    e?.preventDefault?.();
    inputEmail.clearErrors();

    const { valid } = await formEmail.value.validate();
    if (!valid) return;

    try {
      const { data } = await axios.get(route('inscripcion.find-email', inputEmail.email));
      inscripciones.value = data.inscripciones;
      usuario.value = data.usuario;
      inputInscripcion.usuario_id = data.usuario.id;
    } catch (err) {
      inscripciones.value = [];
      usuario.value = {};
      inputInscripcion.usuario_id = -1;
      callMsgError(err.response.data.message);
    }
  };

  const focus = (state, name) => {
    if (state) return;
    const { curriculum, ciclo, temporada } = inputFormGrupo;
    if (name === 'curriculum' && typeof curriculum == 'string') {
      inputFormGrupo.curriculum = null;
      inputFormGrupo.ciclo = null;
    }
    if (name === 'ciclo' && typeof ciclo == 'string') inputFormGrupo.ciclo = null;
    if (name === 'temporada' && typeof temporada == 'string') inputFormGrupo.temporada = null;
  };

  const onChangeCurriculum = (item) => {
    if (typeof item === 'object' && Truthty(item)) {
      ciclos.value = item.ciclos;
      inputFormGrupo.ciclo = null;
      inputFormGrupo.dia_curso = '';
      inputFormGrupo.horario = '';
    }
  };
  const onChangeCiclo = (item) => {
    if (typeof item === 'object' && Truthty(item)) {
      inputFormGrupo.dia_curso = '';
      inputFormGrupo.horario = '';
    }
  };

  const buscarLideres = async (e) => {
    e.preventDefault();
    inputFormGrupo.clearErrors();
    const { valid } = await formGrupo.value.validate();
    if (!valid) return;
    const { temporada, dia_curso, horario, ciclo } = inputFormGrupo;
    const [hora_inicio, hora_fin] = horario.split('-');
    const body = {
      dia_curso,
      hora_inicio,
      hora_fin,
      temporada_id: temporada.id,
      ciclo_id: ciclo.id,
    };
    try {
      const { data } = await axios.post(route('inscripcion.find-lideres'), body);
      grupos_pequenos.value = data.grupos_pequenos;
    } catch (err) {
      grupos_pequenos.value = [];
      const { message } = err.response.data;
      callMsgError(message);
    }
  };

  const onChangeGrupo = (item) => {
    inputInscripcion.grupo_pequeno_id = item.length ? item[0].id : '';
  };
  const inscribirSumbit = async (e) => {
    e.preventDefault();
    const { estado_inscripcion_id, grupo_pequeno_id } = inputInscripcion;
    const { valid } = await formInscripcion.value.validate();
    if (!valid || !Truthty(grupo_pequeno_id)) return;

    const inscripcion = validateExist();
    if (inscripcion && estado_inscripcion_id == inscripcion.estado_inscripcion_id) {
      return Swal.fire({
        title: 'Alerta',
        html: `<b>${usuario.value.fullNombre}</b> ya esta inscrito en este curso.`,
        icon: 'warning',
      });
    } else {
      const html = inscripcion
        ? `<b>${usuario.value.fullNombre}</b> ya esta inscrito en este curso.
          Deseas Cambiar el estado a <b>${
            props.estados.find((x) => x.id == estado_inscripcion_id)?.estado
          }</b>?
          `
        : `Estas seguro de inscribir a <b>${usuario.value.fullNombre}</b>
          A la temporada ${temporada.prefijo}, grupo pequeño <b>${curriculum.nombre}</b> en el <b>${
            ciclo.nombre
          }</b>
          en el horario del <b>${grupo.dia_curso} </b>
          con el estado de <b>${
            props.estados.find((x) => x.id == estado_inscripcion_id)?.estado
          }</b>
          `;

      const { isConfirmed } = await Swal.fire({
        title: 'Confirmar Acción',
        html,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
      });

      if (!isConfirmed) return;
      if (inscripcion) inputInscripcion.id = inscripcion.id;
    }
    try {
      const { data } = await axios.post(route('inscripcion.store'), inputInscripcion);
      if (!data?.message) throw new Error('No message');
      const { message } = data;
      await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
      const { isConfirmed } = await Swal.fire({
        title: 'Más inscripciones de usuario',
        text: '¿Deseas agregar más inscripciones para este usuario?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
      });
      if (!isConfirmed) return reset();
      else {
        reset(false);
        await buscarPorEmail();
      }
    } catch (err) {
      console.error('err:', err);
      const { message } = err.response.data;
      callMsgError(message);
      delete inputInscripcion.id;
    }
  };
  const validateExist = () => {
    const { grupo_pequeno_id } = inputInscripcion;
    const inscripcion = inscripciones.value.find((x) => x.grupo_pequeno_id === grupo_pequeno_id);
    return inscripcion;
  };
  const reset = (email = true) => {
    if (email) {
      inputEmail.reset();
      inputEmail.clearErrors();
      inscripciones.value = [];
      usuario.value = {};
    }
    inputFormGrupo.reset();
    inputFormGrupo.clearErrors();
    inputInscripcion.reset();
    inputInscripcion.clearErrors();
    ciclos.value = [];
    grupo_pequeno.value = [];
    grupos_pequenos.value = [];
  };
  const callMsgError = async (msg) => {
    await Swal.fire({
      title: 'Error!',
      text: msg ? msg : 'Hubo un problema buscar la información, intente más tarde...',
      icon: 'error',
    });
  };

  //   watch(
  //     () => usuario.value,
  //     (newPage, oldPage) => {
  //       console.log('newPage:', newPage);
  //     },
  //     { deep: true },
  //   );
</script>
<template>
  <MainLayout>
    <v-container>
      <v-card color="background" class="px-4 py-2">
        <v-alert v-if="flash?.error" closable color="error" variant="outlined">
          {{ flash.error }}
        </v-alert>

        <v-card-title>INSCRIPCIÓN ALUMNO</v-card-title>
        <v-form @submit="buscarPorEmail" ref="formEmail" lazy-validation>
          <v-row>
            <v-col>
              <v-text-field
                id="email"
                name="email"
                label="CORREO ELETRÓNICO"
                v-model="inputEmail.email"
                placeholder="introduzca el correo electrónico"
                :rules="validate('Email', 'required|email')"
                :error-messages="inputEmail.errors.email"
                clearable
              />
            </v-col>
            <v-col md="4" class="d-flex align-center justify-start">
              <v-btn type="submit" color="info" :loading="loading"> BUSCAR </v-btn>
            </v-col>
          </v-row>
        </v-form>
        <v-data-table
          v-if="inscripciones.length > 0"
          :headers="headers"
          :items="inscripciones"
          :items-per-page="20"
          class="elevation-1 rounded"
          ><template v-slot:no-data>Información no encontrada</template>
          <template v-slot:[`item.lideres`]="{ item }">
            <p v-for="lider in item.grupo_pequeno.lideres" :key="lider.id">
              {{ lider?.persona?.nombre }}
              {{ lider?.persona?.apellido }}
            </p>
          </template>
          <template v-slot:[`item.hora`]="{ item }">
            <p v-if="item.grupo_pequeno.dia_curso == 'none'">none</p>
            <p v-else>{{ item.grupo_pequeno.hora }}</p>
          </template>
          <template v-slot:[`item.reasignar`]="{ item }">
            <div class="d-flex inline-flex ga-2">
              <Link
                :href="route('inscripcion.edit', item)"
                v-if="item.grupo_pequeno.temporada.activo"
              >
                <v-btn color="secondary" small> reasignar </v-btn>
              </Link>
            </div>
            <!-- <v-btn color="secondary" small>
              <pre>{{ item.grupo_pequeno.temporada.activo }}</pre>
            </v-btn> -->
          </template>
        </v-data-table>
        <FormInscripcion
          :temporadas="temporadas"
          :curriculums="curriculums"
          :dias="dias"
          :estados="estados"
          :usuario="usuario"
          :buscarPorEmail="buscarPorEmail"
        />
      </v-card>
    </v-container>
  </MainLayout>
</template>
