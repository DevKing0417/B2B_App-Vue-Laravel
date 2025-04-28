import { ref } from 'vue';

interface ToastOptions {
    message: string;
    type: 'success' | 'error' | 'info' | 'warning';
    duration?: number;
}

interface ToastState {
    isVisible: boolean;
    message: string;
    type: 'success' | 'error' | 'info' | 'warning';
}

export default function useToastNotification() {
    const toastState = ref<ToastState>({
        isVisible: false,
        message: '',
        type: 'info'
    });

    const showToast = (options: ToastOptions) => {
        toastState.value = {
            isVisible: true,
            message: options.message,
            type: options.type
        };

        setTimeout(() => {
            toastState.value.isVisible = false;
        }, options.duration || 3000);
    };

    const showSuccess = (message: string) => {
        showToast({
            message,
            type: 'success'
        });
    };

    const showError = (message: string) => {
        showToast({
            message,
            type: 'error'
        });
    };

    const showInfo = (message: string) => {
        showToast({
            message,
            type: 'info'
        });
    };

    const showWarning = (message: string) => {
        showToast({
            message,
            type: 'warning'
        });
    };

    return {
        toastState,
        showSuccess,
        showError,
        showInfo,
        showWarning
    };
} 