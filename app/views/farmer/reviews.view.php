<!-- Farmer Reviews Section -->
<div class="content-header">
    <h1 class="content-title">Customer Reviews</h1>
    <p class="content-subtitle">Feedback and ratings from buyers</p>
</div>

<div class="reviews-container">
    <?php if (empty($reviews)): ?>
        <div class="empty-state" style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; border: 1px dashed #e0e0e0;">
            <div style="font-size: 3rem; margin-bottom: 20px;">⭐</div>
            <h3>No Reviews Yet</h3>
            <p style="color: #666;">You haven't received any reviews yet.</p>
        </div>
    <?php else: ?>
        <div class="reviews-grid" style="display: grid; gap: 20px;">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card" style="background: white; border: 1px solid #e0e0e0; border-radius: 12px; padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                        <div style="display: flex; gap: 16px;">
                            <div style="width: 50px; height: 50px; background: #e3f2fd; color: #2196F3; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem;">
                                <?= strtoupper(substr($review->buyer_name ?? 'B', 0, 1)) ?>
                            </div>
                            
                            <div>
                                <h4 style="margin: 0 0 4px 0;"><?= htmlspecialchars($review->product_name) ?></h4>
                                <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                    Reviewed by <span style="font-weight: 500;"><?= htmlspecialchars($review->buyer_name) ?></span>
                                </p>
                                <p style="margin: 4px 0 0 0; font-size: 0.8rem; color: #999;">
                                    <?= date('M d, Y', strtotime($review->created_at)) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="star-rating" style="color: #ff9800; font-size: 1.2rem;">
                            <?= str_repeat('★', $review->rating) . str_repeat('☆', 5 - $review->rating) ?>
                        </div>
                    </div>
                    
                    <div style="background: #f9f9f9; padding: 16px; border-radius: 8px; margin-top: 12px;">
                        <p style="margin: 0; color: #444; line-height: 1.5;">
                            "<?= nl2br(htmlspecialchars($review->comment)) ?>"
                        </p>
                    </div>

                    <?php if (!empty($review->reply)): ?>
                        <div class="farmer-reply" style="margin-top: 16px; padding-left: 16px; border-left: 3px solid #4CAF50;">
                            <p style="margin: 0 0 4px 0; font-weight: 500; color: #2e7d32; font-size: 0.9rem;">
                                Your Reply:
                            </p>
                            <p style="margin: 0; color: #666; font-size: 0.9rem;">
                                <?= nl2br(htmlspecialchars($review->reply)) ?>
                            </p>
                            <span style="font-size: 0.8rem; color: #999;">Replied on <?= date('M d', strtotime($review->replied_at)) ?></span>
                        </div>
                    <?php else: ?>
                        <div style="margin-top: 16px;">
                            <button class="btn btn-sm btn-outline" onclick="toggleReplyForm(<?= $review->id ?>)">Reply to Review</button>
                            
                            <form id="reply-form-<?= $review->id ?>" class="reply-form" style="display: none; margin-top: 12px;" onsubmit="submitReply(event, <?= $review->id ?>)">
                                <textarea name="reply" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 8px;" rows="3" placeholder="Write your response..." required></textarea>
                                <div style="display: flex; gap: 8px;">
                                    <button type="submit" class="btn btn-sm btn-primary">Post Reply</button>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="toggleReplyForm(<?= $review->id ?>)">Cancel</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleReplyForm(id) {
    const form = document.getElementById('reply-form-' + id);
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

function submitReply(e, reviewId) {
    e.preventDefault();
    
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    const replyText = form.querySelector('textarea').value;
    
    btn.disabled = true;
    btn.textContent = 'Posting...';
    
    const formData = new FormData();
    formData.append('review_id', reviewId);
    formData.append('reply', replyText);
    
    fetch('<?= ROOT ?>/FarmerReviews/reply', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show reply
            window.location.reload();
        } else {
            alert(data.message || 'Failed to post reply');
            btn.disabled = false;
            btn.textContent = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
        btn.disabled = false;
        btn.textContent = originalText;
    });
}
</script>
