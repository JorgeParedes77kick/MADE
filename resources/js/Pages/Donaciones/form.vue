<script setup>
import { useForm } from '@inertiajs/inertia-vue3';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { defineProps, inject, onMounted, ref } from 'vue';

import ButtonBack from '../../components/ButtonBack';

import MainLayout from '../../components/Layout';
import { ACCION, CRUD, TEXT_BUTTON } from '../../constants/form';
import { previewImage } from '../../utils/image';

const validate = inject('$validation');

const props = defineProps({
    action: String,
    donation: { type: Object, default: {} },
    inscripciones: Array,
    status: String,
});

const loading = ref(false);
const isDisabled = ref(props.action === CRUD.show);

const inputForm = useForm({
    monto: '',
    montoFormat: '',
    motivo: '',
    descripcion: '',
    comprobante: '',
    grupo_pequeno_id: '',
    imagen_landing: '',
    ...props.donation,
    comprobanteFile: null,
});
const form = ref(null);

onMounted(() => {
    if (props.donation.id) inputForm.comprobanteFile = new File([''], props.donation.imagen);
    setTimeout(function () {
        console.log(props.inscripciones);
    }, 1700);
});

const onChangeFile = () => {
    inputForm.comprobante = '';
    inputForm.errors.comprobanteFile = null;
};

const checkDigit = (event) => {
    console.info(event.target.value);
    console.info(event.key);
    console.info(event.which);
    const pattern = /^[\d\s()+-]+$/;
    if (!pattern.test(event.key) && event.which !== 46 && event.which !== 8) {
        event.preventDefault();
        console.info("no valido");
    }

    if (pattern.test(event.key)){
        inputForm.monto = inputForm.monto.concat(event.key);
    }
    console.log(inputForm.monto);
};

const formatPrice = () => {
    let val = (inputForm.monto/1).toFixed(2).replace('.', ',')
    inputForm.montoFormat = val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
};

const validateForm = async (e) => {
    e.preventDefault();
    inputForm.clearErrors();
    const { valid } = await form.value.validate();
    if (valid) submit();
};

const submit = async () => {
    loading.value = true;
    const action = props.action === CRUD.edit ? 'update' : 'store';
    const routeName = `donaciones.${action}`;
    const id = props.action === CRUD.edit ? inputForm.id : null;
    const config = { headers: { 'Content-Type': 'multipart/form-data' } };
    const formData = new FormData();
    const keys = Object.keys(inputForm);
    for (let i = 0; i < keys.length; i++) {
        const key = keys[i];
        formData.append(key, inputForm[key]);
        if (key === 'comprobanteFile') break;
    }
    console.info(routeName);
    try {
        const response = await axios.post(route(routeName, id), formData, config);
        if (response?.data?.message) {
            const { message } = response.data;
            await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });

            router.visit(route('donaciones.index'));
        }
    } catch (err) {
        console.log(err?.response);
        if (err?.response?.data?.server) {
            const { server: message } = err.response.data;
            Swal.fire({ title: 'Error!', text: message, icon: 'error' });
        }
        console.log('err?.response?.data?.errors:', err?.response?.data?.errors);
        if (err?.response?.data?.errors) {
            const { errors } = err.response.data;
            inputForm.errors = errors;
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <MainLayout>
        <v-container fluid>
            <v-card color="background" class="" :disabled="loading" :loading="loading">
                <template v-slot:loader="{ isActive }">
                    <v-progress-linear :active="isActive" color="primary" height="4" indeterminate />
                </template>
                <v-card-title>
                    <ButtonBack :href="route('donaciones.index')" /> DONACI&Oacute;N
                    {{ CRUD.create !== action ? `#${inputForm.id}` : '' }}
                </v-card-title>
                <v-card-subtitle>{{ ACCION[action] }} de Donaci&oacute;n</v-card-subtitle>
                <v-card-text>
                    <v-form @submit="validateForm" ref="form" lazy-validation>
                        <v-row class="row-gap-2">
                            <v-col cols="12" sm="6">
                                <v-text-field
                                    type="input"
                                    id="monto"
                                    name="monto"
                                    label="Monto"
                                    v-model="inputForm.montoFormat"
                                    :disabled="isDisabled"
                                    :rules="validate('Monto', 'required')"
                                    :error-messages="inputForm.errors.monto"
                                    clearable
                                    @keydown="checkDigit"
                                    @update:modelValue="formatPrice"
                                    tabindex="1"
                                />
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-text-field
                                    id="motivo"
                                    name="motivo"
                                    label="Motivo"
                                    v-model="inputForm.motivo"
                                    :disabled="isDisabled"
                                    :rules="validate('Motivo', 'required')"
                                    :error-messages="inputForm.errors.motivo"
                                    tabindex="2"
                                />
                            </v-col>

                            <v-col cols="12" sm="6">
                                <v-file-input
                                    id="comprobante"
                                    name="comprobante"
                                    label="comprobante"
                                    v-model="inputForm.comprobanteFile"
                                    :disabled="isDisabled"
                                    :error-messages="inputForm.errors.comprobanteFile || inputForm.errors.comprobante"
                                    :rules="[rules.requiredFile]"
                                    accept="image/png, image/jpeg, image/bmp"
                                    placeholder="Pick an avatar"
                                    prepend-icon="mdi-camera"
                                    @update:modelValue="onChangeFile"
                                    tabindex="3"
                                />
                                <v-img
                                    v-if="inputForm.comprobanteFile"
                                    :src="previewImage(inputForm.comprobanteFile)"
                                    max-width="500"
                                    max-height="500"
                                    class="d-block mx-auto"
                                />
                                <v-img
                                    v-if="typeof inputForm.comprobante == 'string' && inputForm.comprobante.length > 0"
                                    :src="`/storage/img/donaciones/${inputForm.comprobante}`"
                                    max-width="500"
                                    max-height="500"
                                    class="d-block mx-auto"
                                />
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-select
                                    v-model="inputForm.grupo_pequeno_id"
                                    name="grupo_pequeno_id"
                                    label="Curriculum"
                                    :items="props.inscripciones"
                                    item-title="nombre"
                                    item-value="id"
                                    variant="outlined"
                                    class="rounded-l"
                                    clearable
                                    tabindex="4"
                                ></v-select>
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
                </v-card-text>
            </v-card>
        </v-container>
    </MainLayout>
</template>

<script>
export default {
    data: () => ({
        rules: {
            requiredFile: (v) => (v && v.length > 0) || 'Comprobante Requerido.',
        },
    }),
    methods: {
    },
};
</script>
