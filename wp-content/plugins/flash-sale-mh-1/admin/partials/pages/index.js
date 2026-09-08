const template = `
<div class="q-mt-lg">
    <div v-if="!isLoading">
        <q-btn color="deep-orange" icon="add_task" label="Tạo mới chiến dịch" to="campaign/add" unelevated/>
        <empty-component v-if="records.length == 0"/>
        <div class="row q-col-gutter-md q-mt-sm" v-else>
            <div class="col-12">
                <q-markup-table flat class="hbfs-card">
                    <thead>
                        <tr>
                            <th class="text-left">#</th>
                            <th class="text-left">Tên chiến dịch</th>
                            <th class="text-center">Sản phẩm</th>
                            <th class="text-left">Thời gian</th>
                            <th class="text-left">Trạng thái</th>
                            <th class="text-left">Shortcode</th>
                            <th class="text-center">#</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="record in records" :key="record.id">
                            <td class="text-left">{{record.id}}</td>
                            <td class="text-left">
                                <strong>{{record.name}}</strong>
                                <div class="text-caption text-grey" v-if="record.data_json && record.data_json.admin_note">
                                    📝 {{record.data_json.admin_note}}
                                </div>
                            </td>
                            <td class="text-center">{{record.total}}</td>
                            <td class="text-left">
                                {{formatDateMoment(record.time_begin)}}
                                <br>~ {{formatDateMoment(record.time_end)}}
                            </td>
                            <td class="text-left">
                                <q-badge color="orange" v-if="isRunning(record)">Đang diễn ra</q-badge>
                                <q-badge color="blue"   v-else-if="isUpcoming(record)">Sắp diễn ra</q-badge>
                                <q-badge color="grey"   v-else-if="record.status">Đã kết thúc</q-badge>
                                <q-badge color="red"    v-else>Tắt</q-badge>
                            </td>
                            <td class="text-left">
                                <!-- Sắp diễn ra → shortcode teaser; còn lại → shortcode slider -->
                                <q-btn flat dense color="blue-8" size="sm"
                                       @click="copyUpcomingShortcode(record.id)"
                                       v-if="isUpcoming(record)">
                                    [hbfs_upcoming id="{{ record.id }}"]
                                </q-btn>
                                <q-btn flat dense color="grey-8" size="sm"
                                       @click="copySliderShortcode(record.id)"
                                       v-else>
                                    [hbfs_slider id="{{ record.id }}"]
                                </q-btn>
                            </td>
                            <td class="text-left">
                                <q-btn round color="deep-orange" icon="edit" size="sm" :to="'/campaign/edit/' + record.id" class="q-mr-xs"/>
                                <q-btn round color="accent" icon="delete" size="sm" @click="confirmRemove(record.id)"/>
                            </td>
                        </tr>
                    </tbody>
                </q-markup-table>

                <div class="flex flex-center q-mt-lg" v-if="pagination.max > 1">
                    <q-pagination v-model="pagination.page" :max="pagination.max"
                        direction-links boundary-links
                        icon-first="skip_previous" icon-last="skip_next"
                        icon-prev="fast_rewind" icon-next="fast_forward"
                        :disabled="isLoading" color="deep-orange"/>
                    <span class="q-ml-md text-grey-7">Tổng {{ pagination.total }} chiến dịch</span>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="flex flex-center q-pa-xl">
        <q-spinner-dots color="deep-orange" size="50px"/>
    </div>
</div>
`;

import { getCampaigns, removeCampaign } from '../api/campaign.js'

export default {
    data: () => ({
        records: [],
        pagination: { page: 1, max: 1, total: 0 },
        isLoading: false,
    }),

    methods: {
        isRunning(campaign) {
            if (!campaign.status) return false;
            const now   = moment().unix();
            const slots = campaign.data_json && campaign.data_json.time_slots ? campaign.data_json.time_slots : [];
            if (slots.length > 0) {
                return slots.some(slot => {
                    if (!slot.time || !slot.time[0] || !slot.time[1]) return false;
                    const begin = moment(slot.time[0], 'YYYY-MM-DD HH:mm:ss').unix();
                    const end   = moment(slot.time[1], 'YYYY-MM-DD HH:mm:ss').unix();
                    return begin <= now && now <= end;
                });
            }
            const begin = moment(campaign.time_begin, 'YYYY-MM-DD HH:mm:ss').unix();
            const end   = moment(campaign.time_end,   'YYYY-MM-DD HH:mm:ss').unix();
            return begin <= now && now <= end;
        },
        isUpcoming(campaign) {
            if (!campaign.status) return false;
            const now   = moment().unix();
            const slots = campaign.data_json && campaign.data_json.time_slots ? campaign.data_json.time_slots : [];
            if (slots.length > 0) {
                const hasUpcoming = slots.some(slot => {
                    if (!slot.time || !slot.time[0]) return false;
                    const begin = moment(slot.time[0], 'YYYY-MM-DD HH:mm:ss').unix();
                    return begin > now;
                });
                const isCurrentlyRunning = this.isRunning(campaign);
                return hasUpcoming && !isCurrentlyRunning;
            }
            const begin = moment(campaign.time_begin, 'YYYY-MM-DD HH:mm:ss').unix();
            return begin > now;
        },
        async getData() {
            this.isLoading = true;
            try {
                const res = await getCampaigns(this.pagination.page);
                const d   = res.data;
                this.records          = d.data  || [];
                this.pagination.total = d.pagination ? d.pagination.total    : 0;
                this.pagination.max   = d.pagination ? d.pagination.max_page : 1;
            } finally {
                this.isLoading = false;
            }
        },
        async confirmRemove(id) {
            const ok = await this.CONFIRM('Bạn chắc chắn muốn xóa chiến dịch này?');
            if (!ok) return;
            this.$q.loading.show();
            const res = await removeCampaign(id);
            this.$q.loading.hide();
            const { success, data } = res.data;
            this.NOTIFY(data ? data.msg : 'Thành công', success ? 1 : 0);
            if (success) this.getData();
        },
        copySliderShortcode(id) {
            Quasar.utils.copyToClipboard(`[hbfs_slider id="${id}"]`);
            this.NOTIFY('Đã copy shortcode slider!');
        },
        copyUpcomingShortcode(id) {
            Quasar.utils.copyToClipboard(`[hbfs_upcoming id="${id}"]`);
            this.NOTIFY('Đã copy shortcode teaser sắp diễn ra!');
        },
    },

    template,

    watch: {
        'pagination.page'() { this.getData(); }
    },

    created() {
        this.$eventBus.$emit('set.page_title', 'HBWeb Flash Sale - Danh sách chiến dịch');
        this.getData();
    },
}