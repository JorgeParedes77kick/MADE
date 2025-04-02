export const previewImage = (file) => {
  if (file instanceof File) {
    return URL.createObjectURL(file);
  } else {
    return null;
  }
};
