import { bycApi } from './index.js'

// REST: GET  products/id/{id}
export const getProductById = (id) =>
    bycApi.get(`products/id/${id}`)

// REST: GET  products?search=xxx
export const searchProducts = (search) =>
    bycApi.get('products', { params: { search } })
