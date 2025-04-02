<script setup>
import logoGlobal from '../../../public/images/logo_global.png';
import hello from '../../../public/images/hola.png';
</script>

<template>
    <v-container fuild class="py-0 px-0" style="margin-left:0px; max-width:none;">
        <v-fade-transition mode="out-in">
            <v-row no-gutters>
                <v-col cols="12" id="registerPanel">
                  <slot></slot>
                </v-col>
            </v-row>
        </v-fade-transition>
    </v-container>
</template>

<style scoped>

</style>
