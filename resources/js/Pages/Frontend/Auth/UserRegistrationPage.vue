<script setup>
    import GuestLayout from '../../../Layouts/GuestLayout.vue';
    import { Head, Link, usePage, useForm } from '@inertiajs/vue3';

    const list = usePage();

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        address: '',
        phone: '',
    });

    function CustomerRegisration()
    {
        form.post(route('user.registration'), {
            onSuccess: () => {
                if (list.props.flash.status === true) {
                    successToast(list.props.flash.message);
                    form.reset();
                } else {
                    errorToast(list.props.flash.message);
                }
            },

            onError: (errors) => {
                if (errors.name) {
                    errorToast(errors.name);
                } else if (errors.email) {
                    errorToast(errors.email);
                } else if (errors.password) {
                    errorToast(errors.password);
                } else if (errors.password_confirmation) {
                    errorToast(errors.password_confirmation);
                } else if (errors.address) {
                    errorToast(errors.address);
                } else if (errors.phone) {
                    errorToast(errors.phone);
                } else {
                    errorToast(list.props.flash.message);
                }
            }
        });
    }
</script>

<template>
    <Head>
        <title>Car Rent | Sign Up</title>
    </Head>

    <GuestLayout>
        <section class="registration-section pt-120 pb-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="registration-block text-center">
                            <div class="registration-block-inner">
                                <h3 class="title">Register</h3>
                                <form class="registration-form" @submit.prevent="CustomerRegisration()">
                                    <div class="form-group">
                                        <input class="form-control" type="text" id="name" v-model="form.name"
                                            placeholder="Name">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" id="email" v-model="form.email" class="form-control"
                                            placeholder="Email">
                                    </div>
                                    <div class="frm-group">
                                        <input type="phone" id="phone" v-model="form.phone" class="form-control"
                                            placeholder="Phone">
                                    </div>
                                    <div>
                                        <textarea rows="2" id="address" v-model="form.address" class="form-control"
                                            placeholder="Address"></textarea>
                                    </div>
                                    <div class="frm-group mt-3">
                                        <input type="password" id="password" v-model="form.password"
                                            class="form-control" placeholder="Password">
                                    </div>
                                    <div class="frm-group">
                                        <input type="password" id="password_confirmation"
                                            v-model="form.password_confirmation" class="form-control"
                                            placeholder="Confirm Password">
                                    </div>

                                    <button type="submit" class="cmn-btn w-100">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<style scoped></style>
