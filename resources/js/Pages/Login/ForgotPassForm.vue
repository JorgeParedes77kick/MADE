<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import axios from 'axios';
  import { ref } from 'vue';
  import corona from '../../../../public/images/corona.png';
  import book from '../../../../public/images/libro.png';
  import logGP from '../../../../public/images/logo_gp.png';
  import women from '../../../../public/images/mujer.png';
  import tween from '../../../../public/images/tweens.png';

  const loadingPage = ref(false);
  const setOverlay = (v) => (loadingPage.value = v);

  const message = ref('');
  const setMessage = (v) => (message.value = v);
  const mailConfirmEqualMail = () =>
    fieldsForm.email_confirm === fieldsForm.email || 'Correo Confirmación no coincide';

  const fieldsForm = useForm({
    email: '',
    email_confirm: '',
  });
  const formForgotPass = ref(null);
  const validateForm = async (e) => {
    e.preventDefault();
    fieldsForm.clearErrors();

    const { valid } = await formForgotPass.value.validate();

    if (valid) await submitForm();
  };
  const submitForm = async (form) => {
    setOverlay(true);
    try {
      const result = await axios['post'](route('password.email'), fieldsForm);
      if (result?.data?.message) {
        setOverlay(false);
        const { message } = result.data;
        await Swal.fire({
          title: '<i>Éxito!</i>',
          html:
            'Le hemos enviado al correo electrónico <b>' +
            fieldsForm.email +
            '</b>, su enlace de restablecimiento de contraseña',
          icon: 'success',
        });
        window.location.href = route('login');
      }
    } catch (error) {
      console.log(error?.response);
      if (error?.response?.data?.server) {
        const { server: message } = error.response.data;
        Swal.fire({ title: 'Error!', text: message, icon: 'error' });
      }
      if (error?.response?.data?.errors) {
        const { errors } = error.response.data;
        fieldsForm.errors = errors;
      }
    } finally {
      setOverlay(false);
    }
  };
</script>

<template>
  <v-container
    id="forgotPassContainer"
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
        href="login"
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
          <v-form @submit="validateForm" ref="formForgotPass" class="w-50">
            <v-row no-gutters>
              <v-col cols="12">
                <v-icon
                  icon="mdi-email-heart-outline"
                  style="color: #99c5c0; font-size: 23px"
                ></v-icon
                >&nbsp;<v-label style="color: #99c5c0; font-size: 17px"
                  >Solicitud Nueva Contraseña
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
                  label="Correo Electr&oacute;nico"
                  variant="outlined"
                  placeholder="johndoe@gmail.com"
                  name="mail"
                  style="color: #f4ede8"
                  class="rounded-l"
                  :rules="[rules.required, rules.email]"
                  :error-messages="fieldsForm.errors.email"
                  clearable
                  tabindex="1"
                />
              </v-col>
              <v-col cols="12">
                <v-text-field
                  v-model="fieldsForm.email_confirm"
                  label="Confirme Correo Electr&oacute;nico"
                  variant="outlined"
                  placeholder="johndoe@gmail.com"
                  name="mail"
                  style="color: #f4ede8"
                  class="rounded-l"
                  :rules="[rules.required, rules.email, mailConfirmEqualMail]"
                  clearable
                  tabindex="2"
                />
              </v-col>
              <!--<v-col cols="12" >
                <v-btn type="submit" block style="background-color: #99c5c0;
                            font-weight: bolder; font-size: 14pt; border-color: beige;
                            border-width: 2pt; ">Recuperar Contrase&nacute;a</v-btn>
              </v-col> -->

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
                  Recuperar Contrase&nacute;a</v-btn
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
        email: (value) => {
          const pattern =
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || 'Correo no válido';
        },
      },
    }),
    methods: {
      validate() {
        this.$refs.formForgotPass.validate();
      },
    },
  };
</script>
