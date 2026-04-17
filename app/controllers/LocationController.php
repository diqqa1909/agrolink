<?php

class LocationController
{
    use Controller;

    private $locationModel;

    public function __construct()
    {
        $this->locationModel = new LocationModel();
    }

    public function towns($districtId = null)
    {
        if (ob_get_level()) {
            ob_clean();
        }

        header('Content-Type: application/json');

        $districtId = (int)$districtId;
        if ($districtId <= 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'District is required',
                'towns' => [],
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'towns' => $this->locationModel->getTownsByDistrict($districtId),
        ]);
        exit;
    }
}
