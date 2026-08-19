<?php

use App\Enums\AttachmentDocumentTypeEnum;
use App\Enums\AttachmentFileTypeEnum;
use App\Enums\AttachmentStorageEnum;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignIdFor(User::class);
            $table->string('path');
            $table->enum('disk', AttachmentStorageEnum::toArray())->default(AttachmentStorageEnum::BLOB->value);
            $table->enum('type', AttachmentFileTypeEnum::toArray())->nullable();
            $table->enum('document_type', AttachmentDocumentTypeEnum::toArray())->nullable();
            $table->json('file_meta')->nullable();
            $table->nullableMorphs('attachable');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attachments');
    }
};
