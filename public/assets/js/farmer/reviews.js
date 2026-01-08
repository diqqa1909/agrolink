// Farmer Reviews & Complaints Management

// Load Analytics Data
function loadAnalyticsData() {
    const totalSalesEl = document.getElementById('totalSales');
    const avgRatingEl = document.getElementById('avgRating');
    const repeatCustomersEl = document.getElementById('repeatCustomers');
    const conversionRateEl = document.getElementById('conversionRate');
    
    if (totalSalesEl) totalSalesEl.textContent = '0';
    if (avgRatingEl) avgRatingEl.textContent = '-';
    if (repeatCustomersEl) repeatCustomersEl.textContent = '0';
    if (conversionRateEl) conversionRateEl.textContent = '-';
}

// Reviews & Complaints
function loadFeedbackData(){
    renderReviewsList([]);
    renderComplaintsList([]);
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

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        loadAnalyticsData();
        loadFeedbackData();
    });
} else {
    loadAnalyticsData();
    loadFeedbackData();
}
