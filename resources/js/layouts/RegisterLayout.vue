<script setup></script>

<template>
  <v-app id="registerPanel">
    <div class="h-100 w-100 d-flex align-center justify-center">
      <v-container>
        <v-fade-transition mode="out-in">
          <v-row no-gutters>
            <v-col cols="12">
              <slot></slot>
            </v-col>
          </v-row>
        </v-fade-transition>
      </v-container>
    </div>
  </v-app>
</template>

<style scoped></style>
