<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Kartu aplikasi untuk portal BangData: nama, deskripsi, dan alamat PRISMA Builder supaya BangData
 * menampilkannya sebagai kartu yang bila diklik membuka PRISMA Builder. Hanya profil publik,
 * tanpa token. Alamat tujuan memakai PORTAL_PUBLIK_URL bila diisi, kalau tidak, alamat pemanggil.
 */
class PortalCardController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $url = rtrim((string) (config('app.portal_publik_url') ?: $request->getSchemeAndHttpHost()), '/').'/';

        return response()->json([
            'sukses' => true,
            'data' => [[
                'id' => 'prisma-builder',
                'nama' => 'PRISMA Builder',
                'tagline' => 'Susun kuesioner dan kelola instrumen survei',
                'deskripsi' => 'Bangun kuesioner dengan logika, terbitkan tautan pengisian, dan kelola respons dari semua kegiatan BPS.',
                'tags' => ['Kuesioner', 'Instrumen', 'Respons'],
                'warna' => 'orange',
                'url' => $url,
                'urutan' => 20,
            ]],
        ]);
    }
}
