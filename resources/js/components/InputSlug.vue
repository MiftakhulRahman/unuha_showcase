<script setup lang="ts">
import { ref, watch } from 'vue';
import InputText from 'primevue/inputtext';

const props = defineProps<{
    modelValue: string;
    source?: string;
    placeholder?: string;
    manual?: boolean; // If true, auto-generation is disabled until cleared
}>();

const emit = defineEmits(['update:modelValue']);

const isManuallyEdited = ref(false);

const generateSlug = (text: string) => {
    return text
        .toString()
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')     // Replace spaces with -
        .replace(/[^\w\-]+/g, '') // Remove all non-word chars
        .replace(/\-\-+/g, '-')   // Replace multiple - with single -
        .replace(/^-+/, '')       // Trim - from start of text
        .replace(/-+$/, '');      // Trim - from end of text
};

watch(() => props.source, (newSource) => {
    if (newSource && !isManuallyEdited.value && !props.manual) {
        emit('update:modelValue', generateSlug(newSource));
    }
});

const onInput = (event: Event) => {
    isManuallyEdited.value = true;
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', target.value); // Allow manual input as is, or enforce slugify? Usually allow manual but maybe sanitize slightly.
};

const onBlur = () => {
    // Optional: ensure it's a valid slug on blur
    if (props.modelValue) {
        emit('update:modelValue', generateSlug(props.modelValue));
    }
};
</script>

<template>
    <InputText
        :model-value="modelValue"
        @input="onInput"
        @blur="onBlur"
        :placeholder="placeholder || 'Auto-generated slug'"
        class="w-full"
    />
</template>
