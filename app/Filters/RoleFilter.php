<?php
namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek apakah user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('/auth');
        }

        // 2. Cek apakah role user ada di dalam daftar yang diizinkan ($arguments)
        $userRole = session()->get('role');
        
        if ($arguments !== null && !in_array($userRole, $arguments)) {
            // Jika role tidak cocok, tendang kembali ke halaman sebelumnya atau dashboard
            session()->setFlashdata('error', 'Akses ditolak: Anda tidak memiliki izin untuk membuka halaman ini.');
            
            // Jika Kasir, arahkan ke POS. Jika Gudang, arahkan ke Penerimaan.
            if ($userRole === 'kasir') return redirect()->to('/pos');
            
            return redirect()->to('/items');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu diisi
    }
}