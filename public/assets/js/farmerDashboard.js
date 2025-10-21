// Farmer Dashboard Specific Functionality

const API_BASE = (window.APP_ROOT || '') + '/products';

document.addEventListener('DOMContentLoaded', function() {
  initializeFarmerNavigation();
  initializeFarmerForms();
  loadFarmerProducts();
  loadDummyDashboardData();
  loadDummyOrdersData();
  loadDummyEarningsData();
  loadDummyDeliveriesData();
  loadDummyAnalyticsData();
  loadProfileData();
  loadCropRequestsData();
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

    // Set minimum date to today
    const listingDateInput = document.getElementById('listingDate');
    if (listingDateInput) {
        const today = new Date().toISOString().split('T')[0];
        listingDateInput.setAttribute('min', today);
        listingDateInput.value = today;
    }

    // Set minimum quantity to 10kg
    const quantityInput = document.getElementById('productQuantity');
    if (quantityInput) {
        quantityInput.setAttribute('min', '10');
        quantityInput.setAttribute('step', '1');
    }

    addProductForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validate quantity minimum 10kg
        const quantity = quantityInput ? parseInt(quantityInput.value) : 0;
        if (quantity < 10) {
            showNotification('Minimum quantity is 10kg', 'error');
            quantityInput.style.borderColor = '#ef5350';
            quantityInput.style.background = '#ffebee';
            return;
        }

        const fd = new FormData(addProductForm);
        const url = `${API_BASE}/create`;

        // Show loading state
        const submitBtn = addProductForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Adding...';

        try {
            const r = await fetch(url, { 
                method: 'POST', 
                body: fd, 
                credentials: 'include' 
            });
            
            const raw = await r.text();
            let res;
            
            try { 
                res = JSON.parse(raw);
            } catch (parseError) {
                throw new Error(r.status + ' ' + r.statusText + ' (non-JSON response)');
            }
            
            if (!r.ok || !res.success) {
                // Display validation errors from server
                if (res.errors) {
                    let errorMessages = [];
                    
                    // Clear previous error styling
                    addProductForm.querySelectorAll('.form-control').forEach(input => {
                        input.style.borderColor = '';
                        input.style.background = '';
                    });
                    
                    // Highlight error fields and collect messages
                    for (const [field, error] of Object.entries(res.errors)) {
                        errorMessages.push(error);
                        
                        // Map field names to input IDs
                        const fieldMap = {
                            'category': 'productCategory',
                            'name': 'productName',
                            'price': 'productPrice',
                            'quantity': 'productQuantity',
                            'location': 'productLocation',
                            'listing_date': 'listingDate',
                            'description': 'productDescription',
                            'image': 'productImage'
                        };
                        
                        const inputId = fieldMap[field];
                        const input = document.getElementById(inputId);
                        if (input) {
                            input.style.borderColor = '#ef5350';
                            input.style.background = '#ffebee';
                        }
                    }
                    
                    showNotification('Validation errors:\n' + errorMessages.join('\n'), 'error');
                } else {
                    throw new Error(res.error || ('HTTP ' + r.status));
                }
                return;
            }

            showNotification('Product added successfully', 'success');
            closeModal('addProductModal');
            addProductForm.reset();
            
            // Reset listing date to today
            if (listingDateInput) {
                const today = new Date().toISOString().split('T')[0];
                listingDateInput.value = today;
            }
            
            // Clear any error styling
            addProductForm.querySelectorAll('.form-control').forEach(input => {
                input.style.borderColor = '';
                input.style.background = '';
            });
            
            loadFarmerProducts();
        } catch (err) {
            showNotification('Failed to add product: ' + err.message, 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Clear error styling on input change
    addProductForm.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            this.style.background = '';
        });
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
    // Map overview to dashboard
    if (sectionId === 'overview') sectionId = 'dashboard';
    
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
        const key = link.dataset.section;
        if (key === sectionId || (sectionId === 'dashboard' && key === 'overview')) {
            link.classList.add('active');
        }
    });
}

// Load Dashboard Dummy Data
function loadDummyDashboardData() {
    // Update dashboard stats
    const totalProductsEl = document.getElementById('totalProducts');
    const pendingOrdersEl = document.getElementById('pendingOrders');
    const monthlyEarningsEl = document.getElementById('monthlyEarnings');
    const totalEarningsEl = document.getElementById('totalEarnings');
    
    if (totalProductsEl) totalProductsEl.textContent = '24';
    if (pendingOrdersEl) pendingOrdersEl.textContent = '15';
    if (monthlyEarningsEl) monthlyEarningsEl.textContent = 'Rs. 145,650';
    if (totalEarningsEl) totalEarningsEl.textContent = 'Rs. 842,560';
    
    // Recent Orders
    const recentOrdersEl = document.getElementById('recentOrders');
    if (recentOrdersEl) {
        recentOrdersEl.innerHTML = `
            <div style="margin-bottom: 15px; padding: 15px; border-left: 4px solid #4CAF50; background: #f9f9f9;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong>Order #F2001</strong>
                    <span style="color: #f59e0b; font-weight: bold;">Pending</span>
                </div>
                <div style="color: #666; font-size: 0.9rem;">Green Leaf Restaurant - 50kg Tomatoes</div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <span style="font-weight: bold;">Rs. 6,000</span>
                    <span style="color: #666; font-size: 0.9rem;">Oct 20, 2025</span>
                </div>
            </div>
            <div style="margin-bottom: 15px; padding: 15px; border-left: 4px solid #3b82f6; background: #f9f9f9;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong>Order #F2002</strong>
                    <span style="color: #3b82f6; font-weight: bold;">Processing</span>
                </div>
                <div style="color: #666; font-size: 0.9rem;">Fresh Mart - 100kg Red Rice</div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <span style="font-weight: bold;">Rs. 9,500</span>
                    <span style="color: #666; font-size: 0.9rem;">Oct 19, 2025</span>
                </div>
            </div>
            <div style="margin-bottom: 15px; padding: 15px; border-left: 4px solid #10b981; background: #f9f9f9;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong>Order #F2003</strong>
                    <span style="color: #10b981; font-weight: bold;">Delivered</span>
                </div>
                <div style="color: #666; font-size: 0.9rem;">Paradise Hotel - 80kg Mangoes</div>
                <div style="display: flex; justify-content: space-between; margin-top: 5px;">
                    <span style="font-weight: bold;">Rs. 12,000</span>
                    <span style="color: #666; font-size: 0.9rem;">Oct 18, 2025</span>
                </div>
            </div>
        `;
    }
    
    // Top Products
    const topProductsEl = document.getElementById('topProducts');
    if (topProductsEl) {
        topProductsEl.innerHTML = `
            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 40px; height: 40px; background: #ffebee; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🍅</div>
                    <div>
                        <div style="font-weight: bold;">Fresh Tomatoes</div>
                        <div style="color: #666; font-size: 0.9rem;">Rs. 120/kg</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: bold; color: #4CAF50;">Rs. 36,000</div>
                    <div style="color: #666; font-size: 0.9rem;">300kg sold</div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 40px; height: 40px; background: #fff8e1; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🌾</div>
                    <div>
                        <div style="font-weight: bold;">Red Rice</div>
                        <div style="color: #666; font-size: 0.9rem;">Rs. 95/kg</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: bold; color: #4CAF50;">Rs. 28,500</div>
                    <div style="color: #666; font-size: 0.9rem;">300kg sold</div>
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 15px 0;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 40px; height: 40px; background: #fff3e0; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🥭</div>
                    <div>
                        <div style="font-weight: bold;">Sweet Mangoes</div>
                        <div style="color: #666; font-size: 0.9rem;">Rs. 150/kg</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: bold; color: #4CAF50;">Rs. 22,500</div>
                    <div style="color: #666; font-size: 0.9rem;">150kg sold</div>
                </div>
            </div>
        `;
    }
}

// Load Dummy Orders Data
function loadDummyOrdersData() {
    const ordersTableBody = document.getElementById('ordersTableBody');
    if (!ordersTableBody) return;
    
    ordersTableBody.innerHTML = `
        <tr>
            <td>#F2001</td>
            <td>Green Leaf Restaurant</td>
            <td>50kg Fresh Tomatoes</td>
            <td>Rs. 6,000</td>
            <td><span style="color: #f59e0b; font-weight: bold;">Pending</span></td>
            <td>Oct 20, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder('F2001')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="markAsReady('F2001')">Mark Ready</button>
            </td>
        </tr>
        <tr>
            <td>#F2002</td>
            <td>Fresh Mart Supermarket</td>
            <td>100kg Red Rice</td>
            <td>Rs. 9,500</td>
            <td><span style="color: #3b82f6; font-weight: bold;">Ready</span></td>
            <td>Oct 19, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder('F2002')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="trackOrder('F2002')">Track</button>
            </td>
        </tr>
        <tr>
            <td>#F2003</td>
            <td>Paradise Hotel</td>
            <td>80kg Sweet Mangoes</td>
            <td>Rs. 12,000</td>
            <td><span style="color: #10b981; font-weight: bold;">Completed</span></td>
            <td>Oct 18, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder('F2003')">View</button>
            </td>
        </tr>
        <tr>
            <td>#F2004</td>
            <td>City Grocers</td>
            <td>150kg Carrots</td>
            <td>Rs. 11,250</td>
            <td><span style="color: #10b981; font-weight: bold;">Completed</span></td>
            <td>Oct 17, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder('F2004')">View</button>
            </td>
        </tr>
        <tr>
            <td>#F2005</td>
            <td>Green Market</td>
            <td>200kg Potatoes</td>
            <td>Rs. 18,000</td>
            <td><span style="color: #3b82f6; font-weight: bold;">In Transit</span></td>
            <td>Oct 16, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder('F2005')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="trackOrder('F2005')">Track</button>
            </td>
        </tr>
    `;
}

// Load Dummy Earnings Data
function loadDummyEarningsData() {
    const todayEarningsEl = document.getElementById('todayEarnings');
    const weekEarningsEl = document.getElementById('weekEarnings');
    const monthEarningsDetailEl = document.getElementById('monthEarningsDetail');
    const yearEarningsEl = document.getElementById('yearEarnings');
    
    if (todayEarningsEl) todayEarningsEl.textContent = 'Rs. 12,250';
    if (weekEarningsEl) weekEarningsEl.textContent = 'Rs. 58,450';
    if (monthEarningsDetailEl) monthEarningsDetailEl.textContent = 'Rs. 145,650';
    if (yearEarningsEl) yearEarningsEl.textContent = 'Rs. 842,560';
}

// Load Dummy Deliveries Data
function loadDummyDeliveriesData() {
    const pendingDeliveriesEl = document.getElementById('pendingDeliveries');
    const inTransitDeliveriesEl = document.getElementById('inTransitDeliveries');
    const completedDeliveriesEl = document.getElementById('completedDeliveries');
    const avgDeliveryTimeEl = document.getElementById('avgDeliveryTime');
    
    if (pendingDeliveriesEl) pendingDeliveriesEl.textContent = '8';
    if (inTransitDeliveriesEl) inTransitDeliveriesEl.textContent = '5';
    if (completedDeliveriesEl) completedDeliveriesEl.textContent = '142';
    if (avgDeliveryTimeEl) avgDeliveryTimeEl.textContent = '2.3 days';
}

// Load Dummy Analytics Data
function loadDummyAnalyticsData() {
    const totalSalesEl = document.getElementById('totalSales');
    const avgRatingEl = document.getElementById('avgRating');
    const repeatCustomersEl = document.getElementById('repeatCustomers');
    const conversionRateEl = document.getElementById('conversionRate');
    
    if (totalSalesEl) totalSalesEl.textContent = '12,450kg';
    if (avgRatingEl) avgRatingEl.textContent = '4.8';
    if (repeatCustomersEl) repeatCustomersEl.textContent = '34';
    if (conversionRateEl) conversionRateEl.textContent = '78%';
}

// Load Crop Requests Data
function loadCropRequestsData() {
    const cropRequestsContainer = document.getElementById('cropRequestsContainer');
    if (!cropRequestsContainer) return;
    
    cropRequestsContainer.innerHTML = `
        <div style="margin-bottom: 20px; padding: 20px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #4CAF50;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <strong style="font-size: 1.1rem;">Request #CR001</strong>
                <span style="color: #4CAF50; font-weight: bold;">Active</span>
            </div>
            <div style="margin: 10px 0;">
                <div style="color: #666; margin-bottom: 5px;"><strong>Buyer:</strong> Fresh Mart Supermarket</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Crop Needed:</strong> Organic Tomatoes</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Quantity:</strong> 200kg</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Target Price:</strong> Rs. 130/kg</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Delivery By:</strong> Oct 25, 2025</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Location:</strong> Colombo</div>
            </div>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button class="btn btn-primary" onclick="acceptCropRequest('CR001')">Accept Request</button>
                <button class="btn btn-outline" onclick="viewCropRequestDetails('CR001')">View Details</button>
            </div>
        </div>

        <div style="margin-bottom: 20px; padding: 20px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <strong style="font-size: 1.1rem;">Request #CR002</strong>
                <span style="color: #3b82f6; font-weight: bold;">Active</span>
            </div>
            <div style="margin: 10px 0;">
                <div style="color: #666; margin-bottom: 5px;"><strong>Buyer:</strong> Green Leaf Restaurant</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Crop Needed:</strong> Fresh Spinach</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Quantity:</strong> 50kg</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Target Price:</strong> Rs. 80/kg</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Delivery By:</strong> Oct 23, 2025</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Location:</strong> Kandy</div>
            </div>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button class="btn btn-primary" onclick="acceptCropRequest('CR002')">Accept Request</button>
                <button class="btn btn-outline" onclick="viewCropRequestDetails('CR002')">View Details</button>
            </div>
        </div>

        <div style="margin-bottom: 20px; padding: 20px; background: #f9f9f9; border-radius: 8px; border-left: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <strong style="font-size: 1.1rem;">Request #CR003</strong>
                <span style="color: #f59e0b; font-weight: bold;">Urgent</span>
            </div>
            <div style="margin: 10px 0;">
                <div style="color: #666; margin-bottom: 5px;"><strong>Buyer:</strong> Paradise Hotel</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Crop Needed:</strong> Red Onions</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Quantity:</strong> 100kg</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Target Price:</strong> Rs. 90/kg</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Delivery By:</strong> Oct 22, 2025</div>
                <div style="color: #666; margin-bottom: 5px;"><strong>Location:</strong> Galle</div>
            </div>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button class="btn btn-primary" onclick="acceptCropRequest('CR003')">Accept Request</button>
                <button class="btn btn-outline" onclick="viewCropRequestDetails('CR003')">View Details</button>
            </div>
        </div>
    `;
}

// Load Profile Data
function loadProfileData() {
    const profilePhotoEl = document.getElementById('profilePhoto');
    const profileNameEl = document.getElementById('profileName');
    const profileEmailEl = document.getElementById('profileEmail');
    const profilePhoneEl = document.getElementById('profilePhone');
    const profileLocationEl = document.getElementById('profileLocation');
    const profileCropsEl = document.getElementById('profileCrops');
    const profileAddressEl = document.getElementById('profileAddress');
    
    // Use avatar placeholder service instead of missing image
    if (profilePhotoEl) {
        profilePhotoEl.src = 'https://ui-avatars.com/api/?name=Farmer&background=4CAF50&color=fff&size=150';
        profilePhotoEl.alt = 'Farmer Profile';
    }
    
    if (profileNameEl) profileNameEl.value = 'Ranjith Fernando';
    if (profileEmailEl) profileEmailEl.value = 'ranjith@farm.lk';
    if (profilePhoneEl) profilePhoneEl.value = '+94 77 234 5678';
    if (profileLocationEl) profileLocationEl.value = 'Matale, Central Province';
    if (profileCropsEl) profileCropsEl.value = 'Tomatoes, Rice, Mangoes, Carrots, Potatoes';
    if (profileAddressEl) profileAddressEl.value = '456 Farm Road, Matale, Central Province, Sri Lanka';
}

// Populate table with EXACT database columns
function populateProductsTable(products) {
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    if (!products || products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No products listed yet</td></tr>';
        return;
    }

    products.forEach(p => {
        const row = document.createElement('tr');
        
        // Format dates
        const listingDate = p.listing_date ? new Date(p.listing_date).toLocaleDateString() : '-';
        
        // Category names
        const categoryNames = {
            'vegetables': 'Vegetables', 'fruits': 'Fruits', 'cereals': 'Cereals',
            'yams': 'Yams', 'legumes': 'Legumes', 'spices': 'Spices',
            'leafy': 'Leafy', 'other': 'Other'
        };
        const categoryDisplay = categoryNames[p.category] || 'Other';
        
        // Truncate description
        const description = p.description ? 
            (p.description.length > 50 ? p.description.substring(0, 50) + '...' : p.description) : 
            '-';
        
        row.innerHTML = `
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    ${p.image ? 
                        `<img src="${window.APP_ROOT || ''}/assets/images/products/${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 2px solid #E8F5E9;">` : 
                        `<div style="width: 50px; height: 50px; background: #E8F5E9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #43A047; font-weight: bold; font-size: 1.2rem;">${p.name ? p.name.charAt(0).toUpperCase() : '?'}</div>`
                    }
                    <div style="font-weight: 600;">${escapeHtml(p.name)}</div>
                </div>
            </td>
            <td><span style="padding: 4px 10px; background: #E8F5E9; border-radius: 12px; font-size: 0.85rem; color: #2E7D32;">${categoryDisplay}</span></td>
            <td style="font-weight: 600;">Rs. ${Number(p.price).toFixed(2)}</td>
            <td>${p.quantity} kg</td>
            <td style="color: #555;">${escapeHtml(p.location) || '-'}</td>
            <td style="font-size: 0.85rem; color: #666; max-width: 200px;" title="${escapeHtml(p.description || '')}">${escapeHtml(description)}</td>
            <td style="font-size: 0.9rem; color: #666;">${listingDate}</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-outline" onclick="editProduct(${p.id})" style="margin-right: 5px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    Edit
                </button>
                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Delete
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Product Actions
function editProduct(id) {
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
    showNotification(`Viewing order ${id}`, 'info');
}

function markAsReady(id) {
    showNotification(`Order ${id} marked as ready for pickup`, 'success');
    setTimeout(() => loadDummyOrdersData(), 1000);
}

function trackOrder(id) {
    showNotification(`Tracking order ${id}`, 'info');
}

// Crop Request Actions
function acceptCropRequest(id) {
    showNotification(`Crop request ${id} accepted successfully!`, 'success');
    setTimeout(() => loadCropRequestsData(), 1000);
}

function viewCropRequestDetails(id) {
    showNotification(`Viewing crop request ${id} details`, 'info');
}

// Profile form handlers
function updateProfile() {
    const name = document.getElementById('profileName').value;
    const email = document.getElementById('profileEmail').value;
    const phone = document.getElementById('profilePhone').value;
    const location = document.getElementById('profileLocation').value;
    const crops = document.getElementById('profileCrops').value;
    const address = document.getElementById('profileAddress').value;

    if (!name || !email || !phone || !location || !crops || !address) {
        showNotification('Please fill all required fields', 'error');
        return;
    }

    showNotification('Profile updated successfully!', 'success');
}

function uploadPhoto() {
    let input = document.getElementById('photoUploadInput');
    if (!input) {
        input = document.createElement('input');
        input.type = 'file';
        input.id = 'photoUploadInput';
        input.accept = 'image/*';
        input.style.display = 'none';
        document.body.appendChild(input);
    }

    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                showNotification('Please select a valid image file', 'error');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                showNotification('Image size should be less than 5MB', 'error');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const profilePhoto = document.getElementById('profilePhoto');
                if (profilePhoto) {
                    profilePhoto.src = e.target.result;
                    showNotification('Photo uploaded successfully!', 'success');
                }
            };
            reader.onerror = function() {
                showNotification('Failed to read image file', 'error');
            };
            reader.readAsDataURL(file);
        }
    };

    input.click();
}

// utilities
function escapeHtml(str){ return String(str ?? '').replace(/[&<>"']/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[s])); }

// Export functions
window.showSection = showSection;
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;
window.viewOrder = viewOrder;
window.markAsReady = markAsReady;
window.trackOrder = trackOrder;
window.acceptCropRequest = acceptCropRequest;
window.viewCropRequestDetails = viewCropRequestDetails;
window.updateProfile = updateProfile;
window.uploadPhoto = uploadPhoto;