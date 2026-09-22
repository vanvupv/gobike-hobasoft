/**
 * GOBIKE - VIDEO REVIEW INTERACTION JS
 * Xử lý chuyển đổi video, popup, lọc danh mục và chia sẻ
 */
(function() {
    'use strict';

    document.addEventListener('DOMContentLoaded', function() {
        var page = document.querySelector('.gobike-video-review-page');
        if (!page) return;

        var mainPlayerBox = page.querySelector('.gb-vr-player-box');
        var mainPlayerTitle = page.querySelector('.gb-vr-player-title');
        var mainPlayerDate = page.querySelector('.gb-vr-player-date');
        var mainPlayerViews = page.querySelector('.gb-vr-player-views');
        var mainPlayerProdBtn = page.querySelector('.gb-vr-btn-view-prod');

        // Hàm chuyển đổi link Youtube sang Embed link
        function getYoutubeEmbedUrl(url) {
            if (!url) return '';
            var videoId = '';
            
            // Hỗ trợ link thường watch?v=, link rút gọn youtu.be/, và link shorts/
            if (url.indexOf('youtube.com/watch?v=') !== -1) {
                videoId = url.split('v=')[1].split('&')[0];
            } else if (url.indexOf('youtu.be/') !== -1) {
                videoId = url.split('youtu.be/')[1].split('?')[0];
            } else if (url.indexOf('youtube.com/shorts/') !== -1) {
                videoId = url.split('shorts/')[1].split('?')[0];
            } else if (url.indexOf('youtube.com/embed/') !== -1) {
                return url;
            }

            return videoId ? 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0' : url;
        }

        // Click phát video từ cover ban đầu
        var cover = page.querySelector('.gb-vr-player-cover');
        if (cover) {
            cover.addEventListener('click', function() {
                var url = cover.getAttribute('data-video-url');
                var embedUrl = getYoutubeEmbedUrl(url);
                if (embedUrl) {
                    mainPlayerBox.innerHTML = '<iframe src="' + embedUrl + '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
                }
            });
        }

        // Click vào item ở "Video tiếp theo" hoặc "Video liên quan"
        var videoItems = page.querySelectorAll('.gb-vr-clickable-video');
        videoItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var videoUrl = item.getAttribute('data-video-url');
                var title = item.getAttribute('data-title');
                var date = item.getAttribute('data-date');
                var views = item.getAttribute('data-views');
                var prodUrl = item.getAttribute('data-prod-url');

                if (!videoUrl) return;

                var embedUrl = getYoutubeEmbedUrl(videoUrl);

                // Cập nhật iframe
                mainPlayerBox.innerHTML = '<iframe src="' + embedUrl + '" title="' + (title || 'Video') + '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';

                // Cập nhật tiêu đề & meta
                if (title && mainPlayerTitle) {
                    mainPlayerTitle.textContent = title;
                }
                if (date && mainPlayerDate) {
                    mainPlayerDate.textContent = date;
                }
                if (views && mainPlayerViews) {
                    mainPlayerViews.textContent = views;
                }

                // Cập nhật nút xem sản phẩm
                if (mainPlayerProdBtn) {
                    if (prodUrl) {
                        mainPlayerProdBtn.href = prodUrl;
                        mainPlayerProdBtn.style.display = 'inline-flex';
                    } else {
                        mainPlayerProdBtn.style.display = 'none';
                    }
                }

                // Active class
                var currentActives = page.querySelectorAll('.gb-vr-up-next-item.active');
                currentActives.forEach(function(el) { el.classList.remove('active'); });
                if (item.classList.contains('gb-vr-up-next-item')) {
                    item.classList.add('active');
                }

                // Cuộn mượt lên vị trí Player nếu đang ở dưới
                var playerSection = page.querySelector('.gb-vr-hero-section');
                if (playerSection && window.scrollY > playerSection.offsetTop + 300) {
                    playerSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Click Sidebar Danh mục Lọc Video
        var catItems = page.querySelectorAll('.gb-vr-cat-item');
        catItems.forEach(function(cat) {
            cat.addEventListener('click', function(e) {
                e.preventDefault();
                catItems.forEach(function(c) { c.classList.remove('active'); });
                cat.classList.add('active');

                var catSlug = cat.getAttribute('data-cat-slug');
                // Lọc video theo slug nếu có
                if (!catSlug || catSlug === 'all') {
                    videoItems.forEach(function(v) { v.style.display = ''; });
                } else {
                    videoItems.forEach(function(v) {
                        var itemCat = v.getAttribute('data-cat-slug');
                        if (!itemCat || itemCat.indexOf(catSlug) !== -1) {
                            v.style.display = '';
                        } else {
                            v.style.display = 'none';
                        }
                    });
                }
            });
        });

        // Nút Lưu video
        var saveBtn = page.querySelector('.gb-vr-btn-save');
        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var isSaved = saveBtn.getAttribute('data-saved') === '1';
                if (isSaved) {
                    saveBtn.setAttribute('data-saved', '0');
                    saveBtn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg> Lưu video';
                    saveBtn.style.color = '';
                } else {
                    saveBtn.setAttribute('data-saved', '1');
                    saveBtn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="#0d7030" stroke="#0d7030" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg> Đã lưu';
                    saveBtn.style.color = '#0d7030';
                }
            });
        }

        // Nút Chia sẻ (Copy link)
        var shareBtn = page.querySelector('.gb-vr-btn-share');
        if (shareBtn) {
            shareBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var currentUrl = window.location.href;
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(currentUrl).then(function() {
                        var originalText = shareBtn.innerHTML;
                        shareBtn.innerHTML = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0d7030" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Đã sao chép link';
                        setTimeout(function() {
                            shareBtn.innerHTML = originalText;
                        }, 2500);
                    });
                }
            });
        }
    });
})();
