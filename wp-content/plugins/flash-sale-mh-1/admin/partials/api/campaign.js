import { bycApi } from './index.js'

// REST: GET  campaigns?page=N  (không có / đầu → relative path, axios dùng đúng baseURL)
export const getCampaigns = (page = 1) =>
    bycApi.get('campaigns', { params: { page } })

// REST: GET  campaigns/{id}
export const getCampaign = (id) =>
    bycApi.get(`campaigns/${id}`)

// REST: POST campaigns  (thêm hoặc cập nhật tùy có id hay không)
export const addCampaign = (data) =>
    bycApi.post('campaigns', data)

// REST: DELETE campaigns/{id}
export const removeCampaign = (id) =>
    bycApi.delete(`campaigns/${id}`)
