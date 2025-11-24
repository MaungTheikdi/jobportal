<script setup>
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import axios from 'axios';
import { ref } from 'vue';

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    //email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('betuser.register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('bet.dashboard')">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                            </Link>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <template v-if="$page.props.auth.user">
                                <Link :href="route('bet.logout')" method="post" as="button"
                                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Logout
                                </Link>
                            </template>
                            <template v-else>
                                <Link :href="route('bet.login')"
                                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Log in
                                </Link>
                                <Link :href="route('bet.register')"
                                    class="ml-4 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                                Register
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <div class="max-w-md mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mt-10">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Bet User Registration</h1>

                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <InputLabel for="username" value="Username" />
                        <TextInput id="username" type="text" class="mt-1 block w-full" v-model="form.username" required
                            autofocus autocomplete="username" />
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <!-- <div class="mb-4">
                    <InputLabel for="email" value="Email (Optional)" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        autocomplete="email"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div> -->

                    <div class="mb-4">
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password"
                            required autocomplete="new-password" />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
                            v-model="form.password_confirmation" required autocomplete="new-password" />
                        <InputError class="mt-2" :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center justify-between">
                        <a :href="route('bet.login')" class="text-sm text-indigo-600 hover:text-indigo-900">
                            Already registered? Login
                        </a>
                        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing">
                            Register
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>