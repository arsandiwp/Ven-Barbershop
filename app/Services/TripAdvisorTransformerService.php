<?php

namespace App\Services;

class TripAdvisorTransformerService
{
    public function transformLocationResults(array $rawResults): array
    {
        $locations = [];

        foreach ($rawResults['data'] ?? [] as $item) {
            // Skip items that don't have the required fields
            if (!isset($item['geoId']) || !isset($item['trackingItems']['locationId'])) {
                continue;
            }

            $locations[] = [
                'id' => $item['geoId'],
                'locationId' => $item['trackingItems']['locationId'],
                'name' => $this->cleanHtmlString($item['heading']['htmlString'] ?? ''),
                'fullName' => $item['secondaryTextLineOne']['string'] ?? null,
                'type' => $item['trackingItems']['placeType'] ?? null,
                'country' => $this->extractCountry($item['secondaryTextLineOne']['string'] ?? ''),
                'dataType' => $item['trackingItems']['dataType'] ?? null,
                'documentId' => $item['trackingItems']['documentId'] ?? null,
            ];
        }

        return [
            'status' => 'success',
            'count' => count($locations),
            'locations' => $locations
        ];
    }


    public function processAttractionData($rawData)
    {
        $processedData = [
            'success' => true,
            'message' => 'Data atraksi berhasil diambil',
            'data' => [
                'total_atraksi' => 0,
                'atraksi' => []
            ]
        ];

        // Proses data atraksi
        if (isset($rawData['data']['attractions']) || isset($rawData['data'])) {
            $attractions = $rawData['data']['attractions'] ?? $rawData['data'] ?? [];

            foreach ($attractions as $attraction) {
                $processedAttraction = $this->processSingleAttraction($attraction);
                if ($processedAttraction) {
                    $processedData['data']['atraksi'][] = $processedAttraction;
                }
            }

            $processedData['data']['total_atraksi'] = count($processedData['data']['atraksi']);
        }

        return $processedData;
    }

    private function cleanHtmlString(string $html): string
    {
        // Remove HTML tags and decode entities
        return trim(html_entity_decode(strip_tags($html)));
    }

    private function extractCountry(string $locationString): ?string
    {
        $parts = explode(',', $locationString);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    private function processSingleAttraction($attraction)
    {
        // Cek apakah ini adalah data attraction yang valid
        if (!isset($attraction['cardTitle']) && !isset($attraction['saveId'])) {
            return null;
        }

        $processed = [
            'id' => $this->extractId($attraction),
            'nama' => $this->extractName($attraction),
            'kategori' => $this->extractCategory($attraction),
            'rating' => $this->extractRating($attraction),
            'foto' => $this->extractPhoto($attraction),
            'status' => $this->extractStatus($attraction),
            'badge' => $this->extractBadge($attraction),
            'link' => $this->extractLinks($attraction),
            'tersimpan' => $attraction['isSaved'] ?? false,
            'jarak' => $attraction['distance'] ?? null,
            'deskripsi_tambahan' => $attraction['descriptiveText'] ?? null,
            'info_komersial' => $this->extractCommerceInfo($attraction),
            'label' => $attraction['labels'] ?? [],
            'tracking_info' => [
                'key' => $attraction['trackingKey'] ?? null,
                'title' => $attraction['trackingTitle'] ?? null
            ]
        ];

        return $processed;
    }

    private function extractId($attraction)
    {
        if (isset($attraction['saveId']['id'])) {
            return $attraction['saveId']['id'];
        }

        // Ekstrak dari tracking key jika ada
        if (isset($attraction['trackingKey'])) {
            $trackingData = json_decode($attraction['trackingKey'], true);
            if (isset($trackingData['lid'])) {
                return (string) $trackingData['lid'];
            }
        }

        return null;
    }

    private function extractName($attraction)
    {
        if (isset($attraction['cardTitle']['string'])) {
            // Hapus nomor urut dari nama jika ada (contoh: "1. Grand Indonesia Mall")
            $name = $attraction['cardTitle']['string'];
            return preg_replace('/^\d+\.\s*/', '', $name);
        }

        return 'Nama tidak tersedia';
    }

    private function extractCategory($attraction)
    {
        return [
            'utama' => $attraction['primaryInfo']['text'] ?? 'Tidak berkategori',
            'sekunder' => $attraction['secondaryInfo']['text'] ?? null
        ];
    }

    private function extractRating($attraction)
    {
        if (!isset($attraction['bubbleRating'])) {
            return [
                'nilai' => null,
                'total_ulasan' => 0,
                'teks_ulasan' => 'Tidak ada ulasan',
                'kualitas' => 'Tidak diketahui'
            ];
        }

        $rating = $attraction['bubbleRating']['rating'] ?? 0;
        $reviewsText = $attraction['bubbleRating']['numberReviews']['string'] ?? '(0)';

        // Ekstrak angka dari string seperti "(3,264)"
        $reviewCount = (int) preg_replace('/[^\d]/', '', $reviewsText);

        return [
            'nilai' => $rating,
            'total_ulasan' => $reviewCount,
            'teks_ulasan' => $reviewsText,
            'kualitas' => $this->getRatingQuality($rating)
        ];
    }

    private function extractPhoto($attraction)
    {
        if (!isset($attraction['cardPhoto']['sizes'])) {
            return [
                'thumbnail' => null,
                'url_template' => null,
                'dimensi' => null
            ];
        }

        $photoData = $attraction['cardPhoto']['sizes'];

        return [
            'thumbnail' => str_replace(['{width}', '{height}'], ['300', '200'], $photoData['urlTemplate'] ?? ''),
            'url_template' => $photoData['urlTemplate'] ?? null,
            'dimensi' => [
                'max_width' => $photoData['maxWidth'] ?? null,
                'max_height' => $photoData['maxHeight'] ?? null
            ]
        ];
    }

    private function extractStatus($attraction)
    {
        $status = $attraction['secondaryInfo']['text'] ?? null;

        return [
            'teks' => $status,
            'buka_sekarang' => $status === 'Open now',
            'tutup_sementara' => isset($attraction['closureInfo']) && $attraction['closureInfo'] !== null
        ];
    }

    private function extractBadge($attraction)
    {
        if (!isset($attraction['badge'])) {
            return null;
        }

        $badge = $attraction['badge'];

        return [
            'tipe' => $badge['type'] ?? null,
            'ukuran' => $badge['size'] ?? null,
            'tahun' => $badge['year'] ?? null,
            'detail' => $badge['badgeDetails'] ?? null,
            'deskripsi' => $this->getBadgeDescription($badge['type'] ?? null)
        ];
    }

    private function extractLinks($attraction)
    {
        $links = [
            'detail' => null,
            'tur' => null
        ];

        // Link detail
        if (isset($attraction['cardLink']['route'])) {
            $route = $attraction['cardLink']['route'];
            $links['detail'] = [
                'url' => $route['url'] ?? null,
                'page' => $route['page'] ?? null,
                'content_id' => $route['params']['contentId'] ?? null
            ];
        }

        // Link tours/commerce
        if (isset($attraction['commerceButtons']['secondCommerceButton']['link'])) {
            $commerceLink = $attraction['commerceButtons']['secondCommerceButton']['link'];
            $links['tur'] = [
                'url' => $commerceLink['route']['url'] ?? null,
                'teks' => $commerceLink['text']['string'] ?? 'Lihat Tur',
                'tersedia' => true
            ];
        } else {
            $links['tur'] = [
                'url' => null,
                'teks' => null,
                'tersedia' => false
            ];
        }

        return $links;
    }

    private function extractCommerceInfo($attraction)
    {
        if (!isset($attraction['commerceButtons'])) {
            return [
                'tersedia' => false,
                'tombol_utama' => null,
                'tombol_kedua' => null
            ];
        }

        $commerce = $attraction['commerceButtons'];

        return [
            'tersedia' => isset($commerce['firstCommerceButton']) || isset($commerce['secondCommerceButton']),
            'tombol_utama' => $commerce['firstCommerceButton'] ?? null,
            'tombol_kedua' => isset($commerce['secondCommerceButton']) ? [
                'teks' => $commerce['secondCommerceButton']['link']['text']['string'] ?? null,
                'tersedia' => true
            ] : null
        ];
    }

    private function getRatingQuality($rating)
    {
        if ($rating >= 4.5)
            return 'Luar Biasa';
        if ($rating >= 4.0)
            return 'Sangat Bagus';
        if ($rating >= 3.5)
            return 'Bagus';
        if ($rating >= 3.0)
            return 'Cukup';
        if ($rating >= 2.0)
            return 'Kurang';
        return 'Buruk';
    }

    private function getBadgeDescription($badgeType)
    {
        $descriptions = [
            'TRAVELLER_CHOICE' => 'Pilihan Traveller',
            'RECOMMENDED' => 'Direkomendasikan',
            'POPULAR' => 'Populer',
            'TOP_RATED' => 'Rating Tertinggi'
        ];

        return $descriptions[$badgeType] ?? $badgeType;
    }
}