<script>
/**
 * Forgot Password component
 */
export default {
  data() {
    return {
      isResetError: false,
    };
  },
  mounted() {
    this.isResetError = !!this.error;
  },
  props: {
    submitUrl: {
      type: String,
      required: true,
    },
    email: {
      type: String,
      required: false,
      default: () => null,
    },
    error: {
      type: String,
      required: false,
      default: () => null,
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
    <div class="col-md-8 col-lg-6 col-xl-5">
      <div class="card overflow-hidden">
        <div class="card-body pt-2">
          <div class="text-center">
            <h4 class="text-white text-uppercase mb-4 mt-4">reset password</h4>
          </div>
          <div class="p-2">
            <b-alert
              v-model="isResetError"
              class="mb-4"
              variant="danger"
              dismissible
              >{{ error }}</b-alert
            >
            <form :action="submitUrl" method="POST">
              <slot />
              <div class="form-group">
                <input
                  type="email"
                  name="email"
                  :value="email"
                  class="form-control"
                  id="useremail"
                  placeholder="Enter email"
                  disabled
                />
              </div>
              <b-form-group id="password-group">
                <b-form-input
                  id="password"
                  name="password"
                  type="password"
                  placeholder="Enter password"
                  required
                ></b-form-input>
              </b-form-group>
              <b-form-group>
                <b-form-input
                  id="password-confirm"
                  name="password_confirmation"
                  type="password"
                  placeholder="Confirm password"
                  required
                ></b-form-input>
              </b-form-group>
              <div class="form-group row mb-0">
                <div class="col-12 text-right">
                  <button
                    class="btn btn-primary btn-success btn-block"
                    type="submit"
                  >
                    Reset
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- end card-body -->
      </div>
      <!-- end card -->

      <div class="mt-5 text-center">
        <p>
          Remember It ?
          <a href="/login" class="font-weight-medium text-primary"
            >Sign In here</a
          >
        </p>
      </div>
    </div>
    <!-- end col -->
  </div>
</template>

