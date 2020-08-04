
<script>
import simplebar from "simplebar-vue";

/**
 * Nav-bar Component
 */
export default {
  components: { simplebar },
  methods: {
    toggleMenu() {
      this.$parent.toggleMenu();
    },
    toggleRightSidebar() {
      this.$parent.toggleRightSidebar();
    },
    initFullScreen() {
      document.body.classList.toggle("fullscreen-enable");
      if (
        !document.fullscreenElement &&
        /* alternative standard method */ !document.mozFullScreenElement &&
        !document.webkitFullscreenElement
      ) {
        // current working methods
        if (document.documentElement.requestFullscreen) {
          document.documentElement.requestFullscreen();
        } else if (document.documentElement.mozRequestFullScreen) {
          document.documentElement.mozRequestFullScreen();
        } else if (document.documentElement.webkitRequestFullscreen) {
          document.documentElement.webkitRequestFullscreen(
            Element.ALLOW_KEYBOARD_INPUT
          );
        }
      } else {
        if (document.cancelFullScreen) {
          document.cancelFullScreen();
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen();
        } else if (document.webkitCancelFullScreen) {
          document.webkitCancelFullScreen();
        }
      }
    },
  },
};
</script>

<template>
  <header id="page-topbar">
    <div class="navbar-header">
      <div class="d-flex">
        <button
          id="vertical-menu-btn"
          type="button"
          class="btn btn-sm px-4 font-size-16 header-item"
          @click="toggleMenu"
        >
          <i class="fa fa-fw fa-bars"></i>
        </button>
        <!-- LOGO -->
        <div class="px-4">
          <a href="/" class="logo">
            <span>
              <img src="/images/logo.svg" alt height="22" />
            </span>
          </a>
        </div>
      </div>
      <div class="d-flex">
        <b-dropdown right variant="black" toggle-class="header-item">
          <template v-slot:button-content>
            <div class="dropdown d-inline-block">
              <button type="button" class="btn header-item noti-icon right-bar-toggle toggle-right">
                <i class="bx bx-cog toggle-right"></i>
              </button>
            </div>
          </template>
          <a class="dropdown-item text-danger" href="/logout">
            <i class="bx bx-power-off font-size-16 align-middle mr-1 text-danger"></i> Logout
          </a>
        </b-dropdown>
      </div>
    </div>
  </header>
</template>
