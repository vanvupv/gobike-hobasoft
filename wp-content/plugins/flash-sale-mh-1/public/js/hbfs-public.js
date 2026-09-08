/* jshint esversion: 6 */
(function ($) {
  'use strict';

  /* ============================================================
     FLASH SALE TABS CONTROLLER
     Quản lý các tab sự kiện: đếm ngược đến lúc bắt đầu / kết thúc,
     hiển thị "Bắt đầu sau: HH:MM:SS" hoặc "Sắp diễn ra",
     và tự động kích hoạt tab phù hợp nhất.
     ============================================================ */
  function hbfsInitFlashSaleTabs() {
    $('.flash-sale-tabs').each(function () {
      var $tabsContainer = $(this);
      if ($tabsContainer.data('hbfs-tabs-init')) return;
      $tabsContainer.data('hbfs-tabs-init', true);

      var $wrap = $tabsContainer.closest('.hbfs-slider-wrap');
      var $tabs = $tabsContainer.find('li');
      if (!$tabs.length) return;

      var pad = function (n) { return n < 10 ? '0' + n : '' + n; };

      // Helper parse datetime string
      function parseDate(dtStr) {
        if (!dtStr) return 0;
        var p = dtStr.match(/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/);
        if (!p) {
          return new Date(dtStr).getTime() || 0;
        }
        return new Date(+p[1], +p[2] - 1, +p[3], +p[4], +p[5], +p[6]).getTime();
      }

      function switchSlotRow(targetSlotIdx) {
        if (!$wrap.length) return;
        var $rows = $wrap.find('.hbfs-slot-products-row');
        if ($rows.length) {
          $rows.hide();
          var $targetRow = $wrap.find('.hbfs-slot-row-' + targetSlotIdx);
          if ($targetRow.length) {
            $targetRow.show();
            // Refresh Splide layout
            if ($targetRow[0].splide) {
              $targetRow[0].splide.refresh();
            }
          }
        }
      }

      function updateTabsState() {
        var now = new Date().getTime();
        var activeFound = false;
        var upcomingClosestIndex = -1;
        var minUpcomingDiff = Infinity;

        $tabs.each(function () {
          var $tab = $(this);
          var idx = $tab.data('index');
          var startStr = $tab.data('start') || ($tab.data('date') + ' ' + ($tab.data('time') || '00:00') + ':00');
          var endStr = $tab.data('end') || '';

          var start = parseDate(startStr);
          var end = parseDate(endStr);

          var $status = $tab.find('#tab-status-' + idx);
          var $timer = $tab.find('#tab-timer-' + idx);

          // 1. Đang diễn ra
          if (start <= now && (end === 0 || now < end)) {
            $status.text('Đang diễn ra');
            $timer.empty();
            if (!activeFound && !$tabs.filter('.user-selected').length) {
              $tabs.removeClass('active');
              $tab.addClass('active');
              switchSlotRow($tab.data('slot-target'));
              activeFound = true;
            }
          }
          // 2. Sắp diễn ra trong tương lai
          else if (start > now) {
            var diff = start - now;
            var diffHours = diff / (1000 * 60 * 60);

            if (diff < minUpcomingDiff) {
              minUpcomingDiff = diff;
              upcomingClosestIndex = $tab.index();
            }

            // Nếu sắp diễn ra trong vòng 24h hoặc là đợt kế tiếp -> hiện countdown
            if (diffHours <= 24) {
              $status.text('Bắt đầu sau:');
              var h = Math.floor(diff / (1000 * 60 * 60));
              var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
              var s = Math.floor((diff % (1000 * 60)) / 1000);

              $timer.html(
                '<span class="time-block hh">' + pad(h) + '</span>' +
                '<span class="colon">:</span>' +
                '<span class="time-block mm">' + pad(m) + '</span>' +
                '<span class="colon">:</span>' +
                '<span class="time-block ss">' + pad(s) + '</span>'
              );
            } else {
              $status.text('Sắp diễn ra');
              $timer.empty();
            }
          }
          // 3. Đã kết thúc
          else {
            $status.text('Đã kết thúc');
            $timer.empty();
          }
        });

        // Nếu chưa có tab active (ví dụ chưa đến giờ mở bán nào), chọn đợt sắp tới gần nhất
        if (!activeFound && !$tabs.filter('.user-selected').length && !$tabs.filter('.active').length) {
          if (upcomingClosestIndex >= 0) {
            var $targetTab = $tabs.eq(upcomingClosestIndex);
            $targetTab.addClass('active');
            switchSlotRow($targetTab.data('slot-target'));
          } else {
            var $targetTab = $tabs.first();
            $targetTab.addClass('active');
            switchSlotRow($targetTab.data('slot-target'));
          }
        }
      }

      updateTabsState();
      setInterval(updateTabsState, 1000);

      // Click tab
      $tabs.on('click', function () {
        $tabs.removeClass('active user-selected');
        var $clicked = $(this);
        $clicked.addClass('active user-selected');
        switchSlotRow($clicked.data('slot-target'));
      });
    });
  }

  /* ============================================================
     COUNTDOWN TIMER
     Dùng cho cả product bar và slider header.
     Tìm tất cả [data-hbfs-countdown="YYYY-MM-DD HH:mm:ss"]
     ============================================================ */
  function hbfsCountdown() {
    $('[data-hbfs-countdown]').each(function () {
      var $el = $(this);
      if ($el.data('hbfs-timer')) return; // already running

      var endStr = $el.attr('data-hbfs-countdown') || '';
      // Parse thủ công "YYYY-MM-DD HH:mm:ss" để tránh lỗi trên iOS Safari
      var parts = endStr.match(/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/);
      if (!parts) return;
      var end = new Date(+parts[1], +parts[2]-1, +parts[3], +parts[4], +parts[5], +parts[6]).getTime();

      // Đã hết hạn ngay khi load trang → ẩn ngay, không chạy timer
      if (new Date().getTime() >= end) {
        var $dead = $el.closest('.hbfs-slider-wrap, .hbfs-product-bar, .hbfs-upcoming-grid');
        ($dead.length ? $dead : $el.closest('[class*="hbfs-"]')).hide();
        return;
      }

      var pad = function (n) { return n < 10 ? '0' + n : '' + n; };

      function tick() {
        var now  = new Date().getTime();
        var diff = end - now;

        if (diff <= 0) {
          clearInterval(timer);
          var $container = $el.closest('.hbfs-slider-wrap, .hbfs-product-bar, .hbfs-upcoming-grid');
          if ($container.length) {
            $container.fadeTo(400, 0, function () { $container.hide(); });
          } else {
            $el.closest('[class*="hbfs-"]').fadeTo(400, 0, function () { $(this).hide(); });
          }
          return;
        }

        var d = Math.floor(diff / (1000 * 60 * 60 * 24));
        var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var s = Math.floor((diff % (1000 * 60)) / 1000);

        if (d > 0) {
          $el.html(
            '<span class="hbfs-countdown__block">' + pad(d) + '<small>ngày</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(h) + '<small>giờ</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(m) + '<small>phút</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(s) + '<small>giây</small></span>'
          );
        } else {
          $el.html(
            '<span class="hbfs-countdown__block">' + pad(h) + '<small>giờ</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(m) + '<small>phút</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(s) + '<small>giây</small></span>'
          );
        }
      }

      tick();
      var timer = setInterval(tick, 1000);
      $el.data('hbfs-timer', timer);
    });
  }

  /* ============================================================
     SPLIDE SLIDER INIT
     data-hbfs-splide = JSON config (PHP encode)
     ============================================================ */
  function hbfsInitSplide($scope) {
    if (typeof Splide === 'undefined') {
      console.warn('HBFlashSale: Splide library not loaded.');
      return;
    }

    var root = ($scope && $scope.length) ? $scope[0] : document;
    var els  = root.querySelectorAll ? root.querySelectorAll('[data-hbfs-splide]')
                                     : [];

    Array.prototype.forEach.call(els, function (el) {
      if (el.dataset.hbfsSplideInit) return;
      el.dataset.hbfsSplideInit = '1';

      var config = {};
      try {
        config = JSON.parse(el.getAttribute('data-hbfs-splide') || '{}');
      } catch (e) {
        config = {};
      }

      var opts = $.extend({
        type        : 'slide',
        perPage     : 5,
        perMove     : 1,
        gap         : '15px',
        arrows      : true,
        pagination  : false,
        drag        : true,
        rewind      : false
      }, config);

      var splide = new Splide(el, opts);
      splide.mount();
      el.splide = splide;
    });
  }

  /* ============================================================
     VARIATION PRICE: Khi user chọn biến thể, cập nhật giá flash sale
     ============================================================ */
  function hbfsVariationPriceWatch() {
    $(document).on('found_variation', '.variations_form', function (evt, variation) {
      var variationId = variation.variation_id;
      if (!variationId) return;

      var $bar = $('.hbfs-product-bar');
      if (!$bar.length) return;

      $.post(HBFS.ajax_url, {
        action: 'hbfs_get_variation_bar',
        variation_id: variationId,
        nonce: HBFS.nonce
      }, function (res) {
        if (res.success && res.data) {
          var d = res.data;
          var pct = d.qty > 0 ? Math.min(100, Math.round(d.sold / d.qty * 100)) : 0;
          $bar.find('.hbfs-progress-bar').css('width', pct + '%');
          $bar.find('.hbfs-sold-label').text('Đã bán ' + d.sold + '/' + d.qty);
        }
      }, 'json');
    });
  }

  /* ============================================================
     UPCOMING TEASER: Countdown đến lúc BẮT ĐẦU
     ============================================================ */
  function hbfsUpcomingCountdown() {
    $('[data-hbfs-countdown-start]').each(function () {
      var $el = $(this);
      if ($el.data('hbfs-start-timer')) return;

      var startStr = $el.attr('data-hbfs-countdown-start') || '';
      var parts = startStr.match(/(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}):(\d{2})/);
      if (!parts) return;
      var start = new Date(+parts[1], +parts[2]-1, +parts[3], +parts[4], +parts[5], +parts[6]).getTime();

      var pad = function (n) { return n < 10 ? '0' + n : '' + n; };

      function tick() {
        var now  = new Date().getTime();
        var diff = start - now;

        if (diff <= 0) {
          clearInterval(timer);
          var guardKey = 'hbfs_reloaded_' + startStr.replace(/\D/g, '');
          if (!sessionStorage.getItem(guardKey)) {
            sessionStorage.setItem(guardKey, '1');
            setTimeout(function () { location.reload(); }, 1500);
          } else {
            var $container = $el.closest('.hbfs-slider-wrap, .hbfs-upcoming-grid');
            if ($container.length) {
              $container.fadeTo(600, 0, function () { $container.slideUp(400); });
            }
          }
          return;
        }

        var d = Math.floor(diff / (1000 * 60 * 60 * 24));
        var h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        var s = Math.floor((diff % (1000 * 60)) / 1000);

        if (d > 0) {
          $el.html(
            '<span class="hbfs-countdown__block">' + pad(d) + '<small>ngày</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(h) + '<small>giờ</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(m) + '<small>phút</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(s) + '<small>giây</small></span>'
          );
        } else {
          $el.html(
            '<span class="hbfs-countdown__block">' + pad(h) + '<small>giờ</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(m) + '<small>phút</small></span>'
            + '<span class="hbfs-countdown__sep">:</span>'
            + '<span class="hbfs-countdown__block">' + pad(s) + '<small>giây</small></span>'
          );
        }
      }

      tick();
      var timer = setInterval(tick, 1000);
      $el.data('hbfs-start-timer', timer);
    });
  }

  /* ============================================================
     FIX FRAME POSITION — move frame vào .box-image
     ============================================================ */
  function hbfsFixFramePosition() {
    $('.hbfs-loop-wrap').each(function () {
      var $wrap  = $(this);
      var $frame = $wrap.find('.hbfs-loop-overlay__frame');
      if (!$frame.length) return;

      if ($frame.data('hbfs-moved')) return;

      var $boxImage = $wrap.find('.box-image').first();
      if (!$boxImage.length) return;

      if ($boxImage.css('position') === 'static') {
        $boxImage.css('position', 'relative');
      }

      $boxImage.append($frame);
      $frame.data('hbfs-moved', true);
    });
  }

  /* ============================================================
     INIT
     ============================================================ */
  $(document).ready(function () {
    hbfsInitFlashSaleTabs();
    hbfsCountdown();
    hbfsUpcomingCountdown();
    hbfsInitSplide();
    hbfsVariationPriceWatch();
    hbfsFixFramePosition();
  });

})(jQuery);

