<script setup>
  import { Link, router, useForm, usePage } from '@inertiajs/vue3';

  import axios from 'axios';
  import { onMounted, reactive, ref, watch } from 'vue';
  import { useTheme } from 'vuetify';
  import LeftMenuItem from './LeftMenuItem.vue';

  const pageProps = usePage().props;

  const theme = useTheme();
  const isDarkTheme = ref(false);

  const drawer = ref(false);

  //   const csrf = ref(document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

  const loadingPage = ref(false);
  const formLogout = reactive({});

  const fieldRoles = useForm({
    role_id: 0,
  });
  const setOverlay = (v) => (loadingPage.value = v);

  const dynamicMenu = ref([]);
  const userRoles = ref([]);
  const rolSession = ref([]);

  const toggleTheme = () => {
    isDarkTheme.value = !isDarkTheme.value;
    theme.global.name.value = isDarkTheme.value ? 'dark' : 'light';
    localStorage.setItem('theme', isDarkTheme.value ? 'dark' : 'light');
  };
  const toggleDrawer = () => {
    drawer.value = !drawer.value;
    localStorage.setItem('drawer', drawer.value ? 1 : 0);
  };
  const updateDrawer = (value) => {
    drawer.value = value;
    localStorage.setItem('drawer', drawer.value ? 1 : 0);
  };

  onMounted(() => {
    console.log('pageProps:', pageProps);
    isDarkTheme.value = localStorage.getItem('theme') === 'dark';
    drawer.value = Boolean(parseInt(localStorage.getItem('drawer')));
    theme.global.name.value = isDarkTheme.value ? 'dark' : 'light';

    try {
      const {
        auth: { roles, rol_selected },
        menus_layout,
      } = pageProps;
      dynamicMenu.value = menus_layout.map(({ url_ref, ...x }) => ({
        ...x,
        url_ref: url_ref.startsWith('#') || url_ref.startsWith('/') ? url_ref : `/${url_ref}`,
      }));
      userRoles.value = roles;
      rolSession.value = rol_selected;
    } catch (error) {}
  });

  const activeGroup = ref(null);

  function toggleGroup(index) {
    activeGroup.value = activeGroup.value === index ? null : index;
  }

  function handleSubmit(event, link) {
    event.preventDefault();
    setOverlay(true);

    if (!['', '/', '#'].includes(link)) {
      setTimeout(() => {
        if (link === '/logout') {
          axios
            .post(link, formLogout)
            .then((result) => {
              // window.location.href = 'login';
              router.visit('login');
              setOverlay(false);
            })
            .catch((error) => {
              setOverlay(false);
              console.log(JSON.stringify(error.response.data.message));
            });
        } else {
          // window.location.href = link;
          router.visit(link);
          setOverlay(false);
        }
      }, 2000);
    } else {
      setOverlay(false);
    }
  }

  const applyRol = async (event, id) => {
    event.preventDefault();
    setOverlay(true);
    fieldRoles.role_id = id;
    try {
      const response = await axios.post(route('roles.rolApply'), fieldRoles);
      console.log('response?.message:', response);
      if (response?.data?.message) {
        setOverlay(false);
        const { message } = response.data;
        await Swal.fire({ title: 'Éxito!', text: message, icon: 'success' });
        // window.location.href = 'home';
        router.visit(route('home'));
      }
    } catch (err) {
      console.log(err?.response);
      if (err?.response?.data?.server) {
        const { server: message } = err.response.data;
        Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      }
      if (err?.response?.data?.errors) {
        const { errors } = err.response.data;
        inputForm.errors = errors;
      }
    } finally {
      setOverlay(false);
    }
  };

  const myApp = ref([
    { title: 'Home', icon: 'mdi-home', link: '/home' },
    { title: 'Mi perfil', icon: 'mdi-account', link: '/mi-perfil' },
    { title: 'Roles', icon: 'mdi-power', link: '#' },
    { title: 'Cerrar Sesión', icon: 'mdi-power', link: '/logout' },
  ]);
  watch(
    () => userRoles.value.length,
    (new_value) => {
      if (new_value === 1) {
        const index = myApp.value.findIndex((x) => x.title == 'Roles');
        if (index != -1) myApp.value.splice(index, 1);
      }
    },
  );
</script>
<template>
  <v-app>
    <v-app-bar app color="navbar-color" class="text-navbar-text">
      <div class="mr-auto ml-2 d-flex">
        <Link class="v-btn--icon v-btn--density-default my-auto" :href="route('home')">
          <img
            src="/img/logos/logo_global_blanco.png"
            width="100"
            class="px-2"
            style="filter: drop-shadow(3px 3px 3px rgba(153, 197, 192, 1))"
            alt="arm global"
        /></Link>
        <v-app-bar-nav-icon @click="toggleDrawer"></v-app-bar-nav-icon>
      </div>
      <div class="d-flex align-center ml-auto mr-2">
        <v-btn v-if="isDarkTheme" icon="mdi-weather-night" @click="toggleTheme" />
        <v-btn v-else icon="mdi-weather-sunny" @click="toggleTheme" />
        <v-btn color="#99c5c0" rounded="xl">
          <v-avatar
            size="30"
            v-if="pageProps?.auth?.user?.persona.fotografia"
            :image="`storage/img/perfil/${pageProps?.auth?.user?.persona.fotografia}`"
          >
          </v-avatar>
          <v-avatar size="30" v-else :image="`img/fotoperfil/perfil.png`"> </v-avatar>
          <!-- {{ pageProps?.auth?.user?.persona?.nombre.trim() }} -->

          <v-menu activator="parent" location="bottom" open-on-hover>
            <v-list>
              <v-list-item v-for="(item, i) in myApp" :key="i" link>
                <v-list-item-title>
                  <v-form @submit.prevent="handleSubmit($event, item.link)">
                    <v-btn size="small" variant="plain" type="submit">
                      {{ item.title }}
                    </v-btn>
                  </v-form>
                </v-list-item-title>
                <template v-slot:prepend>
                  <template v-if="item.title !== 'Roles'">
                    <v-icon :icon="item.icon" size="small"></v-icon>
                  </template>
                  <template v-else>
                    <v-icon icon="mdi-menu-left" size="small"></v-icon>
                  </template>
                </template>
                <template v-if="item.title === 'Roles'">
                  <v-menu :open-on-focus="false" activator="parent" submenu location="start">
                    <v-list>
                      <v-list-item
                        v-for="(userRol, r) in userRoles"
                        :key="r"
                        link
                        @click="applyRol($event, userRol.id)"
                      >
                        <v-list-item-title>{{ userRol.nombre }}</v-list-item-title>
                        <template v-slot:prepend v-if="userRol.id === rolSession">
                          <v-icon icon="mdi-check-decagram" size="small"></v-icon>
                        </template>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </template>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-btn>
      </div>
    </v-app-bar>
    <v-navigation-drawer
      color="navbar-color"
      v-model="drawer"
      @update:modelValue="updateDrawer"
      app
      class="text-navbar-text"
    >
      <!-- Sidebar content -->
      <!-- <template v-for="(item, index) in items" :key="index">
        <v-hover>
          <template v-slot:default="{ isHovering, props }">
            <v-list-item
              :title="item.title"
              :to="item.link"
              v-bind="props"
              :class="
                classnames({
                  'bg-navbar-hover': isHovering,
                  'text-navbar-hover-text': isHovering,
                })
              "
            >
            </v-list-item>
          </template>
</v-hover>
</template> -->
      <!-- Dynamic Menu Init-->
      <v-list>
        <template v-for="(menu, index) in dynamicMenu" :key="menu.id">
          <template v-if="menu.menu_padre_id === null">
            <LeftMenuItem :menu="menu" :activeGroup="activeGroup"></LeftMenuItem>
          </template>
        </template>
      </v-list>
      <!-- Dynamic Menu Finish-->
    </v-navigation-drawer>

    <v-main id="body-app">
      <slot></slot>
    </v-main>
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
  </v-app>
</template>
