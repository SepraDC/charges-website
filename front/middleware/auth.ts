export default defineNuxtRouteMiddleware((to) => {
  const { verify } = useAuthState();
  verify().catch((err) => {
    console.error(err);
    return navigateTo("/login");
  });
});
