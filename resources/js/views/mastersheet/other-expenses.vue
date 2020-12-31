<template>
  <div>
    <b-tab active>
      <template v-slot:title>
        <span class="d-inline-block d-sm-none">
          <i class="fas fa-home"></i>
        </span>
        <span class="d-none d-sm-inline-block">Other Expenses</span>
      </template>
      <div class="row align-items-center">
        <div class="col-sm-12 col-md-10">
          <form class="app-search d-none d-lg-block">
            <div class="position-relative">
              <input
                type="text"
                class="form-control"
                placeholder="Filter transactions"
              />
              <span class="bx bx-search-alt"></span>
            </div>
          </form>
        </div>
        <div class="col-sm-12 col-md-2">
          <b-dropdown variant="primary" class="m-2" right text="Right align">
            <template v-slot:button-content>
              Tags({{ tagsLenght }}) <i class="fas fa-angle-down pl-1"></i>
            </template>
            <b-dropdown-form>
              <div class="chip-container align-items-center">
                <b-form-group @submit.stop.prevent>
                  <b-form-input
                    id="dropdown-form-email"
                    class="text-center text-white chip-input mr-1"
                    v-model="currentInput"
                    size="sm"
                    placeholder="Add tags"
                    @keypress.enter="saveChip"
                    @keydown.delete="backspaceDelete"
                  ></b-form-input>
                </b-form-group>
                <div class="chip" v-for="(chip, i) of chips" :key="chip.label">
                  {{ chip }}
                  <i class="fas fa-times" @click="deleteChip(i)" />
                </div>
              </div>

              <b-dropdown-text class="mt-4"
                ><span
                  v-b-tooltip.hover="
                    'Zenvision automatically tags transactions that we believe are Other Expenses.   Please note that while this may not be 100% accurate for you and your business, we recommend that you leave the default settings as is unless you are certain that these transactions are not Other Expenses'
                  "
                  >What are tags?</span
                >
              </b-dropdown-text>
            </b-dropdown-form>
          </b-dropdown>
        </div>
      </div>
    </b-tab>
    <div class="row">
      <div class="col-md-12 mt-5 pt-5">
        <p class="text-center text-white">
          Add a <strong>Bank Account, Credit Card </strong> or
          <strong>Paypal </strong> to see transactions
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    set: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    tagsLenght() {
      return this.chips.length;
    },
  },
  data() {
    return {
      chips: [
        "Shopify",
        "Facebook",
        "Zendrop",
        "Stripe",
        "Paypal",
        "Google",
        "Oberlo",
        "AliExpress",
      ],
      currentInput: "",
    };
  },
  methods: {
    saveChip(e) {
      e.preventDefault();
      const { chips, currentInput, set } = this;
      ((set && chips.indexOf(currentInput) === -1) || !set) &&
        chips.push(currentInput);
      this.currentInput = "";
    },
    deleteChip(index) {
      this.chips.splice(index, 1);
    },
    backspaceDelete({ which }) {
      which == 8 &&
        this.currentInput === "" &&
        this.chips.splice(this.chips.length - 1);
    },
  },
};
</script>

<style lang="scss">
.app-search .form-control {
  background: #32384c;
}
.app-search span {
  line-height: 48px;
}
.chip-container {
  width: 450px;
  min-height: 34px;
  display: flex;
  flex-wrap: wrap;
  align-content: space-between;
  align-items: center;

  .chip {
    margin: 7px;
    background: #ffaa61;
    padding: 10px 15px;
    font-weight: bold;
    color: white;
    border-radius: 5px;
    display: flex;
    align-items: center;
    i {
      cursor: pointer;
      opacity: 0.56;
      margin-left: 8px;
    }
  }
}
.chip-input {
  width: 120px;
  //   height: 40px;
  margin-top: 15px !important;
  border: 2px dashed #989ba5;
  padding: 4px;
  border-radius: 10px;
  background-color: transparent;
}
</style>
