<?php

class LocationModel
{
    use Database;

    public function getDistricts()
    {
        $sql = "SELECT id, district_name, district_code, province
                FROM districts
                WHERE is_active = 1
                ORDER BY province, district_name";

        $result = $this->query($sql);
        return is_array($result) ? $result : [];
    }

    public function getDistrictById($districtId)
    {
        return $this->get_row(
            "SELECT id, district_name, district_code, province
             FROM districts
             WHERE id = :id AND is_active = 1
             LIMIT 1",
            ['id' => (int)$districtId]
        );
    }

    public function getDistrictByName($districtName)
    {
        return $this->get_row(
            "SELECT id, district_name, district_code, province
             FROM districts
             WHERE district_name = :name AND is_active = 1
             LIMIT 1",
            ['name' => trim((string)$districtName)]
        );
    }

    public function getTownsByDistrict($districtId)
    {
        $sql = "SELECT id, town_name, district_id, extra_distance_km, postal_code
                FROM towns
                WHERE district_id = :district_id AND is_active = 1
                ORDER BY town_name";

        $result = $this->query($sql, ['district_id' => (int)$districtId]);
        return is_array($result) ? $result : [];
    }

    public function getTownById($townId)
    {
        return $this->get_row(
            "SELECT id, town_name, district_id, extra_distance_km, postal_code
             FROM towns
             WHERE id = :id AND is_active = 1
             LIMIT 1",
            ['id' => (int)$townId]
        );
    }

    public function getTownByName($townName, $districtId)
    {
        return $this->get_row(
            "SELECT id, town_name, district_id, extra_distance_km, postal_code
             FROM towns
             WHERE district_id = :district_id
             AND town_name = :town_name
             AND is_active = 1
             LIMIT 1",
            [
                'district_id' => (int)$districtId,
                'town_name' => trim((string)$townName),
            ]
        );
    }

    public function resolveDistrictId($districtName)
    {
        $district = $this->getDistrictByName($districtName);
        return $district ? (int)$district->id : null;
    }

    public function resolveTownId($townName, $districtId)
    {
        if (!$districtId || $townName === null || $townName === '') {
            return null;
        }

        $town = $this->getTownByName($townName, $districtId);
        return $town ? (int)$town->id : null;
    }

    public function getDistanceKm($fromDistrictId, $fromTownId, $toDistrictId, $toTownId)
    {
        if (!$fromDistrictId || !$toDistrictId) {
            return null;
        }

        $districtDistance = $this->get_row(
            "SELECT distance_km
             FROM district_distances
             WHERE (from_district_id = :from_district AND to_district_id = :to_district)
                OR (from_district_id = :to_district AND to_district_id = :from_district)
             LIMIT 1",
            [
                'from_district' => (int)$fromDistrictId,
                'to_district' => (int)$toDistrictId,
            ]
        );

        if (!$districtDistance) {
            return null;
        }

        $total = (float)$districtDistance->distance_km;

        if ($fromTownId) {
            $fromTown = $this->getTownById($fromTownId);
            $total += (float)($fromTown->extra_distance_km ?? 0);
        }

        if ($toTownId) {
            $toTown = $this->getTownById($toTownId);
            $total += (float)($toTown->extra_distance_km ?? 0);
        }

        return round($total, 2);
    }

    public function buildLocationPayload($districtId = null, $townId = null)
    {
        $districtId = $districtId ? (int)$districtId : null;
        $townId = $townId ? (int)$townId : null;

        $district = $districtId ? $this->getDistrictById($districtId) : null;
        $town = $townId ? $this->getTownById($townId) : null;

        return [
            'district_id' => $district ? (int)$district->id : null,
            'district_name' => $district->district_name ?? null,
            'town_id' => $town ? (int)$town->id : null,
            'town_name' => $town->town_name ?? null,
        ];
    }
}
