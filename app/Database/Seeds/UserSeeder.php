<?php namespace App\Database\Seeds;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'username' => 'kasir',
                'password' => password_hash('kasir123', PASSWORD_DEFAULT),
                'role'     => 'kasir',
            ],
            [
                'username' => 'gudang',
                'password' => password_hash('gudang123', PASSWORD_DEFAULT),
                'role'     => 'gudang',
            ],
        ];

        // Menyimpan array data ke dalam tabel users
        $this->db->table('users')->insertBatch($data);
    }
}