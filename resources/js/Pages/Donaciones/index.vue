<script setup>
import {defineProps, onMounted} from "vue";
import RegisterLayout from "../../layouts/RegisterLayout.vue";
import Form from "./form.vue";
import {truncarTexto} from "../../utils/string";
import MainLayout from "../../components/Layout.vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
    donaciones: Array,
    status: Number,
});

const headers = [
    { title: 'ID', key: 'id', fixed: true },
    { title: 'Monto', key: 'monto' },
    { title: 'Motivo', key: 'motivo' },
    { title: 'Comprobante', key: 'comprobante', minWidth: '20rem' },
    { title: 'Acciones', key: 'acciones', sortable: false },
];

const onClickDelete = async (item) => {
    const { isConfirmed } = await Swal.fire({
        title: 'Eliminar Donacion',
        text: `Estas seguro de eliminar el Donacion?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Aceptar',
        cancelButtonText: 'Cancelar',
    });
    if (isConfirmed) {

    }
};

const showFile = (source) => {
    Swal.fire({
        title: "",
        text: "",
        width: 600,
        html: "<img src='" + source + "' alt='comprobante' style='width:100%;'>",
    });
};

const formatPrice = (value) => {
    let val = (value/1).toFixed(2).replace('.', ',')
    return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")
};

onMounted(() =>
    setTimeout(function () {
        console.log(props.donaciones);
    }, 1700),
);

</script>

<template>
    <MainLayout>
        <v-container fluid>
            <v-card color="background" class="px-4 py-2" :loading="loading">
                <v-card-title>Mis Donaciones </v-card-title>

                <v-row>
                    <v-col class="gridBtns">
                        <Link :href="route('donaciones.create')">
                            <v-btn :to="{ name: 'donaciones.create' }" color="success" class="ms-auto">
                                Donar
                            </v-btn>
                        </Link>
                    </v-col>
                </v-row>
                <v-row justify="center">
                    <v-col>
                        <v-data-table
                            :headers="headers"
                            :items="donaciones"
                            :items-per-page="10"
                            class="elevation-1 rounded"
                        >
                            <template v-slot:no-data>Información no encontrada</template>
                            <template v-slot:[`item.monto`]="{ item }">
                                {{ formatPrice(item.monto) }}
                            </template>
                            <template v-slot:[`item.comprobante`]="{ item }">
                                <v-icon icon="mdi-check-decagram" size="small" style="color: green;" @click="showFile(item.comprobante)"></v-icon>
                            </template>
                            <template v-slot:[`item.acciones`]="{ item }">
                                <div class="d-flex inline-flex ga-2">
                                    <Link :href="route('donaciones.show', item)">
                                        <v-btn as="v-btn" color="info" small> Ver </v-btn>
                                    </Link>
                                    <Link :href="route('donaciones.edit', item)">
                                        <v-btn
                                            :to="{ name: 'donaciones.edit', params: { id: item.id } }"
                                            color="secondary"
                                            small
                                        >
                                            Editar
                                        </v-btn>
                                    </Link>
                                    <v-btn color="error" small @click="onClickDelete(item)">Eliminar </v-btn>
                                </div>
                            </template>
                        </v-data-table>
                    </v-col>
                </v-row>
            </v-card>
        </v-container>
    </MainLayout>
</template>

