<script>
/**
 * Forgot Password component
 */
export default {
  data() {
    return {
      email: "",
      tryingToReset: false,
      isResetError: false,
    };
  },
  mounted() {
    this.isResetError = !!this.error;
    this.tryingToReset = !!this.status;
  },
  props: {
    submitUrl: {
      type: String,
      required: true,
    },
    error: {
      type: String,
      required: false,
      default: () => null,
    },
    status: {
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
      <div class="card card-login overflow-hidden">
        <div class="card-body pt-2">
          <div class="text-center">
            <h4 class="text-white text-uppercase mb-4 mt-4">reset password</h4>
          </div>
          <div class="p-2">
            <b-alert v-model="isResetError" class="mb-4" variant="danger" dismissible>{{error}}</b-alert>
            <b-alert v-model="tryingToReset" class="mb-4" variant="success" dismissible>{{status}}</b-alert>
            <form :action="submitUrl" method="POST">
              <slot />
              <div class="form-group">
                <input
                  type="email"
                  name="email"
                  v-model="email"
                  class="form-control"
                  id="useremail"
                  placeholder="Email"
                  required
                />
              </div>
              <div class="form-group row mb-3 pt-2">
                <div class="col-12 text-right">
                  <button class="btn btn-primary btn-success btn-block" type="submit">Reset</button>
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
          <a href="/login" class="font-weight-medium text-primary">Log in here</a>
        </p>
      </div>
    </div>
    <!-- end col -->
  </div>
</template>
