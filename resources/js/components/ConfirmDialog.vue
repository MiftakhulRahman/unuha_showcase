<script setup lang="ts">
import { ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { AlertTriangle, Trash2 } from 'lucide-vue-next';

interface Props {
    open: boolean;
    title?: string;
    description?: string;
    confirmText?: string;
    cancelText?: string;
    variant?: 'default' | 'destructive';
    icon?: 'warning' | 'delete';
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Konfirmasi',
    description: 'Apakah Anda yakin ingin melanjutkan?',
    confirmText: 'Ya, Lanjutkan',
    cancelText: 'Batal',
    variant: 'default',
    icon: 'warning',
});

const emit = defineEmits<{
    'update:open': [value: boolean];
    confirm: [];
    cancel: [];
}>();

const handleConfirm = () => {
    emit('confirm');
    emit('update:open', false);
};

const handleCancel = () => {
    emit('cancel');
    emit('update:open', false);
};
</script>

<template>
    <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <div class="flex items-center gap-3">
                    <div
                        :class="[
                            'flex h-12 w-12 shrink-0 items-center justify-center rounded-full',
                            variant === 'destructive'
                                ? 'bg-red-100 dark:bg-red-900/20'
                                : 'bg-yellow-100 dark:bg-yellow-900/20',
                        ]"
                    >
                        <Trash2
                            v-if="icon === 'delete'"
                            :class="[
                                'h-6 w-6',
                                variant === 'destructive'
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-yellow-600 dark:text-yellow-400',
                            ]"
                        />
                        <AlertTriangle
                            v-else
                            :class="[
                                'h-6 w-6',
                                variant === 'destructive'
                                    ? 'text-red-600 dark:text-red-400'
                                    : 'text-yellow-600 dark:text-yellow-400',
                            ]"
                        />
                    </div>
                    <div class="flex-1">
                        <DialogTitle class="text-lg">{{ title }}</DialogTitle>
                    </div>
                </div>
            </DialogHeader>
            <DialogDescription class="pt-2 text-base">
                {{ description }}
            </DialogDescription>
            <DialogFooter class="gap-2 sm:gap-2">
                <Button variant="outline" @click="handleCancel">
                    {{ cancelText }}
                </Button>
                <Button
                    :variant="variant === 'destructive' ? 'destructive' : 'default'"
                    @click="handleConfirm"
                >
                    {{ confirmText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
