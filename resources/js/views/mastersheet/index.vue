<script>
import Layout from "../../layouts/main";
import ChannelDropdown from "../../components/custom-components/channel-dropdown";
import Sidepanel from "./sidepanel";
import Mainpanel from "./mainpanel";

export default {
  components: {
    Layout,
    ChannelDropdown,
    Sidepanel,
    Mainpanel,
  },
};
</script>
<template>
  <Layout>
    <div class="row">
      <!-- Right Sidebar -->
      <div class="col-12 my-2">
        <ChannelDropdown />
      </div>
      <div class="col-3 mt-4">
        <Sidepanel />
      </div>
      <div class="col-9 mt-4">
        <Mainpanel />
      </div>
    </div>
  </Layout>
</template>
<style>
.mastersheet-left {
  background-color: #191e2c;
  opacity: 0.7;
}
.block {
  width: 200px;
  height: 200px;
  background-color: #2f3863;
}
</style>
