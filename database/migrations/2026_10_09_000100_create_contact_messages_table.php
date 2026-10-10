<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kapcsolatfelvételi űrlap üzenetei (a személyes adatok titkosítva)
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 2);
            $table->text('name');                     // titkosított
            $table->text('email');                    // titkosított
            $table->text('phone')->nullable();        // titkosított
            $table->text('message');                  // titkosított
            $table->string('status', 20)->default('new'); // new | answered | closed
            $table->text('admin_note')->nullable();   // titkosított
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};
