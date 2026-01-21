<!-- Wishlist Section - Content view rendered inside buyerLayout -->
<div class="content-header">
    <h1 class="content-title">My Wishlist</h1>
    <p class="content-subtitle">Products you plan to purchase later</p>
</div>

<div class="products-grid" id="wishlist-list">
    <?php if (empty($wishlistItems)): ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
            <div style="font-size: 3rem; margin-bottom: 20px;"></div>
            <h3>Your wishlist is empty</h3>
            <p>Browse products and click "Wishlist" to save them here.</p>
        </div>
    <?php else: ?>
        <?php foreach ($wishlistItems as $item): ?>
            <div class="product-card" 
                 data-wishlist-product="<?= (int)$item->product_id ?>"
                 data-id="<?= (int)$item->product_id ?>"
                 data-name="<?= strtolower(htmlspecialchars($item->name ?? 'Product')) ?>"
                 data-image="<?= !empty($item->image) ? htmlspecialchars($item->image) : '' ?>">
                <div class="product-image">
                    <?php if (!empty($item->image) && file_exists("assets/images/products/" . $item->image)): ?>
                        <img src="<?= ROOT ?>/assets/images/products/<?= htmlspecialchars($item->image) ?>"
                            alt="<?= htmlspecialchars($item->name) ?>">
                    <?php else: ?>
                        <img src="<?= ROOT ?>/assets/images/default-product.svg"
                            alt="<?= htmlspecialchars($item->name) ?>"
                            style="opacity: 0.6;">
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3 class="product-name"><?= htmlspecialchars($item->name ?? 'Product unavailable') ?></h3>
                    <div class="product-price">
                        <?= isset($item->price) ? 'Rs. ' . number_format($item->price, 2) . '/kg' : 'Price unavailable' ?>
                    </div>
                    <div class="product-stock">
                        <?= isset($item->available_quantity) ? htmlspecialchars($item->available_quantity) . 'kg available' : '' ?>
                    </div>
                    <div style="display: flex; gap: 8px; width: 100%;">
                        <button class="btn btn-primary" 
                                style="flex: 1; text-align: center; padding: 10px 16px;"
                                onclick="addToCart(<?= (int)$item->product_id ?>, '<?= addslashes(htmlspecialchars($item->name ?? 'Product')) ?>', <?= (float)($item->price ?? 0) ?>, <?= (float)($item->available_quantity ?? 0) ?>)">
                            Add to Cart
                        </button>
                        <button class="btn btn-danger" 
                                style="flex: 1; text-align: center; padding: 10px 16px;"
                                onclick="removeFromWishlist(<?= (int)$item->product_id ?>)">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
