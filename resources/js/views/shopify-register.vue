<template>
    <Layout>
        <div class="row">
            <!-- <b-button v-b-modal.shopify-register-modal>Open Modal</b-button> -->
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
                        <b-form @submit="changePassword" v-if="show">
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
            <b-modal
                id="shopify-account-exists"
                title="Shopify Account"
                centered
                no-close-on-esc
                no-close-on-backdrop
                hide-header-close
                :hide-footer="true"
            >
                <p class="my-2">
                    You already have an account with us, please
                    <a href="/login">log in.</a>
                </p>
            </b-modal>
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

            formPassword: {
                password: "",
                repeatPassword: ""
            },
            show: true,
            loadingStatus: false,
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
        if (new URL(location.href).searchParams.get("status")) {
            const status = new URL(location.href).searchParams.get("status");
            if (status === "account_exists") {
                setTimeout(() => {
                    this.$bvModal.show("shopify-account-exists");
                    this.loadingStatus = false;
                }, 1000);
            }
        } else {
            setTimeout(() => {
                this.$bvModal.show("shopify-register-modal");
                this.loadingStatus = false;
            }, 1000);
        }
        this.loadingStatus = true;
        this.toggleCurrentChannel("shopify-register");
    },
    props: {
        dataarray: {
            type: Array,
            default: () => {}
        }
    },
    methods: {
        ...mapActions(["toggleCurrentChannel"]),

        async changePassword(event) {
            event.preventDefault();
            try {
                this.loadingStatus = true;
                if (
                    this.formPassword.password ===
                    this.formPassword.repeatPassword
                ) {
                    await axios.patch("changepassword", this.formPassword);
                    this.loadingStatus = false;
                }
                window.location.href = "/";
            } catch (err) {
                console.log({ err });
                this.loadingStatus = false;
            }
        },

        showAccountModal() {
            this.$bvModal.show("account-exist");
        }
    }
};
</script>
