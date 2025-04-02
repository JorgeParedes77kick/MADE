<script setup>
  import { useForm } from '@inertiajs/inertia-vue3';
  import axios from 'axios';
  import { defineProps, onBeforeMount, onMounted, ref } from 'vue';
  import corona from '../../../../public/images/corona.png';
  import book from '../../../../public/images/libro.png';
  import logoGlobal from '../../../../public/images/logo_global.png';
  import logGP from '../../../../public/images/logo_gp.png';
  import women from '../../../../public/images/mujer.png';
  import tween from '../../../../public/images/tweens.png';
  import { checkRut } from '../../constants/form';

  const props = defineProps({
    genderList: Array,
    civilStatusList: Array,
    nationalityList: Array,
    countryList: Array,
    typeDocumentsList: Array,
  });

  const loadingPage = ref(false);

  const expand = ref(false);

  const setExpand = (v) => (expand.value = v);

  const message = ref('');

  const setOverlay = (v) => (loadingPage.value = v);

  const setMessage = (v) => (message.value = v);

  const formRegister = ref(null);
  const fieldsForm = useForm({
    nombre: '',
    apellido: '',
    tipo_documento_id: '',
    dni: '',
    fecha_nacimiento: '',
    genero_id: '',
    estado_civil_id: '',
    nacionalidad_id: '',
    pais_residencia: '',
    region_id: '',
    ciudad: '',
    direccion: '',
    telefono: '',
    ocupacion: '',
    email: '',
    email_confirm: '',
    password: '',
    password_confirm: '',
    persona_id: '',
    nick_name: '',
  });

  const mailConfirmEqualMail = () =>
    fieldsForm.email_confirm === fieldsForm.email || 'Correo Confirmación no coincide';
  const passConfirmEqualPass = () =>
    fieldsForm.password_confirm === fieldsForm.password || 'Contraseña Confirmación no coincide';
  const isDocValid = (v) => {
    console.log(v);
    if (fieldsForm.tipo_documento_id === 1) {
      return checkRut(v) || 'RUT No Valido';
    } else {
      return true;
    }
  };
  const regionList = ref([]);
  const setRegion = (v) => (regionList.value = v);

  const initialize = () => {
    setExpand(true);

    setOverlay(false);
  };

  const validateForm = async (e) => {
    setOverlay(true);
    e.preventDefault();
    fieldsForm.clearErrors();
    const { valid } = await formRegister.value.validate();
    if (valid) handleSubmit();
    setOverlay(false);
  };
  function handleSubmit(e) {
    // make api request
    setMessage('');
    setOverlay(true);
    createNickName();
    axios
      .post('/persona/store', fieldsForm)
      .then((result) => {
        /*console.log("1 "+JSON.stringify(result));
    console.log("2 "+JSON.stringify(result.response));
    console.log("3 "+JSON.stringify(result.data));
    console.log("4 "+JSON.stringify(result.data.person));
    console.log("5 "+JSON.stringify(result.data.person.id));
    console.log("6 "+JSON.stringify(result.status));*/
        setMessage('');
        if (result.status === 200) {
          setOverlay(false);
          Swal.fire({
            title: 'Éxito!',
            text: JSON.stringify(result.data.person),
            icon: 'success',
            allowOutsideClick: false,
            allowEscapeKey: false,
          }).then((result) => {
            window.location.href = route('login');
          });
        } else {
          setMessage(JSON.stringify(result.data));
          setOverlay(false);
        }
      })
      .catch((error) => {
        console.log('catch ', error);
        setOverlay(false);
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          /* console.log("1 " +JSON.stringify(error.response.data));
      console.log("2 " +JSON.stringify(error.response.status));
      console.log("3 " +JSON.stringify(error.response.headers)); */
          if (error.response.status >= 500) {
            setMessage('Error de Sistema, Favor contactar al administrador');
          } else {
            if (error?.response?.data?.errors) {
              const { errors } = error.response.data;
              fieldsForm.errors = errors;
            } else {
              setMessage('Error al Registrar, Favor contactar al administrador');
            }
          }
        } else if (error.request) {
          // The request was made but no response was received
          // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
          // http.ClientRequest in node.js
          // console.log("4 " +JSON.stringify(error.request));
          setMessage('Error al Registrar, Favor contactar al administrador');
        } else {
          // Something happened in setting up the request that triggered an Error
          // console.log('5 Error', error.message);
          setMessage('Error al Registrar, Favor contactar al administrador');
        }
      });
  }

  function createNickName() {
    var names = fieldsForm.nombre.split(' ');
    var apell = fieldsForm.apellido.split(' ');
    var nickName = names[0].trim() + '.' + apell[0].trim();
    fieldsForm.nick_name = nickName
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');
  }

  function updateRegion() {
    setRegion([]);
    fieldsForm.region_id = '';
    Object.values(props.countryList).forEach((country) => {
      if (country.id === fieldsForm.pais_residencia) {
        const newList = Object.values(country.regiones).sort((a, b) => {
          if (a['nombre'] < b['nombre']) return -1;
          if (a['nombre'] > b['nombre']) return 1;
          return 0;
        });
        setRegion(newList);
      }
    });
  }

  const checkDigit = (event) => {
    const pattern = /^[\d\s()+-]+$/;
    if (!pattern.test(event.key) && event.which !== 46 && event.which !== 8) {
      event.preventDefault();
    }
  };

  onBeforeMount(() => setOverlay(true));

  onMounted(() =>
    setTimeout(function () {
      console.log(props.countryList);
      initialize();
    }, 1700),
  );
</script>

<template>
  <v-container
    id="registerContainer"
    fill-height
    fuild
    style="background-color: #222222; max-width: none; width: 60%; max-height: none"
  >
    <v-row>
      <v-fab
        color="light"
        icon="mdi-reply-circle"
        variant="flat"
        href="login"
        location="top end"
        absolute
        style="left: 20px"
      ></v-fab>
      <v-col cols="12" class="text-center">
        <v-img :src="logGP" inline cover height="auto" width="17%"></v-img>
      </v-col>
      <v-container fuild class="float-md-top position-absolute" style="left: 34%; top: 0">
        <v-img :src="women" inline cover height="auto" width="2%"></v-img>
      </v-container>
      <v-container fuild class="float-md-top position-absolute" style="left: 38%; top: 6%">
        <v-img :src="corona" inline cover height="auto" width="4%"></v-img>
      </v-container>
      <v-container fuild class="float-md-top position-absolute" style="left: 4%; top: 0">
        <v-img :src="logoGlobal" inline cover height="auto" width="10%"></v-img>
      </v-container>
      <v-container fuild class="float-md-top position-absolute" style="left: 57%; top: 6%">
        <v-img :src="book" inline cover height="auto" width="5%"></v-img>
      </v-container>
      <v-container fuild class="float-md-top position-absolute" style="left: 63%; top: 0">
        <v-img :src="tween" inline cover height="auto" width="2%"></v-img>
      </v-container>
    </v-row>
    <v-alert
      closable
      title="Error Message"
      :model-value="message.length !== 0"
      :text="message"
      type="error"
      mode="slide-y-reverse-transition"
      class="elevation-7"
    ></v-alert>
    <v-expand-x-transition>
      <v-form @submit="validateForm" ref="formRegister" v-show="expand" lazy-validation>
        <v-row no-gutters>
          <v-col cols="3">
            <v-icon
              icon="mdi-notebook-edit-outline"
              style="color: #99c5c0; font-size: 20px"
            ></v-icon
            >&nbsp;<v-label style="color: #99c5c0; font-size: 17px">Datos Personales </v-label>
            <v-divider
              style="color: #f4ede8; padding-top: 2pt; margin-inline-start: 8%"
            ></v-divider>
          </v-col>
        </v-row>
        <legend>&nbsp;</legend>
        <!-- row 1 -->
        <v-row>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.nombre"
              label="Nombres"
              variant="outlined"
              placeholder="Jhon"
              name="nombres"
              type="input"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.text_valid]"
              clearable
              tabindex="1"
            />
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.apellido"
              label="Apellidos"
              variant="outlined"
              placeholder="Doe"
              name="apellidos"
              type="input"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.text_valid]"
              clearable
              tabindex="2"
            />
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-select
              v-model="fieldsForm.tipo_documento_id"
              name="tipo_documento"
              label="Tipo Documento"
              :items="typeDocumentsList"
              item-title="nombre"
              item-value="id"
              variant="outlined"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required]"
              clearable
              tabindex="3"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.dni"
              label="Número de Documento"
              variant="outlined"
              placeholder="1234567890"
              name="dni"
              type="input"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, isDocValid]"
              :error-messages="fieldsForm.errors.dni"
              clearable
              tabindex="4"
            />
          </v-col>
        </v-row>
        <!-- row 2 -->
        <v-row>
          <v-col cols="12" sm="6" md="6" lg="4" xl="4">
            <v-text-field
              v-model="fieldsForm.fecha_nacimiento"
              label="Fecha Nacimiento"
              variant="outlined"
              placeholder="20/03/1999"
              name="fecha_nacimiento"
              type="date"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required]"
              clearable
              elevation="15"
              :max="
                new Date(Date.now() - new Date().getTimezoneOffset() * 60000 - 87600 * 60 * 60000)
                  .toISOString()
                  .substring(0, 10)
              "
              min="1950-01-01"
              active-picker.sync="YEAR"
              tabindex="5"
            />
          </v-col>
          <v-col cols="12" sm="6" md="6" lg="4" xl="4">
            <v-select
              v-model="fieldsForm.genero_id"
              name="genero_id"
              label="G&eacute;nero"
              :items="genderList"
              item-title="nombre"
              item-value="id"
              variant="outlined"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required]"
              clearable
              tabindex="6"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6" md="6" lg="4" xl="4">
            <v-select
              v-model="fieldsForm.estado_civil_id"
              name="estado_civil_id"
              label="Estado Civil"
              :items="civilStatusList"
              item-title="estado"
              item-value="id"
              variant="outlined"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required]"
              clearable
              tabindex="7"
            ></v-select>
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col cols="3">
            <v-icon icon="mdi-map-marker-radius" style="color: #99c5c0; font-size: 20px"></v-icon
            >&nbsp;<v-label style="color: #99c5c0; font-size: 17px">Ubicaci&oacute;n </v-label>
            <v-divider
              style="color: #f4ede8; padding-top: 2pt; margin-inline-start: 8%"
            ></v-divider>
          </v-col>
        </v-row>
        <legend>&nbsp;</legend>
        <!-- row 3 -->
        <v-row>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-select
              v-model="fieldsForm.nacionalidad_id"
              name="nacionalidad"
              label="Nacionalidad"
              :items="nationalityList"
              item-title="nombre"
              item-value="id"
              variant="outlined"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required]"
              clearable
              tabindex="8"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-select
              v-model="fieldsForm.pais_residencia"
              name="pais"
              label="Pa&iacute;s"
              :items="countryList"
              item-title="nombre"
              item-value="id"
              variant="outlined"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required]"
              @update:modelValue="updateRegion"
              clearable
              tabindex="9"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-select
              v-model="fieldsForm.region_id"
              name="region_id"
              label="Region"
              :items="regionList"
              item-title="nombre"
              item-value="id"
              variant="outlined"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required]"
              clearable
              tabindex="10"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.ciudad"
              label="Ciudad/Comuna"
              variant="outlined"
              placeholder="Mi comuna"
              name="ciudad"
              type="input"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.counter_dir]"
              :error-messages="fieldsForm.errors.ciudad"
              clearable
              tabindex="11"
            />
          </v-col>
        </v-row>
        <!-- row 4 -->
        <v-row>
          <v-col cols="12" sm="8" md="6" lg="6" xl="6">
            <v-text-field
              v-model="fieldsForm.direccion"
              label="Direcci&oacute;n"
              variant="outlined"
              placeholder="Romano"
              name="direccion"
              type="input"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.counter_dir]"
              :error-messages="fieldsForm.errors.direccion"
              clearable
              tabindex="12"
            />
          </v-col>
          <v-col cols="12" sm="4" md="3" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.ocupacion"
              label="Ocupaci&oacute;n"
              variant="outlined"
              placeholder="Jhon"
              name="ocupacion"
              type="input"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.counter_dir]"
              :error-messages="fieldsForm.errors.ocupacion"
              clearable
              tabindex="13"
            />
          </v-col>
          <v-col cols="12" sm="4" md="3" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.telefono"
              label="Tel&eacute;fono"
              variant="outlined"
              placeholder="+## ###########"
              :maxlength="15"
              name="telefono"
              type="input"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.phone]"
              :error-messages="fieldsForm.errors.telefono"
              clearable
              @keydown="checkDigit"
              tabindex="14"
            />
          </v-col>
        </v-row>
        <v-row no-gutters>
          <v-col cols="3">
            <v-icon
              icon="mdi-card-account-details-star-outline"
              style="color: #99c5c0; font-size: 20px"
            ></v-icon
            >&nbsp;<v-label style="color: #99c5c0; font-size: 17px">Datos de Usuario </v-label>
            <v-divider
              style="color: #f4ede8; padding-top: 2pt; margin-inline-start: 8%"
            ></v-divider>
          </v-col>
        </v-row>
        <legend>&nbsp;</legend>
        <!-- row 5 -->
        <v-row>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.email"
              label="Correo Electr&oacute;nico"
              variant="outlined"
              placeholder="johndoe@gmail.com"
              name="email"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.email]"
              :error-messages="fieldsForm.errors.email"
              clearable
              tabindex="15"
            />
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
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
              tabindex="16"
            />
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.password"
              label="Contrase&nacute;a"
              variant="outlined"
              placeholder="******"
              name="password"
              type="password"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, rules.counter, rules.counter_pass]"
              :error-messages="fieldsForm.errors.password"
              clearable
              tabindex="17"
            />
          </v-col>
          <v-col cols="12" sm="6" md="4" lg="3" xl="3">
            <v-text-field
              v-model="fieldsForm.password_confirm"
              label="Confirme Contrase&nacute;a"
              variant="outlined"
              placeholder="******"
              name="password_confirm"
              type="password"
              style="color: #f4ede8"
              class="rounded-l"
              :rules="[rules.required, passConfirmEqualPass, rules.counter_pass]"
              clearable
              tabindex="18"
            />
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12" class="text-center">
            <v-btn
              type="submit"
              large
              @click="validate"
              style="
                background-color: #99c5c0;
                font-weight: bolder;
                font-size: 14pt;
                border-color: beige;
                border-width: 2pt;
              "
              >REGISTRARME</v-btn
            >
          </v-col>
        </v-row>
      </v-form>
    </v-expand-x-transition>
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
        required: (value) => !!value || 'Dato Requerido.',
        counter_pass: (value) => value.length >= 8 || 'Min 8 caracteres',
        counter_dir: (value) => value.length < 250 || 'Max 250 caracteres',
        email: (value) => {
          const pattern =
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || 'e-mail inválido.';
        },
        text_valid: (value) => {
          const pattern = /^[a-zA-Z\u00C0-\u017F'\-_\s]{3,50}$/;
          return pattern.test(value) || 'Letras mayusculas o minúsculas';
        },
        phone: (value) => {
          const pattern = /\+[0-9\s-]+/;
          return pattern.test(value) || 'El número de teléfono debe ser válido +## ###########';
        },
      },
    }),
    methods: {
      validate() {
        this.$refs.formRegister.validate();
      },
    },
  };
</script>
