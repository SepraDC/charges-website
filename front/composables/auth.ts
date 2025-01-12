import { useStorage } from "@vueuse/core";

export const useAuthState = () => {
	const token = useCookie("authToken");

	const authUser = useStorage("authUser", {});

	const config = useRuntimeConfig();

	const verify = async () => {
		const { data, error } = await useFetch(
			`${config.API_BASE_URL || config.public.API_BASE_URL}/verify`,
			{
				method: "GET",
				headers: {
					authorization: `Bearer ${token.value}`,
				},
			},
		);

		if (data.value !== null) {
			authUser.value = data.value;
		}

		return { data, error };
	};

	return {
		token,
		authUser,
		verify,
	};
};

export const useAuth = () => {
	const runtimeConfig = useRuntimeConfig();
	const signIn = async (
		credentials: {
			username: string;
			password: string;
		},
		options: { callbackUrl: string },
	) => {
		const { data, error } = await useFetch(
			`${runtimeConfig.API_BASE_URL || runtimeConfig.public.API_BASE_URL}/auth`,
			{
				method: "POST",
				body: credentials,
			},
		);

		if (data.value !== null) {
			const authCookie = useCookie("authToken", data.value.token);
			authCookie.value = data.value.token;
			const router = useRouter();
			router.push(options.callbackUrl);
		}

		return { data: data.value, error: error.value };
	};

	const signOut = () => {
		const authCookie = useCookie("authToken");
		const authUser = useCookie("authUser");
		authCookie.value = null;
		authUser.value = null;

		navigateTo("login");
	};
	return { signIn, signOut };
};
