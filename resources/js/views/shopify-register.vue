<template>
    <Layout>
        <div class="row">
            <b-button v-b-modal.shopify-register-modal>Open Modal</b-button>
            <b-modal
                id="shopify-register-modal"
                ref="modal"
                centered
                no-close-on-esc
                no-close-on-backdrop
                hide-header-close
                :hide-header="true"
                :hide-footer="true"
            >
                <div>
                    <h3 class="my-4">You are almost set!</h3>
                    <p>
                        Please create a log in to finish setting up your account
                    </p>
                    <b-card bg-variant="light" text-variant="white">
                        <b-form
                            @submit="changePassword"
                            @reset="onReset"
                            v-if="show"
                        >
                            <div class="row">
                                <div class="col-12">
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
                                <div class="col-12">
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
                                            @focus="repeatPasswordFocus = false"
                                            @blur="repeatPasswordFocus = true"
                                            required
                                        ></b-form-input>
                                        <b-form-invalid-feedback
                                            id="password-feeback"
                                            :state="
                                                repeatPasswordFocus
                                                    ? repeatPasswordState
                                                    : true
                                            "
                                            >The password confirmation does not
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
                                        class="button-profile"
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
            </b-modal>
            <loading
                :active.sync="loadingStatus"
                :can-cancel="false"
                :is-full-page="true"
                :background-color="'#191e2c'"
                :opacity="0.8"
            ></loading>
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
            hideSection: true,
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
        passwordState() {
            return this.formPassword.password.length > 5 ? true : false;
        },

        repeatPasswordState() {
            return (
                this.formPassword.password === this.formPassword.repeatPassword
            );
        }
    },
    created() {
        this.loadingStatus = true;
        this.toggleCurrentChannel("shopify-register");
        setTimeout(() => {
            this.$bvModal.show("shopify-register-modal");
            this.loadingStatus = false;
            console.log("Check this: ", this.dataarray);
        }, 2000);
    },
    props: {
        dataarray: {
            type: Array,
            default: () => {}
        }
    },
    methods: {
        ...mapActions(["toggleCurrentChannel"]),
        async updateUserProfile(event) {
            event.preventDefault();
            try {
                this.loadingStatus = true;
                await axios.patch("/user", this.formProfile);

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
                    await axios.post("shopify-register", this.formPassword);
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
        },
        showAccountModal() {
            this.$bvModal.show("deactivate-modal");
        },
        async deactivateAccount() {
            try {
                await axios.delete("/user");
                window.location.href = "/logout";
            } catch (error) {
                window.location.href = "/logout";
            }
        }
    }
};
</script>
<style>
.zv-danger {
    color: #e75555;
}
.deactivate-account {
    cursor: pointer;
    transition: 0.3s;
}
.deactivate-account:hover {
    opacity: 0.5;
}
</style>
