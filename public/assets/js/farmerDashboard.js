// Farmer Dashboard Specific Functionality

const API_BASE = (window.APP_ROOT || '') + '/farmerproducts';

document.addEventListener('DOMContentLoaded', function() {
  initializeFarmerNavigation();
  initializeFarmerForms();
    initializeEditForm();
  loadFarmerProducts();
  loadDummyDashboardData();
  loadDummyOrdersData();
  loadDummyEarningsData();
    loadEarningsDetailsData();
  loadDummyDeliveriesData();
    loadPendingDeliveriesData();
    initializeDeliveriesFilters();
  loadDummyAnalyticsData();
  loadProfileData();
    loadDummyFeedbackData();
});
// Edit product form
let editProductBound = false;
function initializeEditForm(){
    const form = document.getElementById('editProductForm');
    if (!form || editProductBound) return;

    // basic numeric constraints
    const qty = document.getElementById('editProductQuantity');
    if (qty){ qty.setAttribute('min','1'); qty.setAttribute('step','1'); }

    // set min date to today
    const editDate = document.getElementById('editListingDate');
    if (editDate){
        const today = new Date().toISOString().split('T')[0];
        editDate.setAttribute('min', today);
    }

    // image preview
    const imageInput = document.getElementById('editProductImage');
    const imagePreview = document.getElementById('editImagePreview');
    const previewImg = document.getElementById('editPreviewImg');
    if (imageInput && imagePreview && previewImg){
        imageInput.addEventListener('change', (e)=>{
            const file = e.target.files[0];
            if (!file){ imagePreview.style.display='none'; return; }
            const allowedTypes = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
            if (!allowedTypes.includes(file.type)){
                showNotification('Please select a valid image file (JPG, PNG, GIF, or WebP)', 'error');
                imageInput.value=''; imagePreview.style.display='none'; return;
            }
            if (file.size > 5*1024*1024){
                showNotification('Image size must be less than 5MB', 'error');
                imageInput.value=''; imagePreview.style.display='none'; return;
            }
            const reader = new FileReader();
            reader.onload = ev => { previewImg.src = ev.target.result; imagePreview.style.display='block'; };
            reader.readAsDataURL(file);
        });
    }

    form.addEventListener('submit', async function(e){
        e.preventDefault();
        const id = document.getElementById('editProductId').value;
        const name = document.getElementById('editProductName').value.trim();
        const category = document.getElementById('editProductCategory').value;
        const price = document.getElementById('editProductPrice').value;
        const quantity = document.getElementById('editProductQuantity').value;
        const location = document.getElementById('editProductLocation').value.trim();
        const listing_date = document.getElementById('editListingDate').value;

        if (!name || price === '' || quantity === '' || !location || !category || !listing_date){
            showNotification('Please fill all required fields', 'error');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const original = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Saving...';

        try{
            const fd = new FormData();
            fd.append('name', name);
            fd.append('category', category);
            fd.append('price', price);
            fd.append('quantity', quantity);
            fd.append('location', location);
            fd.append('listing_date', listing_date);
            if (imageInput && imageInput.files && imageInput.files[0]){
                fd.append('image', imageInput.files[0]);
            }
            const r = await fetch(`${API_BASE}/update/${id}`, { method:'POST', body: fd, credentials: 'include' });
            const res = await r.json();
            if (!r.ok || !res.success){
                const msg = res?.error || 'Failed to update product';
                showNotification(msg, 'error');
                return;
            }
            showNotification('Product updated', 'success');
            closeModal('editProductModal');
            if (imageInput){ imageInput.value=''; if (imagePreview){ imagePreview.style.display='none'; } }
            loadFarmerProducts();
        }catch(err){
            showNotification('Failed to update product: ' + err.message, 'error');
        }finally{
            submitBtn.disabled = false;
            submitBtn.innerHTML = original;
        }
    });

    // Clear errors on input
    form.querySelectorAll('.form-control').forEach(inp => inp.addEventListener('input', ()=>{
        inp.style.borderColor=''; inp.style.background='';
    }));

    editProductBound = true;
}

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

    // Image preview functionality
    const imageInput = document.getElementById('productImage');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (imageInput && imagePreview && previewImg) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showNotification('Please select a valid image file (JPG, PNG, GIF, or WebP)', 'error');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }
                
                // Validate file size (max 5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    showNotification('Image size must be less than 5MB', 'error');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
                
                // Clear error styling
                imageInput.style.borderColor = '';
                imageInput.style.background = '';
            } else {
                imagePreview.style.display = 'none';
            }
        });

        // Clear preview when modal is closed via Cancel or overlay
        const closeBtns = document.querySelectorAll('#addProductModal [data-modal-close]');
        closeBtns.forEach(btn => btn.addEventListener('click', () => {
            imageInput.value = '';
            previewImg.src = '';
            imagePreview.style.display = 'none';
            imageInput.style.borderColor = '';
            imageInput.style.background = '';
        }));
    }

    addProductForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validate image is required
        const imageInput = document.getElementById('productImage');
        if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
            showNotification('Product image is required. Please upload an image.', 'error');
            if (imageInput) {
                imageInput.style.borderColor = '#ef5350';
                imageInput.style.background = '#ffebee';
            }
            return;
        }

        // Validate image file type
        const file = imageInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Please upload a valid image file (JPG, PNG, GIF, or WebP)', 'error');
            imageInput.style.borderColor = '#ef5350';
            imageInput.style.background = '#ffebee';
            return;
        }

        // Validate image file size (max 5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        if (file.size > maxSize) {
            showNotification('Image file size must be less than 5MB', 'error');
            imageInput.style.borderColor = '#ef5350';
            imageInput.style.background = '#ffebee';
            return;
        }

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
            // Reset form and preview, then close modal
            addProductForm.reset();
            const imgInput = document.getElementById('productImage');
            const imgPreviewWrap = document.getElementById('imagePreview');
            const imgPreview = document.getElementById('previewImg');
            if (imgInput) {
                imgInput.value = '';
                imgInput.style.borderColor = '';
                imgInput.style.background = '';
            }
            if (imgPreviewWrap) imgPreviewWrap.style.display = 'none';
            if (imgPreview) imgPreview.src = '';
            closeModal('addProductModal');
            
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
            const section = this.dataset.section;
            const href = this.getAttribute('href');
            
            // Only prevent default if it's an internal section link
            if (section) {
                e.preventDefault();
                showSection(section);
            } else {
                // Allow default navigation for external links like /farmerprofile
            }
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
        <tr>
            <td>#F2006</td>
            <td>Royal Dine</td>
            <td>60kg Green Beans</td>
            <td>Rs. 7,200</td>
            <td><span style="color: #f59e0b; font-weight: bold;">Pending</span></td>
            <td>Oct 15, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder('F2006')">View</button>
                <button class="btn btn-sm btn-secondary" onclick="markAsReady('F2006')">Mark Ready</button>
            </td>
        </tr>
        <tr>
            <td>#F2007</td>
            <td>Harvest Hub</td>
            <td>120kg Potatoes</td>
            <td>Rs. 10,800</td>
            <td><span style="color: #3b82f6; font-weight: bold;">Ready</span></td>
            <td>Oct 14, 2025</td>
            <td class="action-buttons">
                <button class="btn btn-sm btn-primary" onclick="viewOrder('F2007')">View</button>
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

// Detailed Earnings Table Data
function loadEarningsDetailsData() {
    const tbody = document.getElementById('earningsTableBody');
    if (!tbody) return;

    const rows = [
        { date: 'Oct 20, 2025', order: 'F2003', item: 'Sweet Mangoes', qty: 80, price: 150.00, status: 'Settled' },
        { date: 'Oct 19, 2025', order: 'F2002', item: 'Red Rice', qty: 100, price: 95.00, status: 'Settled' },
        { date: 'Oct 18, 2025', order: 'F2004', item: 'Carrots', qty: 150, price: 75.00, status: 'Settled' },
        { date: 'Oct 17, 2025', order: 'F2005', item: 'Potatoes', qty: 200, price: 90.00, status: 'Processing' },
        { date: 'Oct 16, 2025', order: 'F1999', item: 'Tomatoes', qty: 120, price: 120.00, status: 'Settled' },
        { date: 'Oct 15, 2025', order: 'F1996', item: 'Green Beans', qty: 60, price: 120.00, status: 'Settled' },
        { date: 'Oct 14, 2025', order: 'F1992', item: 'Onions', qty: 90, price: 110.00, status: 'Settled' },
        { date: 'Oct 13, 2025', order: 'F1988', item: 'Leafy Greens', qty: 40, price: 80.00, status: 'Settled' },
    ];

    const fmt = (n) => Number(n).toFixed(2);
    tbody.innerHTML = rows.map(r => {
        const gross = r.qty * r.price;
        const fee = gross * 0.05;
        const net = gross - fee;
        const statusClass = r.status === 'Settled' ? 'color:#10b981;' : 'color:#3b82f6;';
        return `
            <tr>
                <td>${r.date}</td>
                <td>${r.order}</td>
                <td>${escapeHtml(r.item)}</td>
                <td>${r.qty}</td>
                <td>Rs. ${fmt(gross)}</td>
                <td>Rs. ${fmt(fee)}</td>
                <td><strong>Rs. ${fmt(net)}</strong></td>
                <td><span style="${statusClass}">${r.status}</span></td>
            </tr>
        `;
    }).join('');
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

// Detailed Pending Deliveries List
let _pendingDeliveriesData = [];

function renderPendingDeliveriesList(items) {
    const list = document.getElementById('deliveriesList');
    if (!list) return;
    
    if (items.length === 0) {
        list.innerHTML = `
            <div class="delivery-empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h13v10H3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 10h4l3 3v4h-7z"/>
                    <circle stroke-linecap="round" stroke-linejoin="round" cx="7.5" cy="17.5" r="1.5"/>
                    <circle stroke-linecap="round" stroke-linejoin="round" cx="18.5" cy="17.5" r="1.5"/>
                </svg>
                <h3>No deliveries found</h3>
                <p>No matching deliveries at this time</p>
            </div>
        `;
        return;
    }
    
    list.innerHTML = items.map(i => {
        const statusMap = {
            'Out for pickup': 'awaiting',
            'Pending assignment': 'awaiting',
            'Awaiting pickup': 'awaiting',
            'In transit': 'in-transit',
            'Scheduled': 'ready'
        };
        const statusClass = statusMap[i.status] || 'awaiting';
        
        // Parse products from order
        const products = i.products || [
            { name: 'Fresh Tomatoes', qty: '50 kg' },
            { name: 'Red Rice', qty: '100 kg' }
        ];
        
        return `
        <div class="delivery-item">
            <div class="delivery-header">
                <div class="delivery-main-info">
                    <div class="delivery-order-id">Delivery ${i.id}</div>
                    <div class="delivery-buyer">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        ${escapeHtml(i.buyer)}
                    </div>
                </div>
                <span class="delivery-status-badge ${statusClass}">${escapeHtml(i.status)}</span>
            </div>
            
            <div class="delivery-body">
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Order ID</span>
                    <span class="delivery-info-value highlight">#${escapeHtml(i.order)}</span>
                </div>
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Route</span>
                    <span class="delivery-info-value">${escapeHtml(i.route)}</span>
                </div>
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Driver</span>
                    <span class="delivery-info-value">${escapeHtml(i.driver)}</span>
                </div>
                <div class="delivery-info-block">
                    <span class="delivery-info-label">Contact</span>
                    <span class="delivery-info-value">${escapeHtml(i.contact)}</span>
                </div>
            </div>
            
            <div class="delivery-product-list">
                ${products.map(p => `
                    <div class="delivery-product-item">
                        <span class="delivery-product-name">${escapeHtml(p.name)}</span>
                        <span class="delivery-product-qty">${escapeHtml(p.qty)}</span>
                    </div>
                `).join('')}
            </div>
            
            <div class="delivery-footer">
                <div class="delivery-actions">
                    <button class="btn btn-outline" onclick="viewDeliveryDetails('${i.id}')">View Details</button>
                    <button class="btn btn-primary" onclick="trackDelivery('${i.id}')">Track</button>
                </div>
            </div>
        </div>
        `;
    }).join('');
}

function loadPendingDeliveriesData() {
    _pendingDeliveriesData = [
        { id: 'D-3101', order: 'F2002', buyer: 'Fresh Mart Supermarket', route: 'Matale → Kandy', driver: 'Nuwan', contact: '+94 76 123 4567', status: 'Out for pickup' },
        { id: 'D-3102', order: 'F2001', buyer: 'Green Leaf Restaurant', route: 'Matale → Colombo', driver: 'Kasun', contact: '+94 71 555 1234', status: 'Pending assignment' },
        { id: 'D-3103', order: 'F2005', buyer: 'Green Market', route: 'Matale → Gampaha', driver: 'Pradeep', contact: '+94 77 987 6543', status: 'In transit' },
        { id: 'D-3104', order: 'F2006', buyer: 'Royal Dine', route: 'Matale → Kurunegala', driver: 'Saman', contact: '+94 75 222 3344', status: 'Awaiting pickup' },
        { id: 'D-3105', order: 'F2007', buyer: 'Harvest Hub', route: 'Matale → Colombo', driver: 'Ruwan', contact: '+94 70 444 7788', status: 'Scheduled' },
    ];
    applyDeliveriesFilters();
}

function initializeDeliveriesFilters() {
    const search = document.getElementById('deliveriesSearch');
    const sort = document.getElementById('deliveriesSort');
    if (search) search.addEventListener('input', applyDeliveriesFilters);
    if (sort) sort.addEventListener('change', applyDeliveriesFilters);
}

function applyDeliveriesFilters() {
    const search = (document.getElementById('deliveriesSearch')?.value || '').toLowerCase();
    const sort = document.getElementById('deliveriesSort')?.value || 'status';
    let items = _pendingDeliveriesData.slice();

    // Filter by buyer or order id
    if (search) {
        items = items.filter(i => 
            i.buyer.toLowerCase().includes(search) ||
            i.order.toLowerCase().includes(search) ||
            i.id.toLowerCase().includes(search)
        );
    }

    // Sort
    if (sort === 'status') {
        items.sort((a,b) => a.status.localeCompare(b.status));
    }

    renderPendingDeliveriesList(items);
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


// Reviews & Complaints
function loadDummyFeedbackData(){
    renderReviewsList([
        {buyer:'Green Leaf Restaurant', rating:5, comment:'Excellent quality tomatoes. Fresh and well packed!', product:'Tomatoes', date:'Oct 20, 2025', order:'F2003'},
        {buyer:'Fresh Mart Supermarket', rating:4, comment:'Rice was good, delivery was a bit late but acceptable.', product:'Red Rice', date:'Oct 19, 2025', order:'F2002'},
        {buyer:'Paradise Hotel', rating:3, comment:'Carrots were fine, a few pieces were small.', product:'Carrots', date:'Oct 18, 2025', order:'F2004'}
    ]);

    renderComplaintsList([
        {buyer:'City Grocers', status:'open', title:'Damaged packaging on arrival', message:'A few potato bags had torn packaging leading to minor spillage.', date:'Oct 17, 2025', order:'F2005'},
        {buyer:'Royal Dine', status:'in-progress', title:'Quantity short by 2kg', message:'Green beans delivery was short by 2kg from the ordered amount.', date:'Oct 16, 2025', order:'F2006'},
        {buyer:'Harvest Hub', status:'resolved', title:'Invoice discrepancy', message:'Price per kg mismatched the agreed quote. Resolved with a credit note.', date:'Oct 14, 2025', order:'F2007'}
    ]);
}

function renderReviewsList(items){
    const wrap = document.getElementById('reviewsList');
    if (!wrap) return;
    if (!items || items.length === 0){
        wrap.innerHTML = `<div class="empty-state">No reviews yet</div>`;
        return;
    }
    const star = (filled)=>`
        <svg width="16" height="16" viewBox="0 0 24 24" fill="${filled ? '#FFC107' : 'none'}" stroke="${filled ? '#FFC107' : '#BDBDBD'}" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
        </svg>`;
    wrap.innerHTML = items.map(r=>{
        const stars = new Array(5).fill(0).map((_,i)=>star(i < r.rating)).join('');
        const initials = r.buyer.split(' ').map(p=>p[0]).slice(0,2).join('').toUpperCase();
        return `
        <div class="review-card">
            <div class="review-header">
                <div class="buyer-avatar" aria-label="${escapeHtml(r.buyer)}">${initials}</div>
                <div class="review-meta">
                    <div class="buyer-name">${escapeHtml(r.buyer)}</div>
                    <div class="review-sub">${escapeHtml(r.date)} • Order ${escapeHtml(r.order)}</div>
                </div>
                <div class="rating-stars" title="${r.rating} out of 5">${stars}</div>
            </div>
            <div class="review-body">${escapeHtml(r.comment)}</div>
            <div class="review-footer">
                <span class="feedback-badge positive">Positive</span>
                <span class="review-product">${escapeHtml(r.product)}</span>
            </div>
        </div>`;
    }).join('');
}

function renderComplaintsList(items){
    const wrap = document.getElementById('complaintsList');
    if (!wrap) return;
    if (!items || items.length === 0){
        wrap.innerHTML = `<div class="empty-state">No complaints 🎉</div>`;
        return;
    }
    wrap.innerHTML = items.map(c=>{
        const initials = c.buyer.split(' ').map(p=>p[0]).slice(0,2).join('').toUpperCase();
        return `
        <div class="complaint-card">
            <div class="complaint-header">
                <div class="buyer-avatar alt">${initials}</div>
                <div class="complaint-meta">
                    <div class="complaint-title">${escapeHtml(c.title)}</div>
                    <div class="complaint-sub">${escapeHtml(c.buyer)} • ${escapeHtml(c.date)} • Order ${escapeHtml(c.order)}</div>
                </div>
                <span class="complaint-status ${c.status}">${c.status.replace('-', ' ')}</span>
            </div>
            <div class="complaint-body">${escapeHtml(c.message)}</div>
        </div>`;
    }).join('');
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

    // Set name/email from logged-in user when available
    const uname = (window.USER_NAME || '').trim();
    const uemail = (window.USER_EMAIL || '').trim();
    if (profileNameEl) profileNameEl.value = uname || profileNameEl.value || 'Ranjith Fernando';
    if (profileEmailEl) profileEmailEl.value = uemail || profileEmailEl.value || 'ranjith@farm.lk';
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
        
        row.innerHTML = `
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    ${p.image ? 
                        `<img src="${window.APP_ROOT || ''}/assets/images/products/${escapeHtml(p.image)}" alt="${escapeHtml(p.name)}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 2px solid #E8F5E9;">` : 
                        `<img src="${window.APP_ROOT || ''}/assets/images/default-product.svg" alt="${escapeHtml(p.name)}" style="width: 50px; height: 50px; border-radius: 8px; object-fit: cover; border: 2px solid #E8F5E9; opacity: 0.5;">`
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
}

// Product Actions
async function editProduct(id) {
    try{
        const r = await fetch(`${API_BASE}/show/${id}`, { credentials:'include' });
        const res = await r.json();
        if (!r.ok || !res.success || !res.product){
            showNotification(res.error || 'Failed to load product', 'error');
            return;
        }
        const p = res.product;
        document.getElementById('editProductId').value = p.id;
        document.getElementById('editProductName').value = p.name || '';
            const catSel = document.getElementById('editProductCategory');
            if (catSel) catSel.value = (p.category || 'other');
        document.getElementById('editProductPrice').value = p.price ?? '';
        document.getElementById('editProductQuantity').value = p.quantity ?? '';
        document.getElementById('editProductLocation').value = p.location || '';
            const d = p.listing_date ? new Date(p.listing_date) : null;
            const iso = d && !isNaN(d) ? d.toISOString().split('T')[0] : '';
            const editDate = document.getElementById('editListingDate');
            if (editDate) editDate.value = iso;
        openModal('editProductModal');
    }catch(err){
        showNotification('Failed to open edit form: ' + err.message, 'error');
    }
}

function deleteProduct(id) {
  if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) return;
  
  // Show loading notification
  showNotification('Deleting product...', 'info');
  
  fetch(`${API_BASE}/delete/${id}`, {
    method: 'POST',
    credentials: 'include'
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      showNotification('Product deleted successfully', 'success');
      loadFarmerProducts();
    } else {
      showNotification(res.error || 'Failed to delete product', 'error');
    }
  })
  .catch(err => {
    console.error('Delete error:', err);
    showNotification('Failed to delete product. Please try again.', 'error');
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

// Delivery action functions
function viewDeliveryDetails(deliveryId) {
    showNotification(`Viewing details for delivery ${deliveryId}`, 'info');
    // TODO: Implement delivery details modal
}

function trackDelivery(deliveryId) {
    showNotification(`Tracking delivery ${deliveryId}`, 'info');
    // TODO: Implement delivery tracking modal/page
}

// ==================== PROFILE MANAGEMENT ====================

/**
 * Load profile data from server and populate form
 */
function loadProfileData() {
    const form = document.getElementById('profileForm');
    if (!form) return;

    // Call JSON endpoint so DB values persist across sessions
    fetch(`${window.APP_ROOT}/farmerprofile?ajax=1`, {
        credentials: 'include',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => {
        if (!r.ok) throw new Error('Failed to fetch profile');
        return r.json();
    })
    .then(res => {
        if (res.success && res.profile) {
            const profile = res.profile;
            
            // Populate form fields
            document.getElementById('profilePhone').value = profile.phone || '';
            document.getElementById('profileDistrict').value = profile.district || '';
            document.getElementById('profileCrops').value = profile.crops_selling || '';
            document.getElementById('profileAddress').value = profile.full_address || '';
            
            // Update profile photo
            if (profile.profile_photo) {
                const photoUrl = `${window.APP_ROOT}/assets/images/farmer-profiles/${profile.profile_photo}`;
                document.getElementById('profilePhotoDisplay').src = photoUrl;
            }
            
            // Update statistics
            updateProfileStatistics();
        }
    })
    .catch(err => {
        console.log('Profile data loading info:', err.message);
    });
}

/**
 * Save profile data via AJAX
 */
function saveProfileData() {
    const form = document.getElementById('profileForm');
    if (!form) return;

    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => {
        el.classList.remove('show');
        el.textContent = '';
    });

    // Validate form
    const formData = new FormData(form);
    const data = {
        phone: formData.get('phone') || '',
        district: formData.get('district') || '',
        crops_selling: formData.get('crops_selling') || '',
        full_address: formData.get('full_address') || ''
    };

    // Show loading state
    const saveBtn = document.querySelector('.btn-save-profile');
    const originalText = saveBtn.textContent;
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';

    fetch(`${window.APP_ROOT}/farmerprofile/saveProfile`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        credentials: 'include',
        body: new URLSearchParams(data)
    })
    .then(r => r.json())
    .then(res => {
        saveBtn.disabled = false;
        saveBtn.textContent = originalText;

        if (res.success) {
            showNotification(res.message || 'Profile updated successfully', 'success');
            loadProfileData();
        } else {
            // Display validation errors
            if (res.errors && typeof res.errors === 'object') {
                Object.keys(res.errors).forEach(field => {
                    const errorEl = document.getElementById(`error-${field}`);
                    if (errorEl) {
                        errorEl.textContent = res.errors[field];
                        errorEl.classList.add('show');
                    }
                });
                showNotification('Please fix the errors below', 'error');
            } else {
                showNotification(res.error || 'Failed to update profile', 'error');
            }
        }
    })
    .catch(err => {
        saveBtn.disabled = false;
        saveBtn.textContent = originalText;
        showNotification('Error saving profile', 'error');
        console.error('Profile save error:', err);
    });
}

/**
 * Reset form to original values
 */
function resetProfileForm() {
    loadProfileData();
    document.querySelectorAll('.error-message').forEach(el => {
        el.classList.remove('show');
        el.textContent = '';
    });
    showNotification('Form reset to original values', 'info');
}

/**
 * Handle profile photo upload
 */
function initializeProfilePhotoUpload() {
    const photoInput = document.getElementById('profilePhotoInput');
    if (!photoInput) return;

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Please select a valid image file (JPG, PNG, or WebP)', 'error');
            photoInput.value = '';
            return;
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            showNotification('Image size must be less than 5MB', 'error');
            photoInput.value = '';
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(event) {
            const previewContainer = document.getElementById('photoPreviewContainer');
            const preview = document.getElementById('photoPreview');
            
            preview.src = event.target.result;
            previewContainer.style.display = 'block';
            
            // Auto-upload after preview
            uploadProfilePhoto(file);
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Upload profile photo to server
 */
function uploadProfilePhoto(file) {
    const formData = new FormData();
    formData.append('photo', file);

    fetch(`${window.APP_ROOT}/farmerprofile/uploadPhoto`, {
        method: 'POST',
        credentials: 'include',
        body: formData
    })
    .then(r => r.json())
    .then(res => {
        const previewContainer = document.getElementById('photoPreviewContainer');
        
        if (res.success) {
            // Update main photo display
            document.getElementById('profilePhotoDisplay').src = res.photoUrl;
            
            // Hide preview after successful upload
            setTimeout(() => {
                previewContainer.style.display = 'none';
                document.getElementById('profilePhotoInput').value = '';
            }, 1500);
            
            showNotification('Profile photo updated successfully', 'success');
        } else {
            showNotification(res.error || 'Failed to upload photo', 'error');
            previewContainer.style.display = 'none';
            document.getElementById('profilePhotoInput').value = '';
        }
    })
    .catch(err => {
        showNotification('Error uploading photo', 'error');
        console.error('Photo upload error:', err);
        document.getElementById('photoPreviewContainer').style.display = 'none';
        document.getElementById('profilePhotoInput').value = '';
    });
}

/**
 * Open change password modal
 */
/**
 * Change password modal helpers (use global openModal/closeModal)
 */
function openChangePasswordModal() {
    openModal('changePasswordModal');
    // Reset form
    const form = document.getElementById('changePasswordForm');
    if (form) {
        form.reset();
        // Clear errors
        form.querySelectorAll('.error-message').forEach(el => {
            el.classList.remove('show');
            el.textContent = '';
        });
    }
}

function closeChangePasswordModal() {
    closeModal('changePasswordModal');
}

/**
 * Handle password change form submission
 */
function initializePasswordChangeForm() {
    const form = document.getElementById('changePasswordForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('#changePasswordModal .error-message').forEach(el => {
            el.classList.remove('show');
            el.textContent = '';
        });

        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Changing...';

        const formData = new URLSearchParams({
            currentPassword,
            newPassword,
            confirmPassword
        });

        fetch(`${window.APP_ROOT}/farmerprofile/changePassword`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            credentials: 'include',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;

            if (res.success) {
                showNotification('Password changed successfully', 'success');
                closeChangePasswordModal();
                form.reset();
            } else {
                // Display validation errors
                if (res.errors && typeof res.errors === 'object') {
                    Object.keys(res.errors).forEach(field => {
                        const errorEl = document.getElementById(`error-${field}`);
                        if (errorEl) {
                            errorEl.textContent = res.errors[field];
                            errorEl.classList.add('show');
                        }
                    });
                } else {
                    showNotification(res.error || 'Failed to change password', 'error');
                }
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            showNotification('Error changing password', 'error');
            console.error('Password change error:', err);
        });
    });
}

/**
 * Update profile statistics
 */
function updateProfileStatistics() {
    // These will be populated with real data from database
    // For now, they get dummy data from loadDummyDashboardData
    
    // Get statistics from already loaded dashboard data
    const totalProducts = document.getElementById('totalProducts')?.textContent || '0';
    
    document.getElementById('statTotalProducts').textContent = totalProducts;
    document.getElementById('statTotalOrders').textContent = '0'; // TODO: Get from database
    document.getElementById('statTotalEarnings').textContent = 'Rs. 0'; // TODO: Get from database
}

/**
 * Initialize profile functionality on page load
 */
function initializeProfileFunctionality() {
    initializeProfilePhotoUpload();
    initializePasswordChangeForm();
    loadProfileData();
}

// Run profile initialization when page loads
document.addEventListener('DOMContentLoaded', initializeProfileFunctionality);

// Export functions
window.showSection = showSection;
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;
window.viewOrder = viewOrder;
window.markAsReady = markAsReady;
window.trackOrder = trackOrder;
window.updateProfile = updateProfile;
window.uploadPhoto = uploadPhoto;
window.viewDeliveryDetails = viewDeliveryDetails;
window.trackDelivery = trackDelivery;

// Profile functions
window.saveProfileData = saveProfileData;
window.resetProfileForm = resetProfileForm;
window.openChangePasswordModal = openChangePasswordModal;
window.closeChangePasswordModal = closeChangePasswordModal;
window.loadProfileData = loadProfileData;