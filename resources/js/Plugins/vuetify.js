import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
// import { aliases, mdi } from 'vuetify/iconsets/mdi';

import '@mdi/font/css/materialdesignicons.css';
// import '../../sass/app.scss';

const defaultFontFamily =
  "'Objetive', 'Gilroy', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', 'Noto Sans', 'Liberation Sans', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'";

// #5f7281
// #222222
// #deb35f
// #ddbe82
// #8c8885
// #f7f7f7
// #241c1f

const light = {
  dark: false,
  colors: {
    'navbar-color': '#222224',
    'navbar-text': '#deb35f',
    'navbar-active': '#5f7281',
    'navbar-active-text': '#5f7281',
    'navbar-hover': '#deb35f',
    'navbar-hover-text': '#5f7281',
    'data-table-header': '#222222',
    'data-table-body': '#414141',

    background: '#f7f7f7',
    foreground: '#222222',
    input: '#8c8885',
    primary: '#5f7281',
    secondary: '#b0b8c0',
    success: '#66bb6a',
    warning: '#ffca28',
    error: '#e57373',
    info: '#26c6da',
    light: '#f2ede7',
    surface: '#fbfbff',
    dark: '#40251e',
    red: '#e57373',
    pink: '#f06292',
    purple: '#ba68c8',
    'deep-purple': '#9575cd',
    indigo: '#5c6bc0',
    blue: '#42a5f5',
    'light-blue': '#29b6f6',
    cyan: '#26c6da',
    teal: '#26a69a',
    green: '#66bb6a',
    'light-green': '#9ccc65',
    lime: '#d4e157',
    yellow: '#ffee58',
    amber: '#ffca28',
    orange: '#ffa726',
    'deep-orange': '#ff7043',
    brown: '#8d6e63',
    gray: '#bdbdbd',
    'blue-gray': '#78909c',
  },
};

const dark = {
  dark: true,
  colors: {
    'navbar-color': '#0e0e0e',
    'navbar-text': '#b38e4a',
    'navbar-active': '#5f7281',
    'navbar-active-text': '#161617',
    'navbar-hover': '#b38e4a',
    'navbar-hover-text': '#161617',
    // 'data-table-header': '#aaaaaa',
    // 'data-table-body': '#666666',
    'data-table-header': '#212121',
    'data-table-body': '#212121',
    background: '#181a1b',
    foreground: '#e0e0e0',
    // input: '#4b5a66',
    primary: '#3c4b59',
    secondary: '#2a2f33',
    success: '#388e3c',
    warning: '#f57c00',
    error: '#d32f2f',
    info: '#0288d1',
    light: '#2c2f33',
    surface: '#212121',
    dark: '#4a3b33',
    red: '#b71c1c',
    pink: '#c2185b',
    purple: '#7b1fa2',
    'deep-purple': '#512da8',
    indigo: '#303f9f',
    blue: '#1565c0',
    'light-blue': '#0277bd',
    cyan: '#00838f',
    teal: '#00695c',
    green: '#2e7d32',
    'light-green': '#558b2f',
    lime: '#9e9d24',
    yellow: '#f9a825',
    amber: '#ff8f00',
    orange: '#ef6c00',
    'deep-orange': '#d84315',
    brown: '#5d4037',
    gray: '#424242',
    'blue-gray': '#37474f',
  },
};

const vuetify = createVuetify({
  defaults: {
    // Define mensajes predeterminados globales
    VDataTable: {
      noDataText: 'No hay datos disponibles',
    },
    VSelect: {
      noDataText: 'No hay opciones disponibles',
    },
    VCardTitle: {
      style: {
        whiteSpace: 'normal', // Estilo directo
        wordBreak: 'auto-phrase',
      },
    },
    // VLabel: {
    //   style: {
    //     fontSize: '1.2rem',
    //   },
    // },
    // Define utilidades personalizadas
  },
  components,
  directives,
  theme: {
    themes: {
      light,
      dark,
    },
  },
});

export default vuetify;
