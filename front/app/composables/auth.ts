import type { User } from "../@type/User";

export const useAuth = () => {
	const authUser = useState<User | null>("authUser", () => null);
	const isAuthenticated = useState<boolean>("isAuthenticated", () => false);
	const token = useCookie<string | null>("authToken", {
		default: () => null,
		maxAge: 60 * 60 * 24 * 7, // 7 days
		secure: true,
		sameSite: "strict",
		httpOnly: false, // Must be false for client-side access
	});

	const config = useRuntimeConfig();

	// Initialize auth state from token on client side
	const initializeAuth = async () => {
		if (import.meta.client && token.value && !authUser.value) {
			await verify();
		}
	};

	// Verify user with token
	const verify = async () => {
		if (!token.value) {
			authUser.value = null;
			isAuthenticated.value = false;
			return { data: null, error: null };
		}

		try {
			const data = await $fetch<User>(`${config.public.apiBaseURL}/verify`, {
				method: "GET",
				headers: {
					authorization: `Bearer ${token.value}`,
				},
			});

			if (data) {
				authUser.value = data;
				isAuthenticated.value = true;
				return { data, error: null };
			} else {
				// Token is invalid, clear auth state
				await signOut();
				return { data: null, error: "No data received" };
			}
		} catch (err) {
			// Token is invalid, clear auth state
			await signOut();
			return { data: null, error: err };
		}
	};

	// Sign in user
	const signIn = async (
		credentials: {
			username: string;
			password: string;
		},
		options?: { callbackUrl?: string },
	) => {
		try {
			const data = await $fetch<{ token: string }>(
				`${config.public.apiBaseURL}/auth`,
				{
					method: "POST",
					body: credentials,
				},
			);

			if (data?.token) {
				token.value = data.token;
				await verify(); // Verify and set user data

				const router = useRouter();
				await router.push(options?.callbackUrl || "/");

				return { data, error: null };
			}

			return { data: null, error: "No token received" };
		} catch (error) {
			return { data: null, error };
		}
	};

	// Sign out user
	const signOut = async () => {
		token.value = null;
		authUser.value = null;
		isAuthenticated.value = false;

		clearNuxtData();

		// Navigate to login
		await navigateTo("/login");
	};

	const check = () => {
		return isAuthenticated.value && !!authUser.value;
	};

	// Initialize auth on client side
	if (import.meta.client) {
		initializeAuth();
	}

	return {
		// State
		authUser: readonly(authUser),
		isAuthenticated: readonly(isAuthenticated),
		token: readonly(token),
		signIn,
		signOut,
		verify,
		check,
		initializeAuth,
	};
};
