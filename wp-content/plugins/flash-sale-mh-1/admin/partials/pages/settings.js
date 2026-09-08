const template = `
<div class="q-mt-lg">
    <div class="row q-col-gutter-md">
        <div class="col-12 col-md-6">
            <div class="hbfs-card">
                <div class="text-subtitle1 text-weight-bold q-mb-md">⚙️ Cài đặt chung</div>

                <q-toggle v-model="settings.flash_sale_bar" color="deep-orange"
                    label="Hiển thị thanh Flash Sale trong trang chi tiết sản phẩm" class="q-mb-md"/>

                <div v-if="settings.flash_sale_bar" class="q-mb-md q-pl-md">
                    <p class="text-caption text-grey-7">Vị trí hook (0 = trước nút thêm vào giỏ hàng)</p>
                    <q-slider v-model="settings.flash_sale_bar_position" color="deep-orange"
                        :min="0" :max="50" :step="5" label label-always/>
                </div>

                <q-toggle v-model="settings.flash_sale_cart_badge" color="deep-orange"
                    label="Hiển thị badge Flash Sale trong giỏ hàng" class="q-mb-md"/>

                <q-toggle v-model="settings.include_assets" color="deep-orange"
                    label="Load CSS/JS trên toàn bộ trang (tắt nếu muốn load thủ công)" class="q-mb-lg"/>

                <q-btn unelevated color="deep-orange" icon="save" label="Lưu cài đặt" @click="save" class="q-mr-sm"/>
                <q-btn flat color="negative" icon="delete_forever" label="Xóa toàn bộ dữ liệu" @click="resetAllData"/>
            </div>

            <div class="hbfs-card q-mt-md">
                <div class="text-subtitle1 text-weight-bold q-mb-md">🎁 Khuyến mãi & Upbill</div>
                
                <q-toggle v-model="settings.enable_promotion" color="pink"
                    label="Bật tính năng Khuyến mãi & Upbill" class="q-mb-md"/>
                
                <div v-if="settings.enable_promotion" class="q-pl-md">
                    <div class="text-subtitle2 q-mb-sm">Giảm giá mua nhiều (Volume Discount)</div>
                    <div v-for="(tier, index) in settings.promo_tiers" :key="index" class="row q-col-gutter-sm q-mb-sm items-center">
                        <div class="col-3">Mua từ</div>
                        <div class="col-3"><q-input filled dense type="number" v-model="tier.qty" /></div>
                        <div class="col-3">sản phẩm giảm</div>
                        <div class="col-2"><q-input filled dense type="number" v-model="tier.discount" suffix="%" /></div>
                        <div class="col-1">
                            <q-btn round dense color="negative" icon="remove" size="sm" @click="settings.promo_tiers.splice(index, 1)" />
                        </div>
                    </div>
                    <div class="q-mb-md">
                        <q-btn outline color="primary" icon="add" size="sm" label="Thêm mức giảm giá" @click="settings.promo_tiers.push({qty: 0, discount: 0})" />
                    </div>

                    <div class="text-subtitle2 q-mb-sm">Tự động tặng quà</div>
                    <div class="row q-col-gutter-sm q-mb-md items-center">
                        <div class="col-3">Tặng quà khi mua từ</div>
                        <div class="col-3"><q-input filled dense type="number" v-model="settings.gift_qty" /></div>
                        <div class="col-6">sản phẩm</div>
                    </div>
                    <div class="q-mb-md">
                        <q-input filled v-model="settings.gift_product_id" label="ID Sản phẩm quà tặng" hint="Nhập ID sản phẩm sẽ được tự động thêm vào giỏ với giá 0đ khi đạt đủ số lượng" />
                    </div>

                    <div class="text-subtitle2 q-mb-sm">Thông báo Upbill (Giỏ hàng & Thanh toán)</div>
                    <q-input filled type="textarea" rows="2" v-model="settings.upbill_message" label="Nội dung nhắc nhở" hint="Bạn có thể dùng {missing} để hiển thị số lượng còn thiếu và {next_discount} để hiển thị % giảm giá của mốc tiếp theo. Ví dụ: Mua thêm {missing} sản phẩm nữa để được giảm {next_discount} và nhận quà!" class="q-mb-md"/>
                    
                    <q-btn unelevated color="deep-orange" icon="save" label="Lưu cài đặt" @click="save" class="q-mt-sm"/>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="hbfs-card">
                <div class="text-subtitle1 text-weight-bold q-mb-md">📋 Shortcodes</div>
                <q-list dense>
                    <q-item v-for="sc in shortcodes" :key="sc.code" class="q-mb-sm">
                        <q-item-section>
                            <q-btn flat dense color="deep-orange" align="left"
                                @click="copyShortcode(sc.code)">
                                <code>{{ sc.code }}</code>
                            </q-btn>
                            <div class="text-caption text-grey-7 q-ml-sm">{{ sc.desc }}</div>
                        </q-item-section>
                    </q-item>
                </q-list>
            </div>

            <div class="hbfs-card q-mt-md">
                <div class="text-subtitle1 text-weight-bold q-mb-md">ℹ️ Thông tin Plugin</div>
                <q-list dense>
                    <q-item>
                        <q-item-section avatar><q-icon name="bolt" color="deep-orange"/></q-item-section>
                        <q-item-section>HBWeb Flash Sale</q-item-section>
                    </q-item>
                    <q-item>
                        <q-item-section avatar><q-icon name="tag" color="grey"/></q-item-section>
                        <q-item-section>Phiên bản: 1.0.0</q-item-section>
                    </q-item>
                    <q-item>
                        <q-item-section avatar><q-icon name="link" color="grey"/></q-item-section>
                        <q-item-section>
                            <a href="https://hbweb.vn" target="_blank" style="color:inherit">hbweb.vn</a>
                        </q-item-section>
                    </q-item>
                </q-list>
            </div>
        </div>
    </div>
</div>
`;

export default {
    data: () => ({
        isLoading: false,
        settings: {
            flash_sale_bar:          true,
            flash_sale_bar_position: 5,
            flash_sale_cart_badge:   true,
            include_assets:          true,
            enable_promotion:        false,
            promo_tiers:             [ {qty: 2, discount: 10}, {qty: 3, discount: 15} ],
            gift_qty:                3,
            gift_product_id:         '',
            upbill_message:          'Mua thêm {missing} sản phẩm nữa để nhận thêm ưu đãi và quà tặng hấp dẫn!'
        },
        shortcodes: [
            { code: '[hbfs_slider id="1"]',  desc: 'Hiển thị Slider Flash Sale ở bất kỳ đâu (thay 1 bằng ID chiến dịch)' },
        ],
    }),

    methods: {
        save() {
            this.$q.loading.show();
            axios.post(HBFS_CONFIGS.ajax_url, this.jsonToFormData({
                action: 'hbfs_save_option',
                key:    'hbfs_settings',
                data:   this.settings,
                nonce:  HBFS_CONFIGS.nonce,
            })).then(res => {
                this.$q.loading.hide();
                const { success, data } = res.data;
                this.NOTIFY(data ? data.msg : (success ? 'Lưu thành công' : 'Lỗi'), success ? 1 : 0);
                if (success) window.__hbfs_settings = Object.assign({}, this.settings);
            });
        },

        resetAllData() {
            this.$q.dialog({
                title:   'Xóa toàn bộ dữ liệu',
                message: 'Hành động này không thể hoàn tác. Gõ "hbweb" để xác nhận.',
                prompt:  { model: '', type: 'text' },
                cancel:  true,
                persistent: true,
            }).onOk(input => {
                if (input.trim() !== 'hbweb') {
                    this.NOTIFY('Sai mã xác nhận', 0);
                    return;
                }
                this.$q.loading.show();
                axios.post(HBFS_CONFIGS.ajax_url, this.jsonToFormData({
                    action: 'hbfs_reset_data',
                    nonce:  HBFS_CONFIGS.nonce,
                })).then(res => {
                    this.$q.loading.hide();
                    const { success, data } = res.data;
                    this.NOTIFY(data ? data.msg : 'Đã xóa', success ? 1 : 0);
                });
            });
        },

        copyShortcode(sc) {
            Quasar.utils.copyToClipboard(sc);
            this.NOTIFY('Đã copy shortcode!');
        },
    },

    template,

    created() {
        this.$eventBus.$emit('set.page_title', 'HBWeb Flash Sale - Cài đặt');
        this.getSettings().then(data => {
            if (data) this.settings = Object.assign({}, this.settings, data);
        });
    },
}