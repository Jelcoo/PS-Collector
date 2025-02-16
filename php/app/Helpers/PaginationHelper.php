<?php

namespace App\Helpers;

class PaginationHelper
{
    public static function paginate(array $data, int $totalRecords, int $perPage = 25, int $currentPage = 1): mixed
    {
        $currentPage = (int) $currentPage;
        $pages = [
            'totalPages' => ceil($totalRecords / $perPage),
            'currentPage' => $currentPage,
            'previousPages' => [],
            'nextPages' => [],
            'totalRecords' => $totalRecords,
            'recordsPerPage' => $perPage,
        ];

        for ($i = 1; $i <= 2; ++$i) {
            if ($currentPage - $i >= 1) {
                $pages['previousPages'][] = $currentPage - $i;
            }
            if ($currentPage + $i <= $pages['totalPages']) {
                $pages['nextPages'][] = $currentPage + $i;
            }
        }

        return [
            'data' => $data,
            'pages' => $pages,
        ];
    }
}
