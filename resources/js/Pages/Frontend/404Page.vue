<script setup>
    import { Head } from '@inertiajs/vue3';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
</script>

<template>
    <Head>
        <title> Car Rent | 404 Page</title>
    </Head>

    <GuestLayout>
        <section class="error-section pt-120 pb-120">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="error-content text-center">
                            <h2 class="title">Oops! sorry page not found</h2>
                            <p>The page no available or not found!</p>
                            <a :href="route('show.home')" class="cmn-btn">Go to home</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped></style>
