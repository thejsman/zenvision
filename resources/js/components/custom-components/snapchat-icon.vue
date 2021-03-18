<template>
  <div class="flex-start pl-1">
    <div class="d-flex flex-row dropdown">
      <div
        v-for="snapchatAccount in snapchatAccounts"
        :key="snapchatAccount.id"
      >
        <div
          class="border rounded p-2 ml-2 dropbtn"
          :class="{ 'border-primary': snapchatAccount.enabled_on_dashboard }"
          @click="disableFeature ? handleClick(snapchatAccount) : null"
          v-b-tooltip.hover="snapchatAccount.display_name"
        >
          <img src="/images/icons/snapchat-icon.svg" alt height="21" />
        </div>

        <div class="dropdown-content">
          <a
            href="#"
            @click="handleClick(snapchatAccount)"
            v-if="disableFeature"
          >
            {{ snapchatAccount.enabled_on_dashboard ? "Disable" : "Enable" }}</a
          >
          <a href="#" @click="showMsgBoxOne(snapchatAccount, $event)">Remove</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "SnapchatAccount",
  data() {
    return {
      snapchatAccounts: [],
    };
  },
  props: {
    disableFeature: {
      type: Boolean,
      default: true,
    },
  },
  created() {
    this.getsnapchatAccounts();
  },
  methods: {
    async getsnapchatAccounts() {
      try {
        const result = await axios.get("getsnapchataccounts");
        this.snapchatAccounts = result.data;
      } catch (err) {
        console.log(err);
        this.snapchatAccounts = [];
      }
    },
    showMsgBoxOne(account) {
      this.boxOne = "";
      this.$bvModal
        .msgBoxConfirm(
          "Are you sure you want to remove the Snapchat Ad account?"
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
        await axios.patch("snapchat-connect", account);
        await this.getsnapchatAccounts();
      } catch (error) {
        console.log(error);
      }
    },

    async removeChannel(account, event) {
      try {
        await axios.patch("snapchat-connect-delete", account);
        await this.getsnapchatAccounts();
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<style>
</style>
