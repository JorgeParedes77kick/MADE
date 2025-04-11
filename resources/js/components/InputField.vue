<script setup>
  import { computed, defineEmits, defineProps, inject, onMounted, ref, toRefs } from 'vue';

  const validate = inject('$validation');

  const props = defineProps({
    id: { type: String, default: '' },
    modelValue: { type: String, default: '' },
    label: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    name: { type: String, default: '' },
    type: { type: String, default: 'text' },
    color: { type: String, default: '' },
    rules: { type: Array, default: () => [] },
    errorMessages: { type: Array, default: () => [] },
    clearable: { type: Boolean, default: false },
    classInput: { type: String, default: '' },
    variant: { type: String, default: '' },
    classLabel: { type: String, default: '' },
  });

  const loading = ref(false);
  const isDisabled = ref(false);

  onMounted(() => {});

  const emit = defineEmits(['update:modelValue']);

  const { modelValue } = toRefs(props);

  const inputValue = computed({
    get: () => modelValue.value,
    set: (newValue) => emit('update:modelValue', newValue),
  });
</script>

<template>
  <div>
    <v-label :for="id" :color="color" :class="classLabel">{{ label }}</v-label>
    <v-text-field
      v-model="inputValue"
      :id="id"
      :placeholder="placeholder"
      :name="name"
      :variant="variant"
      :type="type"
      :color="color"
      :class="classInput"
      :rules="rules"
      :error-messages="errorMessages"
      :clearable="clearable"
    />
  </div>
</template>
<style></style>
