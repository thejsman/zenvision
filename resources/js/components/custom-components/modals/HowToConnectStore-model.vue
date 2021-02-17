<template>
  <div class="row">
    <div class="m-4 d-flex flex-column">
      <div class="font-weight-bold font-size-14 text-white">
        <h3>How to connect my Shopify store?</h3>
      </div>
      <p class="mt-4 mb-4 text-white">
        In order to connect your store, go to your
        <strong>Shopify Dashboard</strong> and copy the URL at the top of your
        screen. After pasting the URL back into Zenvision, please make sure
        anything after .com has been removed (i.e. /admin, /products, etc…)
      </p>
      <img
        src="/images/howToConnectStore.jpg"
        alt="How to connect my Shopify store"
        class="img-fluid"
      />
      <div
        class="d-flex mt-4 justify-content-between align-items-center text-white"
      >
        <div>
          Still unable to connect?
          <a href="mailto:support@zenvision.io" class="text-white underlined"
            >Contact our support</a
          >
        </div>
        <div>
          <b-button
            variant="cancel"
            class="btn btn-cancel"
            @click="$emit('handle-close')"
            >Ok</b-button
          >
        </div>
      </div>
    </div>
  </div>
</template>
