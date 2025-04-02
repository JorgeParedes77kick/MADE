export function setParam(parametro, valor) {
  const url = new URL(window.location);
  url.searchParams.set(parametro, valor); // Agrega o actualiza el parámetro
  window.history.pushState({}, '', url); // Actualiza la URL sin recargar la página
}
export function getParam(parametro) {
  const url = new URL(window.location);
  const valor = url.searchParams.get(parametro);
  return valor;
}
