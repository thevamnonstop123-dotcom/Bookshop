/**
 * Admin Notifications
 */
(function () {
    'use strict';

    var bell = document.getElementById('adminNotificationBell');
    var panel = document.getElementById('adminNotificationPanel');
    var badge = document.getElementById('adminNotificationBadge');
    var list = document.getElementById('adminNotificationList');

    if (!bell || !panel) return;

    window.toggleAdminNotifications = function () {
        if (panel.classList.contains('open')) {
            panel.classList.remove('open');
        } else {
            panel.classList.add('open');
            loadAdminNotifications();
        }
    };

    document.addEventListener('click', function (e) {
        if (panel.classList.contains('open') && !panel.contains(e.target) && e.target !== bell && !bell.contains(e.target)) {
            panel.classList.remove('open');
        }
    });

    function loadAdminNotifications() {
        fetch('/admin/notifications', {
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.notifications.length === 0) {
                list.innerHTML = '<div class="admin-notification-empty"><i class="fas fa-bell-slash"></i><p>No notifications</p></div>';
            } else {
                list.innerHTML = data.notifications.map(function (n) {
                    var iconClass = n.type === 'new_order' ? 'order' : (n.type === 'low_stock' || n.type === 'out_of_stock' ? 'stock' : 'promotion');
                    var icon = n.type === 'new_order' ? 'fa-shopping-cart' : (n.type === 'low_stock' || n.type === 'out_of_stock' ? 'fa-box' : 'fa-tag');
                    var onclick = n.url ? 'onclick="window.location.href=\'' + n.url + '\'"' : '';
                    return '<div class="admin-notification-item ' + (n.read_at ? '' : 'unread') + '" ' + onclick + '>' +
                        '<div class="admin-notification-item-icon ' + iconClass + '"><i class="fas ' + icon + '"></i></div>' +
                        '<div class="admin-notification-item-content">' +
                        '<div class="admin-notification-item-title">' + n.title + '</div>' +
                        '<div class="admin-notification-item-message">' + n.message + '</div>' +
                        '<div class="admin-notification-item-time">' + n.created_at + '</div>' +
                        '</div></div>';
                }).join('');
            }
            badge.textContent = data.unread_count;
            badge.style.display = data.unread_count > 0 ? 'flex' : 'none';
        });
    }

    window.markAllAdminNotificationsRead = function () {
        fetch('/admin/notifications/read-all', {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        }).then(function () {
            badge.style.display = 'none';
            loadAdminNotifications();
        });
    };

    fetch('/admin/notifications/count', {
        headers: { 'Accept': 'application/json' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        if (data.count > 0) {
            badge.textContent = data.count;
            badge.style.display = 'flex';
        }
    });

})();
