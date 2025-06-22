<script setup>
    import { usePage, useForm, Link, router } from "@inertiajs/vue3";

    const list = usePage();
    const car_for_rent = list.props.car_for_rent;
    const authUser = usePage().props.authCustomer.customer;

    const form = useForm({
        id: null,
        user_id: "",
        car_id: "",
        start_date: "",
        end_date: "",
        total_cost: "",
        status: "",
        pickup_location: "",
        drop_off_location: "",
        pickup_time: "",
        drop_off_time: "",
    });

    const saveRent = () => {
        form.post(route("create.rent"), {
            onSuccess: () => {
                if (list.props.flash.status === true) {
                    successToast(list.props.flash.message);
                    form.reset();
                    router.visit(route("rental.success"));
                } else if (!authUser) {
                    errorToast("Please login to rent a car");
                } else {
                    errorToast(list.props.flash.message);
                }
            },
            onError: (errors) => {
                if (errors.user_id) {
                    errorToast(errors.user_id);
                } else if (errors.car_id) {
                    errorToast(errors.car_id);
                } else if (errors.start_date) {
                    errorToast(errors.start_date);
                } else if (errors.end_date) {
                    errorToast(errors.end_date);
                } else if (errors.status) {
                    errorToast(errors.status);
                } else if (errors.pickup_location) {
                    errorToast(errors.pickup_location);
                } else if (errors.drop_off_location) {
                    errorToast(errors.drop_off_location);
                } else if (errors.pickup_time) {
                    errorToast(errors.pickup_time);
                } else if (errors.drop_off_time) {
                    errorToast(errors.drop_off_time);
                } else {
                    errorToast("An error occurred");
                }
            },
        });
    };
</script>

<template>
    <section
        class="banner-section banner-section--style2 bg_img banner-image"
        style=" background-image: url('https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <div class="banner-content">
                        <h1 class="title">Find your rent car</h1>
                        <p>
                            It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.
                            It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.
                        </p>
                        <Link :href="route('car.page')" class="cmn-btn">See all cars</Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped></style>
