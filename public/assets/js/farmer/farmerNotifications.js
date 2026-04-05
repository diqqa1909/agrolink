(function () {
    'use strict';

    const APP_ROOT = window.APP_ROOT || document.body.getAttribute('data-app-root') || '';
    const seedEl = document.getElementById('farmerNotificationsSeed');
    const seed = seedEl ? JSON.parse(seedEl.textContent || '{}') : {};

    let notifications = Array.isArray(seed.notifications) ? seed.notifications : [];
    let unreadCount = Number(seed.unreadCount || 0);
    let activeFilter = 'all';

    function timeAgo(value) {
        const ts = new Date(value).getTime();
        if (!ts) return 'Just now';

        const diff = Math.max(0, Date.now() - ts);
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `${minutes} min${minutes > 1 ? 's' : ''} ago`;
        if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        return `${days} day${days > 1 ? 's' : ''} ago`;
    }

    function iconFor(type) {
        const map = {
            orders: '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/>',
            deliveries: '<path d="M3 7h13v10H3z"/><path d="M16 10h4l3 3v4h-7z"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="18.5" cy="17.5" r="1.5"/>',
            reviews: '<path d="m12 2 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 16.9 6.4 19.2l1.1-6.2L3 8.6l6.2-.9z"/>',
            crop_requests: '<path d="M4 20c5-1 8-4 9-9"/><path d="M14 11c5 0 6-5 6-8-3 0-8 1-8 6"/><path d="M4 20c0-4 2-7 6-9"/>',
            system: '<circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><circle cx="12" cy="16" r="1"/>',
        };
        return map[type] || map.system;
    }

    function filteredNotifications() {
        if (activeFilter === 'all') return notifications;
        if (activeFilter === 'unread') return notifications.filter(item => !item.is_read);
        return notifications.filter(item => item.category === activeFilter);
    }

    function renderNotifications() {
        const list = document.getElementById('notificationsList');
        if (!list) return;

        const rows = filteredNotifications();
        if (!rows.length) {
            list.innerHTML = '<div class="notifications-empty">No notifications found for this filter.</div>';
            return;
        }

        list.innerHTML = rows.map(item => {
            const unreadClass = item.is_read ? '' : 'is-unread';
            const link = item.link ? `${APP_ROOT}/${item.link}` : '#';
            return `
                <a class="notification-row ${unreadClass}" href="${link}">
                    <div class="notification-type-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">${iconFor(item.icon || item.category)}</svg>
                    </div>
                    <div class="notification-row-content">
                        <div class="notification-row-title-line">
                            <h4>${item.title || 'Notification'}</h4>
                            ${item.is_read ? '' : '<span class="notification-unread-dot"></span>'}
                        </div>
                        <p>${item.message || ''}</p>
                    </div>
                    <div class="notification-row-time">${timeAgo(item.created_at)}</div>
                </a>
            `;
        }).join('');
    }

    function updateActiveFilterUi() {
        document.querySelectorAll('.notification-filter-tab').forEach(tab => {
            tab.classList.toggle('is-active', tab.getAttribute('data-filter') === activeFilter);
        });
    }

    function updateSidebarBadge() {
        const badge = document.getElementById('farmerNotificationBadge');
        if (!badge) return;
        badge.textContent = String(unreadCount);
        badge.classList.toggle('is-hidden', unreadCount <= 0);
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('show');
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('show');
    }

    function markAllAsRead() {
        fetch(`${APP_ROOT}/farmernotifications/markAllAsRead`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                notifications = Array.isArray(res.notifications) ? res.notifications : notifications.map(n => ({ ...n, is_read: true }));
                unreadCount = Number(res.unreadCount || 0);
                updateSidebarBadge();
                renderNotifications();
            })
            .catch(err => console.error('Mark all as read error:', err));
    }

    function saveSettings(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const data = new FormData(form);

        fetch(`${APP_ROOT}/farmernotifications/saveSettings`, {
            method: 'POST',
            credentials: 'include',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: data
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) return;
                notifications = Array.isArray(res.notifications) ? res.notifications : [];
                unreadCount = Number(res.unreadCount || 0);
                activeFilter = 'all';
                updateActiveFilterUi();
                updateSidebarBadge();
                renderNotifications();
                closeModal('notificationSettingsModal');
            })
            .catch(err => console.error('Save notification settings error:', err));
    }

    function init() {
        renderNotifications();
        updateSidebarBadge();

        document.querySelectorAll('.notification-filter-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                activeFilter = this.getAttribute('data-filter') || 'all';
                updateActiveFilterUi();
                renderNotifications();
            });
        });

        const markAllBtn = document.getElementById('markAllNotificationsReadBtn');
        if (markAllBtn) markAllBtn.addEventListener('click', markAllAsRead);

        const openSettingsBtn = document.getElementById('openNotificationSettingsBtn');
        if (openSettingsBtn) {
            openSettingsBtn.addEventListener('click', function () {
                openModal('notificationSettingsModal');
            });
        }

        const settingsForm = document.getElementById('notificationSettingsForm');
        if (settingsForm) settingsForm.addEventListener('submit', saveSettings);

        document.querySelectorAll('[data-close-modal]').forEach(btn => {
            btn.addEventListener('click', function () {
                closeModal(this.getAttribute('data-close-modal'));
            });
        });

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) modal.classList.remove('show');
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

