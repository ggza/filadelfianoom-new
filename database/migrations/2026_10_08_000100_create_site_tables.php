<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A nyilvános oldal szövegblokkjai (a régi oldal szövegei, nyelvenként)
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('key', 40)->unique();     // fundatia, servicii, efecte, ...
            $table->string('admin_label');
            $table->string('title_ro')->nullable();
            $table->string('title_hu')->nullable();
            $table->longText('body_ro')->nullable();
            $table->longText('body_hu')->nullable();
            $table->boolean('show_ro')->default(true);
            $table->boolean('show_hu')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Galéria és hero képek
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('file');                   // uploads/galeria/xxx.webp
            $table->string('thumb');                  // uploads/galeria/kicsi/xxx.webp
            $table->unsignedInteger('width')->default(0);
            $table->unsignedInteger('height')->default(0);
            $table->string('caption_hu')->nullable();
            $table->string('caption_ro')->nullable();
            $table->boolean('in_hero')->default(true);
            $table->boolean('in_gallery')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        // Bejegyzések / hírek
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title_ro')->nullable();
            $table->string('title_hu')->nullable();
            $table->longText('body_ro')->nullable();
            $table->longText('body_hu')->nullable();
            $table->string('image')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // Beköltözési jelentkezések (a személyes mezők titkosítva)
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 2);
            $table->text('representative');           // titkosított
            $table->text('resident');                 // titkosított
            $table->text('diseases')->nullable();     // titkosított
            $table->text('payment_method')->nullable();
            $table->date('preferred_date')->nullable();
            $table->text('phone')->nullable();        // titkosított
            $table->text('email')->nullable();        // titkosított
            $table->string('status', 20)->default('new'); // new | in_progress | closed
            $table->text('admin_note')->nullable();
            $table->string('source', 20)->default('web'); // web | excel
            $table->timestamps();
        });

        // Lakók (a hozzátartozói fiókokhoz)
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('room', 40)->nullable();
            $table->date('moved_in_at')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('resident_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('relation', 60)->nullable();  // pl. lánya, fia
            $table->timestamps();
            $table->unique(['resident_id', 'user_id']);
        });

        // A hozzátartozóknak szánt hírek, fotók (csak bejelentkezve, jogosultsággal)
        Schema::create('resident_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->timestamps();
        });

        Schema::create('resident_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resident_update_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path');                  // storage/app/private/residents/...
            $table->string('thumb');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_photos');
        Schema::dropIfExists('resident_updates');
        Schema::dropIfExists('resident_user');
        Schema::dropIfExists('residents');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('sections');
    }
};
