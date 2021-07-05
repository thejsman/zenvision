<template>
    <div class="my-3">
        <div>
            <h4 class="my-2">Profile</h4>
            <b-card bg-variant="light" text-variant="white">
                <b-form @submit="onSubmit" @reset="onReset" v-if="show">
                    <div class="row">
                        <div class="col">
                            <b-form-group
                                id="firstNameGroup"
                                label="First Name:"
                                label-for="firstName"
                            >
                                <b-form-input
                                    id="firstName"
                                    v-model="formProfile.firstName"
                                    type="text"
                                    placeholder="First name"
                                    required
                                ></b-form-input>
                            </b-form-group>
                        </div>
                        <div class="col">
                            <b-form-group
                                id="lastNameGroup"
                                label="Last Name:"
                                label-for="lastName"
                            >
                                <b-form-input
                                    id="lastName"
                                    v-model="formProfile.lastName"
                                    placeholder="Last name"
                                    required
                                ></b-form-input>
                            </b-form-group>
                        </div>
                        <div class="col">
                            <b-form-group
                                id="emailGroup"
                                label="Email"
                                label-for="email"
                            >
                                <b-form-input
                                    id="email"
                                    v-model="formProfile.email"
                                    placeholder="Email"
                                    type="email"
                                    required
                                ></b-form-input>
                            </b-form-group>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <b-form-group
                                id="phoneNumberGroup"
                                label="Phone Number"
                                label-for="phoneNumber"
                            >
                                <b-form-input
                                    id="phoneNumber"
                                    v-model="formProfile.phoneNumber"
                                    placeholder="Phone Number"
                                    type="tel"
                                    required
                                ></b-form-input>
                            </b-form-group>
                            <b-button type="submit" variant="success"
                                >Save</b-button
                            >
                        </div>
                    </div>
                </b-form>
            </b-card>
        </div>
        <!-- Change password -->
        <div class="pt-5">
            <h4 class="my-2">Change Password</h4>
            <b-card bg-variant="light" text-variant="white">
                <b-form @submit="onSubmit" @reset="onReset" v-if="show">
                    <div class="row">
                        <div class="col-4">
                            <b-form-group
                                id="passwordGroup"
                                label="Password"
                                label-for="password"
                            >
                                <b-form-input
                                    id="password"
                                    v-model="formPassword.password"
                                    type="password"
                                    placeholder="Password"
                                    required
                                ></b-form-input>
                            </b-form-group>
                        </div>
                        <div class="col-4">
                            <b-form-group
                                id="repeatPasswordGroup"
                                label="Repeat New Password"
                                label-for="repeatPassword"
                            >
                                <b-form-input
                                    id="repeatPassword"
                                    v-model="formPassword.repeatPassword"
                                    type="password"
                                    placeholder="Repeat New Password"
                                    required
                                ></b-form-input>
                            </b-form-group>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <b-button type="submit" variant="success"
                                >Save</b-button
                            >
                        </div>
                    </div>
                </b-form>
            </b-card>
        </div>
        <b-card class="mt-3 d-none" header="Will be removed">
            <b-alert :show="showMessage" :variant="updateVariant">{{
                updateResult
            }}</b-alert>
            <pre class="m-0">{{ formProfile }}</pre>
        </b-card>
    </div>
</template>
<script>
import axios from "axios";

export default {
    name: "Profile",

    data() {
        return {
            formProfile: {
                email: "",
                firstName: "",
                lastName: "",
                phoneNumber: ""
            },
            formPassword: {
                password: "",
                repeatPassword: ""
            },
            show: true,
            showMessage: false,
            updateVariant: "success",
            updateResult: ""
        };
    },
    methods: {
        onSubmit(event) {
            event.preventDefault();
            // alert(JSON.stringify(this.form));
        },
        onReset(event) {
            event.preventDefault();
            // Reset our form values
            this.form.email = "";
            this.form.name = "";
            this.form.food = null;
            this.form.checked = [];
            // Trick to reset/clear native browser form validation state
            this.show = false;
            this.$nextTick(() => {
                this.show = true;
            });
        }
    }
};
</script>
