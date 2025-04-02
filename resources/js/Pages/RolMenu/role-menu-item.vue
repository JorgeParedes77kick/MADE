<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import axios from 'axios';
  import { defineProps, onMounted, ref } from 'vue';

  const props = defineProps({
    rootMenu: Array,
    subMenu: String,
    roles: Array,
    rolesMenus: Array,
  });

  const loadingPage = ref(false);
  const setOverlay = (v) => (loadingPage.value = v);

  const inputForm = useForm({
    selectedRolMenu: [],
    rol_id: 0,
    menu_id: 0,
    state: 0,
  });

  function isConfig(menu, rol) {
    //console.log("menu/rol ", menu, rol);
    let exist = false;
    props.rolesMenus.forEach((rolMenu, index) => {
      //console.log("rolMenu ", rolMenu);
      if (rolMenu.menu_id === menu && rolMenu.rol_id === rol) {
        exist = true;
      }
    });
    //console.log("exist ", exist);
    return exist;
  }

  const isChange = async (event) => {
    event.preventDefault();
    let element = event.target;
    console.log('element ', element);
    console.log('selectedRolMenu ', inputForm.selectedRolMenu);

    let checked = element.getAttribute('checked');
    let id = element.getAttribute('id');
    inputForm.rol_id = id.split('mr')[1];
    inputForm.menu_id = id.split('mr')[0];
    if (checked != null) {
      console.log('checked true ', checked);
      inputForm.state = 1;
    } else {
      console.log('checked false ', checked);
      inputForm.state = 0;
    }
    await submitForm();
  };

  const submitForm = async (form) => {
    setOverlay(true);
    try {
      const result = await axios['post'](route('rol-menu.store'), inputForm);
      console.log('result ', result);
      if (result?.data?.message) {
        setOverlay(false);
        console.log('result.data ', result.data);
        const { message } = result.data;
        await Swal.fire({ title: '<i>Éxito!</i>', html: message, icon: 'success' });
      }
    } catch (error) {
      console.log(error?.response);
      if (error?.response?.data?.server) {
        const { server: message } = error.response.data;
        Swal.fire({ title: 'Error!', html: message, icon: 'error' });
      }
      if (error?.response?.data?.errors) {
        const { errors } = error.response.data;
        fieldsForm.errors = errors;
        Swal.fire({ title: 'Error!', html: errors, icon: 'error' });
      }
    } finally {
      setOverlay(false);
    }
  };

  onMounted(() => {
    //console.log("roles ", props.roles);
    //console.log("rolesMenus ", props.rolesMenus);
  });
</script>

<template>
  <tr>
    <td :class="subMenu === 'yes' ? 'font-italic' : 'font-weight-black'">
      <v-icon
        :icon="subMenu === 'yes' ? 'mdi-chevron-double-right' : 'mdi-circle-medium'"
        style="color: darkred; font-size: 17px"
        :class="subMenu === 'yes' ? 'pl-5' : 'pl-0'"
      ></v-icon
      >&nbsp;{{ rootMenu.nombre }}
    </td>

    <td v-for="rol in roles" :key="rol.nombre" class="text-center">
      <v-checkbox
        :id="rootMenu.id + 'mr' + rol.id"
        :key="rootMenu.id + 'mr' + rol.id"
        :model-value="isConfig(rootMenu.id, rol.id)"
        @change="isChange"
        color="red"
        class="d-inline-flex"
      ></v-checkbox>
    </td>
  </tr>
  <template v-if="rootMenu.submenu.length">
    <role-menu-item
      v-for="(subMenu, index) in rootMenu.submenu"
      :rootMenu="subMenu"
      subMenu="yes"
      :roles="roles"
      :rolesMenus="rolesMenus"
    ></role-menu-item>
  </template>
  <v-overlay
    :model-value="loadingPage"
    opacity="0.80"
    :absolute="true"
    contained
    persistent
    class="align-center justify-center"
  >
    <v-progress-circular style="color: #99c5c0" size="37" indeterminate></v-progress-circular>
  </v-overlay>
</template>

<style scoped></style>
