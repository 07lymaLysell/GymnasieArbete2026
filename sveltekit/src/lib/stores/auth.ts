import { writable } from 'svelte/store';

// Skapar en store med initial state
const initialState = {
    isLoggedIn: false,
    user: null,
};

export const authStore = writable(initialState);

// Hjälpfunktion för login
export function loginUser(userData: any) {
    authStore.set({
        isLoggedIn: true,
        user: userData,
    });
    // Spara i localStorage så man förblir inloggad om sidan laddar om
    localStorage.setItem('user', JSON.stringify(userData));
}

// Hjälpfunktion för logout
export function logoutUser() {
    authStore.set({
        isLoggedIn: false,
        user: null,
    });
    localStorage.removeItem('user');
}

// Ladda sparad user från localStorage när appen startar
if (typeof window !== 'undefined') {
    const savedUser = localStorage.getItem('user');
    if (savedUser) {
        authStore.set({
            isLoggedIn: true,
            user: JSON.parse(savedUser),
        });
    }
}