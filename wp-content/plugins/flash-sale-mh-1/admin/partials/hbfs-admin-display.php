<?php
/**
 * Admin page HTML - HBWeb Flash Sale
 * Vue 2 + Quasar (CDN local) SPA - không cần build step.
 */
if ( ! defined( 'WPINC' ) ) die;
?>
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Material+Icons" rel="stylesheet">
<link href="<?php echo HBFS_URL; ?>admin/css/quasar.min.css" rel="stylesheet">
<link href="<?php echo HBFS_URL; ?>admin/css/animate.min.css" rel="stylesheet">
<link rel="stylesheet" href="<?php echo HBFS_URL; ?>admin/css/date2picker.css">
<link rel="stylesheet" href="<?php echo HBFS_URL; ?>admin/css/hbfs-admin.css?v=<?php echo filemtime( HBFS_PATH . 'admin/css/hbfs-admin.css' ); ?>">

<script src="<?php echo HBFS_URL; ?>admin/js/vue.min.js"></script>
<script src="<?php echo HBFS_URL; ?>admin/js/quasar.umd.min.js"></script>
<script src="<?php echo HBFS_URL; ?>admin/js/vue-router.js"></script>
<script src="<?php echo HBFS_URL; ?>admin/js/axios.js"></script>
<script src="<?php echo HBFS_URL; ?>admin/js/date2picker.js"></script>
<script src="<?php echo HBFS_URL; ?>admin/js/moment-with-locales.min.js"></script>
<script src="<?php echo HBFS_URL; ?>admin/js/Sortable.min.js"></script>
<script src="<?php echo HBFS_URL; ?>admin/js/vuedraggable.umd.min.js"></script>

<script>
window.quasarConfig = {
    notify:     {},
    loadingBar: { color: 'orange' }
};
window.HBFS_CONFIGS = {
    plugin_url: '<?php echo HBFS_URL; ?>',
    site_url:   '<?php echo get_site_url(); ?>',
    ajax_url:   '<?php echo admin_url('admin-ajax.php'); ?>',      // giữ lại cho fallback
    rest_url:   '<?php echo esc_url_raw( rest_url( 'hbfs/v1' ) ); ?>', // REST API (bypass Cloudflare)
    nonce:      '<?php echo wp_create_nonce('wp_rest'); ?>',       // wp_rest nonce cho REST API
    version:    '<?php echo HBFS_VERSION; ?>'
};
</script>

<!-- App Root -->
<div id="hbfs-app" class="wrap-hbfs">
    <div class="q-pa-md">

        <!-- Top Toolbar -->
        <q-toolbar class="hbfs-toolbar">
            <q-btn round dense icon="west" color="deep-orange" class="q-mr-sm"
                   to="/" v-show="$route.path !== '/'"></q-btn>
            <q-avatar class="hbfs-logo" v-show="$route.path === '/'">
                <img src="<?php echo HBFS_URL; ?>assets/images/lightning.svg">
            </q-avatar>
            <q-toolbar-title>{{ pageTitle }}</q-toolbar-title>
            <q-space></q-space>
            <q-tabs dense>
                <q-route-tab to="/"         exact label="Chiến dịch Flash Sale"></q-route-tab>
                <q-route-tab to="/settings" exact label="Cài đặt"></q-route-tab>
            </q-tabs>
        </q-toolbar>

    <!-- Page Content -->
        <div class="q-pa-md hbfs-page-content">
            <transition name="fade" mode="out-in">
                <router-view></router-view>
            </transition>
        </div>

    </div>
</div>

<!-- Versioned module URLs - tự động bust cache khi file thay đổi -->
<script>
window.HBFS_URLS = {
    base:            '<?php echo HBFS_URL; ?>admin/partials/',
    productSearch:   '<?php echo HBFS_URL; ?>admin/partials/components/product-search.js?v=<?php echo filemtime( HBFS_PATH . 'admin/partials/components/product-search.js' ); ?>',
    setPriceDialog:  '<?php echo HBFS_URL; ?>admin/partials/components/campaign/set-price-dialog.js?v=<?php echo filemtime( HBFS_PATH . 'admin/partials/components/campaign/set-price-dialog.js' ); ?>',
    apiIndex:        '<?php echo HBFS_URL; ?>admin/partials/api/index.js?v=<?php echo filemtime( HBFS_PATH . 'admin/partials/api/index.js' ); ?>',
    apiCampaign:     '<?php echo HBFS_URL; ?>admin/partials/api/campaign.js?v=<?php echo filemtime( HBFS_PATH . 'admin/partials/api/campaign.js' ); ?>',
    apiProduct:      '<?php echo HBFS_URL; ?>admin/partials/api/product.js?v=<?php echo filemtime( HBFS_PATH . 'admin/partials/api/product.js' ); ?>',
    constants:       '<?php echo HBFS_URL; ?>admin/partials/constants/constants.js?v=<?php echo filemtime( HBFS_PATH . 'admin/partials/constants/constants.js' ); ?>',
    loading:         '<?php echo HBFS_URL; ?>admin/partials/components/loading.js?v=<?php echo filemtime( HBFS_PATH . 'admin/partials/components/loading.js' ); ?>',
};
</script>

<script type="module">
const _u = window.HBFS_URLS || {};
const base = '<?php echo HBFS_URL; ?>admin/partials/';

// Lazy async components — mỗi trang chỉ load khi navigate đến
const indexPage       = () => import('<?php echo HBFS_URL; ?>admin/partials/pages/index.js?v=<?php echo filemtime( HBFS_PATH . "admin/partials/pages/index.js" ); ?>');
const settingsPage    = () => import('<?php echo HBFS_URL; ?>admin/partials/pages/settings.js?v=<?php echo filemtime( HBFS_PATH . "admin/partials/pages/settings.js" ); ?>');
const campaignAddPage = () => import('<?php echo HBFS_URL; ?>admin/partials/pages/campaign-add.js?v=<?php echo filemtime( HBFS_PATH . "admin/partials/pages/campaign-add.js" ); ?>');
const emptyComponent  = () => import('<?php echo HBFS_URL; ?>admin/partials/components/data-empty.js?v=<?php echo filemtime( HBFS_PATH . "admin/partials/components/data-empty.js" ); ?>');

const EventBus = new Vue();

const router = new VueRouter({
    routes: [
        { path: '/',                    component: indexPage       },
        { path: '/settings',            component: settingsPage    },
        { path: '/campaign/add',        component: campaignAddPage },
        { path: '/campaign/edit/:id',   component: campaignAddPage },
    ]
});

// Global mixins
Vue.mixin({
    methods: {
        addCommas(n) {
            n += '';
            return n.replace(/(\d)(?=(\d{3})+$)/g, '$1,');
        },
        formatDateMoment(d, fmt = 'HH:mm DD/MM/YYYY') {
            return moment(d, 'YYYY-MM-DD HH:mm:ss').format(fmt);
        },
        jsonToFormData(data) {
            const fd = new FormData();
            const build = (fd, data, pk) => {
                if (data && typeof data === 'object' && !(data instanceof File)) {
                    Object.keys(data).forEach(k => build(fd, data[k], pk ? `${pk}[${k}]` : k));
                } else {
                    fd.append(pk, data == null ? '' : data);
                }
            };
            build(fd, data, null);
            return fd;
        },
        NOTIFY(msg, type = 1) {
            this.$q.notify({ message: msg, progress: true,
                type: type == 1 ? 'positive' : 'negative',
                color: type == 1 ? 'green' : 'red',
                position: 'top', timeout: 2500 });
        },
        CONFIRM(text) {
            return new Promise(res => {
                this.$q.dialog({ title: 'Xác nhận', message: text, cancel: true, persistent: true })
                    .onOk(() => res(true)).onCancel(() => res(false));
            });
        },
        async getSettings() {
            if (window.__hbfs_settings) return window.__hbfs_settings;
            const fd = this.jsonToFormData({ action: 'hbfs_get_option', key: 'hbfs_settings', nonce: HBFS_CONFIGS.nonce });
            const res = await axios.post(HBFS_CONFIGS.ajax_url, fd);
            window.__hbfs_settings = res.data.data || {};
            return window.__hbfs_settings;
        }
    }
});

Vue.use(DatePicker);
Vue.component('empty-component', emptyComponent);
Vue.prototype.$eventBus = EventBus;

new Vue({
    el: '#hbfs-app',
    router,
    data() { return { pageTitle: 'HBWeb Flash Sale' }; },
    methods: {
        setTitle(t) { this.pageTitle = t; }
    },
    created() {
        EventBus.$on('set.page_title', this.setTitle);
    }
});

// Fix height
const setVH = () => {
    const vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
    const el = document.querySelector('.wrap-hbfs');
    if (el) el.style.minHeight = `${vh - 120}px`;
};
window.onresize = setVH;
setVH();
document.title = 'HBWeb Flash Sale';
document.body.classList.add('hbfs-admin-app');
</script>
