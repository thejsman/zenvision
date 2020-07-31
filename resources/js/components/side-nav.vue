<script>
import MetisMenu from "metismenujs/dist/metismenujs";

export default {
  components: {},
  mounted: function() {
    const sidebarLayout = localStorage.getItem("sidebar-layout");
    switch (sidebarLayout) {
      case "light-sidebar":
        this.lightSidebar();
        break;
      case "compact-sidebar":
        this.compactSidebar();
        break;
      case "icon-sidebar":
        this.iconSidebar();
        break;
      case "colored-sidebar":
        this.coloredSidebar();
        break;
      case "boxed-layout":
        this.boxedLayout();
        break;
      default:
        document.body.setAttribute(this.iconSidebar(), "dark");
        break;
    }
    // eslint-disable-next-line no-unused-vars
    var menuRef = new MetisMenu("#side-menu");
    var links = document.getElementsByClassName("side-nav-link-ref");
    var matchingMenuItem = null;
    for (var i = 0; i < links.length; i++) {
      if (window.location.pathname === links[i].pathname) {
        matchingMenuItem = links[i];
        break;
      }
    }

    if (matchingMenuItem) {
      matchingMenuItem.classList.add("active");
      var parent = matchingMenuItem.parentElement;

      /**
       * TODO: This is hard coded way of expading/activating parent menu dropdown and working till level 3.
       * We should come up with non hard coded approach
       */
      if (parent) {
        parent.classList.add("mm-active");
        const parent2 = parent.parentElement.closest("ul");
        if (parent2 && parent2.id !== "side-menu") {
          parent2.classList.add("mm-show");

          const parent3 = parent2.parentElement;
          if (parent3) {
            parent3.classList.add("mm-active");
            var childAnchor = parent3.querySelector(".has-arrow");
            var childDropdown = parent3.querySelector(".has-dropdown");
            if (childAnchor) childAnchor.classList.add("mm-active");
            if (childDropdown) childDropdown.classList.add("mm-active");

            const parent4 = parent3.parentElement;
            if (parent4) parent4.classList.add("mm-show");
            const parent5 = parent4.parentElement;
            if (parent5) parent5.classList.add("mm-active");
          }
        }
      }
    }
  },
  methods: {
    defaultSidebar() {
      localStorage.setItem("sidebar-layout", "default-sidebar");
      document.body.setAttribute("data-sidebar", "dark");
      document.body.removeAttribute("data-layout-size", "boxed");
      document.body.removeAttribute("data-sidebar-size", "small");
      document.body.removeAttribute("data-topbar", "small");
      document.body.classList.remove("vertical-collpsed");
    },
    lightSidebar() {
      localStorage.setItem("sidebar-layout", "light-sidebar");
      document.body.setAttribute("data-topbar", "dark");
      document.body.removeAttribute("data-sidebar");
      document.body.removeAttribute("data-layout-size", "boxed");
      document.body.removeAttribute("data-sidebar-size", "small");
      document.body.classList.remove("vertical-collpsed");
    },
    compactSidebar() {
      localStorage.setItem("sidebar-layout", "compact-sidebar");
      document.body.setAttribute("data-sidebar-size", "small");
      document.body.setAttribute("data-sidebar", "dark");
      document.body.removeAttribute("data-layout-size", "boxed");
      document.body.classList.remove("vertical-collpsed");
      document.body.removeAttribute("data-topbar", "dark");
    },
    iconSidebar() {
      localStorage.setItem("sidebar-layout", "icon-sidebar");
      document.body.setAttribute("data-keep-enlarged", "true");
      document.body.classList.add("vertical-collpsed");
      document.body.setAttribute("data-sidebar", "dark");
      document.body.removeAttribute("data-topbar", "dark");
      document.body.removeAttribute("data-layout-size", "boxed");
    },
    boxedLayout() {
      localStorage.setItem("sidebar-layout", "boxed-layout");
      document.body.setAttribute("data-keep-enlarged", "true");
      document.body.classList.add("vertical-collpsed");
      document.body.setAttribute("data-layout-size", "boxed");
      document.body.removeAttribute("data-sidebar", "colored");
      document.body.setAttribute("data-sidebar", "dark");
      document.body.removeAttribute("data-topbar", "dark");
    },
    coloredSidebar() {
      localStorage.setItem("sidebar-layout", "colored-sidebar");
      document.body.setAttribute("data-sidebar", "colored");
      document.body.removeAttribute("data-layout-size", "boxed");
      document.body.removeAttribute("data-sidebar-size", "small");
      document.body.classList.remove("vertical-collpsed");
    }
  }
};
</script>

<template>
  <!-- ========== Left Sidebar Start ========== -->

  <!--- Sidemenu -->
  <div id="sidebar-menu">
    <!-- Left Menu Start -->
    <ul id="side-menu" class="metismenu list-unstyled">
      <li class="menu-title">Menu</li>

      <li>
        <a href="/" class="side-nav-link-ref">
          <i class="bx bx-home-circle"></i>
          <span>Dashboard</span>
        </a>
      </li>
    </ul>
  </div>
  <!-- Sidebar -->
</template>
