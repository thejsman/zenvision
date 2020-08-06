<script>
/**
 * Login component
 */
export default {
  data() {
    return {
      email: "",
      password: "",
      tryingToLogIn: false,
      isAuthError: false
    };
  },
  props: {
    submitUrl: {
      type: String,
      required: true
    },
    authError: {
      type: String,
      required: false,
      default: () => null
    }
  },
  mounted() {
    this.isAuthError = !!this.authError;
  }
};
</script>

<template>
  <div class="row justify-content-center">
    <div class="col-12 text-center mb-5">
      <div>
        <img src="/images/logo.svg" alt="Zenvision" class="img-fluid" />
      </div>
    </div>
    <div class="col-md-8 col-lg-6 col-xl-5">
      <div class="card overflow-hidden">
        <div class="card-body pt-0">
          <div class="text-center">
            <h4 class="text-white text-uppercase mb-4 mt-4">Log in</h4>
          </div>
          <b-alert v-model="isAuthError" variant="danger" class="mt-3" dismissible>{{authError}}</b-alert>

          <b-form class="p-2" :action="submitUrl" method="POST">
            <slot />
            <b-form-group id="input-group-1">
              <b-form-input
                id="input-1"
                name="email"
                v-model="email"
                type="email"
                placeholder="Email"
                required
              ></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-2">
              <b-form-input
                id="input-2"
                v-model="password"
                name="password"
                type="password"
                placeholder="Password"
                required
              ></b-form-input>
            </b-form-group>
            <div class="custom-control custom-checkbox">
              <input id="customControlInline" type="checkbox" class="custom-control-input" />
              <label class="custom-control-label" for="customControlInline">Remember me</label>
            </div>
            <div class="mt-3">
              <b-button type="submit" variant="primary" class="btn-block btn-success">Log In</b-button>
            </div>
            <div class="mt-4 text-center">
              <a href="/password/reset" class="text-muted">
                <i class="mdi mdi-lock mr-1"></i> Forgot your password?
              </a>
            </div>
          </b-form>
        </div>
        <!-- end card-body -->
      </div>
      <!-- end card -->

      <div class="mt-5 text-center">
        <p>
          Don't have an account ?
          <a
            href="/register"
            class="font-weight-medium text-primary"
          >Signup now</a>
        </p>
      </div>
      <!-- end row -->
    </div>
    <!-- end col -->
  </div>
  <!-- end row -->
</template>

