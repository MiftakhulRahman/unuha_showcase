import { useToast as usePrimeToast } from 'primevue/usetoast';

export function useToast() {
    const toast = usePrimeToast();

    return {
        success: (message: string) => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: message, life: 3000 });
        },
        error: (message: string) => {
            toast.add({ severity: 'error', summary: 'Gagal', detail: message, life: 4000 });
        },
        warning: (message: string) => {
            toast.add({ severity: 'warn', summary: 'Peringatan', detail: message, life: 3500 });
        },
        info: (message: string) => {
            toast.add({ severity: 'info', summary: 'Info', detail: message, life: 3000 });
        },
        // Pesan standar untuk operasi CRUD
        createSuccess: (entityName: string = 'Data') => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: `${entityName} berhasil ditambahkan`, life: 3000 });
        },
        updateSuccess: (entityName: string = 'Data') => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: `${entityName} berhasil diperbarui`, life: 3000 });
        },
        deleteSuccess: (entityName: string = 'Data') => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: `${entityName} berhasil dihapus`, life: 3000 });
        },
        bulkDeleteSuccess: (count: number) => {
            toast.add({ severity: 'success', summary: 'Berhasil', detail: `${count} data berhasil dihapus`, life: 3000 });
        },
        operationFailed: (message: string = 'Operasi gagal. Silakan coba lagi.') => {
            toast.add({ severity: 'error', summary: 'Gagal', detail: message, life: 4000 });
        },
    };
}
