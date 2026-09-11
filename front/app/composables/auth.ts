import type { User } from "../@type/User";
import { LOGIN_PATH, loginUrlFor } from "../utils/redirect";

const SESSION_MAX_AGE = 60 * 60 * 24 * 7; // 7 days in seconds
const REMEMBER_ME_MAX_AGE = 60 * 60 * 24 * 30; // 30 days in seconds

// Module scope on purpose: parallel 401s must not each start a navigation.
let signOutInFlight = false;

export const useAuth = () => {
	const authUser = useState<User | null>("authUser", () => null);
	const isAuthenticated = useState<boolean>("isAuthenticated", () => false);

	const tokenCookieOptions = {
		default: () => null,
		// A Secure cookie is dropped by the browser over plain HTTP, which would
		// make the login loop back forever in a local HTTP dev environment.
		secure: !import.meta.client || window.location.protocol === "https:",
		sameSite: "strict" as const,
		httpOnly: false, // Must be false for client-side access
	};

	// Two refs over the same cookie: the lifetime is chosen when we write it.
	const token = useCookie<string | null>("authToken", {
		...tokenCookieOptions,
		maxAge: SESSION_MAX_AGE,
	});
	const rememberedToken = useCookie<string | null>("authToken", {
		...tokenCookieOptions,
		maxAge: REMEMBER_ME_MAX_AGE,
	});

	const config = useRuntimeConfig();
	const router = useRouter();

	const clearAuthState = () => {
		token.value = null;
		authUser.value = null;
		isAuthenticated.value = false;
	};

	const verify = async () => {
		if (!token.value) {
			clearAuthState();
			return { data: null, error: "No token" };
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
				clearAuthState();
				return { data: null, error: "No data received" };
			}
		} catch (err) {
			clearAuthState();
			return { data: null, error: err };
		}
	};

	const signIn = async (
		credentials: {
			username: string;
			password: string;
			remember_me?: boolean;
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
				if (credentials.remember_me) {
					rememberedToken.value = data.token;
				} else {
					token.value = data.token;
				}

				const { error } = await verify();

				if (error) {
					return { data: null, error };
				}

				await router.push(options?.callbackUrl || "/");

				return { data, error: null };
			}

			return { data: null, error: "No token received" };
		} catch (error) {
			return { data: null, error };
		}
	};

	/**
	 * Drop the session and send the user to login. `returnTo` is the page they
	 * were on, so they land back on it after signing in again; it is sanitized,
	 * which also makes this safe to bind straight to a DOM event.
	 */
	const signOut = async (returnTo?: unknown) => {
		clearAuthState();

		if (signOutInFlight || router.currentRoute.value.path === LOGIN_PATH) {
			return;
		}

		signOutInFlight = true;
		try {
			await router.replace(loginUrlFor(returnTo));
		} finally {
			signOutInFlight = false;
		}
	};

	/** True only when the session is fully hydrated, so verify() can be skipped. */
	const check = () => {
		return isAuthenticated.value && authUser.value !== null;
	};

	/** Resolve the session with at most one API call. Used by both middlewares. */
	const ensureSession = async () => {
		if (check()) return true;
		if (!token.value) return false;

		const { error } = await verify();

		return !error;
	};

	return {
		authUser: readonly(authUser),
		isAuthenticated: readonly(isAuthenticated),
		token: readonly(token),
		signIn,
		signOut,
		verify,
		check,
		ensureSession,
	};
};
