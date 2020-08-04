<script>
export default {
  mounted: function () {
      // this.topbarLight();
  },
  methods: {
    /**
     * Menu clicked show the submenu
     */
    onMenuClick(event) {
      event.preventDefault();
      const nextEl = event.target.nextSibling;
      if (nextEl && !nextEl.classList.contains("show")) {
        const parentEl = event.target.parentNode;
        if (parentEl) {
          parentEl.classList.remove("show");
        }
        nextEl.classList.add("show");
      } else if (nextEl) {
        nextEl.classList.remove("show");
      }
      return false;
    },
    topbarLight() {
      document.body.setAttribute("data-topbar", "light");
      document.body.removeAttribute("data-layout-size", "boxed");
    },
    boxedWidth() {
      document.body.setAttribute("data-layout-size", "boxed");
      document.body.removeAttribute("data-topbar", "light");
      document.body.setAttribute("data-topbar", "dark");
    },
    coloredHeader() {
      document.body.setAttribute("data-topbar", "colored");
      document.body.removeAttribute("data-layout-size", "boxed");
    }
  }
};
</script>
<template>
  <div class="topnav">
    <div class="container-fluid">
      <nav class="navbar navbar-light navbar-expand-lg topnav-menu">
        <div id="topnav-menu-content" class="collapse navbar-collapse">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" href="/">
                <i class="bx bx-home-circle mr-2"></i>Dashboard
              </a>
            </li>

            <li class="nav-item dropdown active">
              <a
                class="nav-link dropdown-toggle arrow-none"
                href="#"
                id="topnav-layout"
                role="button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                @click="onMenuClick"
              >
                <i class="bx bx-layout mr-2"></i>Layouts
                <div class="arrow-down"></div>
              </a>
              <div class="dropdown-menu dropdown-menu-right active" aria-labelledby="topnav-layout">
                <a class="dropdown-item active" href="javascript:void(0);">Horizontal</a>
                <a
                  class="dropdown-item"
                  href="javascript:void(0);"
                  @click="() => $root.changeLayout('vertical')"
                >Vertical</a>
                <a
                  @click="topbarLight"
                  href="javascript: void(0);"
                  class="dropdown-item"
                >Light Topbar</a>
                <a @click="boxedWidth" href="javascript: void(0);" class="dropdown-item">Boxed</a>
                <a
                  @click="coloredHeader"
                  href="javascript: void(0);"
                  class="dropdown-item"
                >Colored Header</a>
              </div>
            </li>
          </ul>
        </div>
      </nav>
    </div>
  </div>
</template>
