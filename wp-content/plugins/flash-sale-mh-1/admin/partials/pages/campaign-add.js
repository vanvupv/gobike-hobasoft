const template = `
<div class="q-mt-lg">
    <div v-if="!isLoading">
    
    <!-- {{record}} -->
        <div class="row q-col-gutter-md q-mt-sm">
            <div class="col-4">

                <q-input  filled v-model="record.name"  label="Tên chiến dịch" class="q-mb-md"/>

                <q-input  filled type="textarea" v-model="record.data_json.admin_note" rows="2" label="Ghi chú chiến dịch (Chỉ xem trong admin)" class="q-mb-md"/>

                <div class="q-mb-md">
                    <label class="text-subtitle2 text-weight-bold block q-mb-xs">Các khoảng thời gian diễn ra chiến dịch:</label>
                    <div v-for="(slot, index) in record.data_json.time_slots" :key="index" class="q-mb-sm q-pa-sm" style="border:1px solid #e0e0e0; border-radius:6px; background:#fafafa;">
                        <div class="row q-col-gutter-sm items-center">
                            <div class="col-10">
                                <div class="text-caption text-weight-bold text-deep-orange q-mb-xs">Đợt {{ index + 1 }}</div>
                                <date-picker v-model="slot.time" type="datetime" range placeholder="Chọn ngày giờ" confirm format="DD-MM-YYYY HH:mm:ss" valueType="YYYY-MM-DD HH:mm:ss" :lang="lang"></date-picker>
                            </div>
                            <div class="col-2 text-right">
                                <q-btn round dense color="accent" icon="remove" size="sm" @click="removeTimeSlot(index)" v-if="record.data_json.time_slots.length > 1"></q-btn>
                            </div>
                        </div>
                    </div>
                    <q-btn outline color="primary" icon="add" size="sm" label="Thêm đợt thời gian" @click="addTimeSlot" class="q-mt-sm"></q-btn>
                </div>
                
                <div class="row q-col-gutter-md q-mb-md">
                    <q-input filled v-model="record.data_json.link_label" label="Button bên phải" class="col-6" />
                    <q-input filled v-model="record.data_json.link" label="Đường dẫn button" class="col-6" />
                    <q-input filled v-model="record.data_json.product_limit" label="Giới hạn sản phẩm hiển thị Slide" class="col-6" />
                    <q-input filled v-model="record.data_json.per_page" label="Số sản phẩm trên 1 trang tại trang Flash Sale" class="col-6" />
                </div>
                <div class="q-mb-md">
                    <q-toggle v-model="record.sold_increase" color="pink" icon="trending_up" label="Tự động tăng số lượng bán" />
                    <q-toggle v-model="record.status" color="pink" icon="done" label="Active" />
                </div>
                 <q-btn color="pink" icon="add_task" label="Hoàn tất" @click="addCampaign"/>
                 <q-btn color="purple" icon="format_paint" label="Custom Style" @click="customCss = true"/>
                 <q-btn color="orange" icon="category" @click="isExpand = !isExpand" label="Sản phẩm"/>

            </div>
            
            <div class="col-8">
                <!-- Tabs chuyển đợt thời gian để quản lý sản phẩm -->
                <q-tabs v-model="activeSlotIndex" dense class="text-grey" active-color="deep-orange" indicator-color="deep-orange" align="left" narrow-indicator>
                    <q-tab v-for="(slot, sIdx) in record.data_json.time_slots" :key="sIdx" :name="sIdx" :label="'Đợt ' + (sIdx + 1) + (slot.time && slot.time[0] ? ' (' + formatSlotDate(slot.time[0]) + ')' : '')" />
                </q-tabs>
                <q-separator class="q-mb-md" />

                <q-input filled :loading="isLoading" label="🔍 Gõ tên sản phẩm để tìm kiếm & thêm vào Đợt này" debounce="800"
                    @click="isExpand = true"></q-input>
                <q-markup-table flat class="card-item q-mt-md input-pt-13" v-if="currentSlotProducts.length > 0">
                    <thead>
                        <tr>
                            <th class="text-left q-pa-md">Ảnh</th>
                            <th class="text-left q-pa-md">Tên sản phẩm</th>
                            <th class="text-left q-pa-md">Mã SP</th>
                           
                            <th class="text-left q-pa-md">Giá Flash sale</th>
                            <th class="text-left q-pa-md">Số lượng</th>
                            <th class="text-left q-pa-md">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in currentSlotProducts" :key="product.product_id">
                            <td class="text-left q-pa-md">
                                <q-img :src="product.image" :ratio="1" style="width:50px"></q-img>
                            </td>
                            <td class="text-left q-pa-md" v-html>{{product.name}}</td>
                            <td class="text-left q-pa-md"> {{product.sku}} </td>
                          
                            <td class="text-left q-pa-md">
                                <q-input filled dense v-model="product.flash_sale_price"  type="number"/>
                            </td>
                            <td class="text-left q-pa-md">
                                <q-input filled dense v-model="product.qty"  type="number"/>
                            </td>
                            <td class="text-left q-pa-md">
                                <q-btn round color="accent" icon="remove" size="sm" @click="removeProduct(product.product_id)"></q-btn>
                            </td>
                        </tr>
                        
                    </tbody>
                </q-markup-table>
                <div v-else class="text-center q-pa-lg text-grey">
                    Chưa có sản phẩm nào cho đợt này. Bấm vào ô tìm kiếm hoặc nút "Sản phẩm" để thêm.
                </div>
            </div>
            
        </div>
    </div>
    
    <q-dialog v-model="isExpand" persistent maximized transition-show="slide-up"
        transition-hide="slide-down">
        <q-card class="input-pt-13">
            <q-card-section class="row items-center" style="background:#FDA524; color:#fff">
                <div class="text-h6">Sản phẩm Flash Sale — Đợt {{ activeSlotIndex + 1 }}</div>
                <q-space />
                <q-btn icon="close" flat round dense v-close-popup />
            </q-card-section>

            <q-card-section class="">
                <!-- Tabs chuyển đợt trong popup -->
                <div class="row items-center q-mb-md">
                    <div class="text-subtitle1 text-weight-bold q-mr-md">Đang chọn sản phẩm cho:</div>
                    <q-tabs v-model="activeSlotIndex" dense class="text-grey" active-color="deep-orange" indicator-color="deep-orange" align="left">
                        <q-tab v-for="(slot, sIdx) in record.data_json.time_slots" :key="sIdx" :name="sIdx" :label="'Đợt ' + (sIdx + 1) + (slot.time && slot.time[0] ? ' (' + formatSlotDate(slot.time[0]) + ')' : '')" />
                    </q-tabs>
                </div>
                <ProductSearch @addProduct="addProduct" />
                <div class="card-item q-mt-md">
                    <div class="text-right">
                        <q-btn-dropdown icon="expand" flat dense class="set-price-btn"> 
                            <div class="row no-wrap q-pa-md">
                                <div class="column" style="min-width: 250px">
                                    <div class="text-subtitle q-mb-md">Thiết lập giảm giá</div>
                                    <q-btn-toggle  v-model="set_price.is_percent_sale" push spread dense class="price-toggle" no-caps
                                        toggle-color="primary" color="white" text-color="primary" :options="[
                                                                {label: '%', value: 'percent'},
                                                                {label: '$', value: 'price'},
                                                                {label: 'Đồng $', value: 'same_price'}
                                                    ]" />
                                    
                                    <q-input v-model="set_price.value" type="number">
                                </div>
                                <q-separator vertical inset class="q-mx-lg" />
                                <div class="column items-between" style="min-width: 190px">
                                    <q-btn class="q-mt-lg" color="primary" label="Áp dụng" push size="sm" v-close-popup @click="setPriceAll"/>
                                </div>
                            </div>
                        </q-btn-dropdown>

                    </div>
                    <div class="row">
                        <div class="col-1 q-pa-sm"  style="width:80px">Ảnh</div>
                        <div class="col-2 q-pa-sm">Tên sản phẩm</div>
                        <div class="col-1 q-pa-sm">Mã SP</div>
                        <div class="col-1 q-pa-sm">Giá thường</div>
                        <div class="col-1 q-pa-sm">Giá KM</div>
                        <div class="col-2 q-pa-sm">Giá Flash sale</div>
                        <div class="col-1 q-pa-sm">Đã bán</div>
                        <div class="col-1 q-pa-sm">Số lượng</div>
                        <div class="col-1 q-pa-sm">SL Thật</div>
                        <div class="col-1 q-pa-sm">#</div>
                    </div>
                    <draggable v-model="currentSlotProducts" v-bind="dragOptions" tag="div" handle=".handle" @start="drag = true"
                        @end="drag = false">
                        <transition-group type="transition" name="flip-list">
                            <div class="row items-center" v-for="product in currentSlotProducts" :key="product.product_id" style="border-bottom:1px dashed #ddd">
                                <div class="col-1 q-pa-sm" style="width:80px">
                                    <q-img :src="product.image" :ratio="1" style="width:50px"></q-img>
                                </div>
                                <div class="col-2 q-pa-sm">{{product.name}}</div>
                                <div class="col-1 q-pa-sm">{{product.sku}}</div>
                                <div class="col-1 q-pa-sm"><q-input filled dense v-model="product.regular_price" type="number" /></div>
                                <div class="col-1 q-pa-sm flex justify-between">
                                    <template v-if="product.sale_price && product.regular_price && product.children.length == 0">
                                        <span class="w-price">{{ addCommas(product.sale_price)}}</span>    
                                        <span> <q-badge color="red">-{{calcSalePercent(product)}}%</q-badge> </span> 

                                    </template>
                                </div>
                                <div class="col-2 q-pa-sm">
                                    <q-input filled dense v-model="product.flash_sale_price" type="number">
                                        <template v-slot:after>
                                            <SetPriceDialog :product="product" @setPrice="setPrice"/>
                                        </template>
                                    </q-input>
                                </div>
                                <div class="col-1 q-pa-sm"><q-input filled dense v-model="product.sold" type="number" /></div>
                                <div class="col-1 q-pa-sm"><q-input filled dense v-model="product.qty" type="number" /></div>
                                <div class="col-1 q-pa-sm">
                                    <q-checkbox v-model="product.real_sold" color="pink" />

                                </div>
                                <div class="col-1 q-pa-sm">
                                    <q-btn round color="pink" icon="pets" class="handle" size="sm"></q-btn>
                                    <q-btn round color="accent" icon="remove" size="sm" @click="removeProduct(product.product_id)"></q-btn>
                                </div>
                                <div class="col-12" v-if="product.children && product.children.length">
                                    <div class="row" v-for="child in product.children">
                                        <div class="col-1 q-pa-sm" style="width:80px">
                                        </div>
                                        <div class="col-2 q-pa-sm">
                                            <q-img :src="product.image" :ratio="1" style="width:50px"></q-img>
                                            {{child.name.replace(product.name, '')}}
                                        </div>
                                        <div class="col-1 q-pa-sm">{{child.sku}}</div>
                                        <div class="col-1 q-pa-sm">{{ addCommas(child.regular_price)}}</div>
                                        <div class="col-1 q-pa-sm flex justify-between">
                                            <template v-if="child.sale_price">
                                                <span class="w-price">{{ addCommas(child.sale_price)}} </span>
                                                <span> <q-badge color="red">-{{calcSalePercent(child)}}%</q-badge> </span>

                                            </template>
                                        </div>

                                        <div class="col-2 q-pa-sm">
                                            <q-input filled dense v-model="child.flash_sale_price" type="number">
                                                <!-- <template v-slot:after>
                                                    <SetPriceDialog :product/>
                                                </template> -->
                                            </q-input>
                                        </div>
                                        <div class="col-1 q-pa-sm">
                                            <q-input filled dense v-model="child.sold" type="number" />
                                        </div>
                                        <div class="col-1 q-pa-sm">
                                            <q-input filled dense v-model="child.qty" type="number" />
                                        </div>
                                        <div class="col-1 q-pa-sm">
                                            <q-checkbox v-model="child.real_sold" color="pink" />

                                        </div>
                                        <div class="col-1 q-pa-sm">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </transition-group>
                    </draggable>
                </div>  
              
            </q-card-section>
        </q-card>
    </q-dialog>
    <q-dialog v-model="customCss" position="bottom">
        <q-card style="width: 900px;max-width: 900px; border-radius: 0;" class="q-custom-style">
            <q-card-section class="row items-center text-white bg-purple">
                <div class="text-h6">Custom Css</div>
                <q-space />
                <q-btn icon="close" flat round dense v-close-popup />
            </q-card-section>
    
            <q-card-section class="">
                <div class="q-mb-md">    
                    <div class="flex items-center q-mb-sm">
                        Banner Flash Sale:
                        <q-space />
                        <q-toggle v-model="record.data_json.persistent_banner" color="purple" label="Giữ banner ngay cả khi sự kiện diễn ra" left-label/>
                    </div>
                    <img width="100%" :src="record.data_json.banner" v-if="record.data_json.banner" @click="uploadFrameImage('banner')"
                        class="cursor-pointer" />
                    <q-btn outline color="pink" label="Khung ảnh FS" icon="upload" v-else @click="uploadFrameImage('banner')" />
                    <q-btn outline color="pink"  icon="delete" v-if="record.data_json.banner" @click="record.data_json.banner = ''" />
                </div>

                <p>Tone màu (Header Slider): </p>
                <q-input filled v-model="color" class="my-input q-mb-md" :style="'color:' + color" label="Tông màu Header Slider">
                    <template v-slot:append>
                        <q-icon name="colorize" class="cursor-pointer" :style="'color:' + color">
                            <q-popup-proxy transition-show="scale" transition-hide="scale">
                                <q-color v-model="color" style="width:600px" format-model="rgb"/>
                            </q-popup-proxy>
                        </q-icon>
                    </template>
                </q-input>

                <p>Màu nền khung Flash Sale (trang sản phẩm đơn): <small class="text-grey">Để trống = dùng màu Header Slider</small></p>
                <q-input filled v-model="record.data_json.bar_bg_color" class="my-input q-mb-md" :style="record.data_json.bar_bg_color ? 'color:' + record.data_json.bar_bg_color : ''" label="Màu nền .hbfs-product-bar">
                    <template v-slot:append>
                        <q-icon name="colorize" class="cursor-pointer" :style="record.data_json.bar_bg_color ? 'color:' + record.data_json.bar_bg_color : ''">
                            <q-popup-proxy transition-show="scale" transition-hide="scale">
                                <q-color v-model="record.data_json.bar_bg_color" style="width:600px" format-model="rgb"/>
                            </q-popup-proxy>
                        </q-icon>
                        <q-btn flat round dense icon="clear" size="xs" v-if="record.data_json.bar_bg_color" @click="record.data_json.bar_bg_color = ''" />
                    </template>
                </q-input>

                <p>Màu nền khung danh sách sản phẩm (Slider / Teaser): <small class="text-grey">Mặc định: #160803</small></p>
                <q-input filled v-model="record.data_json.frame_bg_color" class="my-input q-mb-md" :style="record.data_json.frame_bg_color ? 'color:' + record.data_json.frame_bg_color : ''" label="Màu nền .hbfs-slider-box-frame (Mặc định: #160803)">
                    <template v-slot:append>
                        <q-icon name="colorize" class="cursor-pointer" :style="record.data_json.frame_bg_color ? 'color:' + record.data_json.frame_bg_color : ''">
                            <q-popup-proxy transition-show="scale" transition-hide="scale">
                                <q-color v-model="record.data_json.frame_bg_color" style="width:600px" format-model="rgb"/>
                            </q-popup-proxy>
                        </q-icon>
                        <q-btn flat round dense icon="clear" size="xs" v-if="record.data_json.frame_bg_color" @click="record.data_json.frame_bg_color = ''" />
                    </template>
                </q-input>
   

                

                <p>Số cột sản phẩm: </p>
                <q-list dense class="q-mb-md">
                    <q-item>
                        <q-item-section avatar>
                            <q-icon color="purple" name="dashboard" />
                        </q-item-section>
                        <q-item-section>
                            <q-slider  color="purple" v-model="record.data_json.show_col" :min="0" :max="8" :step="1" label label-always />
                        </q-item-section>
                    </q-item>
                </q-list>

                <div class="row q-col-gutter-md q-mb-md">
                    <div class="col-6">
                        <q-input
                            filled
                            type="number"
                            v-model.number="record.data_json.autoplay_interval"
                            label="⏱ Thời gian tự chạy slide (giây)"
                            :hint="'Hiện tại: ' + (record.data_json.autoplay_interval || 3) + 's — nhập 0 để tắt autoplay'"
                            min="0"
                            max="60"
                        />
                    </div>
                    <div class="col-6 flex items-center">
                        <q-toggle
                            v-model="record.data_json.autoplay_pause_hover"
                            color="purple"
                            icon="pause_circle"
                            label="Dừng khi hover chuột"
                        />
                    </div>
                </div>
                <div class="row q-col-gutter-md">
                    
                    <div class="col-3">
                        <p>Khung Flash Sale</p>
                        <div class="q-mb-md">
                            <img width="180px" :src="record.data_json.frame_image" v-if="record.data_json.frame_image" @click="uploadFrameImage('frame_image')"
                                class="cursor-pointer" />
                            <q-btn outline color="pink" label="Khung ảnh FS" icon="upload" v-else @click="uploadFrameImage('frame_image')" />
                            <q-btn outline color="pink"  icon="delete" v-if="record.data_json.frame_image" @click="record.data_json.frame_image = ''" />
                        </div>
                    </div>
                    <div class="col-9">
                        <p>Style CSS: (Class của Chiến dịch là: .hbfs-slider-wrap-{{$route.params.id}})</p>
                        <q-input v-model="record.data_json.css" filled type="textarea" rows="6" style="width:100%" />
                    </div>
                </div>
                
            </q-card-section>
        </q-card>
    </q-dialog>
    
</div>   
`;

// Dùng dynamic import với URL có version từ PHP → tránh browser cache cũ
const _urls = window.HBFS_URLS || {};

const _imports = await Promise.all([
    import(_urls.constants      || '../constants/constants.js'),
    import(_urls.productSearch  || '../components/product-search.js'),
    import(_urls.setPriceDialog || '../components/campaign/set-price-dialog.js'),
    import(_urls.apiCampaign    || '../api/campaign.js'),
]);
const { LANG_DATE_PICKER } = _imports[0];
const ProductSearch  = _imports[1].default;
const SetPriceDialog = _imports[2].default;
const { addCampaign, getCampaign } = _imports[3];

// HBWeb Flash Sale - hbfs_ prefix APIs
export default {
    data: () => ({
        isLoading: false,
        products: [],
        color: '',
        tab: null,
        activeSlotIndex: 0,
        record: {
            name: 'Flash sale cuối năm',
            time: '',
            sold_increase: true,
            status: true,
            products: [],
            data_json: {
                color: '',
                link_label: 'Xem tất cả',
                link: '#',
                css: '',
                show_col: 5,
                header: 'tiki',
                frame_image: '',
                banner: '',
                persistent_banner: false,
                product_limit: 10,
                per_page: 15,
                autoplay_interval: 3,
                autoplay_pause_hover: true,
                bar_bg_color: '',
                time_slots: [ { time: null } ],
                admin_note: ''
            },
        },
        customCss: false,
        isExpand: false,
        lang: LANG_DATE_PICKER,
        drag: false,
        set_price: {
            is_percent_sale: 'percent',
            value: null
        }
    }),
   
    methods: {
        formatSlotDate(dtStr) {
            if (!dtStr) return '';
            const m = moment(dtStr, 'YYYY-MM-DD HH:mm:ss');
            return m.isValid() ? m.format('DD/MM HH:mm') : dtStr;
        },
        addTimeSlot(){
            this.record.data_json.time_slots.push({ time: null });
            this.activeSlotIndex = this.record.data_json.time_slots.length - 1;
        },
        removeTimeSlot(index){
            const slotIdx = index;
            // Xóa sản phẩm thuộc slot này
            this.record.products = this.record.products.filter(p => (p.slot_index || 0) !== slotIdx);
            // Cập nhật lại slot_index cho các sản phẩm ở slot phía sau
            this.record.products.forEach(p => {
                if ((p.slot_index || 0) > slotIdx) {
                    p.slot_index = (p.slot_index || 0) - 1;
                }
            });
            this.record.data_json.time_slots.splice(index, 1);
            if (this.activeSlotIndex >= this.record.data_json.time_slots.length) {
                this.activeSlotIndex = Math.max(0, this.record.data_json.time_slots.length - 1);
            }
        },
        async addCampaign(){
            const slots = this.record.data_json.time_slots || [];
            const validSlots = slots.filter(el => el.time && el.time[0] && el.time[1]);
            if(!validSlots.length){
                this.NOTIFY('Bạn cần điền ít nhất một khoảng thời gian bắt đầu và kết thúc', false);
                return;
            }
            if(!this.record.name){
                this.NOTIFY('Bạn cần điền Tên chiến dịch', false);
                return;
            }
            if(!this.record.products.length){
                this.NOTIFY('Danh sách sản phẩm rỗng', false);
                return;
            }
            const checkQty = this.record.products.find(el => el.qty == '' || el.qty == 0);
            if(checkQty){
                this.NOTIFY('Hãy điền đầy đủ số lượng Flash sale', false);
                return;
            }
            
            // SetField Data_json
            this.record.data_json.color = this.color;
            this.$q.loading.show();
            const res = await addCampaign(this.record);
            this.$q.loading.hide();

            const { success, data: wrapper } = res.data;
            const msg     = wrapper ? wrapper.msg  : '';
            const result  = wrapper ? wrapper.data : null;
            this.NOTIFY(msg, success);
            if(success && result && !this.record.id){
                this.$set(this.record, 'id', result);
            }
        },
        removeProduct(id){
            const currentSlot = this.activeSlotIndex || 0;
            const index = this.record.products.findIndex(el => el.product_id === id && (el.slot_index || 0) === currentSlot);
            if(index > -1) {
                this.record.products.splice(index, 1);
            }
        },
        addProduct(product){
            const currentSlot = this.activeSlotIndex || 0;
            const index = this.record.products.findIndex(el => el.product_id === product.product_id && (el.slot_index || 0) === currentSlot);
            if(index > -1){
                this.NOTIFY('Sản phẩm này đã có trong đợt này', false);
                return;
            }
            product = Object.assign({qty: 10, real_sold: false, sold: 0, slot_index: currentSlot}, product);
            if(product.children && product.children.length){
                product.children.forEach(c => {
                    c.qty = 10;
                    c.sold = 0;
                    c.real_sold = false;
                    c.slot_index = currentSlot;
                });
            }
            const p = [...this.record.products, product];
            this.$set(this.record, 'products', p);
            this.NOTIFY('Thêm sản phẩm vào Đợt ' + (currentSlot + 1) + ' thành công');
        },
        async getCampaign(){
            const res = await getCampaign(this.$route.params.id);
            const { success, data: wrapper } = res.data;
            const data = wrapper ? wrapper.data : null;
            if(success && data){
                data.time = [data.time_begin, data.time_end];
                
                const mergedJson = Object.assign({}, this.record.data_json, data.data_json);
                if(!mergedJson.time_slots || !mergedJson.time_slots.length){
                    mergedJson.time_slots = [ { time: [data.time_begin, data.time_end] } ];
                }
                
                mergedJson.persistent_banner = mergedJson.persistent_banner === true || mergedJson.persistent_banner === 'true';
                this.color     = mergedJson.color || '';
                data.data_json = mergedJson;
                data.status        = !!data.status;
                data.sold_increase = !!data.sold_increase;

                data.products = (data.products || []).map(el => ({
                    ...el,
                    slot_index: el.slot_index !== undefined ? parseInt(el.slot_index) : 0,
                    real_sold: !!el.real_sold,
                    children: (el.children || []).map(ch => ({
                        ...ch,
                        slot_index: ch.slot_index !== undefined ? parseInt(ch.slot_index) : (el.slot_index !== undefined ? parseInt(el.slot_index) : 0),
                        real_sold: !!ch.real_sold
                    }))
                }));

                this.record = data;
            }
            else{
                this.NOTIFY('Không tìm thấy bản ghi');
                this.$router.push('/');
            }
        },
        calcSalePercent(product){
            const { sale_price, regular_price, flash_sale_price } = product;
            if(flash_sale_price)
                return Math.round(100 - flash_sale_price/regular_price * 100);

            if(sale_price && regular_price)
                return Math.round(100 - sale_price/regular_price * 100);
            return '';
        },
        setPriceAll(){
            const value = this.set_price.value;
            const currentSlot = this.activeSlotIndex || 0;
            if(this.set_price.is_percent_sale === 'percent'){
                if(value > 100){
                    this.NOTIFY('Không thể giảm giá lớn hơn 100%', false);
                    return; 
                }
                this.record.products.forEach((el, i) => {
                    if ((el.slot_index || 0) !== currentSlot) return;
                    const fs_price = value/100 * el.regular_price;
                    el.flash_sale_price = el.regular_price - Math.round(fs_price/1000) * 1000;
                    if(el.children && el.children.length)
                        el.children.forEach(c => {
                            const fs_price = value/100 * c.regular_price;
                            c.flash_sale_price = c.regular_price - Math.round(fs_price/1000) * 1000;
                        });
                    this.$set(this.record.products, i, el);
                });
            }
            else if(this.set_price.is_percent_sale === 'price'){
               this.record.products.forEach((el, i) => {
                   if ((el.slot_index || 0) !== currentSlot) return;
                   const fs_price = el.regular_price - value > 0 ? el.regular_price - value : 0;
                   el.flash_sale_price = Math.round(fs_price/1000) * 1000;
                   if(el.children && el.children.length)
                       el.children.forEach(c => {
                           const fs_price = c.regular_price - value > 0 ? c.regular_price - value : 0;
                           c.flash_sale_price = Math.round(fs_price/1000) * 1000;
                       });
                   this.$set(this.record.products, i, el);
               });
            }
            else{
                this.record.products.forEach((el, i) => {
                    if ((el.slot_index || 0) !== currentSlot) return;
                    const fs_price = el.regular_price > value ? value : 0;
                    el.flash_sale_price = Math.round(fs_price/1000) * 1000;
                    if(el.children && el.children.length)
                        el.children.forEach(c => {
                            const fs_price = c.regular_price > value ? value : 0;
                            c.flash_sale_price = Math.round(fs_price/1000) * 1000;
                        });
                    this.$set(this.record.products, i, el);
                });
            }
        },
        setPrice(p){
            const currentSlot = this.activeSlotIndex || 0;
            const index = this.record.products.findIndex(el => el.product_id == p.product_id && (el.slot_index || 0) === currentSlot);
            if(index > -1)
                this.$set(this.record.products, index, p);
        },
        uploadFrameImage(field)
        {
            const file_frame = wp.media.frames.file_frame = wp.media({ title: 'Upload Khung Ảnh Flash Sale', library: { type: 'image' }, button: { text: 'Lựa chọn' }, multiple: false });

            file_frame.on('select', () => {
                const attachment = file_frame.state().get('selection').first().toJSON();
                this.record.data_json[field] = attachment.url;
            });
            file_frame.open();
        },
    },
    components:{
        ProductSearch,
        SetPriceDialog
    },
    template: template,
    computed: {
        currentSlotProducts: {
            get() {
                const slot = this.activeSlotIndex || 0;
                return this.record.products.filter(p => (p.slot_index || 0) === slot);
            },
            set(newSlotList) {
                const slot = this.activeSlotIndex || 0;
                // Giữ lại các sản phẩm của các slot khác
                const otherSlotProducts = this.record.products.filter(p => (p.slot_index || 0) !== slot);
                newSlotList.forEach(p => p.slot_index = slot);
                this.record.products = [...otherSlotProducts, ...newSlotList];
            }
        },
        dragOptions() {
            return {
                animation: 0,
                group: "description",
                disabled: false,
                ghostClass: "ghost"
            };
        }
    },
    created(){
        if(this.$route.params.id){
            this.getCampaign();
            this.$eventBus.$emit('set.page_title', 'Cập nhật chiến dịch');
        }
        else
            this.$eventBus.$emit('set.page_title', 'Tạo mới chiến dịch');
    }
}