<template>
  <div class="flex-start pl-2">
    <div class="d-flex flex-row dropdown">
      <div v-for="account in ppAccounts" :key="account.id">
        <div
          class="border rounded p-2 ml-1 dropbtn"
          :class="{ 'border-primary': account.enabled_on_dashboard }"
          @click="disableFeature ? handleClick(account) : null"
          v-b-tooltip.hover="account.name"
        >
          <img src="/images/icons/paypal.png" alt height="21" />
        </div>
        <div class="dropdown-content">
          <a href="#" @click="handleClick(account)" v-if="disableFeature">
            {{ account.enabled_on_dashboard ? "Disable" : "Enable" }}</a
          >
          <a href="#" @click="showMsgBoxOne(account, $event)">Remove</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return { ppAccounts: [] };
  },
  props: { disableFeature: { type: Boolean, default: true } },
  created() {
    this.getPaypalAccounts();
  },
  methods: {
    async getPaypalAccounts() {
      try {
        const result = await axios.get("getpaypalaccounts");
        this.ppAccounts = result.data;
      } catch (err) {
        console.log(err);
        this.ppAccounts = [];
      }
    },
    showMsgBoxOne(account) {
      this.boxOne = "";
      this.$bvModal
        .msgBoxConfirm("Are you sure you want to remove the Paypal account?")
        .then((value) => {
          this.boxOne = value;
          if (value) {
            this.removeChannel(account);
          }
        })
        .catch((err) => {});
    },
    async handleClick(account) {
      this.$emit("togglePaypalAccount", account.id);
      try {
        await axios.patch("paypal", account);
        await this.getPaypalAccounts();
      } catch (error) {
        console.log(error);
      }
    },
    async removeChannel(account, event) {
      try {
        await axios.patch("paypaldelete", account);
        await this.getPaypalAccounts();
      } catch (error) {
        console.log(error);
        this.ppAccounts = [];
      }
    },
  },
};
</script>