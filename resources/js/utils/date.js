import dayjs from 'dayjs';

export const DATE = {
  YYYYMMDD: 'YYYYMMDD',
  YYYY_MM_DD: 'YYYY-MM-DD',
  DDMMYYYY: 'DDMMYYYY',
  DD_MM_YYYY: 'DD-MM-YYYY',
  YYYY_WWW: 'YYYY-[W]WW',
};

export const FormatFecha = (fecha, type, DateTypeIn = DATE.YYYY_MM_DD) => {
  if (![1, 2, 3, 4].includes(type)) return fecha;
  const fechaJs = dayjs(fecha, DateTypeIn);
  if (!fechaJs.isValid()) return fecha;

  const FechaFormat = {
    1: fechaJs.format(DATE.YYYYMMDD),
    2: fechaJs.format(DATE.DDMMYYYY),
    3: fechaJs.format(DATE.DD_MM_YYYY),
    4: fechaJs.format(DATE.YYYY_MM_DD),
  };
  return FechaFormat[type];
};

/**
 * Obtiene un día específico de la semana a partir de una fecha dada.
 *
 * @param {string|dayjs.Dayjs} date - La fecha de referencia. Puede ser una cadena de texto, un objeto Date, o un timestamp.
 * @param {1|2|3|4|5|6|7} targetDay - El día de la semana que deseas obtener ( 1 = lunes, ..., 6 = sábado).
 * @returns {dayjs.Dayjs} Un objeto Day.js representando el día específico de la semana.
 * @throws {Error} Si `targetDay` no está entre 0 y 6.
 */
export const getSpecificDayOfWeek = (date, targetDay) => {
  if (targetDay < 1 || targetDay > 8) {
    throw new Error('targetDay debe ser un número entre 1 y 8.');
  }
  let day;
  if (typeof date === 'string' || !date) day = dayjs(date, DATE.YYYY_MM_DD);
  else day = date;
  // console.log('day:', day.isUTC(), day.local(), day.utc(), day.format(DATE.YYYY_MM_DD));
  const currentDayOfWeek = day.day();
  // Calcular la diferencia hasta el día objetivo
  const diffToTargetDay = targetDay - currentDayOfWeek;
  return day.add(diffToTargetDay, 'day');
};
