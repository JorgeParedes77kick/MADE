export const truncarTexto = (texto, length = 150) => {
  if (texto.length > length) {
    return texto.substring(0, length - 3) + '...';
  } else {
    return texto;
  }
};
