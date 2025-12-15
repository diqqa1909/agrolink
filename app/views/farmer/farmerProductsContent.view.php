<div class="content-section">
    <div class="content-header">
        <h1 class="content-title">My Products</h1>
        <button class="btn btn-add-product" data-modal="addProductModal">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Product
        </button>
    </div>
    <div class="content-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price/KG</th>
                        <th>Quantity</th>
                        <th>Location</th>
                        <th>Listed Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="productsTableBody">
                    <!-- Products will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div id="addProductModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Add New Product</h3>
        </div>
        <div class="modal-body">
            <form id="addProductForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="productName">Product Name *</label>
                    <input type="text" id="productName" name="name" class="form-control" required placeholder="e.g., Fresh Tomatoes">
                </div>

                <div class="form-group">
                    <label for="productCategory">Category *</label>
                    <select id="productCategory" name="category" class="form-control" required>
                        <option value="other">Other</option>
                        <option value="vegetables">Vegetables</option>
                        <option value="fruits">Fruits</option>
                        <option value="cereals">Cereals & Grains</option>
                        <option value="yams">Yams & Tubers</option>
                        <option value="legumes">Legumes & Pulses</option>
                        <option value="spices">Spices & Herbs</option>
                        <option value="leafy">Leafy Greens</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="productPrice">Price per KG (Rs.) *</label>
                    <input type="number" id="productPrice" name="price" class="form-control" step="0.01" min="0" required placeholder="120.00">
                </div>

                <div class="form-group">
                    <label for="productQuantity">Available Quantity (KG) *</label>
                    <input type="number" id="productQuantity" name="quantity" class="form-control" min="1" required placeholder="100">
                </div>

                <div class="form-group">
                    <label for="productLocation">Farm Location *</label>
                    <input type="text" id="productLocation" name="location" class="form-control" placeholder="e.g., Matale, Central Province" required>
                </div>

                <div class="form-group">
                    <label for="listingDate">Available From *</label>
                    <input type="date" id="listingDate" name="listing_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label for="productImage">Product Image *</label>
                    <input type="file" id="productImage" name="image" class="form-control" accept="image/*" required>
                    <span class="form-hint">Please upload a clear image of your product (Max 5MB, JPG/PNG/GIF/WebP)</span>
                    <div id="imagePreview" style="margin-top: 12px; display: none;">
                        <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #E8F5E9;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Product</button>
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Edit Product</h3>
        </div>
        <div class="modal-body">
            <form id="editProductForm">
                <input type="hidden" id="editProductId">

                <div class="form-group">
                    <label for="editProductName">Product Name *</label>
                    <input type="text" id="editProductName" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="editProductCategory">Category *</label>
                    <select id="editProductCategory" name="category" class="form-control" required>
                        <option value="vegetables">Vegetables</option>
                        <option value="fruits">Fruits</option>
                        <option value="cereals">Cereals & Grains</option>
                        <option value="yams">Yams & Tubers</option>
                        <option value="legumes">Legumes & Pulses</option>
                        <option value="spices">Spices & Herbs</option>
                        <option value="leafy">Leafy Greens</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="editProductPrice">Price per KG (Rs.) *</label>
                    <input type="number" id="editProductPrice" name="price" class="form-control" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="editProductQuantity">Available Quantity (KG) *</label>
                    <input type="number" id="editProductQuantity" name="quantity" class="form-control" min="1" required>
                </div>

                <div class="form-group">
                    <label for="editProductLocation">Farm Location *</label>
                    <input type="text" id="editProductLocation" name="location" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="editListingDate">Available From *</label>
                    <input type="date" id="editListingDate" name="listing_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="editProductImage">Replace Image (optional)</label>
                    <input type="file" id="editProductImage" name="image" class="form-control" accept="image/*">
                    <span class="form-hint">Leave empty to keep current image. Max 5MB, JPG/PNG/GIF/WebP</span>
                    <div id="editImagePreview" style="margin-top: 12px; display: none;">
                        <img id="editPreviewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #E8F5E9;">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-modal-close>Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>