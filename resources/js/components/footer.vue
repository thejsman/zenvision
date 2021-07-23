<template>
    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    {{ new Date().getFullYear() }} © Zenvision.
                </div>
                <div class="col-sm-6">
                    <div class="d-flex justify-content-end text-white">
                        <a
                            href="https://www.zenvision.io/terms-and-conditions/"
                            target="_blank"
                            rel="noopener"
                            class="text-white footer-link"
                            >Terms & Conditions</a
                        >
                        <a
                            href="https://www.zenvision.io/privacy-policy/"
                            target="_blank"
                            rel="noopener"
                            class="ml-3 text-white footer-link"
                            >Privacy Policy</a
                        >
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end footer -->
</template>
<style>
.footer-link {
    opacity: 0.5;
}
.footer-link:hover {
    opacity: 1;
}
</style>
