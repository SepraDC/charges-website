import { useAuthState } from "../composables/auth";

export default defineNuxtRouteMiddleware(async (from, to) => {
	const { verify } = useAuthState();

	const toast = useToast();

	const { error } = await verify();

	if (error.value?.data.code === 401) {
		toast.add({
			title: `${error.value.data.code}: ${error.value.data.message}`,
		});
		return "/login";
	}
});
