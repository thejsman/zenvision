<template>
  <div class="row">
    <div class="m-4 d-flex flex-column">
      <div
        class="font-weight-bold font-size-14 text-white d-flex align-items-center"
      >
        <img
          src="/images/icons/shopify-icon.svg"
          alt
          height="21"
          class="mr-2"
        />
        <div>Connect to Shopify store</div>
      </div>
      <p class="mt-4 mb-4">
        Enter your existing
        <strong>Shopify store URL</strong> below and you'll be redirected to
        Shopify to connect your account to Zenvision
      </p>
      <b-alert
        v-model="isAuthError"
        variant="danger"
        class="mt-3"
        dismissible
        >{{ authError }}</b-alert
      >

      <b-form class="p-2" @submit.prevent="validateShopify" method="POST">
        <slot />
        <b-form-group id="input-group-1">
          <b-form-input
            id="input-1"
            name="store_url"
            v-model="store_url"
            v-on:blur="onBlur"
            v-on:paste="onPaste"
            type="text"
            placeholder="store-name.myshopify.com"
            required
          ></b-form-input>
        </b-form-group>
        <div class="d-flex mt-4 justify-content-between align-items-center p-0">
          <div>
            <a
              class="text-muted font-size-10"
              href="#"
              v-b-modal.how-to-connect-store
              >How to connect my Shopify store?</a
            >
          </div>
          <div>
            <b-button
              variant="primary"
              class="btn btn-dark border-0 mr-2"
              @click="$emit('handle-close')"
              >Cancel</b-button
            >
            <b-button type="submit" variant="primary" class="btn btn-success"
              >Connect</b-button
            >
          </div>
        </div>
      </b-form>
    </div>
    <b-modal
      id="how-to-connect-store"
      size="lg"
      centered
      hide-footer
      hide-header
    >
      <HowToConnectStore
        @handle-close="$bvModal.hide('how-to-connect-store')"
      />
    </b-modal>
  </div>
</template>
<script>
import HowToConnectStore from "./HowToConnectStore-model";
import axios from "axios";

export default {
  name: "ShopifyConnect",
  components: { HowToConnectStore },
  data() {
    return {
      store_url: "",
      tryingToLogIn: false,
      isAuthError: false,
    };
  },
  props: {
    submitUrl: {
      type: String,
    },
    authError: {
      type: String,
      required: false,
      default: () => null,
    },
  },
  mounted() {
    this.isAuthError = !!this.authError;
  },
  methods: {
    onBlur: function (element) {
      let store_url = this.store_url.trim();

      if (store_url !== "") {
        // remove https or http from url if exist
        if (
          store_url.indexOf("http://") == 0 ||
          store_url.indexOf("https://") == 0
        ) {
          store_url = store_url.replace(/^https?\:\/\//i, "");
        }

        // remove anything after the .com
        if (store_url.indexOf(".com") != -1) {
          store_url = store_url.substring(0, store_url.indexOf(".com") + 4);
        }

        this.store_url = store_url;
      }
    },
    onPaste: function (element) {
      let clipboard_text = element.clipboardData.getData("Text");

      if (clipboard_text !== "") {
        this.store_url = clipboard_text;
        element.target.blur();
      }
    },
    validateShopify() {
      console.log(this.store_url);
      if (this.store_url !== "") {
        window.location = `https://${this.store_url}/admin/oauth/authorize?client_id=6475dbe1c3d0b763d819fc4d053d771e&scope=read_orders,write_orders,read_all_orders&redirect_uri=https://d7df9bf702fa.ngrok.io/shopify/auth`;
      }
    },
  },
};
</script>

