// Farmer Dashboard Specific Functionality

const API_BASE = (window.APP_ROOT || '') + '/products';

document.addEventListener('DOMContentLoaded', function() {
  initializeFarmerNavigation();
  initializeFarmerForms();
  loadFarmerProducts();
});

// Load products from backend
function loadFarmerProducts() {
  fetch(`${API_BASE}/farmerList`, { credentials: 'include' })
    .then(r => r.json())
    .then(res => {
      if (res.success) populateProductsTable(res.products);
      else showNotification(res.error || 'Failed to load', 'error');
    })
    .catch(() => showNotification('Failed to load products', 'error'));
}

// Submit add product
let addProductBound = false;

function initializeFarmerForms() {
    const addProductForm = document.getElementById('addProductForm');
    if (!addProductForm || addProductBound) return;

    addProductForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        console.log('Add Product button clicked');

        const fd = new FormData(addProductForm);
        const url = `${API_BASE}/create`;
        console.log('POST:', url);

        try {
            const r = await fetch(url, { method: 'POST', body: fd, credentials: 'include' });
            const raw = await r.text();
            let res;
            try { res = JSON.parse(raw); } catch {
                console.error('Non-JSON response:', raw);
                throw new Error(r.status + ' ' + r.statusText + ' (non-JSON)');
            }
            if (!r.ok || !res.success) {
                throw new Error(res.error || ('HTTP ' + r.status));
            }

            showNotification('Product added', 'success');
            closeModal('addProductModal');
            addProductForm.reset();
            loadFarmerProducts();
        } catch (err) {
            console.error('Add product failed:', err);
            showNotification('Failed to add: ' + err.message, 'error');
        }
    });

    addProductBound = true;
}

// Initialize Navigation
function initializeFarmerNavigation() {
    // Menu navigation
    document.querySelectorAll('.menu-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            showSection(section);
        });
    });
}

// Section Navigation
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show selected section
    const targetSection = document.getElementById(sectionId + '-section');
    if (targetSection) {
        targetSection.style.display = 'block';
    }
    
    // Update active menu link
    document.querySelectorAll('.menu-link').forEach(link => {
        link.classList.remove('active');
        if (link.dataset.section === sectionId) {
            link.classList.add('active');
        }
    });
}

// Initialize Dashboard with Mock Data
function initializeFarmerDashboard() {
    // Mock farmer data
    const farmerData = {
        name: 'John Farmer',
        email: 'john@farm.com',
        phone: '+94 77 123 4567',
        totalProducts: 12,
        pendingOrders: 5,
        monthlyEarnings: 45230,
        totalEarnings: 342560,
        products: [
            { id: 1, name: 'Tomatoes', category: 'Vegetables', price: 120, stock: 50, status: 'Available', harvestDate: '2025-10-15' },
            { id: 2, name: 'Carrots', category: 'Vegetables', price: 80, stock: 30, status: 'Available', harvestDate: '2025-10-12' },
            { id: 3, name: 'Rice', category: 'Grains', price: 150, stock: 100, status: 'Available', harvestDate: '2025-09-20' }
        ],
        orders: [
            { id: 1001, buyer: 'Restaurant ABC', products: 'Tomatoes (10kg)', total: 1200, status: 'pending', date: '2025-10-18' },
            { id: 1002, buyer: 'Market XYZ', products: 'Carrots (5kg)', total: 400, status: 'shipped', date: '2025-10-17' },
            { id: 1003, buyer: 'Hotel DEF', products: 'Rice (20kg)', total: 3000, status: 'delivered', date: '2025-10-15' }
        ]
    };
    
    // Update dashboard stats
    const totalProductsEl = document.getElementById('totalProducts');
    const pendingOrdersEl = document.getElementById('pendingOrders');
    const monthlyEarningsEl = document.getElementById('monthlyEarnings');
    const totalEarningsEl = document.getElementById('totalEarnings');
    
    if (totalProductsEl) totalProductsEl.textContent = farmerData.totalProducts;
    if (pendingOrdersEl) pendingOrdersEl.textContent = farmerData.pendingOrders;
    if (monthlyEarningsEl) monthlyEarningsEl.textContent = `Rs. ${farmerData.monthlyEarnings.toLocaleString()}`;
    if (totalEarningsEl) totalEarningsEl.textContent = `Rs. ${farmerData.totalEarnings.toLocaleString()}`;
    
    // Update user info
    const farmerNameEl = document.getElementById('farmerName');
    const userAvatarEl = document.getElementById('userAvatar');
    
    if (farmerNameEl) farmerNameEl.textContent = farmerData.name;
    if (userAvatarEl) {
        const initials = farmerData.name.split(' ').map(n => n[0]).join('').toUpperCase();
        userAvatarEl.textContent = initials;
    }
    
    // Populate tables
    populateProductsTable(farmerData.products);
    populateOrdersTable(farmerData.orders);
    populateRecentOrders(farmerData.orders.slice(0, 3));
    
    // Set profile form values
    const profileNameEl = document.getElementById('profileName');
    const profileEmailEl = document.getElementById('profileEmail');
    const profilePhoneEl = document.getElementById('profilePhone');
    
    if (profileNameEl) profileNameEl.value = farmerData.name;
    if (profileEmailEl) profileEmailEl.value = farmerData.email;
    if (profilePhoneEl) profilePhoneEl.value = farmerData.phone;
}

// Render table (name, price, quantity, location)
function populateProductsTable(products) {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;

    // Clear existing rows
    tbody.innerHTML = '';

    // Populate table with products
    products.forEach(p => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${escapeHtml(p.name)}</td>
            <td>Rs. ${Number(p.price).toFixed(2)}</td>
            <td>${p.quantity}</td>
            <td>${p.location || '-'}</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-outline" onclick="editProduct(${p.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})">Delete</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Populate Orders Table
function populateOrdersTable(orders) {
    const tbody = document.getElementById('ordersTableBody');
    if (!tbody) return;
    
    tbody.innerHTML = orders.map(order => `
        <tr>
            <td>#${order.id}</td>
            <td>${order.buyer}</td>
            <td>${order.products}</td>
            <td>Rs. ${order.total}</td>
            <td><span class="order-status ${order.status}">${order.status}</span></td>
            <td>${order.date}</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder(${order.id})">View</button>
                <button class="btn btn-sm btn-secondary" onclick="updateOrderStatus(${order.id})">Update</button>
            </td>
        </tr>
    `).join('');
}

// Populate Recent Orders
function populateRecentOrders(orders) {
    const container = document.getElementById('recentOrders');
    if (!container) return;
    
    container.innerHTML = orders.map(order => `
        <div class="order-card">
            <div class="order-header">
                <div class="order-title">Order #${order.id}</div>
                <span class="order-status ${order.status}">${order.status}</span>
            </div>
            <div class="order-details">
                <div class="order-detail">
                    <div class="order-detail-label">Buyer</div>
                    <div class="order-detail-value">${order.buyer}</div>
                </div>
                <div class="order-detail">
                    <div class="order-detail-label">Products</div>
                    <div class="order-detail-value">${order.products}</div>
                </div>
                <div class="order-detail">
                    <div class="order-detail-label">Total</div>
                    <div class="order-detail-value">Rs. ${order.total}</div>
                </div>
                <div class="order-detail">
                    <div class="order-detail-label">Date</div>
                    <div class="order-detail-value">${order.date}</div>
                </div>
            </div>
        </div>
    `).join('');
}

// Product Actions
function editProduct(id) {
  // Example: open an edit modal (not included here)
  showNotification(`Edit product #${id} (hook up an edit modal)`, 'info');
}

function deleteProduct(id) {
  if (!confirm('Delete this product?')) return;
  fetch(`${API_BASE}/delete/${id}`, {
    method: 'POST',
    credentials: 'include'
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      showNotification('Product deleted', 'success');
      loadFarmerProducts();
    } else showNotification(res.error || 'Failed to delete', 'error');
  })
  .catch(() => showNotification('Failed to delete product', 'error'));
}

// Order Actions
function viewOrder(id) {
    console.log('View order:', id);
    showNotification(`View order #${id} details (Demo)`, 'info');
}

function updateOrderStatus(id) {
    console.log('Update order status:', id);
    showNotification(`Update order #${id} status (Demo)`, 'info');
}

// utilities
function escapeHtml(str){ return String(str ?? '').replace(/[&<>"']/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[s])); }

// Export functions
window.showSection = showSection;
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;
window.viewOrder = viewOrder;
window.updateOrderStatus = updateOrderStatus;