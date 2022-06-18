<?php namespace Yfktn\LaporanKependudukan\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateYfktnLaporankependudukanRtrw extends Migration
{
    public function up()
    {
        Schema::create('yfktn_laporankependudukan_rtrw', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('nama_rtrw', 50)->index();
            $table->integer('laporan_kependudukan_id')->unsigned();
            $table->integer('jumlah_lahir_laki')->unsigned()->default(0);
            $table->integer('jumlah_lahir_perempuan')->unsigned()->default(0);
            $table->integer('jumlah_mati_laki')->unsigned()->default(0);
            $table->integer('jumlah_mati_perempuan')->unsigned()->default(0);
            $table->integer('jumlah_pendatang_laki')->unsigned()->default(0);
            $table->integer('jumlah_pendatang_perempuan')->unsigned()->default(0);
            $table->integer('jumlah_pindah_laki')->unsigned()->default(0);
            $table->integer('jumlah_pindah_perempuan')->unsigned()->default(0);
            $table->integer('jumlah_awal_kk')->unsigned()->default(0);
            $table->integer('jumlah_awal_laki')->unsigned()->default(0);
            $table->integer('jumlah_awal_perempuan')->unsigned()->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('yfktn_laporankependudukan_rtrw');
    }
}
