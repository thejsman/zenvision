<template>
  <div class="flex-start pl-1">
    <div class="d-flex flex-row dropdown">
      <div
        v-for="facebookAccount in facebookAccounts"
        :key="facebookAccount.id"
      >
        <div
          class="border rounded p-2 ml-2 dropbtn"
          :class="{ 'border-primary': facebookAccount.enabled_on_dashboard }"
          @click="disableFeature ? handleClick(facebookAccount) : null"
          v-b-tooltip.hover="facebookAccount.ad_account_name"
        >
          <img src="/images/icons/facebook-icon.svg" alt height="21" />
        </div>

        <div class="dropdown-content">
          <a
            href="#"
            @click="handleClick(facebookAccount)"
            v-if="disableFeature"
          >
            {{ facebookAccount.enabled_on_dashboard ? "Disable" : "Enable" }}</a
          >
          <a href="#" @click="showMsgBoxOne(facebookAccount, $event)">Remove</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "FacebookAccount",
  data() {
    return {
      facebookAccounts: [],
    };
  },
  props: {
    disableFeature: {
      type: Boolean,
      default: true,
    },
  },
  created() {
    this.getfacebookAccounts();
  },
  methods: {
    async getfacebookAccounts() {
      try {
        const result = await axios.get("getfacebookaccounts");
        this.facebookAccounts = result.data;
      } catch (err) {
        console.log(err);
        this.facebookAccounts = [];
      }
    },
    showMsgBoxOne(account) {
      this.boxOne = "";
      this.$bvModal
        .msgBoxConfirm(
          "Are you sure you want to remove the Facebook Ad account?"
        )
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
        await axios.patch("fbconnect", account);
        await this.getfacebookAccounts();
      } catch (error) {
        console.log(error);
      }
    },

    async removeChannel(account, event) {
      try {
        await axios.patch("fbconnectdelete", account);
        await this.getfacebookAccounts();
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<style>
</style>
