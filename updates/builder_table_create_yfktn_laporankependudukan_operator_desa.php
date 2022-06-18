<?php namespace Yfktn\LaporanKependudukan\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateYfktnLaporankependudukanOperatorDesa extends Migration
{
    public function up()
    {
        Schema::create('yfktn_laporankependudukan_operator_desa', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('desa_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('yfktn_laporankependudukan_operator_desa');
    }
}
