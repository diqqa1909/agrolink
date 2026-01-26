<?php
/**
 * AgroLink Simple Shipping Cost Calculator
 * 
 * This class ONLY calculates shipping costs.
 * No order creation, no transporter matching - just pure calculation.
 * 
 * @version 1.0 - Minimal
 */

class SimpleShippingCalculator {
    
    private $db;
    private $config;
    
    /**
     * Constructor
     * @param PDO $database PDO database connection
     */
    public function __construct($database) {
        $this->db = $database;
        $this->loadConfiguration();
    }
    
    /**
     * Load platform configuration
     */
    private function loadConfiguration() {
        try {
            $stmt = $this->db->query("SELECT config_key, config_value FROM platform_config");
            $this->config = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->config[$row['config_key']] = $row['config_value'];
            }
        } catch (PDOException $e) {
            // Default values if config table not found
            $this->config = [
                'platform_fee_percentage' => 5,
                'platform_fee_min_lkr' => 20,
                'platform_fee_max_lkr' => 150,
                'transporter_earning_percentage' => 85,
                'vehicle_size_multiplier_max' => 2,
                'default_crop_volume_factor' => 1.0
            ];
        }
    }
    
    /**
     * Calculate shipping cost
     * 
     * @param array $params Calculation parameters
     * @return array Result with cost breakdown
     */
    public function calculateShippingCost($params) {
        try {
            // Validate inputs
            $validation = $this->validateInputs($params);
            if (!$validation['valid']) {
                return ['success' => false, 'error' => $validation['error']];
            }
            
            // Step 1: Get crop volume factor
            $cropFactor = $this->getCropVolumeFactor($params['crop_name']);
            
            // Step 2: Calculate effective weight
            $effectiveWeight = $params['weight_kg'] * $cropFactor;
            
            // Step 3: Get available vehicles for this weight
            $vehicles = $this->getAvailableVehicles($effectiveWeight);
            
            if (empty($vehicles)) {
                return [
                    'success' => false,
                    'error' => 'No suitable vehicle available for weight: ' . 
                               number_format($effectiveWeight, 2) . ' kg (effective)'
                ];
            }
            
            // Step 4: Calculate total distance
            $distanceData = $this->calculateTotalDistance(
                $params['pickup_district_id'],
                $params['pickup_town_id'],
                $params['delivery_district_id'],
                $params['delivery_town_id']
            );
            
            if (!$distanceData['success']) {
                return $distanceData;
            }
            
            // Step 5: Calculate costs for all available vehicles
            $vehicleOptions = [];
            foreach ($vehicles as $vehicle) {
                $costBreakdown = $this->calculateVehicleCost(
                    $vehicle,
                    $distanceData['total_distance_km'],
                    $effectiveWeight
                );
                $vehicleOptions[] = array_merge($vehicle, $costBreakdown);
            }
            
            // Step 6: Select cheapest vehicle
            usort($vehicleOptions, function($a, $b) {
                return $a['total_shipping_cost_lkr'] <=> $b['total_shipping_cost_lkr'];
            });
            
            $selectedVehicle = $vehicleOptions[0];
            
            // Step 7: Return complete calculation
            return [
                'success' => true,
                'calculation' => [
                    // Input data
                    'crop_name' => $params['crop_name'],
                    'order_weight_kg' => $params['weight_kg'],
                    'crop_volume_factor' => $cropFactor,
                    'effective_weight_kg' => round($effectiveWeight, 2),
                    
                    // Distance breakdown
                    'district_distance_km' => $distanceData['district_distance_km'],
                    'pickup_extra_km' => $distanceData['pickup_extra_km'],
                    'delivery_extra_km' => $distanceData['delivery_extra_km'],
                    'total_distance_km' => $distanceData['total_distance_km'],
                    
                    // Selected vehicle
                    'selected_vehicle' => [
                        'id' => $selectedVehicle['id'],
                        'name' => $selectedVehicle['vehicle_name'],
                        'max_weight_kg' => $selectedVehicle['max_weight_kg']
                    ],
                    
                    // Cost breakdown
                    'base_fee_lkr' => $selectedVehicle['base_fee_lkr'],
                    'distance_cost_lkr' => $selectedVehicle['distance_cost_lkr'],
                    'weight_cost_lkr' => $selectedVehicle['weight_cost_lkr'],
                    'subtotal_lkr' => $selectedVehicle['subtotal_lkr'],
                    'platform_fee_lkr' => $selectedVehicle['platform_fee_lkr'],
                    'total_shipping_cost_lkr' => $selectedVehicle['total_shipping_cost_lkr'],
                    'transporter_earning_lkr' => $selectedVehicle['transporter_earning_lkr'],
                    
                    // Alternative vehicles (for user to choose)
                    'alternative_vehicles' => array_slice($vehicleOptions, 1, 2)
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Shipping calculation error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error calculating shipping cost. Please try again.'
            ];
        }
    }
    
    /**
     * Validate input parameters
     */
    private function validateInputs($params) {
        $required = [
            'pickup_district_id',
            'pickup_town_id',
            'delivery_district_id',
            'delivery_town_id',
            'crop_name',
            'weight_kg'
        ];
        
        foreach ($required as $field) {
            if (!isset($params[$field]) || $params[$field] === '') {
                return [
                    'valid' => false,
                    'error' => "Missing required field: $field"
                ];
            }
        }
        
        if ($params['weight_kg'] <= 0) {
            return ['valid' => false, 'error' => 'Weight must be greater than 0'];
        }
        
        if ($params['weight_kg'] > 5000) {
            return [
                'valid' => false,
                'error' => 'Weight exceeds maximum (5000 kg). Contact support for bulk orders.'
            ];
        }
        
        return ['valid' => true];
    }
    
    /**
     * Get crop volume factor
     */
    private function getCropVolumeFactor($cropName) {
        try {
            $stmt = $this->db->prepare(
                "SELECT volume_factor FROM crop_volume_factors 
                 WHERE crop_name = ? AND is_active = 1 LIMIT 1"
            );
            $stmt->execute([$cropName]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return floatval($result['volume_factor']);
            }
            
            // Return default if crop not found
            return floatval($this->config['default_crop_volume_factor']);
            
        } catch (PDOException $e) {
            error_log("Crop factor error: " . $e->getMessage());
            return 1.0;
        }
    }
    
    /**
     * Get available vehicles based on effective weight
     */
    private function getAvailableVehicles($effectiveWeight) {
        try {
            $maxWeight = $effectiveWeight * floatval($this->config['vehicle_size_multiplier_max']);
            
            $stmt = $this->db->prepare(
                "SELECT id, vehicle_name, max_weight_kg, base_fee_lkr, 
                        cost_per_km_lkr, cost_per_kg_lkr
                 FROM vehicle_types 
                 WHERE max_weight_kg >= ? 
                 AND max_weight_kg <= ?
                 AND is_active = 1 
                 ORDER BY max_weight_kg ASC"
            );
            
            $stmt->execute([$effectiveWeight, $maxWeight]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Vehicle selection error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate total distance
     */
    private function calculateTotalDistance($pickupDistrictId, $pickupTownId, 
                                           $deliveryDistrictId, $deliveryTownId) {
        try {
            // Get district to district distance (bidirectional)
            $stmt = $this->db->prepare(
                "SELECT distance_km FROM district_distances 
                 WHERE (from_district_id = ? AND to_district_id = ?) 
                 OR (from_district_id = ? AND to_district_id = ?)
                 LIMIT 1"
            );
            $stmt->execute([
                $pickupDistrictId, 
                $deliveryDistrictId,
                $deliveryDistrictId,
                $pickupDistrictId
            ]);
            $districtDistance = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$districtDistance) {
                return [
                    'success' => false,
                    'error' => 'Distance data not available for selected districts'
                ];
            }
            
            $distanceKm = intval($districtDistance['distance_km']);
            
            // Get pickup town extra distance
            $stmt = $this->db->prepare(
                "SELECT extra_distance_km FROM towns WHERE id = ? LIMIT 1"
            );
            $stmt->execute([$pickupTownId]);
            $pickupTown = $stmt->fetch(PDO::FETCH_ASSOC);
            $pickupExtraKm = $pickupTown ? intval($pickupTown['extra_distance_km']) : 0;
            
            // Get delivery town extra distance
            $stmt->execute([$deliveryTownId]);
            $deliveryTown = $stmt->fetch(PDO::FETCH_ASSOC);
            $deliveryExtraKm = $deliveryTown ? intval($deliveryTown['extra_distance_km']) : 0;
            
            // Calculate total
            $totalDistance = $distanceKm + $pickupExtraKm + $deliveryExtraKm;
            
            return [
                'success' => true,
                'district_distance_km' => $distanceKm,
                'pickup_extra_km' => $pickupExtraKm,
                'delivery_extra_km' => $deliveryExtraKm,
                'total_distance_km' => $totalDistance
            ];
            
        } catch (PDOException $e) {
            error_log("Distance calculation error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error calculating distance'
            ];
        }
    }
    
    /**
     * Calculate cost for a specific vehicle
     */
    private function calculateVehicleCost($vehicle, $totalDistance, $effectiveWeight) {
        // Base components
        $baseFee = floatval($vehicle['base_fee_lkr']);
        $distanceCost = $totalDistance * floatval($vehicle['cost_per_km_lkr']);
        $weightCost = $effectiveWeight * floatval($vehicle['cost_per_kg_lkr']);
        
        // Subtotal
        $subtotal = $baseFee + $distanceCost + $weightCost;
        
        // Platform fee
        $platformFeePercent = floatval($this->config['platform_fee_percentage']) / 100;
        $platformFee = $subtotal * $platformFeePercent;
        
        // Apply min/max limits
        $platformFeeMin = floatval($this->config['platform_fee_min_lkr']);
        $platformFeeMax = floatval($this->config['platform_fee_max_lkr']);
        $platformFee = max($platformFeeMin, min($platformFeeMax, $platformFee));
        
        // Total cost
        $totalCost = $subtotal + $platformFee;
        
        // Transporter earning
        $transporterPercent = floatval($this->config['transporter_earning_percentage']) / 100;
        $transporterEarning = $totalCost * $transporterPercent;
        
        return [
            'base_fee_lkr' => round($baseFee, 2),
            'distance_cost_lkr' => round($distanceCost, 2),
            'weight_cost_lkr' => round($weightCost, 2),
            'subtotal_lkr' => round($subtotal, 2),
            'platform_fee_lkr' => round($platformFee, 2),
            'total_shipping_cost_lkr' => round($totalCost, 2),
            'transporter_earning_lkr' => round($transporterEarning, 2)
        ];
    }
    
    // ============================================
    // HELPER FUNCTIONS FOR DROPDOWNS
    // ============================================
    
    /**
     * Get all crops grouped by category
     */
    public function getAllCrops() {
        try {
            $stmt = $this->db->query(
                "SELECT crop_name, category, volume_factor 
                 FROM crop_volume_factors 
                 WHERE is_active = 1 
                 ORDER BY category, crop_name"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get all districts
     */
    public function getAllDistricts() {
        try {
            $stmt = $this->db->query(
                "SELECT id, district_name, district_code, province 
                 FROM districts 
                 WHERE is_active = 1 
                 ORDER BY province, district_name"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get towns by district
     */
    public function getTownsByDistrict($districtId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, town_name, extra_distance_km, postal_code 
                 FROM towns 
                 WHERE district_id = ? AND is_active = 1 
                 ORDER BY town_name"
            );
            $stmt->execute([$districtId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Get all vehicle types
     */
    public function getAllVehicleTypes() {
        try {
            $stmt = $this->db->query(
                "SELECT * FROM vehicle_types WHERE is_active = 1 ORDER BY max_weight_kg"
            );
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

// ============================================
// USAGE EXAMPLE
// ============================================

/*
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=agrolink;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create calculator
$calculator = new SimpleShippingCalculator($pdo);

// Calculate shipping cost
$params = [
    'pickup_district_id' => 4,      // Kandy
    'pickup_town_id' => 15,         // Teldeniya
    'delivery_district_id' => 5,    // Matale
    'delivery_town_id' => 23,       // Naula
    'crop_name' => 'Carrot',
    'weight_kg' => 20
];

$result = $calculator->calculateShippingCost($params);

if ($result['success']) {
    $calc = $result['calculation'];
    
    echo "=== SHIPPING COST CALCULATION ===\n";
    echo "Crop: {$calc['crop_name']} ({$calc['order_weight_kg']} kg)\n";
    echo "Effective Weight: {$calc['effective_weight_kg']} kg\n";
    echo "Total Distance: {$calc['total_distance_km']} km\n";
    echo "Vehicle: {$calc['selected_vehicle']['name']}\n\n";
    
    echo "Cost Breakdown:\n";
    echo "- Base Fee: LKR {$calc['base_fee_lkr']}\n";
    echo "- Distance Cost: LKR {$calc['distance_cost_lkr']}\n";
    echo "- Weight Cost: LKR {$calc['weight_cost_lkr']}\n";
    echo "- Platform Fee: LKR {$calc['platform_fee_lkr']}\n";
    echo "================================\n";
    echo "TOTAL: LKR {$calc['total_shipping_cost_lkr']}\n";
} else {
    echo "Error: {$result['error']}\n";
}
*/

?>