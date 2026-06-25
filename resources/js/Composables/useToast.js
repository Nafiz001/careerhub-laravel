import { reactive, readonly } from 'vue';

// Lightweight global toast store. Shared across the app via a module singleton.
const state = reactive({
    toasts: [],
});

let seq = 0;

function push(message, type = 'success', timeout = 4000) {
    if (!message) return;
    const id = ++seq;
    state.toasts.push({ id, message, type });
    if (timeout > 0) {
        setTimeout(() => dismiss(id), timeout);
    }
    return id;
}

function dismiss(id) {
    const i = state.toasts.findIndex((t) => t.id === id);
    if (i !== -1) state.toasts.splice(i, 1);
}

export function useToast() {
    return {
        toasts: readonly(state).toasts,
        success: (m, t) => push(m, 'success', t),
        error: (m, t) => push(m, 'error', t),
        info: (m, t) => push(m, 'info', t),
        push,
        dismiss,
    };
}
