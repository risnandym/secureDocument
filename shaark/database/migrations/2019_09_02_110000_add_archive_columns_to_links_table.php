<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArchiveColumnsToLinksTable extends Migration
{
    public function up()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->string('archive')->nullable()->after('extra');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->renameColumn('extra', 'preview');
        });
    }

    public function down()
    {
        Schema::table('links', function (Blueprint $table) {
            $table->renameColumn('preview', 'extra');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('archive');
        });
    }
}
