<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Member;
use App\Models\Product;
use App\Models\Role;
use App\Models\SavingType;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::insert([
            ['name' => 'Admin'],
            ['name' => 'Kasir'],
        ]);

        User::insert([
            ['name' => 'Admin','role_id' => 1,'username' => 'admin','password' => bcrypt('admin')],
            ['name' => 'Kasir','role_id' => 2,'username' => 'kasir','password' => bcrypt('kasir')],
        ]);

        Member::insert([
            ['name' => 'Budi Sugiarto', 'description' => 'PT. ABC', 'whatsapp' => '081234567891', 'code' => '1234']
        ]);

        Customer::insert([
            ['name' => 'Andi Madieka', 'description' => 'PT. ABC', 'whatsapp' => '081234567891']
        ]);

        Supplier::insert([
            ['name' => 'Wahyu Sumaji', 'description' => 'PT. XYZ', 'whatsapp' => '081234567891']
        ]);

        Product::insert([
            ['name' => 'Indomie Goreng', 'buy_price' => 3000, 'price_for_member' => 3200, 'price_for_customer' => 3200,  'barcode' => 1234],
            ['name' => 'Le Mineral Galon', 'buy_price' => 19000, 'price_for_member' => 20000, 'price_for_customer' => 21000,  'barcode' => 2234],
        ]);

        Account::insert([
            ['id'=>1,'group'=>'Aset','sub'=>'Aset Lancar','code'=>'101','name'=>'Kas','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>1],
            ['id'=>2,'group'=>'Aset','sub'=>'Aset Lancar','code'=>'102','name'=>'Bank','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>1],
            ['id'=>3,'group'=>'Aset','sub'=>'Aset Lancar','code'=>'103','name'=>'Piutang Usaha','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>4,'group'=>'Aset','sub'=>'Aset Lancar','code'=>'104','name'=>'Piutang Simpan Pinjam','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>5,'group'=>'Aset','sub'=>'Aset Lancar','code'=>'105','name'=>'Persediaan Toko','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>6,'group'=>'Aset','sub'=>'Aset Tetap','code'=>'151','name'=>'Peralatan','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>7,'group'=>'Aset','sub'=>'Aset Tetap','code'=>'152','name'=>'Akumulasi Penyusutan','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>8,'group'=>'Kewajiban','sub'=>'Jangka Pendek','code'=>'201','name'=>'Utang Usaha','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>9,'group'=>'Kewajiban','sub'=>'Simpanan','code'=>'202','name'=>'Simpanan Anggota Wajib','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>10,'group'=>'Kewajiban','sub'=>'Simpanan','code'=>'203','name'=>'Simpanan Anggota Sukarela','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>11,'group'=>'Modal','sub'=>'Modal','code'=>'301','name'=>'Modal Koperasi','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>12,'group'=>'Modal','sub'=>'Modal','code'=>'302','name'=>'SHU Ditahan','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>13,'group'=>'Pendapatan','sub'=>'Toko Anggota','code'=>'401','name'=>'Pendapatan Toko dari Anggota','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>14,'group'=>'Pendapatan','sub'=>'Toko Umum','code'=>'402','name'=>'Pendapatan Toko dari Umum','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>15,'group'=>'Pendapatan','sub'=>'Simpan Pinjam Anggota','code'=>'403','name'=>'Pendapatan Bunga dari Anggota','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>16,'group'=>'Pendapatan','sub'=>'Simpan Pinjam Umum','code'=>'404','name'=>'Pendapatan Bunga dari Umum','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>17,'group'=>'Pendapatan','sub'=>'Jasa Anggota','code'=>'405','name'=>'Pendapatan Jasa dari Anggota','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>18,'group'=>'Pendapatan','sub'=>'Jasa Umum','code'=>'406','name'=>'Pendapatan Jasa dari Umum','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>19,'group'=>'Pendapatan','sub'=>'Lain-lain','code'=>'407','name'=>'Pendapatan Lain-lain','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>20,'group'=>'HPP','sub'=>'Toko','code'=>'501','name'=>'Harga Pokok Penjualan','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>21,'group'=>'Beban','sub'=>'Operasional','code'=>'601','name'=>'Beban Gaji','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>22,'group'=>'Beban','sub'=>'Operasional','code'=>'602','name'=>'Beban Listrik & Air','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>23,'group'=>'Beban','sub'=>'Operasional','code'=>'603','name'=>'Beban ATK','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>24,'group'=>'Beban','sub'=>'Operasional','code'=>'604','name'=>'Beban Penyusutan','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>25,'group'=>'Beban','sub'=>'Lain-lain','code'=>'605','name'=>'Beban Lain-lain','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>26,'group'=>'Aset','sub'=>'Aset Lancar','code'=>'106','name'=>'PPN Masukan','normal_balance'=>'Debit','initial_balance'=>0,'is_payment_gateway'=>0],
            ['id'=>27,'group'=>'Kewajiban','sub'=>'Jangka Pendek','code'=>'204','name'=>'PPN Keluaran','normal_balance'=>'Kredit','initial_balance'=>0,'is_payment_gateway'=>0],
        ]);

        SavingType::insert([
            ['id'=>1,'name'=>'Simpanan Pokok','account_id'=>11],
            ['id'=>2,'name'=>'Simpanan Wajib','account_id'=>9],
            ['id'=>3,'name'=>'Simpanan Sukarela','account_id'=>10],
        ]);
    }
}
