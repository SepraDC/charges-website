<template>
    <button @click="signIn( { username: 'lucas', password: 'Password123!' }, { callbackUrl: '/' })" class="bg-blue-600 p-2 border-rounded">Username and Password</button>
</template>

<script setup lang="ts">
definePageMeta({
    auth: {
        unauthenticatedOnly:true,
        navigateAuthenticatedTo: '/',
    }
})

const {signIn} = useAuth();
</script>
