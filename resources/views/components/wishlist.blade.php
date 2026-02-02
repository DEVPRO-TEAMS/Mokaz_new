<!-- Wishlist flottant (style cart) -->
<div id="wishlistCart" class="card shadow-lg position-fixed end-0 mt-2 me-3 d-none" style="top: 60px; z-index: 1055; width: 320px;">
    <div class="card-header d-flex justify-content-between align-items-center py-2 bg-light">
        <h6 class="mb-0">Ma liste de souhaits</h6>
        <button type="button" class="btn-close btn-sm" onclick="toggleWishlistCart()" aria-label="Fermer"></button>
    </div>
    <div class="card-body p-0" style="max-height: 50vh; overflow-y: auto;">
        <ul class="list-group list-group-flush">
            <!-- Produit 1 -->
            <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="https://i.pinimg.com/736x/a7/c6/fe/a7c6fe4e1868a144d562024de841cb6d.jpg" class="rounded border" width="50" height="50" alt="Produit 1">
                    <div>
                        <div class="fw-semibold">Produit 1</div>
                        <div class="text-muted small">12 000 FCFA</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger">
                    <i class="icon icon-trash"></i>
                </button>
            </li>
            <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="https://i.pinimg.com/736x/a7/c6/fe/a7c6fe4e1868a144d562024de841cb6d.jpg" class="rounded border" width="50" height="50" alt="Produit 1">
                    <div>
                        <div class="fw-semibold">Produit 1</div>
                        <div class="text-muted small">12 000 FCFA</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger">
                    <i class="icon icon-trash"></i>
                </button>
            </li>
            <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="https://i.pinimg.com/736x/a7/c6/fe/a7c6fe4e1868a144d562024de841cb6d.jpg" class="rounded border" width="50" height="50" alt="Produit 1">
                    <div>
                        <div class="fw-semibold">Produit 1</div>
                        <div class="text-muted small">12 000 FCFA</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger">
                    <i class="icon icon-trash"></i>
                </button>
            </li>
            <li class="list-group-item d-flex align-items-center justify-content-between py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <img src="https://i.pinimg.com/736x/a7/c6/fe/a7c6fe4e1868a144d562024de841cb6d.jpg" class="rounded border" width="50" height="50" alt="Produit 1">
                    <div>
                        <div class="fw-semibold">Produit 1</div>
                        <div class="text-muted small">12 000 FCFA</div>
                    </div>
                </div>
                <button class="btn btn-sm btn-outline-danger">
                    <i class="icon icon-trash"></i>
                </button>
            </li>
            <!-- Ajouter plus de produits ici -->
        </ul>
    </div>
    <div class="card-footer d-flex justify-content-between py-2 px-3">
        <button class="btn btn-sm btn-outline-secondary" onclick="toggleWishlistCart()">Fermer</button>
    </div>
</div>