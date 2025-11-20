<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  modelValue?: string
  placeholder?: string
  rows?: number
  disabled?: boolean
  required?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  rows: 3,
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const value = computed({
  get: () => props.modelValue || '',
  set: (val) => emit('update:modelValue', val),
})
</script>

<template>
  <textarea
    v-model="value"
    :rows="rows"
    :placeholder="placeholder"
    :disabled="disabled"
    :required="required"
    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm placeholder-gray-500 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500 dark:border-gray-600 dark:bg-gray-800 dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
  />
</template>
