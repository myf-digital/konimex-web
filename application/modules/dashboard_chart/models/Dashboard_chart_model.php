<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_chart_model extends CI_Model
{
    public function load($data)
    {
        $productivity = [
            'labels' => [
                'Ahmad Junaedi', 'Lamsar', 'Agus Wibowo', 'Tasyahudin',
                'Dede Agus', 'Rizal', 'Anggi', 'Eka Bunga', 'Santi', 'Firmansyah'
            ],
            'data' => [89, 5, 35, 51, 65, 18, 3, 2, 0, 100],
        ];
        $performance = [
            'labels' => ['PAR001', 'PAR002', 'PAR004', 'PAR010', 'PAR101', 'PAR007', 'PAR008', 'PAR011'],
            'data_schedule' => [0, 2, 0, 0, 0, 0, 2, 0],
            'data_call' => [0, 1, 0, 0, 0, 0, 1, 0],
            'data_extra' => [0, 7, 0, 0, 0, 0, 3, 0],
            'data_crc' => [0, 5, 0, 0, 0, 0, 4, 0],
            'data_order' => [0, 4, 0, 0, 0, 0, 5, 0],
        ];
        $summary = [
            'labels' => ['JABODETABEK', 'WEST JAVA', 'EAST JAVA BANUS', 'CENTRAL JAVA', 'SULAMPUA', 'SUMATERA'],
            'data_quota' => [20, 15, 18, 14, 10, 20],
            'data_actual' => [18, 12, 16, 10, 8, 17],
        ];

        return [
            'productivity' => $productivity,
            'performance' => $performance,
            'summary' => $summary,
        ];
    }
}
