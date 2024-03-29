import { ofetch } from "ofetch";
import { useStorage } from "@vueuse/core";

export const useAuthState = () => {
  const token = useCookie("authToken");

  const authUser = useStorage("authUser", {});

  const config = useRuntimeConfig();

  const verify = async () => {
    authUser.value = await ofetch(
      `${config.API_BASE_URL || config.public.API_BASE_URL}/verify`,
      {
        method: "GET",
        headers: {
          authorization: "Bearer " + token.value,
        },
      }
    );
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
    options: { callbackUrl: string }
  ) => {
    const { token } = await ofetch(
      `${runtimeConfig.API_BASE_URL || runtimeConfig.public.API_BASE_URL}/auth`,
      {
        method: "POST",
        body: credentials,
      }
    ).catch((err) => {
      console.error(err);
    });

    const authCookie = useCookie("authToken", token);
    authCookie.value = token;

    const router = useRouter();
    router.push(options.callbackUrl);
  };
  return { signIn };
};
