<?php

namespace App\Controllers;

use App\Models\LinkModel;
use CodeIgniter\Controller;

class LinkController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function create()
    {
        $model = new LinkModel();
        $url = $this->request->getPost('url');

        // URL'yi doğrula
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return redirect()->to('/')->with('error', 'Geçersiz URL formatı.');
        }

        // URL'nin zaten var olup olmadığını kontrol et
        $existingLink = $model->where('original_url', $url)->first();
        if ($existingLink) {
            return redirect()->to('/')->with('message', 'Kısa URL: ' . base_url($existingLink['short_code']));
        }

        // Güvenli bir kısa kod oluştur
        $shortCode = $this->generateShortCode(12); // Kısa kodun 12 karakter uzunluğunda olduğundan emin ol

        $data = [
            'original_url' => $url,
            'short_code' => $shortCode,
        ];

        $model->insert($data);

        return redirect()->to('/')->with('message', 'Kısa URL: ' . base_url($shortCode));
    }

    public function redirect($code)
    {
        $model = new LinkModel();
        $link = $model->where('short_code', $code)->first();

        if ($link) {
            return redirect()->to($link['original_url']);
        } else {
            return redirect()->to('/')->with('error', 'URL bulunamadı.');
        }
    }

    private function generateShortCode($length = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_';
        $charactersLength = strlen($characters);
        $shortCode = '';

        // Kısa kod istenen uzunluğa ulaşana kadar döngüyü çalıştır
        while (strlen($shortCode) < $length) {
            // Rastgele baytlar üret
            $randomBytes = random_bytes(1);
            
            // Rastgele baytı 0 ile 255 arasındaki bir tamsayıya dönüştür
            $byteInt = ord($randomBytes);
            
            // Tamsayıyı karakter kümesindeki bir karaktere eşleştir
            $shortCode .= $characters[$byteInt % $charactersLength];
        }

        return $shortCode;
    }
}
