function logout() {
            if (confirm('Are you sure you want to logout?')) {
                localStorage.removeItem('user_id');
                localStorage.removeItem('user_email');
                localStorage.removeItem('user_role');
                localStorage.removeItem('user_name');
                localStorage.removeItem('business_name');
                window.location.href = 'auth/logout.php';
            }
        }
        
        // Remove authentication check to allow dashboard access without login
        document.addEventListener('DOMContentLoaded', function() {
            initAdminDashboard();
        });

        // Initialize admin dashboard
        function initAdminDashboard() {
            loadDashboardData();
            loadUsers();
            loadOrders();
            loadProducts();
            setupNavigation();
            setupForms();
            // Show dashboard section by default on initial load
            showSection('dashboard');
        }

        // Navigation setup
        function setupNavigation() {
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');
                    showSection(section);
                    
                    menuLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        // Show specific section
        function showSection(sectionName) {
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => section.style.display = 'none');
            
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }
            
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('data-section') === sectionName) {
                    link.classList.add('active');
                }
            });

            // Load analytics data when analytics section is shown
            if (sectionName === 'analytics') {
                loadAnalytics();
            }
        }

        // Load dashboard data
        function loadDashboardData() {
            // Mock data - in real app, fetch from APIs
            document.getElementById('recentOrders').innerHTML = `
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-007</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">John Buyer → Ranjith Farmer - Rs. 2,450</div>
                    <span class="badge badge-success">Completed</span>
                </div>
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">#ORD-2025-008</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Green Valley Restaurant → Multiple Farmers - Rs. 8,900</div>
                    <span class="badge badge-warning">Processing</span>
                </div>
            `;
            
            // New registrations
            document.getElementById('newRegistrations').innerHTML = `
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">Saman Perera</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Farmer - Kandy</div>
                    <span class="badge badge-info">Pending Approval</span>
                </div>
                <div style="margin-bottom: var(--spacing-sm); padding-bottom: var(--spacing-sm); border-bottom: 1px solid var(--light-gray);">
                    <div style="font-weight: var(--font-weight-bold);">Fresh Mart Ltd</div>
                    <div style="font-size: 0.9rem; color: var(--dark-gray);">Buyer - Colombo</div>
                    <span class="badge badge-success">Approved</span>
                </div>
            `;
        }

        // Load users data
        // Load users data
function loadUsers(){
    const tbody = document.getElementById('usersTableBody');
    const usersData = tbody.getAttribute('data-users');
    const users = usersData ? JSON.parse(usersData) : [];

    let html = '';
    users.forEach(user => {
        html += `
            <tr>
                <td>${user.id || 'N/A'}</td>
                <td>${user.name || 'N/A'}</td>
                <td>${user.email || 'N/A'}</td>
                <td><span class="badge badge-${user.role === 'farmer' ? 'success' : user.role === 'buyer' ? 'info' : user.role === 'transporter' ? 'warning' : 'danger'}">${user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : 'User'}</span></td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="openUpdateUserModal('${user.id}')">View</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser('${user.id}')">Delete</button>
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

        // Load orders data
        function loadOrders() {
            // Order statistics
            document.getElementById('pendingOrdersCount').textContent = '5';
            document.getElementById('processingOrdersCount').textContent = '8';
            document.getElementById('completedOrdersCount').textContent = '234';
            document.getElementById('averageOrderValue').textContent = 'Rs. 1,250';
            
            const tbody = document.getElementById('ordersTableBody');
            tbody.innerHTML = `
                <tr>
                    <td>#ORD-2025-001</td>
                    <td>John Buyer</td>
                    <td>Ranjith Fernando</td>
                    <td>Rs. 2,450</td>
                    <td><span class="badge badge-success">Completed</span></td>
                    <td><span class="badge badge-success">Paid</span></td>
                    <td>2025-01-05</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="viewOrder('ORD-2025-001')">View</button>
                    </td>
                </tr>
                <tr>
                    <td>#ORD-2025-002</td>
                    <td>Green Valley Restaurant</td>
                    <td>Multiple Farmers</td>
                    <td>Rs. 8,900</td>
                    <td><span class="badge badge-warning">Processing</span></td>
                    <td><span class="badge badge-warning">Pending</span></td>
                    <td>2025-01-07</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="viewOrder('ORD-2025-002')">View</button>
                    </td>
                </tr>
            `;
        }

        // Load products data
        function loadProducts() {
            // Product statistics
            document.getElementById('totalProducts').textContent = '156';
            document.getElementById('activeProducts').textContent = '142';
            document.getElementById('outOfStock').textContent = '8';
            document.getElementById('pendingApproval').textContent = '6';
            
            // Category counts
            document.getElementById('vegetableCount').textContent = '89';
            document.getElementById('fruitCount').textContent = '34';
            document.getElementById('grainCount').textContent = '23';
            
            const tbody = document.getElementById('productsTableBody');
            tbody.innerHTML = `
                <tr>
                    <td>
                        <div style="font-weight: var(--font-weight-bold);">Fresh Tomatoes</div>
                        <div style="font-size: 0.9rem; color: var(--dark-gray);">Organic tomatoes from Matale</div>
                    </td>
                    <td>Ranjith Fernando</td>
                    <td><span class="badge badge-success">Vegetables</span></td>
                    <td>Rs. 120/kg</td>
                    <td>45kg</td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="viewProduct('PROD-001')">View</button>
                        <button class="btn btn-sm btn-warning" onclick="moderateProduct('PROD-001')">Moderate</button>
                    </td>
                </tr>
            `;
        }

        // Setup forms
        function setupForms() {
            // Send notification form
            const sendNotificationForm = document.getElementById('sendNotificationForm');
            if (sendNotificationForm) {
                sendNotificationForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showNotification?.('Notification sent successfully!', 'success');
                    closeModal?.('sendNotificationModal');
                    this.reset();
                });
            }
            
            // Settings forms
            const platformSettingsForm = document.getElementById('platformSettingsForm');
            if (platformSettingsForm) {
                platformSettingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    showNotification?.('Platform settings saved successfully!', 'success');
                });
            }
        }
        
        // Function to reload table
        async function reloadTable() {
            try {
                const response = await fetch('<?=ROOT?>/users/getTable');
                const html = await response.text();
                document.getElementById('users-table-body').innerHTML = html;
                attachDeleteListeners(); // Re-attach event listeners
            } catch (error) {
                console.error('Error reloading table:', error);
            }
        }

        // Function to attach delete button listeners
        function attachDeleteListeners() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-userid');
                    deleteUser(userId);
                });
            });
        }

        // Initial attachment
        document.addEventListener('DOMContentLoaded', function() {
            attachDeleteListeners();
        });

        // Admin-specific functions
        function suspendUser(userId) {
            if (confirm('Are you sure you want to suspend this user?')) {
                showNotification?.('User suspended successfully', 'warning');
                loadUsers();
            }
        }

        async function deleteUser(userId){
            if(!confirm('Are you sure you want to delete this user? This action cannot be undone.')){
                return;
            }

            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/deleteUser', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({user_id: userId})
                });
                
                const result = await response.json();

                if (result.success) {
                    showNotification('User deleted successfully', 'success');
                    updateUserCount();
                    window.location.reload();
                } else {
                    showNotification(result.message || 'Error deleting user', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Network error. Please try again.', 'error');
            }
        }

        // Optional: Notification function
        function showNotification(message, type) {
            // Your notification implementation
            alert(message); // Simple alert for demo
        }

        function viewOrder(orderId) {
            showNotification?.('Order details modal will be implemented', 'info');
        }

        function viewProduct(productId) {
            showNotification?.('Product details modal will be implemented', 'info');
        }

        function moderateProduct(productId) {
            showNotification?.('Product moderation modal will be implemented', 'info');
        }

        function viewDispute(disputeId) {
            showNotification?.('Dispute details modal will be implemented', 'info');
        }

        function resolveDispute(disputeId) {
            if (confirm('Mark this dispute as resolved?')) {
                showNotification?.('Dispute resolved successfully', 'success');
            }
        }

        function exportUsers() {
            showNotification?.('Exporting users data...', 'info');
        }

        function exportTransactions() {
            showNotification?.('Exporting transaction data...', 'info');
        }

        function performMaintenance(type) {
            const actions = {
                'backup': 'Creating system backup...',
                'cleanup': 'Cleaning database...',
                'cache': 'Clearing cache...',
                'maintenance': 'Enabling maintenance mode...'
            };
            
            showNotification?.(actions[type], 'info');
            
            setTimeout(() => {
                showNotification?.(`${type} completed successfully!`, 'success');
            }, 2000);
        }

        // Update analytics data
        function loadAnalytics() {
            document.getElementById('monthlyActiveUsers').textContent = '189';
            document.getElementById('platformGrowth').textContent = '12.5%';
            document.getElementById('userRetention').textContent = '87%';
            document.getElementById('customerSatisfaction').textContent = '94%';
        }

        // Utility: Dummy getCurrentUser if not defined
        if (typeof getCurrentUser !== 'function') {
            function getCurrentUser() {
                return null;
            }
        }

        // Utility: Dummy showNotification if not defined
        if (typeof showNotification !== 'function') {
            function showNotification(msg, type) {
                alert(msg);
            }
        }

        // Utility: Dummy closeModal if not defined
        if (typeof closeModal !== 'function') {
            function closeModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) modal.style.display = 'none';
            }
        }

        //////////////////MY FUNCTIONS////////////////////
        async function updateUserCount(){
            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/updateUserCount');
                const result = await response.json();

                if (result.success) {
                    document.getElementById('totalUsers').textContent = result.userCount;
                }
            } catch (error) {
                console.error('Error loading user content:', error);
            }
        }

        //update count every 30s
        document.addEventListener('DOMContentLoaded', function(){
            updateUserCount();
            setInterval(updateUserCount, 30000);
        });

        // Function to open Add User Modal
        function openAddUserModal() {
            document.getElementById('addUserModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.getElementById('addUserForm').reset();
            document.getElementById('addUserMessage').style.display = 'none';
            document.getElementById('addUserFormErrors').style.display = 'none';
        }

        // Function to close Add User Modal
        function closeAddUserModal() {
            document.getElementById('addUserModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('addUserForm').reset();
        }

        // Function to open Update User Modal
        async function openUpdateUserModal(userId) {
            try {
                const response = await fetch(`<?=ROOT?>/adminDashboard/getUser/${userId}`);
                const result = await response.json();

                if(result.success){
                    populateUpdateModal(result.data);
                    document.getElementById('updateUserModal').style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                } else {
                    showMessage(result.message || 'Failed to load user details', 'error');
                }
            } catch(error) {
                console.error('Error loading user details:', error);
                showMessage('Network error occurred', 'error');
            }
        }

        // Function to close Update User Modal
        function closeUpdateUserModal() {
            document.getElementById('updateUserModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('updateUserForm').reset();
        }

        // Function to populate update modal
        function populateUpdateModal(user) {
            document.getElementById('updateUserId').value = user.id;
            document.getElementById('updateName').value = user.name;
            document.getElementById('updateEmail').value = user.email;
            document.getElementById('updateRole').value = user.role;
            document.getElementById('updatePass').value = ''; // Clear password field for security
            document.getElementById('updateUserMessage').style.display = 'none';
            document.getElementById('updateUserFormErrors').style.display = 'none';
        }

        // Add User Form submission
        document.getElementById('addUserForm').addEventListener('submit', async function(e){
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/register',{
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                showMessage(result.message, result.success ? 'success' : 'error', 'addUserMessage');

                if (result.success) {
                    showNotification('User added successfully!', 'success');
                    closeAddUserModal();
                    this.reset();
                    updateUserCount();
                    window.location.reload();
                } else {
                    // Show validation errors
                    if (result.errors) {
                        let errorHtml = '<strong>Please fix the following errors:</strong><ul>';
                        for (const error in result.errors) {
                            errorHtml += `<li>${result.errors[error]}</li>`;
                        }
                        errorHtml += '</ul>';
                        document.getElementById('addUserFormErrors').innerHTML = errorHtml;
                        document.getElementById('addUserFormErrors').style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error occurred. Please try again.', 'error', 'addUserMessage');
            }
        });

        // Update User Form submission
        document.getElementById('updateUserForm').addEventListener('submit', async function(e){
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('<?=ROOT?>/adminDashboard/updateUser',{
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if(result.success){
                    showMessage('User updated successfully!', 'success', 'updateUserMessage');
                    closeUpdateUserModal();
                    loadUsers(); // Refresh the user list
                    window.location.reload();
                } else {
                    showMessage(result.message || 'Failed to update user', 'error', 'updateUserMessage');
                    // Show validation errors
                    if (result.errors) {
                        let errorHtml = '<strong>Please fix the following errors:</strong><ul>';
                        for (const error in result.errors) {
                            errorHtml += `<li>${result.errors[error]}</li>`;
                        }
                        errorHtml += '</ul>';
                        document.getElementById('updateUserFormErrors').innerHTML = errorHtml;
                        document.getElementById('updateUserFormErrors').style.display = 'block';
                    }
                }
            } catch (error) {
                console.error('Error updating user:', error);
                showMessage('Network error while updating user', 'error', 'updateUserMessage');
            }
        });

        function showMessage(message, type, elementId = 'message') {
            const messageDiv = document.getElementById(elementId);
            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            messageDiv.style.display = 'block';
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        }