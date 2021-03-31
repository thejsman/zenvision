<template>
  <div class="flex-start pl-1">
    <div class="d-flex flex-row dropdown">
      <div v-for="googleAccount in googleAccounts" :key="googleAccount.id">
        <div
          class="border rounded p-2 ml-2 dropbtn"
          :class="{ 'border-primary': googleAccount.enabled_on_dashboard }"
          @click="disableFeature ? handleClick(googleAccount) : null"
          v-b-tooltip.hover="googleAccount.ad_account_id"
        >
          <img src="/images/icons/google-icon.svg" alt height="21" />
        </div>

        <div class="dropdown-content">
          <a href="#" @click="handleClick(googleAccount)" v-if="disableFeature">
            {{ googleAccount.enabled_on_dashboard ? "Disable" : "Enable" }}</a
          >
          <a href="#" @click="showMsgBoxOne(googleAccount, $event)">Remove</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "googleAccount",
  data() {
    return {
      googleAccounts: [],
    };
  },
  props: {
    disableFeature: {
      type: Boolean,
      default: true,
    },
  },
  created() {
    this.getgoogleAccounts();
  },
  methods: {
    async getgoogleAccounts() {
      try {
        const result = await axios.get("google-connect-getaccounts");
        this.googleAccounts = result.data;
      } catch (err) {
        console.log(err);
        this.googleAccounts = [];
      }
    },
    showMsgBoxOne(account) {
      this.boxOne = "";
      this.$bvModal
        .msgBoxConfirm("Are you sure you want to remove the Google account?")
        .then((value) => {
          this.boxOne = value;
          console.log("Yes", value);
          if (value) {
            this.removeChannel(account);
          }
        })
        .catch((err) => {
          // An error occurred
        });
    },
    async handleClick(account) {
      try {
        await axios.patch("google-connect", account);
        await this.getgoogleAccounts();
      } catch (error) {
        console.log(error);
      }
    },

    async removeChannel(account, event) {
      try {
        await axios.patch("google-connect-delete", account);
        await this.getgoogleAccounts();
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<style>
</style>
