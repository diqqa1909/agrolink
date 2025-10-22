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
  loadReviewsData();
});

// Load products from backend
function loadFarmerProducts() {
  const url = `${API_BASE}/farmerList`;
  
  fetch(url, { credentials: 'include' })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        populateProductsTable(res.products);
      } else {
        showNotification(res.error || 'Failed to load', 'error');
      }
    })
    .catch(err => {
      showNotification('Failed to load products', 'error');
    });
}

// Submit add product
let addProductBound = false;
let editProductBound = false;

function initializeFarmerForms() {
    console.log('=== INITIALIZING FARMER FORMS ===');
    
    const addProductForm = document.getElementById('addProductForm');
    console.log('Add product form found:', !!addProductForm);
    console.log('Add product bound:', addProductBound);
    
    if (!addProductForm || addProductBound) return;

    // Set minimum date to today
    const listingDateInput = document.getElementById('listingDate');
    if (listingDateInput) {
        const today = new Date().toISOString().split('T')[0];
        listingDateInput.setAttribute('min', today);
        listingDateInput.value = today;
        console.log('Listing date initialized to:', today);
    }

    // Set minimum quantity to 10kg
    const quantityInput = document.getElementById('productQuantity');
    if (quantityInput) {
        quantityInput.setAttribute('min', '10');
        quantityInput.setAttribute('step', '1');
        console.log('Quantity minimum set to 10kg');
    }

    addProductForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        
        console.log('=== ADD PRODUCT FORM SUBMITTED ===');

        // Validate quantity minimum 10kg
        const quantity = quantityInput ? parseInt(quantityInput.value) : 0;
        console.log('Quantity entered:', quantity);
        
        if (quantity < 10) {
            console.warn('Quantity validation failed: less than 10kg');
            showNotification('Minimum quantity is 10kg', 'error');
            quantityInput.style.borderColor = '#ef5350';
            quantityInput.style.background = '#ffebee';
            return;
        }

        const fd = new FormData(addProductForm);
        
        // Log form data
        console.log('Form data being sent:');
        for (let [key, value] of fd.entries()) {
            console.log(`  ${key}:`, value instanceof File ? `[File: ${value.name}]` : value);
        }
        
        const url = `${API_BASE}/create`;
        console.log('Posting to URL:', url);

        // Show loading state
        const submitBtn = addProductForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Adding...';

        try {
            console.log('Sending POST request...');
            const r = await fetch(url, { 
                method: 'POST', 
                body: fd, 
                credentials: 'include' 
            });
            
            console.log('Response received:', r.status, r.statusText);
            console.log('Response headers:', Array.from(r.headers.entries()));
            
            const raw = await r.text();
            console.log('Raw response text:', raw.substring(0, 500)); // First 500 chars
            
            let res;
            
            try { 
                res = JSON.parse(raw);
                console.log('Parsed JSON response:', res);
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                console.error('Raw response that failed to parse:', raw);
                throw new Error(r.status + ' ' + r.statusText + ' (non-JSON response)');
            }
            
            if (!r.ok || !res.success) {
                console.warn('Request failed:', res);
                
                // Display validation errors from server
                if (res.errors) {
                    console.log('Validation errors:', res.errors);
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

            console.log('Product added successfully!');
            showNotification('✅ Product added successfully! Your product is now listed.', 'success');
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
            
            console.log('Reloading products...');
            loadFarmerProducts();
        } catch (err) {
            console.error('Add product error:', err);
            console.error('Error stack:', err.stack);
            showNotification('Failed to add product: ' + err.message, 'error');
        } finally {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            console.log('Form submission complete');
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
    console.log('Add product form bound successfully');

    // Initialize Edit Product Form
    const editProductForm = document.getElementById('editProductForm');
    console.log('Edit product form found:', !!editProductForm);
    console.log('Edit product bound:', editProductBound);
    
    if (editProductForm && !editProductBound) {
        editProductForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            
            console.log('=== EDIT PRODUCT FORM SUBMITTED ===');

            const productId = document.getElementById('editProductId').value;
            console.log('Product ID:', productId);
            
            if (!productId) {
                console.error('Product ID is missing');
                showNotification('Product ID is missing', 'error');
                return;
            }

            const fd = new FormData(editProductForm);
            
            // Log form data
            console.log('Edit form data being sent:');
            for (let [key, value] of fd.entries()) {
                console.log(`  ${key}:`, value instanceof File ? `[File: ${value.name}]` : value);
            }
            
            const url = `${API_BASE}/update/${productId}`;
            console.log('Posting to URL:', url);

            const submitBtn = editProductForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '💾 Saving...';

            try {
                console.log('Sending UPDATE request...');
                const r = await fetch(url, { 
                    method: 'POST', 
                    body: fd, 
                    credentials: 'include' 
                });
                
                console.log('Update response:', r.status, r.statusText);
                console.log('Update response headers:', Array.from(r.headers.entries()));
                
                const raw = await r.text();
                console.log('Update raw response:', raw.substring(0, 500));
                
                let res;
                
                try { 
                    res = JSON.parse(raw);
                    console.log('Update parsed response:', res);
                } catch (parseError) {
                    console.error('Update JSON Parse Error:', parseError);
                    console.error('Raw that failed:', raw);
                    throw new Error(r.status + ' ' + r.statusText + ' (non-JSON response)');
                }
                
                if (!r.ok || !res.success) {
                    console.warn('Update failed:', res);
                    
                    if (res.errors) {
                        console.log('Update validation errors:', res.errors);
                        let errorMessages = [];
                        
                        editProductForm.querySelectorAll('.form-control').forEach(input => {
                            input.style.borderColor = '';
                            input.style.background = '';
                        });
                        
                        for (const [field, error] of Object.entries(res.errors)) {
                            errorMessages.push(error);
                            
                            const fieldMap = {
                                'category': 'editProductCategory',
                                'name': 'editProductName',
                                'price': 'editProductPrice',
                                'quantity': 'editProductQuantity',
                                'location': 'editProductLocation',
                                'listing_date': 'editListingDate',
                                'description': 'editProductDescription',
                                'image': 'editProductImage'
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

                console.log('Product updated successfully!');
                showNotification('Product updated successfully! 🎉', 'success');
                closeModal('editProductModal');
                editProductForm.reset();
                document.getElementById('currentImagePreview').innerHTML = '';
                
                editProductForm.querySelectorAll('.form-control').forEach(input => {
                    input.style.borderColor = '';
                    input.style.background = '';
                });
                
                console.log('Reloading products after update...');
                loadFarmerProducts();
            } catch (err) {
                console.error('Update product error:', err);
                console.error('Error stack:', err.stack);
                showNotification('Failed to update product: ' + err.message, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                console.log('Edit form submission complete');
            }
        });

        editProductForm.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                this.style.background = '';
            });
        });

        editProductBound = true;
        console.log('Edit product form bound successfully');
    }
}

// Initialize Navigation
function initializeFarmerNavigation() {
    console.log('=== INITIALIZING NAVIGATION ===');
    
    // Menu navigation
    const menuLinks = document.querySelectorAll('.menu-link');
    console.log('Menu links found:', menuLinks.length);
    
    menuLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            console.log('Navigation clicked:', section);
            showSection(section);
        });
    });
}

// Section Navigation
function showSection(sectionId) {
    console.log('=== SHOWING SECTION:', sectionId, '===');
    
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
        console.log('Section shown:', sectionId);
    } else {
        console.error('Section not found:', sectionId + '-section');
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
    
    // Load detailed earnings table
    loadEarningsTable();
}

// Load Earnings Table
function loadEarningsTable() {
    const earningsTableBody = document.getElementById('earningsTableBody');
    if (!earningsTableBody) return;
    
    earningsTableBody.innerHTML = `
        <tr>
            <td>Oct 22, 2025</td>
            <td>#F2001</td>
            <td>Green Leaf Restaurant</td>
            <td>50kg Fresh Tomatoes</td>
            <td>Rs. 6,000</td>
            <td>Rs. 300</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 5,700</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 21, 2025</td>
            <td>#F2009</td>
            <td>Wellness Cafe</td>
            <td>85kg Sweet Corn</td>
            <td>Rs. 8,500</td>
            <td>Rs. 425</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 8,075</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 20, 2025</td>
            <td>#F2003</td>
            <td>Paradise Hotel</td>
            <td>80kg Sweet Mangoes</td>
            <td>Rs. 12,000</td>
            <td>Rs. 600</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 11,400</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 19, 2025</td>
            <td>#F2004</td>
            <td>City Grocers</td>
            <td>150kg Carrots</td>
            <td>Rs. 11,250</td>
            <td>Rs. 562</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 10,688</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 18, 2025</td>
            <td>#F2007</td>
            <td>Paradise Hotel</td>
            <td>75kg Fresh Spinach</td>
            <td>Rs. 6,750</td>
            <td>Rs. 338</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 6,412</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 17, 2025</td>
            <td>#F2008</td>
            <td>Healthy Foods Ltd</td>
            <td>150kg Carrots</td>
            <td>Rs. 13,500</td>
            <td>Rs. 675</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 12,825</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 16, 2025</td>
            <td>#F2005</td>
            <td>Green Market</td>
            <td>200kg Potatoes</td>
            <td>Rs. 18,000</td>
            <td>Rs. 900</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 17,100</td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending</span></td>
        </tr>
        <tr>
            <td>Oct 15, 2025</td>
            <td>#F2010</td>
            <td>Organic Shop</td>
            <td>90kg Green Beans</td>
            <td>Rs. 9,900</td>
            <td>Rs. 495</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 9,405</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 14, 2025</td>
            <td>#F2011</td>
            <td>Sunrise Restaurant</td>
            <td>60kg Cabbage</td>
            <td>Rs. 4,800</td>
            <td>Rs. 240</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 4,560</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 13, 2025</td>
            <td>#F2012</td>
            <td>Fresh Mart Supermarket</td>
            <td>100kg Red Rice</td>
            <td>Rs. 9,500</td>
            <td>Rs. 475</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 9,025</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 12, 2025</td>
            <td>#F2013</td>
            <td>Fresh Foods Market</td>
            <td>110kg Pumpkin</td>
            <td>Rs. 7,700</td>
            <td>Rs. 385</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 7,315</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 11, 2025</td>
            <td>#F2014</td>
            <td>Health Hub</td>
            <td>95kg Broccoli</td>
            <td>Rs. 14,250</td>
            <td>Rs. 712</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 13,538</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 10, 2025</td>
            <td>#F2015</td>
            <td>Green Leaf Restaurant</td>
            <td>70kg Lettuce</td>
            <td>Rs. 5,600</td>
            <td>Rs. 280</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 5,320</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
        <tr>
            <td>Oct 09, 2025</td>
            <td>#F2016</td>
            <td>City Grocers</td>
            <td>180kg White Onions</td>
            <td>Rs. 16,200</td>
            <td>Rs. 810</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 15,390</td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending</span></td>
        </tr>
        <tr>
            <td>Oct 08, 2025</td>
            <td>#F2017</td>
            <td>Paradise Hotel</td>
            <td>130kg Cherry Tomatoes</td>
            <td>Rs. 19,500</td>
            <td>Rs. 975</td>
            <td style="font-weight: bold; color: #4CAF50;">Rs. 18,525</td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Paid</span></td>
        </tr>
    `;
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
    
    // Load detailed deliveries table
    loadDeliveriesTable();
}

// Load Deliveries Table
function loadDeliveriesTable() {
    const deliveriesTableBody = document.getElementById('deliveriesTableBody');
    if (!deliveriesTableBody) return;
    
    deliveriesTableBody.innerHTML = `
        <tr>
            <td>#DEL001</td>
            <td>#F2001</td>
            <td>Green Leaf Restaurant</td>
            <td>50kg Fresh Tomatoes</td>
            <td>Matale → Colombo</td>
            <td>Transport Lanka</td>
            <td>Rohan Silva<br><small style="color: #666;">+94 77 234 5678</small></td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending Pickup</span></td>
            <td>Oct 20, 2025</td>
            <td>Oct 22, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL001')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL001')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL002</td>
            <td>#F2002</td>
            <td>Fresh Mart Supermarket</td>
            <td>100kg Red Rice</td>
            <td>Matale → Kandy</td>
            <td>Swift Logistics</td>
            <td>Kamal Fernando<br><small style="color: #666;">+94 71 345 6789</small></td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending Pickup</span></td>
            <td>Oct 19, 2025</td>
            <td>Oct 21, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL002')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL002')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL003</td>
            <td>#F2005</td>
            <td>City Grocers</td>
            <td>200kg Potatoes</td>
            <td>Matale → Galle</td>
            <td>Express Movers</td>
            <td>Nimal Perera<br><small style="color: #666;">+94 76 456 7890</small></td>
            <td><span style="color: #3b82f6; font-weight: bold; padding: 4px 10px; background: #eff6ff; border-radius: 12px;">In Transit</span></td>
            <td>Oct 16, 2025</td>
            <td>Oct 22, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL003')">Track</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL003')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL004</td>
            <td>#F2007</td>
            <td>Paradise Hotel</td>
            <td>75kg Fresh Spinach</td>
            <td>Matale → Negombo</td>
            <td>Fast Track Delivery</td>
            <td>Saman Kumara<br><small style="color: #666;">+94 75 567 8901</small></td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending Pickup</span></td>
            <td>Oct 21, 2025</td>
            <td>Oct 23, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL004')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL004')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL005</td>
            <td>#F2008</td>
            <td>Healthy Foods Ltd</td>
            <td>150kg Carrots</td>
            <td>Matale → Jaffna</td>
            <td>North Express</td>
            <td>Dharshan Kumar<br><small style="color: #666;">+94 72 678 9012</small></td>
            <td><span style="color: #3b82f6; font-weight: bold; padding: 4px 10px; background: #eff6ff; border-radius: 12px;">In Transit</span></td>
            <td>Oct 18, 2025</td>
            <td>Oct 23, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL005')">Track</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL005')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL006</td>
            <td>#F2009</td>
            <td>Green Market</td>
            <td>120kg Red Onions</td>
            <td>Matale → Anuradhapura</td>
            <td>Central Carriers</td>
            <td>Ajith Bandara<br><small style="color: #666;">+94 77 789 0123</small></td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending Pickup</span></td>
            <td>Oct 21, 2025</td>
            <td>Oct 24, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL006')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL006')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL007</td>
            <td>#F2010</td>
            <td>Organic Shop</td>
            <td>90kg Green Beans</td>
            <td>Matale → Trincomalee</td>
            <td>East Coast Transport</td>
            <td>Pradeep Silva<br><small style="color: #666;">+94 71 890 1234</small></td>
            <td><span style="color: #3b82f6; font-weight: bold; padding: 4px 10px; background: #eff6ff; border-radius: 12px;">In Transit</span></td>
            <td>Oct 17, 2025</td>
            <td>Oct 22, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL007')">Track</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL007')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL008</td>
            <td>#F2011</td>
            <td>Sunrise Restaurant</td>
            <td>60kg Cabbage</td>
            <td>Matale → Badulla</td>
            <td>Hill Country Movers</td>
            <td>Lalith Rathnayake<br><small style="color: #666;">+94 76 901 2345</small></td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending Pickup</span></td>
            <td>Oct 22, 2025</td>
            <td>Oct 24, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL008')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL008')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL009</td>
            <td>#F2012</td>
            <td>Wellness Cafe</td>
            <td>85kg Sweet Corn</td>
            <td>Matale → Kurunegala</td>
            <td>Quick Move Services</td>
            <td>Ruwan Jayasinghe<br><small style="color: #666;">+94 75 012 3456</small></td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending Pickup</span></td>
            <td>Oct 21, 2025</td>
            <td>Oct 23, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL009')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL009')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL010</td>
            <td>#F2003</td>
            <td>Paradise Hotel</td>
            <td>80kg Sweet Mangoes</td>
            <td>Matale → Galle</td>
            <td>Coastal Express</td>
            <td>Chaminda Perera<br><small style="color: #666;">+94 77 123 4567</small></td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Delivered</span></td>
            <td>Oct 18, 2025</td>
            <td>Oct 20, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL010')">View</button>
                <button class="btn btn-sm btn-outline" onclick="downloadPOD('DEL010')">POD</button>
            </td>
        </tr>
        <tr>
            <td>#DEL011</td>
            <td>#F2004</td>
            <td>City Grocers</td>
            <td>150kg Carrots</td>
            <td>Matale → Colombo</td>
            <td>Metro Transport</td>
            <td>Asanka Fernando<br><small style="color: #666;">+94 71 234 5678</small></td>
            <td><span style="color: #10b981; font-weight: bold; padding: 4px 10px; background: #ecfdf5; border-radius: 12px;">Delivered</span></td>
            <td>Oct 17, 2025</td>
            <td>Oct 19, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL011')">View</button>
                <button class="btn btn-sm btn-outline" onclick="downloadPOD('DEL011')">POD</button>
            </td>
        </tr>
        <tr>
            <td>#DEL012</td>
            <td>#F2013</td>
            <td>Fresh Foods Market</td>
            <td>110kg Pumpkin</td>
            <td>Matale → Ratnapura</td>
            <td>Gem City Logistics</td>
            <td>Upul Dissanayake<br><small style="color: #666;">+94 76 345 6789</small></td>
            <td><span style="color: #3b82f6; font-weight: bold; padding: 4px 10px; background: #eff6ff; border-radius: 12px;">In Transit</span></td>
            <td>Oct 19, 2025</td>
            <td>Oct 23, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL012')">Track</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL012')">Contact</button>
            </td>
        </tr>
        <tr>
            <td>#DEL013</td>
            <td>#F2014</td>
            <td>Health Hub</td>
            <td>95kg Broccoli</td>
            <td>Matale → Kandy</td>
            <td>Swift Logistics</td>
            <td>Mahesh Wijesinghe<br><small style="color: #666;">+94 72 456 7890</small></td>
            <td><span style="color: #f59e0b; font-weight: bold; padding: 4px 10px; background: #fff7ed; border-radius: 12px;">Pending Pickup</span></td>
            <td>Oct 22, 2025</td>
            <td>Oct 24, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewDeliveryDetails('DEL013')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="contactTransporter('DEL013')">Contact</button>
            </td>
        </tr>
    `;
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
                <button class="btn btn-danger" onclick="rejectCropRequest('CR001')">Reject</button>
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
                <button class="btn btn-danger" onclick="rejectCropRequest('CR002')">Reject</button>
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
                <button class="btn btn-danger" onclick="rejectCropRequest('CR003')">Reject</button>
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
    console.log('=== POPULATING PRODUCTS TABLE ===');
    console.log('Products to display:', products?.length || 0);
    
    const tbody = document.getElementById('productsTableBody');
    if (!tbody) {
        console.error('productsTableBody element not found!');
        return;
    }

    tbody.innerHTML = '';

    if (!products || products.length === 0) {
        console.log('No products to display');
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #999;">No products listed yet</td></tr>';
        return;
    }

    console.log('Products data:', products);

    products.forEach((p, index) => {
        console.log(`Product ${index + 1}:`, p);
        
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
        
        row.innerHTML = `
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    ${p.image ? 
                        `<img src="${window.APP_ROOT || ''}/assets/images/products/${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 2px solid #E8F5E9;">` : 
                        `<div style="width: 50px; height: 50px; background: linear-gradient(135deg, #E8F5E9, #C8E6C9); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #2E7D32; font-weight: bold; font-size: 1.5rem;">${p.name ? p.name.charAt(0).toUpperCase() : '?'}</div>`
                    }
                    <div style="font-weight: 600;">${escapeHtml(p.name)}</div>
                </div>
            </td>
            <td><span style="padding: 4px 10px; background: #E8F5E9; border-radius: 12px; font-size: 0.85rem; color: #2E7D32;">${categoryDisplay}</span></td>
            <td style="font-weight: 600;">Rs. ${Number(p.price).toFixed(2)}</td>
            <td>${p.quantity} kg</td>
            <td style="color: #555;">${escapeHtml(p.location) || '-'}</td>
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
    
    console.log('Table populated successfully');
}

// Product Actions
function editProduct(id) {
    console.log('=== EDIT PRODUCT CLICKED ===');
    console.log('Product ID:', id);
    
    const url = `${API_BASE}/edit/${id}`;
    console.log('Fetching product data from:', url);
    
    fetch(url, {
        method: 'GET',
        credentials: 'include'
    })
    .then(r => {
        console.log('Edit fetch response:', r.status, r.statusText);
        return r.json();
    })
    .then(res => {
        console.log('Edit fetch data:', res);
        
        if (!res.success) {
            throw new Error(res.error || 'Failed to load product');
        }

        const product = res.product;
        console.log('Product to edit:', product);
        
        // Populate form
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editProductName').value = product.name || '';
        document.getElementById('editProductCategory').value = product.category || 'other';
        document.getElementById('editProductPrice').value = product.price || '';
        document.getElementById('editProductQuantity').value = product.quantity || '';
        document.getElementById('editProductLocation').value = product.location || '';
        document.getElementById('editListingDate').value = product.listing_date || '';
        document.getElementById('editProductDescription').value = product.description || '';
        
        console.log('Form populated with product data');
        
        // Show current image
        const preview = document.getElementById('currentImagePreview');
        if (preview) {
            if (product.image) {
                preview.innerHTML = `
                    <div style="text-align: center; margin-top: 10px;">
                        <p style="margin-bottom: 5px; color: #666; font-size: 0.9rem;">Current Image:</p>
                        <img src="${window.APP_ROOT}/assets/images/products/${product.image}" 
                             alt="Current product" 
                             style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #E8F5E9;">
                        <p style="margin-top: 5px; color: #666; font-size: 0.85rem;">Upload a new image to replace</p>
                    </div>
                `;
            } else {
                preview.innerHTML = '<p style="color: #999; font-size: 0.9rem; margin-top: 10px;">No image uploaded</p>';
            }
        }
        
        // Open modal
        console.log('Opening edit modal');
        openModal('editProductModal');
        
    })
    .catch(err => {
        console.error('Edit product error:', err);
        console.error('Error stack:', err.stack);
        showNotification('Failed to load product: ' + err.message, 'error');
    });
}

function deleteProduct(id) {
  console.log('=== DELETE PRODUCT CLICKED ===');
  console.log('Product ID:', id);
  
  if (!confirm('Delete this product?')) {
    console.log('Delete cancelled by user');
    return;
  }
  
  const url = `${API_BASE}/delete/${id}`;
  console.log('Deleting from URL:', url);
  
  fetch(url, {
    method: 'POST',
    credentials: 'include'
  })
  .then(r => {
    console.log('Delete response:', r.status, r.statusText);
    return r.json();
  })
  .then(res => {
    console.log('Delete response data:', res);
    
    if (res.success) {
      console.log('Product deleted successfully');
      showNotification('Product deleted', 'success');
      loadFarmerProducts();
    } else {
      console.error('Delete failed:', res.error);
      showNotification(res.error || 'Failed to delete', 'error');
    }
  })
  .catch(err => {
    console.error('Delete error:', err);
    showNotification('Failed to delete product', 'error');
  });
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

function rejectCropRequest(id) {
    if (confirm(`Are you sure you want to reject crop request ${id}?`)) {
        showNotification(`Crop request ${id} has been rejected`, 'info');
        setTimeout(() => loadCropRequestsData(), 1000);
    }
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

// Delivery Actions
function viewDeliveryDetails(id) {
    showNotification(`Viewing delivery ${id} details`, 'info');
}

function contactTransporter(id) {
    showNotification(`Contacting transporter for delivery ${id}`, 'info');
}

function downloadPOD(id) {
    showNotification(`Downloading Proof of Delivery for ${id}`, 'info');
}

// Load Reviews Data
function loadReviewsData() {
    const reviewsContainer = document.getElementById('reviewsContainer');
    if (!reviewsContainer) return;

    const reviews = [
        {
            id: 'REV001',
            buyer: 'Sunflower Restaurant',
            product: 'Organic Tomatoes',
            rating: 5,
            date: 'Oct 21, 2025',
            comment: 'Absolutely fresh and high quality tomatoes! Perfect for our restaurant. The packaging was excellent and delivery was on time. Will definitely order again.',
            orderAmount: 'Rs. 6,500',
            verified: true
        },
        {
            id: 'REV002',
            buyer: 'Samantha Perera',
            product: 'Fresh Carrots',
            rating: 5,
            date: 'Oct 20, 2025',
            comment: 'Best carrots I\'ve bought! Very fresh, crispy, and sweet. Great quality for the price. Highly recommend this farmer!',
            orderAmount: 'Rs. 2,400',
            verified: true
        },
        {
            id: 'REV003',
            buyer: 'Green Valley Hotel',
            product: 'Red Onions',
            rating: 4,
            date: 'Oct 18, 2025',
            comment: 'Good quality onions. Slight delay in delivery but product quality made up for it. Would order again.',
            orderAmount: 'Rs. 4,500',
            verified: true
        },
        {
            id: 'REV004',
            buyer: 'Nimal Fernando',
            product: 'Basmati Rice',
            rating: 5,
            date: 'Oct 17, 2025',
            comment: 'Excellent quality rice! Very aromatic and cooks perfectly. Packaging was secure and delivery was prompt. Thank you!',
            orderAmount: 'Rs. 8,500',
            verified: true
        },
        {
            id: 'REV005',
            buyer: 'Lakeside Cafe',
            product: 'Sweet Corn',
            rating: 5,
            date: 'Oct 16, 2025',
            comment: 'Super fresh corn! Our customers loved the sweet corn soup we made. Will be a regular customer for sure.',
            orderAmount: 'Rs. 3,200',
            verified: true
        },
        {
            id: 'REV006',
            buyer: 'Priya Jayawardena',
            product: 'Fresh Cabbage',
            rating: 4,
            date: 'Oct 15, 2025',
            comment: 'Very fresh and good size cabbages. One was slightly damaged but overall satisfied with the purchase.',
            orderAmount: 'Rs. 1,800',
            verified: true
        },
        {
            id: 'REV007',
            buyer: 'Paradise Hotel',
            product: 'Green Beans',
            rating: 5,
            date: 'Oct 14, 2025',
            comment: 'Outstanding quality! Very tender and fresh green beans. Perfect for our hotel\'s menu. Professional service.',
            orderAmount: 'Rs. 5,400',
            verified: true
        },
        {
            id: 'REV008',
            buyer: 'Kamal Silva',
            product: 'Potatoes',
            rating: 5,
            date: 'Oct 13, 2025',
            comment: 'Great quality potatoes at a reasonable price. Clean, uniform size, and fresh. Fast delivery too!',
            orderAmount: 'Rs. 3,600',
            verified: true
        },
        {
            id: 'REV009',
            buyer: 'Royal Banquet Hall',
            product: 'Fresh Spinach',
            rating: 4,
            date: 'Oct 11, 2025',
            comment: 'Good quality leafy greens. Very fresh but could have been packed better to avoid bruising.',
            orderAmount: 'Rs. 2,100',
            verified: true
        },
        {
            id: 'REV010',
            buyer: 'Dilani Wijesinghe',
            product: 'Cucumber',
            rating: 5,
            date: 'Oct 10, 2025',
            comment: 'Crispy, fresh cucumbers! Perfect for salads. Excellent farmer to deal with. Very responsive and helpful.',
            orderAmount: 'Rs. 1,500',
            verified: true
        },
        {
            id: 'REV011',
            buyer: 'Spice Garden Restaurant',
            product: 'Bell Peppers',
            rating: 5,
            date: 'Oct 08, 2025',
            comment: 'Vibrant, fresh bell peppers! Different colors were all fresh and crunchy. Great for our stir-fries.',
            orderAmount: 'Rs. 4,800',
            verified: true
        },
        {
            id: 'REV012',
            buyer: 'Ranjith Kumar',
            product: 'Fresh Lettuce',
            rating: 4,
            date: 'Oct 06, 2025',
            comment: 'Fresh lettuce, good quality. Would appreciate if it came pre-washed but overall happy with the product.',
            orderAmount: 'Rs. 1,200',
            verified: true
        },
        {
            id: 'REV013',
            buyer: 'Ocean View Hotel',
            product: 'Cherry Tomatoes',
            rating: 5,
            date: 'Oct 05, 2025',
            comment: 'Beautiful cherry tomatoes! Sweet and fresh, perfect for our salad bar. Consistently high quality.',
            orderAmount: 'Rs. 6,200',
            verified: true
        },
        {
            id: 'REV014',
            buyer: 'Chamari Rathnayake',
            product: 'Broccoli',
            rating: 5,
            date: 'Oct 03, 2025',
            comment: 'Very fresh broccoli! Nice green color and firm texture. Kids loved it. Will order again soon!',
            orderAmount: 'Rs. 2,800',
            verified: true
        },
        {
            id: 'REV015',
            buyer: 'Mountain Top Restaurant',
            product: 'Organic Eggplant',
            rating: 5,
            date: 'Oct 01, 2025',
            comment: 'Premium quality eggplants! Glossy, firm, and fresh. Makes the best curry. Highly professional farmer.',
            orderAmount: 'Rs. 3,900',
            verified: true
        }
    ];

    let html = '<div class="reviews-list">';
    
    reviews.forEach(review => {
        const stars = '⭐'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
        const verifiedBadge = review.verified ? '<span class="verified-badge">✓ Verified Purchase</span>' : '';
        
        html += `
            <div class="review-card">
                <div class="review-header">
                    <div class="review-buyer-info">
                        <div class="buyer-avatar">${review.buyer.charAt(0)}</div>
                        <div>
                            <h4 class="buyer-name">${escapeHtml(review.buyer)}</h4>
                            <p class="review-meta">
                                <span class="review-date">${review.date}</span>
                                ${verifiedBadge}
                            </p>
                        </div>
                    </div>
                    <div class="review-rating">
                        <div class="stars">${stars}</div>
                        <span class="rating-number">${review.rating}.0</span>
                    </div>
                </div>
                <div class="review-product-info">
                    <strong>Product:</strong> ${escapeHtml(review.product)} 
                    <span class="order-amount">(${review.orderAmount})</span>
                </div>
                <div class="review-comment">
                    ${escapeHtml(review.comment)}
                </div>
                <div class="review-actions">
                    <button class="btn-link" onclick="respondToReview('${review.id}')">
                        💬 Respond
                    </button>
                    <button class="btn-link" onclick="reportReview('${review.id}')">
                        🚩 Report
                    </button>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    reviewsContainer.innerHTML = html;
}

// Review Actions
function respondToReview(id) {
    showNotification(`Opening response form for review ${id}`, 'info');
}

function reportReview(id) {
    if (confirm('Are you sure you want to report this review?')) {
        showNotification(`Review ${id} has been reported for moderation`, 'success');
    }
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
window.rejectCropRequest = rejectCropRequest;
window.viewCropRequestDetails = viewCropRequestDetails;
window.updateProfile = updateProfile;
window.uploadPhoto = uploadPhoto;
window.viewDeliveryDetails = viewDeliveryDetails;
window.contactTransporter = contactTransporter;
window.downloadPOD = downloadPOD;
window.respondToReview = respondToReview;
window.reportReview = reportReview;