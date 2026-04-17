<div class="content-section farmer-deliveries-page">
    <div class="content-header">
        <h1 class="content-title">Running Deliveries</h1>
        <p class="content-subtitle">Track transporter progress for your outgoing orders.</p>
    </div>

    <div class="dashboard-stats farmer-deliveries-stats">
        <div class="stat-card">
            <div class="stat-number"><?= (int)($deliverySummary->accepted_deliveries ?? 0) ?></div>
            <div class="stat-label">Accepted</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= (int)($deliverySummary->in_transit_deliveries ?? 0) ?></div>
            <div class="stat-label">In Transit</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= (int)($deliverySummary->delivered_deliveries ?? 0) ?></div>
            <div class="stat-label">Delivered</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?= (int)($deliverySummary->total_deliveries ?? 0) ?></div>
            <div class="stat-label">Total Deliveries</div>
        </div>
    </div>

    <?php $activeFilter = $filter ?? 'running'; ?>
    <div class="content-card delivery-filter-card">
        <div class="delivery-filter-row">
            <div class="delivery-filter-group" role="tablist" aria-label="Delivery filters">
                <a class="delivery-filter-link <?= $activeFilter === 'running' ? 'active' : '' ?>" href="<?= ROOT ?>/farmerdeliveries?status=running">Running</a>
                <a class="delivery-filter-link <?= $activeFilter === 'pending' ? 'active' : '' ?>" href="<?= ROOT ?>/farmerdeliveries?status=pending">Pending</a>
                <a class="delivery-filter-link <?= $activeFilter === 'accepted' ? 'active' : '' ?>" href="<?= ROOT ?>/farmerdeliveries?status=accepted">Accepted</a>
                <a class="delivery-filter-link <?= $activeFilter === 'in_transit' ? 'active' : '' ?>" href="<?= ROOT ?>/farmerdeliveries?status=in_transit">In Transit</a>
                <a class="delivery-filter-link <?= $activeFilter === 'delivered' ? 'active' : '' ?>" href="<?= ROOT ?>/farmerdeliveries?status=delivered">Delivered</a>
                <a class="delivery-filter-link <?= $activeFilter === 'all' ? 'active' : '' ?>" href="<?= ROOT ?>/farmerdeliveries?status=all">All</a>
            </div>
        </div>
    </div>

    <?php if (empty($deliveries)): ?>
        <div class="empty-state farmer-deliveries-empty">
            <div class="farmer-deliveries-empty-icon">🚚</div>
            <h3>No deliveries found</h3>
            <p>Deliveries will appear here once transport requests are created.</p>
        </div>
    <?php else: ?>
        <div class="table-container farmer-deliveries-table-wrap">
            <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Order</th>
                        <th>Buyer</th>
                        <th>Transporter</th>
                        <th>Route</th>
                        <th>Weight</th>
                        <th>Shipping</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deliveries as $delivery): ?>
                        <tr>
                            <td>#<?= (int)$delivery->id ?></td>
                            <td>#<?= (int)$delivery->order_id ?></td>
                            <td><?= htmlspecialchars($delivery->buyer_name ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($delivery->transporter_name ?? 'Unassigned') ?></td>
                            <td>
                                <div class="farmer-delivery-route-main">
                                    <?= htmlspecialchars($delivery->farmer_city ?? 'Unknown') ?> → <?= htmlspecialchars($delivery->buyer_city ?? 'Unknown') ?>
                                </div>
                                <div class="farmer-delivery-route-sub">
                                    <?= isset($delivery->distance_km) ? number_format((float)$delivery->distance_km, 1) . ' km' : 'Distance N/A' ?>
                                </div>
                            </td>
                            <td><?= number_format((float)($delivery->total_weight_kg ?? 0), 2) ?> kg</td>
                            <td>Rs. <?= number_format((float)($delivery->shipping_fee ?? 0), 2) ?></td>
                            <td>
                                <span class="order-status status-<?= htmlspecialchars($delivery->status ?? 'pending') ?>">
                                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($delivery->status ?? 'pending'))) ?>
                                </span>
                            </td>
                            <td><?= !empty($delivery->updated_at) ? date('M d, Y H:i', strtotime($delivery->updated_at)) : '-' ?></td>
                            <td>
                                <?php
                                $canReviewTransporter =
                                    ($delivery->status ?? '') === 'delivered'
                                    && !empty($delivery->transporter_id)
                                    && !in_array((int)$delivery->order_id, $submittedFeedbackOrderIds ?? [], true);
                                ?>
                                <?php if ($canReviewTransporter): ?>
                                    <button
                                        type="button"
                                        class="btn btn-secondary btn-sm"
                                        onclick="FarmerDeliveryFeedback.openModal(
                                            <?= (int)$delivery->order_id ?>,
                                            <?= (int)$delivery->transporter_id ?>,
                                            '<?= htmlspecialchars(addslashes($delivery->transporter_name ?? 'Transporter')) ?>'
                                        )">
                                        Review Transporter
                                    </button>
                                <?php elseif (($delivery->status ?? '') === 'delivered' && !empty($delivery->transporter_id)): ?>
                                    <span style="color: #65b57c; font-weight: 600;">Feedback sent</span>
                                <?php else: ?>
                                    <span style="color: #666;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<div id="farmerTransporterFeedbackModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 560px;">
        <div class="modal-header">
            <h3>Review Transporter</h3>
            <button type="button" class="modal-close" onclick="FarmerDeliveryFeedback.closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="farmerTransporterFeedbackForm">
                <input type="hidden" id="feedbackOrderId" name="order_id">
                <input type="hidden" id="feedbackTransporterId" name="transporter_id">

                <div class="form-group">
                    <label for="feedbackTransporterName">Transporter</label>
                    <input type="text" id="feedbackTransporterName" class="form-control" readonly>
                </div>

                <div class="form-group">
                    <label for="feedbackRating">Rating *</label>
                    <select id="feedbackRating" name="rating" class="form-control" required>
                        <option value="">Select rating</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Very Poor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="feedbackComment">Review *</label>
                    <textarea id="feedbackComment" name="comment" class="form-control" rows="4" required placeholder="Share your delivery experience"></textarea>
                </div>

                <div class="form-group">
                    <label for="feedbackSatisfaction">Customer Satisfaction</label>
                    <select id="feedbackSatisfaction" name="satisfaction_status" class="form-control">
                        <option value="very_satisfied">Very Satisfied</option>
                        <option value="satisfied">Satisfied</option>
                        <option value="neutral" selected>Neutral</option>
                        <option value="dissatisfied">Dissatisfied</option>
                        <option value="very_dissatisfied">Very Dissatisfied</option>
                    </select>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" id="feedbackOnTime" name="on_time_flag" value="1">
                    <label for="feedbackOnTime" style="margin: 0;">The delivery was completed on time</label>
                </div>

                <div class="form-group">
                    <label for="feedbackComplaint">Complaint Details</label>
                    <textarea id="feedbackComplaint" name="complaint_text" class="form-control" rows="3" placeholder="Optional complaint details"></textarea>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="FarmerDeliveryFeedback.closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const FarmerDeliveryFeedback = (() => {
    const modal = document.getElementById('farmerTransporterFeedbackModal');
    const form = document.getElementById('farmerTransporterFeedbackForm');

    function notify(message, type = 'info') {
        if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            alert(message);
        }
    }

    function openModal(orderId, transporterId, transporterName) {
        document.getElementById('feedbackOrderId').value = orderId;
        document.getElementById('feedbackTransporterId').value = transporterId;
        document.getElementById('feedbackTransporterName').value = transporterName;
        form.reset();
        document.getElementById('feedbackOrderId').value = orderId;
        document.getElementById('feedbackTransporterId').value = transporterId;
        document.getElementById('feedbackTransporterName').value = transporterName;
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
    }

    function submit(event) {
        event.preventDefault();
        const formData = new FormData(form);

        fetch('<?= ROOT ?>/farmerdeliveries/submitFeedback', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notify(data.message || 'Feedback submitted successfully', 'success');
                    closeModal();
                    window.location.reload();
                } else {
                    notify(data.message || 'Failed to submit feedback', 'error');
                }
            })
            .catch(() => {
                notify('Failed to submit feedback', 'error');
            });
    }

    if (form) {
        form.addEventListener('submit', submit);
    }

    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    return { openModal, closeModal };
})();
</script>
