<!-- My Reviews Section -->
<div class="content-header">
    <h1 class="content-title">My Reviews</h1>
    <p class="content-subtitle">Reviews you've written for products and farmers</p>
</div>

<div class="reviews-container">
    <?php if (empty($reviews)): ?>
        <div class="empty-state">
            <div style="font-size: 3rem; margin-bottom: 20px;">📝</div>
            <h3>No Reviews Yet</h3>
            <p style="color: #666; margin-bottom: 24px;">You haven't written any reviews yet. Go to your orders to review products you've purchased.</p>
            <a href="<?= ROOT ?>/buyerorders" class="btn btn-primary">Go to Orders</a>
        </div>
    <?php else: ?>
        <div class="reviews-grid" style="display: grid; gap: 20px;">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <div style="display: flex; gap: 16px;">
                            <?php if (!empty($review->product_image) && file_exists("assets/images/products/" . $review->product_image)): ?>
                                <img src="<?= ROOT ?>/assets/images/products/<?= htmlspecialchars($review->product_image) ?>" 
                                     alt="<?= htmlspecialchars($review->product_name) ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 60px; height: 60px; background: #f5f5f5; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                    🌱
                                </div>
                            <?php endif; ?>
                            
                            <div>
                                <h4 style="margin: 0 0 4px 0;"><?= htmlspecialchars($review->product_name) ?></h4>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                    Sold by <span style="font-weight: 500;"><?= htmlspecialchars($review->farmer_name) ?></span>
                                </p>
                                <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: #999;">
                                    Reviewed on <?= date('M d, Y', strtotime($review->created_at)) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="star-rating" style="color: #ff9800; font-size: 1.2rem;">
                            <?= str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating) ?>
                        </div>
                    </div>
                    
                    <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin-top: 12px;">
                        <p style="margin: 0; color: #444; line-height: 1.5; font-style: italic;">
                            "<?= nl2br(htmlspecialchars($review->comment)) ?>"
                        </p>
                    </div>

                    <?php if (!empty($review->reply)): ?>
                        <div class="farmer-reply" style="margin-top: 16px; padding-left: 16px; border-left: 3px solid #4CAF50;">
                            <p style="margin: 0 0 4px 0; font-weight: 500; color: #2e7d32; font-size: 0.9rem;">
                                Response from Farmer:
                            </p>
                            <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                <?= nl2br(htmlspecialchars($review->reply)) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .empty-state {
        grid-column: 1/-1;
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        border: 1px dashed #e0e0e0;
    }
</style>
