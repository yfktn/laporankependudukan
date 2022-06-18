<?php namespace Yfktn\LaporanKependudukan\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateYfktnLaporankependudukanDesa extends Migration
{
    public function up()
    {
        Schema::create('yfktn_laporankependudukan_desa', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('nama', 2024);
            $table->text('keterangan')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('slug', 2024)->index();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('yfktn_laporankependudukan_desa');
    }
}
