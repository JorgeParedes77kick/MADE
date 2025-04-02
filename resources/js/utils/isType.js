/**
 * Determines if all elements in the dataset are truthy.
 * @param {...any[]} data - The dataset to check.
 * @returns {boolean} Returns true if all elements are truthy, otherwise returns false.
 */
export const Truthty = (...data) => {
  if (data.length === 0) return false;
  const values = data.map((d) => {
    if (d === undefined || d === null || (!d && d !== 0)) return false;
    if (Array.isArray(d)) return d.length !== 0;
    if (typeof d === 'object' && Object.keys(d).length === 0) return false;
    return true;
  });
  return !values.includes(false);
};

/**
 * Checks if the dataset is empty or invalid.
 * @param {any} data - The dataset to check.
 * @returns {boolean} Returns true if the dataset is empty or invalid, otherwise returns false.
 */
export const isEmpty = (data) => {
  return typeof data === 'undefined' || !Truthty(data) || data === '';
};

/**
 * Determines if the object is a real object (not an array or an empty object).
 * @param {any} data - The object to check.
 * @returns {boolean} Returns true if the object is a real object, otherwise returns false.
 */
export const isObject = (data) => {
  return data !== null && typeof data === 'object';
};
