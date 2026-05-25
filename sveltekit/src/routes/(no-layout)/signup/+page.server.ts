import { fail, redirect } from '@sveltejs/kit';
import type { Actions } from './$types';

export const actions = {
    default: async ({ request }) => {
        const data = await request.formData();

        const firstname = data.get('firstname')?.toString() ?? '';
        const surname = data.get('surname')?.toString() ?? '';
        const username = data.get('username')?.toString() ?? '';
        const email = data.get('email')?.toString() ?? '';
        const password = data.get('password')?.toString() ?? '';

        if (!firstname || !surname || !username || !email || !password) {
            return fail(400, { message: 'Alla fält är obligatoriska' });
        }

        try {
            const formData = new FormData();
            formData.append('firstname', firstname);
            formData.append('surname', surname);
            formData.append('username', username);
            formData.append('password', password);
            formData.append('email', email);

            const response = await fetch('http://localhost/api/adduser.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                redirect(303, '/');
            } else {
                return fail(400, { message: result.message });
            }
        } catch (error) {
            return fail(500, {
                message: 'Ett fel uppstod: ' + (error instanceof Error ? error.message : String(error))
            });
        }
    }
} satisfies Actions;
