<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import axios from 'axios';
  import { defineProps, onMounted, ref } from 'vue';
  import corona from '../../../../public/images/corona.png';
  import book from '../../../../public/images/libro.png';
  import logGP from '../../../../public/images/logo_gp.png';
  import women from '../../../../public/images/mujer.png';
  import tween from '../../../../public/images/tweens.png';

  const props = defineProps({
    mail: String,
  });

  const loadingPage = ref(false);
  const setOverlay = (v) => (loadingPage.value = v);
  const setEmail = (v) => (fieldsForm.email = v);

  const btnActive = ref(true);
  const setBtnActive = (v) => (btnActive.value = v);
  const passConfirmEqualPass = () =>
    fieldsForm.password_confirmation === fieldsForm.password ||
    'Contraseña Confirmación no coincide';

  const fieldsForm = useForm({
    email: '',
    password: '',
    password_confirmation: '',
    token: '',
  });
  const formNewPass = ref(null);
  const validateForm = async (e) => {
    setOverlay(true);
    e.preventDefault();
    fieldsForm.clearErrors();
    const { valid } = await formNewPass.value.validate();
    if (valid) await submitForm();
    setOverlay(false);
  };
  const submitForm = async (formNewPass) => {
    setOverlay(true);
    try {
      const result = await axios['post'](route('password.update'), fieldsForm);

      if (result?.data?.message) {
        setOverlay(false);
        const { message } = result.data;
        await Swal.fire({
          title: '<i>Éxito!</i>',
          html: 'Su <b>contrase&ntilde;a</b> ha sido actualiza!',
          icon: 'success',
        });
        window.location.href = route('login');
      }
    } catch (error) {
      console.log(JSON.stringify(error));
      if (error?.response?.data?.server) {
        const { server: message } = error.response.data;
        Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      }

      if (error?.response?.data?.errors) {
        const { errors } = error.response.data;
        fieldsForm.errors = errors;
      }
      if (error?.response?.status === 500) {
        Swal.fire({
          title: '<i>Error!</i>',
          html: 'Ocurrio un error al registrar nueva contraseña, Favor contactar al administrador de la Página',
          icon: 'error',
        });
      }
    } finally {
      setOverlay(false);
    }
  };

  const validateToken = async () => {
    setOverlay(true);

    await axios
      .get('/user/validate-token/' + fieldsForm.email + '/' + fieldsForm.token)
      .then((result) => {
        setOverlay(false);
        if (result) {
          if (result.status === 200) {
            if (result.data.canResetPass === false) {
              Swal.fire({
                title: 'Error!',
                text: 'Estimado usuario, el link a caducado, por favor realice una nueva solicitud ',
                icon: 'error',
                allowOutsideClick: false,
                allowEscapeKey: false,
              }).then((result) => {});
            } else {
              setBtnActive(false);
            }
            console.log('1', result.data);
          } else {
            console.log('2', JSON.stringify(result));
          }
        } else {
          console.log('3', JSON.stringify(result));
        }
      })
      .catch((error) => {
        console.log('4', JSON.stringify(error));
        console.log(JSON.stringify(error.response.data.message));
        setOverlay(false);
      });
  };

  onMounted(() => {
    setOverlay(true);
    const { pathname } = window.location;
    const splitPathname = pathname.split('/');
    const itemId = splitPathname[splitPathname.length - 1];
    fieldsForm.token = itemId;
    setEmail(props.mail);
    //const csrfToken = document.querySelector('head meta[name="csrf-token"]');
    //alert(csrfToken.name+""+csrfToken.content);
    validateToken();
  });
</script>

<template>
  <v-container
    id="resetPassContainer"
    fluid
    class="py-0 px-0"
    color="#222222"
    style="height: 100vh; width: 40%"
  >
    <v-row no-gutters>
      <v-fab
        color="light"
        icon="mdi-reply-circle"
        variant="flat"
        href="/login"
        location="top start"
        absolute
        style="right: 24px; top: 0"
      ></v-fab>
    </v-row>
    <v-card
      elevation="12"
      class="d-flex align-center justify-center"
      color="#222222"
      style="height: 100vh"
    >
      <v-row>
        <v-container fuild class="float-md position-absolute" style="left: 22%">
          <v-img :src="women" inline cover height="auto" width="4%"></v-img>
        </v-container>
        <v-container fuild class="float-md position-absolute" style="left: 28%">
          <v-img :src="corona" inline cover height="auto" width="6%"></v-img>
        </v-container>
        <v-container fuild class="float-md position-absolute" style="left: 61%">
          <v-img :src="book" inline cover height="auto" width="7%"></v-img>
        </v-container>
        <v-container fuild class="float-md position-absolute" style="left: 69%">
          <v-img :src="tween" inline cover height="auto" width="4%"></v-img>
        </v-container>
        <v-col cols="12" class="d-flex justify-center">
          <v-img :src="logGP" inline cover height="auto" width="25%"></v-img>
        </v-col>
        <v-col class="d-flex justify-center">
          <v-form @submit="validateForm" ref="formNewPass" class="w-50" lazy-validation>
            <v-row no-gutters>
              <v-col cols="12">
                <v-icon icon="mdi-lock-reset" style="color: #99c5c0; font-size: 23px"></v-icon
                >&nbsp;<v-label style="color: #99c5c0; font-size: 17px"
                  >Registro Nueva Contraseña
                </v-label>
                <v-divider
                  style="color: #f4ede8; padding-top: 2pt; margin-inline-start: 8%"
                ></v-divider>
              </v-col>
            </v-row>
            <legend>&nbsp;</legend>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="fieldsForm.email"
                  :value="mail"
                  label="Correo Electr&oacute;nico"
                  variant="outlined"
                  placeholder="johndoe@gmail.com"
                  name="mail"
                  style="color: #f4ede8"
                  class="rounded-l"
                  :rules="[rules.required, rules.email]"
                  tabindex="1"
                  :readonly="true"
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="fieldsForm.password"
                  label="Contrase&nacute;a"
                  variant="outlined"
                  placeholder="******"
                  name="password"
                  type="password"
                  style="color: #f4ede8"
                  class="rounded-l"
                  :rules="[rules.required, rules.counter_pass]"
                  :error-messages="fieldsForm.errors.password"
                  clearable
                  tabindex="2"
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="fieldsForm.password_confirmation"
                  label="Confirme Contrase&nacute;a"
                  variant="outlined"
                  placeholder="******"
                  name="password_confirm"
                  type="password"
                  style="color: #f4ede8"
                  class="rounded-l"
                  :rules="[rules.required, passConfirmEqualPass, rules.counter_pass]"
                  clearable
                  tabindex="3"
                />
              </v-col>
              <v-col cols="12">
                <v-btn
                  :disabled="btnActive"
                  type="submit"
                  block
                  style="
                    background-color: #99c5c0;
                    font-weight: bolder;
                    font-size: 14pt;
                    border-color: beige;
                    border-width: 2pt;
                  "
                  >Crear Nueva Contraseña</v-btn
                >
              </v-col>

              <v-col cols="12" class="d-flex justify-center">
                <v-btn
                  type="submit"
                  class="btn-login"
                  block
                  style="
                    background-color: #99c5c0;
                    font-weight: bolder;
                    border-color: beige;
                    border-width: 2pt;
                  "
                >
                  Crear Nueva Contrase&nacute;aa</v-btn
                >
              </v-col>
            </v-row>
          </v-form>
        </v-col>
      </v-row>
    </v-card>
  </v-container>
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

<style lang="scss" scoped></style>

<script>
  export default {
    data: () => ({
      email: '',
      rules: {
        required: (value) => !!value || 'Campo Requerido.',
        counter_pass: (value) => value.length >= 8 || 'Min 8 characters',
        email: (value) => {
          const pattern =
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || 'Correo no válido';
        },
      },
    }),
  };
</script>
