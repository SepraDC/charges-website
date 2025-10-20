export default defineNuxtRouteMiddleware(async () => {
	const { verify, check, isAuthenticated } = useAuth();

	// If already authenticated, no need to verify again
	if (isAuthenticated.value) {
		return;
	}

	if (check()) {
		return;
	}

	const { error } = await verify();

	if (error) {
		return navigateTo("/login");
	}
});
