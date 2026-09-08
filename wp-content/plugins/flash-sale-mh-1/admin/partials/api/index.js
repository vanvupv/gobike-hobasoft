// API base - HBWeb Flash Sale (dùng REST API thay admin-ajax.php để bypass Cloudflare)
const bycApi = axios.create({
    baseURL: (HBFS_CONFIGS.rest_url || '').replace(/\/?$/, '/'), // đảm bảo có trailing slash
    timeout: 30000,
    headers: {
        'X-WP-Nonce':       HBFS_CONFIGS.nonce,
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type':     'application/json',
    },
    withCredentials: true,
});

// Helper POST JSON (thay thế jsonToFormData — REST API nhận JSON)
const jsonToFormData = (data) => data; // Giữ tên cũ để không đổi code ở campaign.js/product.js

export { bycApi, jsonToFormData };