<script>
import simplebar from "simplebar-vue";
import { mapGetters, mapActions } from "vuex";

/**
 * Nav-bar Component
 */
export default {
    components: { simplebar },
    computed: {
        ...mapGetters(["currentChannel"])
    },
    methods: {
        ...mapActions(["toggleCurrentChannel"]),
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
        }
    }
};
</script>

<template>
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="px-2 d-none d-lg-block"></div>

                <!-- LOGO -->
                <div class="px-4">
                    <a href="/" class="logo" aria-label="Zenvision">
                        <span>
                            <img src="/images/logo.svg" alt height="22" />
                        </span>
                    </a>
                </div>
            </div>
            <div class="d-none d-md-block">
                <a
                    class="btn btn-link text-white"
                    href="/"
                    :class="{ inactive: currentChannel !== 'PA' }"
                    @click="toggleCurrentChannel('PA')"
                    aria-label="Profit Analysis"
                    >Profit Analysis</a
                >
                <a
                    class="btn btn-link text-white"
                    href="/mastersheet"
                    :class="{ inactive: currentChannel !== 'MS' }"
                    @click="toggleCurrentChannel('MS')"
                    aria-label="Mastersheet"
                    >Mastersheet</a
                >
            </div>
            <div class="d-flex align-items-center">
                <div class="dropdown hamburger d-md-none">
                    <button
                        class="btn btn-light dropdown-toggle"
                        type="button"
                        data-toggle="dropdown"
                    >
                        <div></div>
                        <div></div>
                        <div></div>
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <a
                                class="btn btn-link text-white"
                                href="/"
                                :class="{ inactive: currentChannel !== 'PA' }"
                                @click="toggleCurrentChannel('PA')"
                                aria-label="Profit Analysis"
                                >Profit Analysis</a
                            >
                        </li>
                        <li>
                            <a
                                class="btn btn-link text-white"
                                href="/mastersheet"
                                :class="{ inactive: currentChannel !== 'MS' }"
                                @click="toggleCurrentChannel('MS')"
                                aria-label="Mastersheet"
                                >Mastersheet</a
                            >
                        </li>
                    </ul>
                </div>

                <div class="d-flex">
                    <b-dropdown
                        right
                        variant="black"
                        toggle-class="header-item"
                    >
                        <template v-slot:button-content>
                            <div class="dropdown d-inline-block">
                                <button
                                    type="button"
                                    class="btn header-item noti-icon right-bar-toggle toggle-right"
                                    aria-label="settings"
                                >
                                    <i class="bx bx-cog toggle-right"></i>
                                </button>
                            </div>
                        </template>

                        <a
                            class="dropdown-item"
                            href="/profile"
                            aria-label="profile"
                        >
                            <i
                                class="bx bx-user font-size-16 align-middle mr-1 text-danger"
                            ></i>
                            Profile
                        </a>
                        <a
                            class="dropdown-item"
                            href="mailto:support@zenvision.io"
                            aria-label="support"
                        >
                            <i
                                class="bx bx-support font-size-16 align-middle mr-1 text-danger"
                            ></i>
                            Support
                        </a>
                        <a
                            class="dropdown-item"
                            href="/logout"
                            aria-label="logout"
                        >
                            <i
                                class="bx bx-power-off font-size-16 align-middle mr-1 text-danger"
                            ></i>
                            Logout
                        </a>
                    </b-dropdown>
                </div>
            </div>
        </div>
    </header>
</template>
<style scoped>
.inactive {
    opacity: 0.5;
}
.inactive:hover {
    opacity: 1;
}
.hamburger div {
    width: 20px;
    height: 1px;
    background-color: rgb(255, 255, 255);
    margin: 5px 0;
}

.header-item {
    height: 57px;
}
</style>
