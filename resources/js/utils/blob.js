/**
 * Maneja la descarga de un archivo.
 * @function excelDescarga
 * @param {Blob} data - Los datos del archivo a descargar.
 * @param {string} nombreArchivo - El nombre que se le dará al archivo descargado.
 */
export const excelDescarga = (data, nombreArchivo) => {
  const url = window.URL.createObjectURL(new Blob([data]));
  const link = document.createElement('a');
  link.href = url;
  link.setAttribute('download', nombreArchivo);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link); // Limpiar el DOM después de la descarga
};
/**
 * Maneja los errores que ocurren durante la solicitud.
 * @function excelError
 * @param {Object} error - El objeto de error devuelto por Axios.
 */
export const excelError = (error) => {
  const blob = error.response?.data;
  if (blob) {
    const reader = new FileReader();
    reader.onload = () => {
      try {
        const errorObj = JSON.parse(reader.result);
        if (errorObj?.message) {
          Swal.fire({
            text: errorObj.message,
            icon: 'warning',
          });
        }
      } catch (e) {
        console.error('No se pudo analizar el error:', reader.result);
      }
    };
    reader.readAsText(blob);
  } else {
    console.error('Error al descargar el archivo:', error);
  }
};
