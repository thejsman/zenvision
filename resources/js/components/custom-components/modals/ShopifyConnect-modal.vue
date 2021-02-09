<template>
  <div class="row">
    <div class="m-4 d-flex flex-column">
      <div
        class="font-weight-bold font-size-14 text-white d-flex align-items-center"
      >
        <img
          src="/images/icons/shopify-icon.svg"
          alt
          height="40"
          class="shopify-icon"
        />
        <h3>Connect to Shopify store</h3>
      </div>
      <p class="text100">
        Enter your existing
        <b>Shopify store URL</b> below and you'll be redirected to Shopify to
        connect your account to Zenvision
      </p>

      <b-alert
        :show="dismissCountDown"
        variant="danger"
        class="mt-3"
        fade
        dismissible
        @dismissed="dismissCountDown = 0"
        @dismiss-count-down="countDownChanged"
        >{{ formError }}</b-alert
      >

      <b-form class="p-0" @submit.prevent="connectToShopifyStore" method="POST">
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
            <a class="font-size-10" href="#" v-b-modal.how-to-connect-store
              >How to connect my Shopify store?</a
            >
          </div>
          <div>
            <b-button
              variant="cancel"
              class="btn btn-cancel"
              @click="$emit('handle-close')"
              >Cancel</b-button
            >

            <b-button
              type="submit"
              variant="success"
              class="btn btn-success ml-2"
              >Connect</b-button
            >
          </div>
        </div>
      </b-form>
    </div>
    <b-modal
      id="how-to-connect-store"
      size="xl"
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
      formError: "",
      dismissSecs: 5,
      dismissCountDown: 0,
    };
  },
  props: {
    submitUrl: {
      type: String,
    },
  },
  methods: {
    countDownChanged(dismissCountDown) {
      this.dismissCountDown = dismissCountDown;
    },
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
    showAlert(message) {
      this.formError = message;
      this.dismissCountDown = this.dismissSecs;
    },

    //Check valid URL
    CheckURL(str) {
      const pattern = new RegExp(
        "^(https?:\\/\\/)?" + // protocol
          "((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
          "((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
          "(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
          "(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
          "(\\#[-a-z\\d_]*)?$",
        "i"
      ); // fragment locator

      //check if its a shopify store url and a valid url
      return str.includes(".myshopify.com") && pattern.test(str);
    },

    async connectToShopifyStore() {
      let store_url = this.store_url.trim();

      //check empty string, is a valid URL and is of Shopify store
      if (store_url !== "" && !this.CheckURL(store_url)) {
        this.showAlert("Please enter a valid Shopify URL.");
      } else {
        try {
          //Check if the store is already connected
          const {
            data: { count },
          } = await axios.get("validateShopifyStoreUrl", {
            params: {
              store_url,
            },
          });

          // if store exists
          if (count) {
            this.showAlert("Shopify store already connected.");
          } else {
            window.location = `https://${this.store_url}/${process.env.MIX_SHOPIFY_AUTH_URL}`;
          }
        } catch (error) {
          console.log(error);
          this.showAlert("Something went wrong. Please try again later.");
        }
      }
    },
  },
};
</script>
