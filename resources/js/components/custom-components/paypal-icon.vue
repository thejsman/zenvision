<template>
  <div class="flex-start pl-1">
    <div class="d-flex flex-row dropdown">
      <div v-for="Account in ppAccounts" :key="Account.id">
        <div
          class="border rounded mr-2 p-2 dropbtn"
          :class="{ 'border-primary': Account.enabled_on_dashboard }"
          @click="handleClick(Account)"
          v-b-tooltip.hover="Account.name"
        >
          <img src="/images/icons/paypal.png" alt height="21" />
        </div>
        <div class="dropdown-content">
          <a href="#" @click="handleClick(Account)">
            {{ Account.enabled_on_dashboard ? "Disable" : "Enable" }}</a
          >
          <a href="#" @click="showMsgBoxOne(Account, $event)">Remove</a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "PaypalAccounts",
  data() {
    return {};
  },
  props: {
    ppAccounts: {
      type: Array,
      default: () => [],
    },
  },
  created: function () {},
  methods: {
    showMsgBoxOne(account) {
      this.boxOne = "";
      this.$bvModal
        .msgBoxConfirm("Are you sure you want to remove the Ad account?")
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
      this.$emit("togglePaypalAccount", account.id);
      try {
        await axios.patch("paypal", account);
      } catch (error) {
        console.log(error);
      }
    },
    async removeChannel(account, event) {
      try {
        this.$emit("removePaypalAccount", account.id);
        await axios.patch("paypaldelete", account);
      } catch (error) {
        console.log(error);
      }
    },
  },
};
</script>

<style>
/* The container <div> - needed to position the dropdown content */
.dropdown {
  position: relative;
  display: inline-block;
}

/* Dropdown Content (Hidden by Default) */
.dropdown-content {
  display: none;
  position: absolute;
  top: 50px;
  right: -35px;
  background-color: #2e3549;
  min-width: 100px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  z-index: 1;
  border-radius: 8px;
}

/* Links inside the dropdown */
.dropdown-content a {
  color: white;
  padding: 12px 16px;

  text-decoration: none;
  display: block;
}

/* Change color of dropdown links on hover */
.dropdown-content a:hover {
  background-color: #3c445c;
  border-radius: 8px;
}

/* Show the dropdown menu on hover */
.dropdown:hover .dropdown-content {
  display: block;
}
.dropdown-content:after {
  content: "";
  position: absolute;
  width: 0;
  height: 0;
  border-width: 10px;
  border-style: solid;
  border-color: transparent transparent #2e3549 transparent;
  top: -18px;
  left: 35px;
}
</style>
