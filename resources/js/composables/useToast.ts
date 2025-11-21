import { toast } from 'vue-sonner';

export function useToast() {
    return {
        success: (message: string) => {
            toast.success(message, {
                duration: 3000,
            });
        },
        error: (message: string) => {
            toast.error(message, {
                duration: 4000,
            });
        },
        warning: (message: string) => {
            toast.warning(message, {
                duration: 3500,
            });
        },
        info: (message: string) => {
            toast.info(message, {
                duration: 3000,
            });
        },
        // Pesan standar untuk operasi CRUD
        createSuccess: (entityName: string = 'Data') => {
            toast.success(`${entityName} berhasil ditambahkan`, {
                duration: 3000,
            });
        },
        updateSuccess: (entityName: string = 'Data') => {
            toast.success(`${entityName} berhasil diperbarui`, {
                duration: 3000,
            });
        },
        deleteSuccess: (entityName: string = 'Data') => {
            toast.success(`${entityName} berhasil dihapus`, {
                duration: 3000,
            });
        },
        bulkDeleteSuccess: (count: number) => {
            toast.success(`${count} data berhasil dihapus`, {
                duration: 3000,
            });
        },
        operationFailed: (message: string = 'Operasi gagal. Silakan coba lagi.') => {
            toast.error(message, {
                duration: 4000,
            });
        },
    };
}
