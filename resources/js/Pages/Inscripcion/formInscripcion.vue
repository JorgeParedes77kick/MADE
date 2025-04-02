<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import { defineProps, inject, onMounted, ref, watch } from 'vue';

  import { Truthty } from '../../utils/isType';

  const validate = inject('$validation');

  const props = defineProps({
    temporadas: { type: Array, default: [] },
    curriculums: { type: Array, default: [] },
    dias: { type: Array, default: [] },
    estados: { type: Array, default: [] },
    usuario: { type: Object, default: {} },
    action: String,
    buscarPorEmail: { type: Function, default: () => {} },
  });

  onMounted(() => {});
  const isDisabled = ref(false);
  const loading = ref(false);

  const formGrupo = ref(null);
  const formInscripcion = ref(null);

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
  const grupo_pequeno = ref([]);
  const grupos_pequenos = ref([]);

  const headersGrupos = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Día', key: 'dia_curso' },
    { title: 'Hora', key: 'hora', minWidth: '6rem' },
    { title: 'Lideres', key: 'lideres' },
  ];
  watch(
    () => props.usuario,
    (newPage, oldPage) => {
      inputInscripcion.usuario_id = newPage.id;
    },
    { deep: true },
  );
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
      if (data?.message) {
        grupos_pequenos.value = [];
        callMsgError(data?.message, 'No hay grupos pequeños disponibles', 'info');
      } else grupos_pequenos.value = data.grupos_pequenos;
    } catch (err) {
      grupos_pequenos.value = [];
      const { message } = err.response.data;
      callMsgError(message);
    }
  };

  const onChangeGrupo = (item) => {
    inputInscripcion.grupo_pequeno_id = item.length ? item[0].id : '';
    inputInscripcion.grupo_pequeno = item.length ? item[0] : '';
  };
  const inscribirSumbit = async (e) => {
    e.preventDefault();
    const { usuario } = props;
    const { estado_inscripcion_id, grupo_pequeno_id, grupo_pequeno } = inputInscripcion;
    // console.log('inputInscripcion:', inputInscripcion);
    const { valid } = await formInscripcion.value.validate();
    if (!valid || !Truthty(grupo_pequeno_id)) return;

    const inscripcion = validateExist();
    if (inscripcion && estado_inscripcion_id == inscripcion.estado_inscripcion_id) {
      return Swal.fire({
        title: 'Alerta',
        html: `<b>${usuario.fullNombre}</b> ya esta inscrito en este curso.`,
        icon: 'warning',
      });
    } else {
      const { curriculum, ciclo, temporada } = inputFormGrupo;
      console.log('inscripcion:', curriculum, ciclo, grupo_pequeno);

      const html = inscripcion
        ? `<b>${usuario.fullNombre}</b> ya esta inscrito en este curso.
          Deseas Cambiar el estado a <b>${props.estados.find((x) => x.id == estado_inscripcion_id)?.estado}</b>?
          `
        : `Estas seguro de inscribir a <b>${usuario.fullNombre}</b>
          A la temporada <b>${temporada.prefijo}</b>, grupo pequeño <b>${curriculum.nombre}</b> en el <b>${ciclo.nombre}</b>
          en el horario del <b>${grupo_pequeno?.dia_curso} </b>
          con el estado de <b>${props.estados.find((x) => x.id == estado_inscripcion_id)?.estado}</b>
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
    // if (email) {
    //   inputEmail.reset();
    //   inputEmail.clearErrors();
    //   inscripciones.value = [];
    //   usuario.value = {};
    // }
    inputFormGrupo.reset();
    inputFormGrupo.clearErrors();
    inputInscripcion.reset();
    inputInscripcion.clearErrors();
    ciclos.value = [];
    grupo_pequeno.value = [];
    grupos_pequenos.value = [];
  };
  const callMsgError = async (msg, title = 'Error!', icon = 'error') => {
    await Swal.fire({
      title,
      text: msg ? msg : 'Hubo un problema buscar la información, intente más tarde...',
      icon,
    });
  };
</script>

<template>
  <div>
    <v-form @submit="buscarLideres" v-if="Truthty(usuario.id)" ref="formGrupo" lazy-validation>
      <v-card-title>INSCRIBIR A GP</v-card-title>
      <v-row>
        <v-col sm="6" md="4">
          <v-combobox
            id="temporada"
            name="temporada"
            label="Temporada"
            v-model="inputFormGrupo.temporada"
            :disabled="isDisabled"
            :rules="validate('Temporada', 'required')"
            :error-messages="inputFormGrupo.errors.temporada"
            :items="temporadas"
            item-title="prefijo"
            item-value="id"
            @update:focused="(s) => focus(s, 'temporada')"
            autocomplete="off"
            clearable
          />
        </v-col>
        <v-col sm="6" md="4">
          <v-combobox
            id="curriculum"
            name="curriculum"
            label="Curriculum"
            v-model="inputFormGrupo.curriculum"
            :disabled="isDisabled"
            :rules="validate('Curriculum', 'required')"
            @update:focused="(s) => focus(s, 'curriculum')"
            :error-messages="inputFormGrupo.errors.curriculum"
            :items="curriculums"
            item-title="nombre"
            item-value="id"
            @update:modelValue="onChangeCurriculum"
            autocomplete="off"
            clearable
          />
        </v-col>
        <v-col sm="6" md="4">
          <v-combobox
            id="ciclo"
            name="ciclo"
            label="Ciclo"
            v-model="inputFormGrupo.ciclo"
            :disabled="isDisabled"
            :rules="validate('Ciclo', 'required')"
            @update:focused="(s) => focus(s, 'ciclo')"
            :error-messages="inputFormGrupo.errors.ciclo"
            :items="ciclos"
            item-title="nombre"
            item-value="id"
            @update:modelValue="onChangeCiclo"
            autocomplete="off"
            clearable
          />
        </v-col>
        <v-col cols="12" class="d-flex justify-end">
          <v-btn color="info" @click="buscarLideres"> Buscar Grupos Pequeños </v-btn>
        </v-col>
      </v-row>
    </v-form>
    <v-form
      v-if="Truthty(usuario.id)"
      @submit="inscribirSumbit"
      ref="formInscripcion"
      lazy-validation
    >
      <v-row>
        <v-col>
          <v-data-table
            :headers="headersGrupos"
            :items="grupos_pequenos"
            class="elevation-1 rounded"
            hide-default-footer
            hide-default-header
            show-select
            select-strategy="single"
            v-model="grupo_pequeno"
            @update:modelValue="onChangeGrupo"
            :rules="validate('Grupo Pequeño', 'required')"
            return-object
          >
            <template v-slot:no-data> No hay grupos pequeños disponibles </template>
            <template v-slot:item.data-table-select="{ internalItem, isSelected, toggleSelect }">
              <v-checkbox-btn
                :model-value="isSelected(internalItem)"
                color="primary"
                @update:model-value="toggleSelect(internalItem)"
              ></v-checkbox-btn>
            </template>
            <template v-slot:[`item.id`]="{ item }"> #{{ item.id }} </template>
            <template v-slot:[`item.monitores`]="{ item }">
              <p v-for="monitor in item.monitores" :key="monitor.id">
                {{ monitor?.persona?.nombre }}
                {{ monitor?.persona?.apellido }}
              </p>
            </template>
            <template v-slot:[`item.lideres`]="{ item }">
              <p v-for="lider in item.lideres" :key="lider.id">
                {{ lider?.persona?.nombre }} {{ lider?.persona?.apellido }}
              </p>
            </template>
            <template v-slot:[`item.hora`]="{ item }">
              <p v-if="item.dia_curso == 'none'">none</p>
              <p v-else>{{ item.hora }}</p>
            </template>
          </v-data-table>
        </v-col>
      </v-row>
      <v-row>
        <v-col sm="6" md="4">
          <v-select
            id="estado"
            name="estado"
            label="Estado de Inscripción"
            v-model="inputInscripcion.estado_inscripcion_id"
            :rules="validate('Estado de Inscripción', 'required')"
            :error-messages="inputInscripcion.errors.estado_inscripcion_id"
            :items="estados"
            item-title="estado"
            item-value="id"
            autocomplete="off"
          ></v-select>
        </v-col>
        <v-col cols="6" md="8" class="d-flex align-end">
          <v-btn
            class="ms-auto"
            type="submit"
            color="primary"
            :disabled="!Truthty(inputInscripcion.estado_inscripcion_id)"
            :loading="loading"
          >
            Inscribir
          </v-btn>
        </v-col>
      </v-row>
    </v-form>
  </div>
</template>
