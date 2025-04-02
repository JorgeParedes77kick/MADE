/**
 *
 * @param {Object[]} arr
 * @param {String[]} keys
 * @returns
 */
export function uniqueByKeys(arr, keys) {
  // Ensure keys is an array
  if (!Array.isArray(keys)) {
    keys = [keys];
  }

  // Create a Set to store unique combinations
  const uniqueCombinations = new Set();

  // Iterate over the array of objects
  arr.forEach((obj) => {
    // Extract the values of the specified keys
    const keyValues = keys.map((key) => obj[key]);

    // Create a string representation of the combination
    const combination = keyValues.join(',');

    // Add the combination to the Set
    uniqueCombinations.add(combination);
  });

  // Create a new array with the unique objects
  const uniqueObjects = [];
  arr.forEach((obj) => {
    const keyValues = keys.map((key) => obj[key]);
    const combination = keyValues.join(',');
    if (uniqueCombinations.has(combination)) {
      uniqueObjects.push(obj);
      uniqueCombinations.delete(combination);
    }
  });

  return uniqueObjects;
}
