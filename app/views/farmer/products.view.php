<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - AgroLink</title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/css/style.css">
</head>
<body>
  <div class="container section">
    <h1 class="section-title">Available Products</h1>

    <form method="GET" class="grid grid-3 mb-md" style="gap:1rem;">
      <input class="form-control" type="text" name="search" placeholder="Search..." value="<?= esc($filters['search'] ?? '') ?>">
      <input class="form-control" type="number" step="0.01" name="max_price" placeholder="Max price" value="<?= esc($filters['max_price'] ?? '') ?>">
      <input class="form-control" type="text" name="location" placeholder="Location" value="<?= esc($filters['location'] ?? '') ?>">
      <button class="btn btn-primary">Filter</button>
    </form>

    <div class="product-grid">
      <?php if (!empty($products)): foreach($products as $p): ?>
        <div class="product-card">
          <img class="product-image" src="<?= ROOT ?>/<?= $p->image ?: 'assets/img/default-product.jpg' ?>" alt="<?= esc($p->name) ?>">
          <div class="product-info">
            <div class="product-name"><?= esc($p->name) ?></div>
            <div class="product-price">Rs. <?= number_format($p->price, 2) ?></div>
            <div class="product-meta">
              <span>by <?= esc($p->farmer_name) ?></span>
              <?php if($p->location): ?>
                <span><?= esc($p->location) ?></span>
              <?php endif; ?>
            </div>
            <p class="product-stock"><?= $p->quantity ?> available</p>
            <button class="btn btn-primary btn-sm" onclick="addToCart('<?= $p->id ?>')">Add to cart</button>
          </div>
        </div>
      <?php endforeach; else: ?>
        <p>No products found.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="<?= ROOT ?>/assets/js/main.js"></script>
  <script src="<?= ROOT ?>/assets/js/buyerDashboard.js"></script>
</body>
</html>