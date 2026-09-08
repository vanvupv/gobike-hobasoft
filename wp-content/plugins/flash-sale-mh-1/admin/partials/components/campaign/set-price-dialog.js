const template = `
    <q-btn-dropdown icon="expand" flat dense class="set-price-btn"> 
        <div class="row no-wrap q-pa-md" v-if="product_clone.hasOwnProperty('name')">
            <div class="column" style="min-width: 250px">
                <div class="text-subtitle q-mb-md">Thiết lập giảm giá</div>
                <q-btn-toggle  v-model="is_percent_sale" push spread dense class="price-toggle" no-caps
                    toggle-color="deep-orange" color="white" text-color="deep-orange" :options="[
                                            {label: '%', value: 'percent'},
                                            {label: '$', value: 'price'},
                                            {label: 'Đồng $', value: 'same_price'}
                                ]" />
                
                <q-input :value="value" @input="calcPrice($event)" type="number">
            </div>

            <q-separator vertical inset class="q-mx-lg" />

            <div class="column items-between" style="min-width: 190px">
                
                <template v-if="product_clone.children.length == 0">
                    <div class="flex justify-between" v-if="product_clone.regular_price">
                        <div style="width:80px">Giá thường:</div> <span class="text-bold">{{addCommas(product_clone.regular_price)}}</span>
                        <div class="text-bold" style="width:40px"></div>
                    </div>
                    <div class="flex justify-between" v-if="product_clone.sale_price">
                        <div style="width:80px">Giá KM:</div> <span class="text-bold">{{addCommas(product_clone.sale_price)}}</span>
                        <div class="text-bold text-right" style="width:40px">
                            <q-badge color="red" v-if="product_clone.sale_price">-{{calcSalePercent(product_clone.sale_price, product_clone.regular_price)}}%</q-badge>
                        </div>
                    </div>
                    <div class="flex justify-between" >
                        <div style="width:80px">Giá FS:</div> <span class="text-bold text-red" v-if="product_clone.flash_sale_price >= 0 && product_clone.flash_sale_price != null">{{addCommas(product_clone.flash_sale_price)}}</span>
                        <div class="text-bold text-right" style="width:40px">
                            <q-badge color="red" v-if="product_clone.flash_sale_price">-{{calcSalePercent(product_clone.flash_sale_price, product_clone.regular_price)}}%</q-badge>
                        </div>

                    </div>
                </template>
                <template v-else>
                    <q-markup-table bordered flat>
                        <thead>
                            <tr>
                                <th class="text-left"></th>
                                <th class="text-left">Biến thể</th>
                                <th class="text-left">Giá thường</th>
                                <th class="text-left">Giá KM</th>
                                <th class="text-left">Giá FS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="child in product_clone.children">
                                <td class="text-left"></td>
                                <td class="text-left">{{child.name.replace(product_clone.name, '')}}</td>
                                <td class="text-left">
                                   <span v-if="child.regular_price">{{addCommas(child.regular_price)}}</span>
                                </td>
                                <td class="text-left">
                                    <div class="flex justify-between" v-if="child.sale_price">
                                        <span class="text-bold">{{addCommas(child.sale_price)}}</span>
                                        <div class="text-bold text-right" style="width:40px">
                                            <q-badge color="red" v-if="child.sale_price">-{{calcSalePercent(child.sale_price,
                                                child.regular_price)}}%</q-badge>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <div class="flex justify-between">
                                         <span class="text-bold text-red"
                                            v-if="child.flash_sale_price >= 0 && child.flash_sale_price != null">{{addCommas(child.flash_sale_price)}}</span>
                                        <div class="text-bold text-right" style="width:40px">
                                            <q-badge color="red" v-if="child.flash_sale_price">-{{calcSalePercent(child.flash_sale_price,
                                                child.regular_price)}}%</q-badge>
                                        </div>
                                    
                                    </div>
                                </td>
                                
                            </tr>
                            
                        </tbody>
                    </q-markup-table>
                </template>


                <q-btn class="q-mt-lg" color="deep-orange" label="Áp dụng" push size="sm" v-close-popup @click="setPrice"/>
            </div>
        </div>
    </q-btn-dropdown> 
`;



export default {
    props: ['product'],
    data: () => ({
        product_clone: null,
        is_percent_sale:'percent',
        value: null,
        flash_sale_price: null,
    }),
   
    methods: {
        setPrice(){
            this.$emit('setPrice', this.product_clone)
        },
        calcPrice($event){
            const value = $event
            if(!$event){
                this.NOTIFY('Hãy nhập giá trị để bắt đầu', false)
                return;
            }
            if(this.product_clone.children.length == 0){
                this.changePriceItem(this.product_clone, value)
            }
            // Variant Product
            else{
                this.product_clone.children.forEach(product => {
                    this.changePriceItem(product, value)
                })
                
            }

            console.log(this.product_clone);

            this.$set(this,'product_clone', JSON.parse(JSON.stringify(this.product_clone)))
            
        },
        changePriceItem(obj, value){
            if(this.is_percent_sale === 'percent'){
                if(value > 100){
                    this.NOTIFY('Không thể giảm giá lớn hơn 100%', false)
                    return; 
                }
                const fs_price = value/100 * obj.regular_price;
                obj.flash_sale_price = obj.regular_price - Math.round(fs_price/1000) * 1000

            }
            else if(this.is_percent_sale === 'price'){
                if(parseInt(value) > parseInt(obj.regular_price)){
                    this.NOTIFY('Không thể giảm giá lớn hơn giá sản phẩm', false)
                    return; 
                }
                const fs_price =  obj.regular_price - value;
                obj.flash_sale_price = Math.round(fs_price/1000) * 1000
            }
            else{
                if(parseInt(value) > parseInt(obj.regular_price)){
                    this.NOTIFY('Không thể giảm giá lớn hơn giá sản phẩm', false)
                    return; 
                }
                obj.flash_sale_price = value
            }
            
        },
        calcSalePercent(sale_price, regular_price){
            if(sale_price && regular_price)
              return Math.round(100 - sale_price/regular_price * 100)
            return ''
        }
	},
	components:{

	},
    template: template,
    
    created(){
        this.product_clone = JSON.parse(JSON.stringify(this.product))
    }

}