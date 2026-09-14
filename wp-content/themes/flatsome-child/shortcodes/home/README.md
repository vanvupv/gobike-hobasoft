# HƯỚNG DẪN SỬ DỤNG CÁC SHORTCODE TRANG CHỦ (GOBIKE)

Thư mục này chứa toàn bộ các khối giao diện (Section) dạng Shortcode được thiết kế chuyên biệt cho Trang chủ GOBIKE, dùng để chèn vào các phần tử **Text** hoặc **HTML** trong **Flatsome UX Builder**.

---

## 1. Cơ chế tự động nạp (Auto-loader)
Mọi tệp tin PHP mới được tạo trong thư mục `shortcodes/home/` (hoặc `shortcodes/`) sẽ **tự động được nạp** vào theme thông qua `functions.php`:
```php
foreach (glob(__DIR__ . '/shortcodes/home/*.php') as $file) {
    require_once $file;
}
```
> Khi cần thêm một section mới, bạn chỉ việc tạo tệp `sc-ten-khoi.php` tại đây và khai báo `add_shortcode(...)`. Không cần sửa thêm bất kỳ dòng nào trong `functions.php`.

---

## 2. Bảng tổng hợp các Shortcode hiện có

| STT | Tên Section | Tệp Tin Mã Nguồn | Shortcode Flatsome | Tham Số Hỗ Trợ |
|:---:|:---|:---|:---|:---|
| **01** | **Khối Danh Mục Lớn** (1 To + 8 Nhỏ) | [sc-category-block.php](file:///e:/1.%20D%E1%BB%B0%20%C3%81N%20TH%E1%BB%B0C%20T%E1%BA%BE%20(2026)/(17)%20GOBIKE%20(28082026)/wp-content/themes/flatsome-child/shortcodes/home/sc-category-block.php) | `[gobike_category_block]` | `cat`: slug danh mục (bắt buộc)<br>`title`: tiêu đề khối<br>`subcat`: tên link phụ<br>`subcat_link`: URL link phụ<br>`limit`: số lượng sản phẩm (mặc định 9)<br>`view_all`: link xem tất cả |
| **02** | **Khối Flash Sale** (Giờ vàng giá sốc) | [sc-flash-sale.php](file:///e:/1.%20D%E1%BB%B0%20%C3%81N%20TH%E1%BB%B0C%20T%E1%BA%BE%20(2026)/(17)%20GOBIKE%20(28082026)/wp-content/themes/flatsome-child/shortcodes/home/sc-flash-sale.php) | `[gobike_home_flash_sale]` | `title`: tiêu đề khối<br>`limit`: số sản phẩm (mặc định 5)<br>`view_all`: URL xem tất cả |
| **03** | **Khối Tabs Thương Hiệu** (ADO, Phoenix...) | [sc-brand-tabs.php](file:///e:/1.%20D%E1%BB%B0%20%C3%81N%20TH%E1%BB%B0C%20T%E1%BA%BE%20(2026)/(17)%20GOBIKE%20(28082026)/wp-content/themes/flatsome-child/shortcodes/home/sc-brand-tabs.php) | `[gobike_home_brand_tabs]` | `title`: tiêu đề khối<br>`limit`: số sản phẩm mỗi tab (mặc định 8) |
| **04** | **Khối Video Reviews** (Trải nghiệm thực tế) | [sc-video-reviews.php](file:///e:/1.%20D%E1%BB%B0%20%C3%81N%20TH%E1%BB%B0C%20T%E1%BA%BE%20(2026)/(17)%20GOBIKE%20(28082026)/wp-content/themes/flatsome-child/shortcodes/home/sc-video-reviews.php) | `[gobike_home_video_reviews]` | `title`: tiêu đề khối<br>`subtitle`: dòng mô tả phụ<br>`view_all`: link xem tất cả<br>*(Tích hợp CPT `video_review`, lọc tab động, popup YouTube và khối marketing Tại sao nên xem)* |
| **05** | **Bộ Lọc Nhanh & Tư Vấn 60 Giây** | [sc-quick-finder.php](file:///e:/1.%20D%E1%BB%B0%20%C3%81N%20TH%E1%BB%B0C%20T%E1%BA%BE%20(2026)/(17)%20GOBIKE%20(28082026)/wp-content/themes/flatsome-child/shortcodes/home/sc-quick-finder.php) | `[gobike_quick_finder]` | Không cần tham số (Tích hợp sẵn 5 bộ lọc, popup tư vấn, gửi mail & lưu Lead vào CPT `customer_lead`) |

---

## 3. Ví dụ cách chèn trong Flatsome UX Builder

1. Mở trang chủ bằng **UX Builder**.
2. Thêm một phần tử **Text** hoặc **HTML**.
3. Dán shortcode mong muốn, ví dụ:
   ```text
   [gobike_category_block cat="xe-dap-tro-luc-dien" title="XE ĐẠP TRỢ LỰC ĐIỆN" subcat="Phụ kiện xe điện" subcat_link="/danh-muc/phu-kien/"]
   ```
4. Bấm **Apply** ➔ **Update** là trang hiển thị ngay lập tức.
