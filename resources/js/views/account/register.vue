<script>
/** * Register component */ export default {
  data() {
    return {
      firstname: "",
      lastname: "",
      email: "",
      password: "",
      phone: "",
      isRegisterError: false,
      registerSuccess: false,
      phoneFocus: false,
      passwordFocus: false,
      emailFocus: false,
    };
  },
  props: {
    submitUrl: { type: String, required: true },
    regError: { type: String, required: false, default: () => null },
  },
  mounted() {
    this.isRegisterError = !!this.regError;
  },
  computed: {
    phoneState() {
      return this.phone.length > 9 ? true : false;
    },
    passwordState() {
      return this.password.length > 5 ? true : false;
    },
    emailState() {
      const email_regex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/i;
      if (email_regex.test(this.email)) return true;
      else return false;
    },
  },
};
</script>
<template>
  <div class="row justify-content-center">
    <div class="col-12 text-center mb-5">
      <div>
        <img src="/images/logo.svg" alt="Zenvision" class="img-fluid" />
      </div>
    </div>

    <div class="col-md-8 col-lg-6 col-xl-5 mt-2">
      <div class="card card-login overflow-hidden">
        <div class="text-center">
          <h4 class="text-white text-uppercase mb-4 mt-4">Sign Up</h4>
        </div>

        <div class="card-body pt-0">
          <b-alert
            v-model="registerSuccess"
            class="mt-3"
            variant="success"
            dismissible
            >Registration successfull.</b-alert
          >
          <b-alert
            v-model="isRegisterError"
            class="mt-3"
            variant="danger"
            dismissible
            >{{ regError }}</b-alert
          >
          <b-form class="p-2" :action="submitUrl" method="POST">
            <slot />
            <b-form-group id="firstname-group">
              <b-form-input
                id="firstname"
                v-model="firstname"
                name="firstname"
                type="text"
                placeholder="First name"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group id="lastname-group">
              <b-form-input
                id="lastname"
                v-model="lastname"
                name="lastname"
                type="text"
                placeholder="Last name"
                required
              ></b-form-input>
            </b-form-group>
            <b-form-group id="fullname-group">
              <b-form-input
                id="email"
                name="email"
                @focus="emailFocus = false"
                @blur="emailFocus = true"
                v-model="email"
                type="email"
                placeholder="Enter email"
                required
              ></b-form-input>
              <b-form-invalid-feedback :state="emailFocus ? emailState : true">
                Please provide a valid email.
              </b-form-invalid-feedback>
            </b-form-group>
            <b-form-group id="phone-group">
              <b-form-input
                id="phone"
                v-model="phone"
                @focus="phoneFocus = false"
                @blur="phoneFocus = true"
                aria-describedby="phone-feedback"
                name="phone"
                type="tel"
                placeholder="Phone number"
                required
              ></b-form-input>
              <b-form-invalid-feedback
                :state="phoneFocus ? phoneState : true"
                id="phone-feedback"
                >Phone number should be a 10 digit
                number</b-form-invalid-feedback
              >
            </b-form-group>
            <b-form-group id="password-group">
              <b-form-input
                id="password"
                v-model="password"
                @focus="passwordFocus = false"
                @blur="passwordFocus = true"
                aria-descrivedby="password-feedback"
                name="password"
                type="password"
                placeholder="Enter password"
                required
              ></b-form-input>
              <b-form-invalid-feedback
                id="password-feeback"
                :state="passwordFocus ? passwordState : true"
                >Password must be at least 6 character
                long</b-form-invalid-feedback
              >
            </b-form-group>

            <div class="mt-4">
              <b-button
                type="submit"
                variant="primary"
                class="btn-block"
                :disabled="!phoneState || !emailState || !passwordState"
                >Register</b-button
              >
            </div>

            <div class="mt-4 text-center">
              <p class="mb-0">
                By registering you agree to the
                <a href="javascript: void(0);" class="text-primary"
                  >Terms & Conditions</a
                >
              </p>
            </div>
          </b-form>
        </div>
        <!-- end card-body -->
      </div>
      <!-- end card -->

      <div class="mt-5 text-center">
        <p>
          Already have an account ?
          <a href="/login" class="font-weight-medium text-primary">Log in</a>
        </p>
      </div>
    </div>
    <!-- end col -->
  </div>
</template>
<style lang="scss" module></style>
