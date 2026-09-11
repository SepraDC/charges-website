import { sanitizeRedirect } from "../utils/redirect";

export default defineNuxtRouteMiddleware(async (to) => {
	const { ensureSession } = useAuth();

	if (!(await ensureSession())) {
		return;
	}

	return navigateTo(sanitizeRedirect(to.query.redirect), { replace: true });
});
