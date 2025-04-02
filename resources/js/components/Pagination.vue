<script setup>
  import { defineProps, ref, watch } from 'vue';

  const props = defineProps({
    current_page: { type: Number, default: -1 },
    from: { type: Number, default: -1 },
    last_page: { type: Number, default: -1 },
    path: { type: String, default: '' },
    per_page: { type: [Number, String], default: '' },
    to: { type: Number, default: -1 },
    total: { type: Number, default: -1 },
    onChangePage: { type: Function, default: (...args) => {} },
    onChangePerPage: { type: Function, default: (...args) => {} },
  });

  // // Computed para current_page
  const current = ref(props.current_page);
  const perPage = ref(props.per_page);
  // // Watch para detectar cambios en current_page
  watch(
    () => props.current_page,
    (newPage, oldPage) => {
      current.value = newPage;
      console.log('newPage:', newPage);
    },
  );
  watch(
    () => props.per_page,
    (newPage, oldPage) => {
      perPage.value = parseInt(newPage);
      current.value = 1;
    },
  );

  const updateModelPaginate = (item) => {
    props.onChangePage(item);
  };
  const updatePerpage = (item) => {
    props.onChangePerPage(item);
  };
</script>

<template>
  <v-row justify="end" align="center">
    <v-col class="ml-2 d-none d-sm-none d-md-flex d-lg-flex d-xl-flex justify-start" md="1" lg="3">
      <i>{{ total }}</i>
    </v-col>
    <v-col class="d-none d-sm-none d-md-flex d-lg-flex d-xl-flex justify-end" md="2" lg="2">
      <label for="perPageSelect">Items por página:</label>
    </v-col>
    <v-col
      cols="5"
      sm="3"
      md="2"
      lg="2"
      class="d-flex justify-end justify-md-start justify-lg-start justify-xl-start"
    >
      <v-select
        id="perPageSelect"
        class="select-paginate"
        :items="[10, 20, 30, 50, 100]"
        variant="outlined"
        density="compact"
        v-model="perPage"
        hide-details
        style="max-width: 6rem"
        @update:modelValue="updatePerpage"
      />
    </v-col>
    <v-col cols="9" md="6" lg="4" xl="2" class="d-flex justify-end">
      <v-pagination
        v-model="current"
        :length="last_page"
        :total-visible="6"
        @update:modelValue="updateModelPaginate"
        size="small"
      />
    </v-col>
  </v-row>
</template>
<style>
  /* .select-paginate .v-input__details {
  display: none;
} */
</style>
