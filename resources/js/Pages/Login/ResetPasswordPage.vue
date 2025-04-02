<script setup>
import {defineProps} from "vue"
import RegisterLayout from "../../layouts/RegisterLayout.vue";
import ResetPasswordForm from "./ResetPasswordForm.vue"

const props = defineProps({
  mail : String,
});

</script>

<template class="py-0 px-0">
  <RegisterLayout>
    <ResetPasswordForm :mail="mail"></ResetPasswordForm>
  </RegisterLayout>
</template>
