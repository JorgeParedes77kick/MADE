<script setup>
import logoGlobal from '../../../public/images/logo_global.png';
import hello from '../../../public/images/hola.png';
</script>

<template>
    <v-container class="py-0 px-0" style="margin-left:0; max-width:none; max-height: none;">
        <v-fade-transition mode="out-in">
            <v-row no-gutters justify="space-between">
                <v-col cols="12" md="5" lg="5" xl="5" id="loginPanel" >
                    <v-container class="float-md-top position-absolute" style="left: 6%; top: 4%; width: 30%">
                        <v-img :src="logoGlobal" inline cover height="auto" width="30%" ></v-img>
                    </v-container>
                    <v-container class="float-md-end position-absolute" style="left: 6%; bottom: 10%; width: 30%">
                        <v-img :src="hello" inline cover height="auto" width="70%" ></v-img>
                    </v-container>
                </v-col>
                <v-col cols="12" md="7" lg="7" xl="7" id="loginPanelForm">
                    <slot></slot>
                </v-col>
            </v-row>
        </v-fade-transition>
    </v-container>
</template>

<style scoped>

</style>
