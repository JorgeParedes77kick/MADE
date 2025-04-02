// export const CustomProxy = <T extends { [index: string]: any }, K>(obj: T, defaultReturn: K) => {
//   const element = new Proxy(obj, {
//     get(target, prop: string | symbol) {
//       return prop in target ? target[prop as keyof typeof target] : defaultReturn;
//     },
//   });
//   return element as T & { [index: string]: K };
// };
export const CustomProxy = (obj, defaultReturn) => {
  const element = new Proxy(obj, {
    get(target, prop) {
      return prop in target ? target[prop] : defaultReturn;
    },
  });
  return element;
};
