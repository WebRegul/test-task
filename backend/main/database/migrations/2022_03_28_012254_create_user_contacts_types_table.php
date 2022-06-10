<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\UserContactType;

class CreateUserContactsTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_contacts_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('name')->unique()->comment('типа slug, для валидации');
            $table->timestamps();
            $table->softDeletes();
        });

        $userContaсtTypes = [
            [
                'title' => 'Телефон для клиентов',
                'name' => 'phone',
            ],
            [
                'title' => 'Почта',
                'name' => 'email',
            ],
            [
                'title' => 'Ссылка на Instagram',
                'name' => 'instagram',
            ],
            [
                'title' => 'Ссылка на Facebook',
                'name' => 'facebook',
            ],
            [
                'title' => 'Ссылка на Вконтакте',
                'name' => 'vkontakte',
            ],
        ];

        foreach ($userContaсtTypes as $contactData) {
            $userContaсtType = new UserContactType($contactData);
            $userContaсtType->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_contacts_types');
    }
}
