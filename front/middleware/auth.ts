export default defineNuxtRouteMiddleware(async () => {
  const { verify } = useAuthState();
  await verify().catch((err) => {
    console.error(err);
    return navigateTo("/login");
  });
});
