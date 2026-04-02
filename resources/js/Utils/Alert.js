import Swal from 'sweetalert2';

/**
 * Premium Dark SweetAlert2 Configuration
 */
const Alert = Swal.mixin({
    background: '#1c2333', // var(--bg-elevated)
    color: '#e6edf3',      // var(--text-primary)
    confirmButtonColor: '#c9a227', // var(--accent)
    cancelButtonColor: '#151b23',  // var(--bg-tertiary)
    customClass: {
        popup: 'premium-popup',
        title: 'premium-title',
        confirmButton: 'premium-confirm-btn',
        cancelButton: 'premium-cancel-btn',
    }
});

/**
 * Success Toast (Top Right)
 */
export const toast = (message, icon = 'success') => {
    Alert.fire({
        text: message,
        icon: icon,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#0d1117', // var(--bg-secondary)
    });
};

/**
 * Confirmation Dialog
 */
export const confirm = async (title, text, confirmButtonText = 'Yes, Proceed') => {
    const result = await Alert.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel',
        reverseButtons: true
    });

    return result.isConfirmed;
};

export default Alert;
