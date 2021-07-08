<template>
    <Layout>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="my-2">
                    <div>
                        <h4 class="my-3">Profile</h4>
                        <b-card bg-variant="light" text-variant="white">
                            <b-form
                                @submit="updateUserProfile"
                                @reset="onReset"
                                v-if="show"
                            >
                                <div class="row">
                                    <div class="col-lg">
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
                                    <div class="col-lg">
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
                                    <div class="col-lg">
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
                                                @focus="emailFocus = false"
                                                @blur="emailFocus = true"
                                            ></b-form-input>
                                            <b-form-invalid-feedback
                                                :state="
                                                    emailFocus
                                                        ? emailState
                                                        : true
                                                "
                                            >
                                                Please provide a valid email.
                                            </b-form-invalid-feedback>
                                        </b-form-group>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4 col-md-12">
                                        <b-form-group
                                            id="phoneNumberGroup"
                                            label="Phone Number"
                                            label-for="phoneNumber"
                                        >
                                            <b-form-input
                                                id="phoneNumber"
                                                v-model="
                                                    formProfile.phoneNumber
                                                "
                                                placeholder="Phone Number"
                                                type="tel"
                                                @focus="phoneFocus = false"
                                                @blur="phoneFocus = true"
                                                required
                                            ></b-form-input>
                                            <b-form-invalid-feedback
                                                :state="
                                                    phoneFocus
                                                        ? phoneState
                                                        : true
                                                "
                                                id="phone-feedback"
                                                >Phone number should be a 10 digit
                                                number</b-form-invalid-feedback
                                            >
                                        </b-form-group>
                                        <b-button
                                            type="submit"
                                            variant="success"
                                            :disabled="
                                                !phoneState || !emailState
                                            "
                                            >Save</b-button
                                        >
                                    </div>
                                </div>
                            </b-form>
                        </b-card>
                    </div>
                    <!-- Change password -->
                    <div class="mt-5">
                        <h4 class="my-4">Change Password</h4>
                        <b-card bg-variant="light" text-variant="white">
                            <b-form
                                @submit="changePassword"
                                @reset="onReset"
                                v-if="show"
                            >
                                <div class="row">
                                    <div class="col-lg-4 col-md-12">
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
                                                @focus="passwordFocus = false"
                                                @blur="passwordFocus = true"
                                                required
                                            ></b-form-input>
                                            <b-form-invalid-feedback
                                                id="password-feeback"
                                                :state="
                                                    passwordFocus
                                                        ? passwordState
                                                        : true
                                                "
                                                >Password must be at least 6 character
                                                long</b-form-invalid-feedback
                                            >
                                        </b-form-group>
                                    </div>
                                    <div class="col-lg-4 col-md-12">
                                        <b-form-group
                                            id="repeatPasswordGroup"
                                            label="Repeat New Password"
                                            label-for="repeatPassword"
                                        >
                                            <b-form-input
                                                id="repeatPassword"
                                                v-model="
                                                    formPassword.repeatPassword
                                                "
                                                type="password"
                                                placeholder="Repeat New Password"
                                                @focus="
                                                    repeatPasswordFocus = false
                                                "
                                                @blur="
                                                    repeatPasswordFocus = true
                                                "
                                                required
                                            ></b-form-input>
                                            <b-form-invalid-feedback
                                                id="password-feeback"
                                                :state="
                                                    repeatPasswordFocus
                                                        ? repeatPasswordState
                                                        : true
                                                "
                                                >The password confirmation does
                                                not
                                                match.</b-form-invalid-feedback
                                            >
                                        </b-form-group>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4">
                                        <b-button
                                            type="submit"
                                            variant="success"
                                            :disabled="
                                                !passwordState ||
                                                    !repeatPasswordState
                                            "
                                            >Save</b-button
                                        >
                                    </div>
                                </div>
                            </b-form>
                        </b-card>
                    </div>
                    <div class="mt-3">
                        <b-alert
                            dismissible
                            v-model="showMessage"
                            :variant="messageVariant"
                            >{{ updateResult }}</b-alert
                        >
                    </div>
                    <loading
                        :active.sync="loadingStatus"
                        :can-cancel="false"
                        :is-full-page="true"
                        :background-color="'#191e2c'"
                        :opacity="0.8"
                    ></loading>
                </div>
            </div>
        </div>
    </Layout>
</template>
<script>
import axios from "axios";
import Loading from "vue-loading-overlay";
import Layout from "../layouts/main";
import { mapActions } from "vuex";
export default {
    name: "Profile",
    components: { Loading, Layout },
    data() {
        return {
            formProfile: {
                id: null,
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
            messageVariant: "success",
            updateResult: "",
            loadingStatus: false,
            phoneFocus: false,
            emailFocus: false,
            passwordFocus: false,
            repeatPasswordFocus: false
        };
    },

    computed: {
        phoneState() {
            return this.formProfile.phoneNumber.length > 9 ? true : false;
        },
        passwordState() {
            return this.formPassword.password.length > 5 ? true : false;
        },
        emailState() {
            const email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
            if (email_regex.test(this.formProfile.email)) return true;
            else return false;
        },
        repeatPasswordState() {
            return (
                this.formPassword.password === this.formPassword.repeatPassword
            );
        }
    },
    created() {
        this.toggleCurrentChannel("Profile");
        this.getUserProfile();
    },
    methods: {
        ...mapActions(["toggleCurrentChannel"]),
        async getUserProfile() {
            try {
                this.loadingStatus = true;
                const { data } = await axios.get("/user");
                const { id, firstname, lastname, email, phone } = data;

                this.formProfile.id = id;
                this.formProfile.firstName = firstname;
                this.formProfile.lastName = lastname;
                this.formProfile.email = email;
                this.formProfile.phoneNumber = phone;

                this.loadingStatus = false;
            } catch (err) {
                this.formProfile.firstName = "";
                this.formProfile.lastName = "";
                this.formProfile.email = "";
                this.formProfile.phoneNumber = "";
                this.loadingStatus = false;
            }
        },

        async updateUserProfile(event) {
            event.preventDefault();
            try {
                this.loadingStatus = true;
                const result = await axios.patch("/user", this.formProfile);

                this.messageVariant = "success";
                this.updateResult = "Profile updated successfully";
                this.showMessage = true;
                this.loadingStatus = false;
            } catch (err) {
                if (Object.keys(err.response.data.errors).length > 0) {
                    Object.entries(err.response.data.errors).forEach(
                        ([k, v]) => {
                            this.updateResult = v[0];
                        }
                    );
                } else {
                    this.updateResult =
                        "Something went wrong, please try later";
                }
                this.messageVariant = "danger";

                this.showMessage = true;
                this.loadingStatus = false;
            }
        },
        async changePassword(event) {
            event.preventDefault();
            try {
                this.loadingStatus = true;
                if (
                    this.formPassword.password ===
                    this.formPassword.repeatPassword
                ) {
                    const result = await axios.patch(
                        "changepassword",
                        this.formPassword
                    );
                    this.messageVariant = "success";
                    this.updateResult = "Password updated successfully";
                    this.showMessage = true;
                    this.loadingStatus = false;
                }
            } catch (err) {
                if (Object.keys(err.response.data.errors).length > 0) {
                    Object.entries(err.response.data.errors).forEach(
                        ([k, v]) => {
                            this.updateResult = v[0];
                        }
                    );
                } else {
                    this.updateResult =
                        "Something went wrong, please try later";
                }
                this.messageVariant = "danger";

                this.showMessage = true;
                this.loadingStatus = false;
            }
        },
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
