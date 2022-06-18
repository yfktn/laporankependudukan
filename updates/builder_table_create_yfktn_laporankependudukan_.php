<?php namespace Yfktn\LaporanKependudukan\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateYfktnLaporankependudukan extends Migration
{
    public function up()
    {
        Schema::create('yfktn_laporankependudukan_', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('desa_id')->unsigned()->index();
            $table->smallInteger('periode_bulan')->unsigned()->index();
            $table->smallInteger('periode_tahun')->unsigned()->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('yfktn_laporankependudukan_');
    }
}
