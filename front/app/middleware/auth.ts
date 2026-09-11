import { loginUrlFor } from "../utils/redirect";

export default defineNuxtRouteMiddleware(async (to) => {
	const { ensureSession } = useAuth();

	if (await ensureSession()) {
		return;
	}

	return navigateTo(loginUrlFor(to.fullPath), { replace: true });
});
