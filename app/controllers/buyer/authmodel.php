<?php
 
class Reference_VehicleModel {
    protected $allowedColumns = [
        'transporter_id', 'type', 'registration',
        'color', 
        'capacity', 'fuel_type', 'model', 'status'
    ];
}


$htmlAddColor = <<<HTML
    <div class="form-group">
        <label for="vehicleColor">Vehicle Color *</label>
        <input type="text" id="vehicleColor" name="color" class="form-control" required>
    </div>
HTML;

$jsEditColor = <<<JS

    <div class="form-group">
        <label for="editVehicleColor">Vehicle Color</label>
        <input type="text" id="editVehicleColor" name="color" class="form-control" value="\${escapeHtml(vehicle.color || '')}">
    </div>
JS;

//ADDING FILTER LOGIC (e.g., "Filter Vehicles by Fuel Type")


$htmlFilter = <<<HTML
    <div style="margin-bottom: 20px;">
        <label>Filter by Fuel:</label>
        <select id="fuelTypeFilter" class="form-control" style="width: 200px; display: inline-block;">
            <option value="">All Fuels</option>
            <option value="petrol">Petrol</option>
            <option value="diesel">Diesel</option>
        </select>
    </div>
HTML;

$jsFilter = <<<JS
    document.getElementById('fuelTypeFilter')?.addEventListener('change', function(e) {
        const fuel = e.target.value;
        fetch(transporterApi('getVehicles?fuel=' + encodeURIComponent(fuel)), { credentials: 'include' })
            .then(parseJsonResponse)
            .then(data => {
                displayVehicles(data.vehicles || []);
            });
    });
JS;

class Reference_Controller_Filter {
    public function getVehicles() {
        $user_id = 1; 
        $model = new Reference_VehicleModel();
        
        $fuelFilter = $_GET['fuel'] ?? null;
        
        $vehicles = $model->getVehiclesWithFilter($user_id, $fuelFilter);
        echo json_encode(['success' => true, 'vehicles' => $vehicles]);
    }
}

class Reference_Model_Filter {
    public function getVehiclesWithFilter($user_id, $fuel = null) {
        $query = "SELECT * FROM vehicles WHERE transporter_id = :transporter_id";
        $params = ['transporter_id' => $user_id];

        if (!empty($fuel)) {
            $query .= " AND fuel_type = :fuel";
            $params['fuel'] = $fuel;
        }
        
        $query .= " ORDER BY created_at DESC";
    }
}

// ADDING SORT LOGIC (e.g., "Sort Vehicles by Capacity")

$htmlSort = <<<HTML
    <button id="sortCapacityBtn" class="btn btn-outline" data-sort="desc">Sort by Capacity (High-Low)</button>
HTML;

/*
 * [2. JAVASCRIPT]
 */
$jsSort = <<<JS
    document.getElementById('sortCapacityBtn')?.addEventListener('click', function(e) {
        let currentSort = this.dataset.sort;
        let newSort = currentSort === 'desc' ? 'asc' : 'desc';
        this.dataset.sort = newSort;
        this.textContent = newSort === 'desc' ? 'Sort by Capacity (High-Low)' : 'Sort by Capacity (Low-High)';

        fetch(transporterApi('getVehicles?sort_capacity=' + newSort), { credentials: 'include' })
            .then(parseJsonResponse)
            .then(data => displayVehicles(data.vehicles || []));
    });
JS;


class Reference_Controller_Sort {
    public function getVehicles() {
        $sortDirection = $_GET['sort_capacity'] ?? null;

    }
}


class Reference_Model_Sort {
    public function getVehiclesSorted($user_id, $sortDirection = null) {
        $query = "SELECT * FROM vehicles WHERE transporter_id = :transporter_id";
        
        if ($sortDirection === 'asc') {
            $query .= " ORDER BY capacity ASC";
        } elseif ($sortDirection === 'desc') {
            $query .= " ORDER BY capacity DESC";
        } else {
            $query .= " ORDER BY created_at DESC"; // Default
        }
    }
}

// ADDING SEARCH LOGIC (e.g., "Search Deliveries by City")

$htmlSearch = <<<HTML
    <div class="search-box" style="margin-bottom: 20px;">
        <input type="text" id="searchInput" class="form-control" placeholder="Search City..." style="width: 300px; display: inline-block;">
        <button id="searchBtn" class="btn btn-primary">Search</button>
    </div>
HTML;

/*
 * [2. JAVASCRIPT]
 */
$jsSearch = <<<JS
    document.getElementById('searchBtn')?.addEventListener('click', function() {
        const query = document.getElementById('searchInput').value;
        fetch(transporterApi('getDeliveries?search=' + encodeURIComponent(query)), { credentials: 'include' })
            .then(parseJsonResponse)
            .then(data => displayDeliveries(data.deliveries || []));
    });
JS;


class Reference_Controller_Search {
    public function getDeliveries() {
        $searchQuery = $_GET['search'] ?? null;
        // ... pass to model
    }
}

class Reference_Model_Search {
    public function searchDeliveries($transporterId, $search) {
        $query = "SELECT * FROM delivery_requests WHERE transporter_id = :id";
        $params = ['id' => $transporterId];

        if (!empty($search)) {
            // Use LIKE with % wildcards for partial text matching
            $query .= " AND farmer_city LIKE :search";
            $params['search'] = '%' . $search . '%';
        }
        

    }
}

?>

// ADDING A NEW CRUD FIELD (e.g., "Vehicle Color")

//ALTER TABLE vehicles ADD COLUMN color VARCHAR(50) DEFAULT NULL;