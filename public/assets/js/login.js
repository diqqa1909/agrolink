window.APP_ROOT = "<?=ROOT?>";

        // Auto-remove notifications after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                setTimeout(() => {
                    removeNotification(notification);
                }, 5000);
            });
        });

        // Function to remove notification with animation
        function removeNotification(notification) {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }

        // Function to show new notification
        function showNotification(message, type = 'error') {
            const container = document.getElementById('notificationContainer');
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <span>${message}</span>
                <button class="notification-close" onclick="removeNotification(this.parentElement)">&times;</button>
            `;
            
            container.appendChild(notification);
            
            // Auto-remove after 5 seconds
            setTimeout(() => {
                removeNotification(notification);
            }, 5000);
        }

        // Form submission handling (optional - for AJAX submissions)
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            // You can add client-side validation here if needed
            // For server-side errors, they'll be handled by PHP
        });

        // Close notification when clicking anywhere on it
        document.addEventListener('click', function(e) {
            if (e.target.closest('.notification')) {
                const notification = e.target.closest('.notification');
                if (!e.target.classList.contains('notification-close')) {
                    removeNotification(notification);
                }
            }
        });