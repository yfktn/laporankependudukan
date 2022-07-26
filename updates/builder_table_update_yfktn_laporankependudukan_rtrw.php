<?php namespace Yfktn\LaporanKependudukan\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateYfktnLaporankependudukanRtrw extends Migration
{
    public function up()
    {
        Schema::table('yfktn_laporankependudukan_rtrw', function($table)
        {
            $table->integer('jumlah_pindah_kk')->unsigned()->default(0);
            $table->integer('jumlah_pendatang_kk')->unsigned()->default(0);
        });
    }
    
    public function down()
    {
        Schema::table('yfktn_laporankependudukan_rtrw', function($table)
        {
            $table->dropColumn('jumlah_pindah_kk');
            $table->dropColumn('jumlah_pendatang_kk');
        });
    }
}
