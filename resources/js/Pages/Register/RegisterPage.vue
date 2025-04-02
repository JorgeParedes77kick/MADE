<script setup>
import {defineProps} from "vue";
import RegisterLayout from "../../layouts/RegisterLayout.vue";
import RegisterForm from "./RegisterForm.vue";

const props = defineProps({
  genderList : Array,
  civilStatusList : Array,
  nationalityList : Array,
  countryList : Array,
  typeDocumentsList : Array,
});

</script>

<template class="py-0 px-0">
    <RegisterLayout>
        <RegisterForm :genderList="genderList" :civilStatusList="civilStatusList" :nationalityList="nationalityList" :countryList="countryList" :typeDocumentsList="typeDocumentsList"></RegisterForm>
    </RegisterLayout>
</template>

