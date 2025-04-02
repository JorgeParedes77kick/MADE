<script setup>
  import { defineProps, onMounted, ref } from 'vue';
  import { Truthty } from '../../utils/isType';
  import { CustomProxy } from '../../utils/proxy';
  const props = defineProps({
    idGrupo: { type: Number, default: -1 },
    edit: { type: Boolean, default: true },
    textoBtn: { type: String, default: 'Asistencia' },
    colorBtn: { type: String, default: 'success' },
  });
  const loading = ref(false);
  const isDisabled = ref(!props.edit);

  onMounted(() => {});

  const alumnos = ref([]);
  const semanas = ref(0);
  const estados = ref([]);
  const colorEstados = CustomProxy({ 1: 'gray', 2: 'success', 3: 'error', 4: 'warning' }, 'dark');

  const headers = ref([
    { title: 'Nombre', key: 'usuario.nombreCompleto', width: '20rem' },
    { title: 'Semanas', align: 'center', children: [] },
  ]);

  const buscarAsistencias = async () => {
    loading.value = true;
    try {
      const response = await axios.get(
        route(props.edit ? 'mis-salones.asistencia' : 'asistencias.grupo', props.idGrupo),
      );
      const { data } = response;
      alumnos.value = data?.inscripciones ?? [];
      semanas.value = data?.semanas ?? 0;
      estados.value = data?.estados ?? [];
      const genSemana = [];
      for (let i = 0; i < (data?.semanas ?? 0); i++) {
        genSemana.push({
          title: `${i + 1}`,
          key: `semana_${i + 1}`,
          sortable: false,
          width: '1rem',
          align: 'center',
        });
      }

      // Asignar los encabezados dinámicamente
      headers.value = [
        { title: 'Nombre', key: 'usuario.nombreCompleto', width: '20rem' },
        { title: 'Semanas', align: 'center', children: genSemana },
      ];
    } catch (error) {
      console.log(error);
    }
    loading.value = false;
  };
  const onClickSave = async () => {
    loading.value = true;
    const body = {
      inscrito: [],
      presente: [],
      ausente: [],
      recuperado: [],
      noAplica: [],
      otros: [],
    };
    const keys = CustomProxy(
      { 1: 'inscrito', 2: 'presente', 3: 'ausente', 4: 'recuperado', 5: 'noAplica' },
      'otros',
    );
    let asistencias = [];
    alumnos.value.forEach((inscripcion) => {
      asistencias = [...asistencias, ...inscripcion.asistencias];
    });
    asistencias.forEach((asistencia) => {
      const key = keys[asistencia.estado_asistencia_id];
      body[key].push(asistencia);
    });

    try {
      const { data } = await axios.put(route('mis-salones.update', props.idGrupo), body);
      if (!data?.message) throw new Error('No message');
      const { message } = data;
      // Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
    } catch (error) {
      console.log('error:', error);
      // if (err?.response?.data?.server) {
      //   const { server: message } = err.response.data;
      //   Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      // }
    }
    loading.value = false;
  };
  const onClickUpdateValueAsistencia = (item, i_inscripcion, i_asistencia) => {
    const estado = alumnos.value[i_inscripcion]?.asistencias?.[i_asistencia]?.estado_asistencia_id;
    if (!Truthty(estado)) return;
    alumnos.value[i_inscripcion].asistencias[i_asistencia].estado_asistencia_id = item.id;
    alumnos.value[i_inscripcion].asistencias[i_asistencia].estado_asistencia = { ...item };
  };
</script>

<template>
  <!-- Modal -->
  <!-- <v-dialog v-model="dialog" max-width="800px"> -->
  <v-dialog width="auto">
    <template v-slot:activator="{ props: activatorProps }">
      <v-btn v-if="edit" :color="colorBtn" @click="buscarAsistencias" v-bind="activatorProps">
        {{ textoBtn }}
      </v-btn>
      <v-btn
        v-else
        class="w-100"
        :color="colorBtn"
        @click="buscarAsistencias"
        v-bind="activatorProps"
      >
        <slot></slot>
      </v-btn>
    </template>
    <template v-slot:default="{ isActive }">
      <v-card title="Asistencias" :loading="loading">
        <template v-slot:append>
          <v-btn icon="mdi-close" variant="text" @click="isActive.value = false"></v-btn>
        </template>
        <v-card-text>
          <v-data-table
            :headers="headers"
            :items="alumnos"
            class="elevation-1 rounded"
            hide-default-footer
          >
            <template v-slot:no-data>
              <v-alert>No hay datos disponibles</v-alert>
            </template>
            <!-- <template v-slot:[`item.usuario.nombreCompleto`]="{ item }">
              {{ item.usuario.nombreCompleto }}
            </template> -->
            <template
              v-for="n in semanas"
              :key="`semana_${n}`"
              v-slot:[`item.semana_${n}`]="{ item, index }"
            >
              <!-- <pre>{{ item.asistencias[n - 1]?.estado_asistencia?.estado }}</pre> -->

              <v-menu>
                <template v-slot:activator="{ props }">
                  <v-chip
                    v-bind="edit ? props : {}"
                    :color="colorEstados[item.asistencias[n - 1]?.estado_asistencia_id]"
                    variant="elevated"
                    class="text-uppercase"
                    size="large"
                  >
                    {{ item.asistencias[n - 1]?.estado_asistencia?.estado[0] }}</v-chip
                  >
                </template>
                <v-list>
                  <v-list-item
                    v-for="est in estados"
                    :key="est.id"
                    :value="est.id"
                    @click="onClickUpdateValueAsistencia(est, index, n - 1)"
                  >
                    <v-list-item-title>{{ est.estado }}</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </template>
          </v-data-table>
        </v-card-text>

        <v-card-actions v-if="edit">
          <v-btn color="secondary" @click="isActive.value = false" :loading="loading"
            >Cancelar</v-btn
          >
          <v-btn color="primary" @click="onClickSave" :loading="loading">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </template>
  </v-dialog>
</template>
