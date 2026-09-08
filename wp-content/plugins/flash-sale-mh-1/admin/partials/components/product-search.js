const template = `
<div class="auto-complete-search" v-click-outside-app="defocusApp">
    <div class="row flex justify-between items-center q-col-gutter-sm">
        <div style="flex:1">
            <q-input filled :loading="isLoading" v-model="filters.search"
                label="🔍 Gõ tên sản phẩm để tìm kiếm" debounce="600"
                @focus="showForm = true" clearable>
            </q-input>
        </div>
        <div>
            <q-btn round color="deep-orange" icon="add" size="sm" @click="addProductById"
                title="Thêm theo ID hoặc SKU"/>
        </div>
    </div>

    <div class="search-result scroll-bar-thin-5" v-show="showForm" ref="list">
        <table class="full-width" v-if="products.length > 0">
            <thead style="background:#f5f5f5">
                <th class="text-left q-pa-sm">Ảnh</th>
                <th class="text-left q-pa-sm">Tên sản phẩm</th>
                <th class="text-left q-pa-sm">Mã SP</th>
                <th class="text-left q-pa-sm">Giá thường</th>
                <th class="text-left q-pa-sm">Giá KM</th>
                <th class="text-left q-pa-sm">#</th>
            </thead>
            <tbody>
                <tr v-for="product in products" :key="product.product_id"
                    :style="product.campaigns_using && product.campaigns_using.length
                        ? 'border-bottom:1px solid #f0f0f0; opacity:0.55; background:#fff8f5;'
                        : 'border-bottom:1px solid #f0f0f0'">
                    <td class="q-pa-sm">
                        <q-img :src="product.image" :ratio="1" style="width:44px;border-radius:4px"/>
                    </td>
                    <td class="q-pa-sm">
                        <div>{{ product.name }}</div>
                        <div v-if="product.campaigns_using && product.campaigns_using.length" style="margin-top:3px; display:flex; flex-wrap:wrap; gap:3px;">
                            <q-chip
                                v-for="c in product.campaigns_using"
                                :key="c.id"
                                dense color="orange-2" text-color="orange-9"
                                icon="campaign"
                                style="font-size:10px; height:18px;"
                                :title="'Chiến dịch: ' + c.name + ' (' + c.time_begin.slice(0,10) + ' → ' + c.time_end.slice(0,10) + ')'">
                                {{ c.name }}
                            </q-chip>
                        </div>
                    </td>
                    <td class="q-pa-sm text-grey-7">{{ product.sku }}</td>
                    <td class="q-pa-sm">{{ addCommas(product.regular_price) }}</td>
                    <td class="q-pa-sm">
                        <template v-if="product.sale_price">{{ addCommas(product.sale_price) }}</template>
                    </td>
                    <td class="q-pa-sm">
                        <q-btn round
                            :color="product.campaigns_using && product.campaigns_using.length ? 'grey-5' : 'deep-orange'"
                            icon="add" size="xs"
                            :title="product.campaigns_using && product.campaigns_using.length
                                ? 'Sản phẩm đang dùng trong ' + product.campaigns_using.length + ' chiến dịch khác'
                                : 'Thêm vào chiến dịch'"
                            @click="$emit('addProduct', product)"/>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="text-center q-pa-md text-grey-6" v-else>
            <span v-if="filters.search">Không tìm thấy kết quả cho "{{ filters.search }}"</span>
            <span v-else>Gõ từ khoá để tìm kiếm sản phẩm</span>
        </p>
    </div>
</div>
`;

import { searchProducts, getProductById } from '../api/product.js'

export default {
    props: ['addProduct'],

    data: () => ({
        filters:  { search: '' },
        products: [],
        showForm: false,
        isLoading: false,
    }),

    methods: {
        async getData() {
            if (!this.filters.search || this.filters.search.length < 2) return;
            this.isLoading = true;
            try {
                const res = await searchProducts(this.filters.search);
                this.products = (res.data && res.data.data) ? res.data.data : [];
                this.showForm = true;
            } finally {
                this.isLoading = false;
            }
        },

        async addProductById() {
            const id = await this.PROMPT('Thêm sản phẩm', 'Nhập ID hoặc Mã SKU sản phẩm');
            if (!id) return;
            this.$q.loading.show();
            const res = await getProductById(id);
            this.$q.loading.hide();
            // ok() PHP: {success, data: {msg, data: productData}}
            const { success, data: wrapper } = res.data;
            const msg     = wrapper ? wrapper.msg  : '';
            const product = wrapper ? wrapper.data : null;
            if (success && product)
                this.$emit('addProduct', product);
            else
                this.NOTIFY(msg || 'Không tìm thấy sản phẩm', 0);
        },

        defocusApp() {
            this.showForm = false;
        },
    },

    template,

    directives: {
        'click-outside-app': {
            bind(el, binding) {
                const handler = event => {
                    if (!el.contains(event.target) && el !== event.target)
                        binding.value(event);
                };
                el.__hbfsClickOutside__ = handler;
                document.addEventListener('click', handler);
            },
            unbind(el) {
                document.removeEventListener('click', el.__hbfsClickOutside__);
            }
        }
    },

    watch: {
        'filters.search'(val) {
            if (!val) {
                this.products = [];
                this.showForm = false;
            } else {
                this.getData();
            }
        }
    },
}