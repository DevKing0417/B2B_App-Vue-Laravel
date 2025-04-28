import { useToast } from 'vue-toastification';

export default function useToastNotification() {
    const toast = useToast();

    const showSuccess = (message) => {
        toast.success(message);
    };

    const showError = (message) => {
        toast.error(message);
    };

    const showWarning = (message) => {
        toast.warning(message);
    };

    const showInfo = (message) => {
        toast.info(message);
    };

    return {
        showSuccess,
        showError,
        showWarning,
        showInfo
    };
} 