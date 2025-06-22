<script setup>
    import { Head } from '@inertiajs/vue3';
    import AdminLayout from '@/Layouts/AdminLayout.vue';
    import CarListComponent from '../../../Components/Admin/Car/CarListComponent.vue';
</script>

<template>
    <Head>
        <title>Car Rent | Car List</title>
    </Head>
    <AdminLayout>
        <CarListComponent />
    </AdminLayout>
</template>

<style scoped></style>
