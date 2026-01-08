// Farmer Products Management

const API_BASE = (window.APP_ROOT || '') + '/farmerproducts';

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

function initializeProductForms() {
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
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showNotification('Please select a valid image file (JPG, PNG, GIF, or WebP)', 'error');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }
                
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    showNotification('Image size must be less than 5MB', 'error');
                    imageInput.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
                
                imageInput.style.borderColor = '';
                imageInput.style.background = '';
            } else {
                imagePreview.style.display = 'none';
            }
        });

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

        const imageInput = document.getElementById('productImage');
        if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
            showNotification('Product image is required. Please upload an image.', 'error');
            if (imageInput) {
                imageInput.style.borderColor = '#ef5350';
                imageInput.style.background = '#ffebee';
            }
            return;
        }

        const file = imageInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            showNotification('Please upload a valid image file (JPG, PNG, GIF, or WebP)', 'error');
            imageInput.style.borderColor = '#ef5350';
            imageInput.style.background = '#ffebee';
            return;
        }

        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            showNotification('Image file size must be less than 5MB', 'error');
            imageInput.style.borderColor = '#ef5350';
            imageInput.style.background = '#ffebee';
            return;
        }

        const quantity = quantityInput ? parseInt(quantityInput.value) : 0;
        if (quantity < 10) {
            showNotification('Minimum quantity is 10kg', 'error');
            quantityInput.style.borderColor = '#ef5350';
            quantityInput.style.background = '#ffebee';
            return;
        }

        const fd = new FormData(addProductForm);
        const url = `${API_BASE}/create`;

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
                if (res.errors) {
                    let errorMessages = [];
                    
                    addProductForm.querySelectorAll('.form-control').forEach(input => {
                        input.style.borderColor = '';
                        input.style.background = '';
                    });
                    
                    for (const [field, error] of Object.entries(res.errors)) {
                        errorMessages.push(error);
                        
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
            
            if (listingDateInput) {
                const today = new Date().toISOString().split('T')[0];
                listingDateInput.value = today;
            }
            
            addProductForm.querySelectorAll('.form-control').forEach(input => {
                input.style.borderColor = '';
                input.style.background = '';
            });
            
            loadFarmerProducts();
        } catch (err) {
            showNotification('Failed to add product: ' + err.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    addProductForm.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            this.style.background = '';
        });
    });

    addProductBound = true;
}

// Populate table with database products
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
        
        const listingDate = p.listing_date ? new Date(p.listing_date).toLocaleDateString() : '-';
        
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

// Export functions
window.editProduct = editProduct;
window.deleteProduct = deleteProduct;
window.loadFarmerProducts = loadFarmerProducts;
window.populateProductsTable = populateProductsTable;

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        initializeProductForms();
        initializeEditForm();
        loadFarmerProducts();
    });
} else {
    initializeProductForms();
    initializeEditForm();
    loadFarmerProducts();
}
