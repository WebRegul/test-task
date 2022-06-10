<?php

use Faker\Generator;

/**
 * Class SecurityMethodsTest
 */
class SecurityMethodsTest extends TestCase
{
    /**
     * @var array
     */
    private static $DATA;

    public static function setUpBeforeClass(): void
    {
        self::$DATA = collect([]);
    }

    /**
     * @return void
     */
    public function testCorrectRegistration()
    {
        if (empty(env('API_VERSION'))) {
            dd(__METHOD__, 'не определена переменная API_VERSION в .env: см .env.example');
        }

        $faker = app(Generator::class);
        $login = $faker->numerify('79#########');
        $password = $faker->password(6);

        $this->post(sprintf('%s/security/registration', env('API_VERSION')), [
            'login' => $login,
            'password' => $password
        ]);

        $this->seeStatusCode(200);

        self::$DATA->put('user_id', strval($this->response->original['user_id']));
        self::$DATA->put('code', strval($this->response->original['code']));

    }

    /**
     * @return void
     */
    public function testIncorrectRegistration()
    {
        mt_srand(time());

        $faker = app(Generator::class);
        $data = [
            $faker->numerify('79#######'),
            $faker->words,
            $faker->phoneNumber,
            'привет мир!',
        ];

        $this->post(sprintf('%s/security/registration', env('API_VERSION')), [
            'login' => $data[rand(0, sizeof($data) - 1)],
            'password' => $data[rand(0, sizeof($data) - 1)]
        ]);

        $this->seeJson(['success' => false]);
    }

    /**
     * @return void
     */
    public function testCorrectVerify()
    {
        $userId = self::$DATA->get('user_id');
        $code = self::$DATA->get('code');

        $this->post(sprintf('%s/security/verify', env('API_VERSION')), [
            'user_id' => $userId,
            'code' => $code
        ]);

        $this->seeStatusCode(200);
    }

    /**
     * @return void
     */
    public function testIncorrectVerify()
    {
        mt_srand(time());

        $faker = app(Generator::class);
        $data = [
            $faker->numerify('79#######'),
            $faker->numerify('####'),
            $faker->words,
            $faker->phoneNumber,
            $faker->uuid,
            'привет мир!',
        ];

        $this->post(sprintf('%s/security/verify', env('API_VERSION')), [
            'user_id' => $data[rand(0, sizeof($data) - 1)],
            'code' => $data[rand(0, sizeof($data) - 1)]
        ]);

        $this->seeJson(['success' => false]);
    }

    /**
     * @return void
     */
    public function testCorrectRepeatSendCode()
    {
        $this->testCorrectRegistration();

        $userId = self::$DATA->get('user_id');

        $this->post(sprintf('%s/security/repeat-send-code/%s', env('API_VERSION'), $userId));

        $this->seeStatusCode(200);
    }

    /**
     * @return void
     */
    public function testIncorrectRepeatSendCode()
    {
        $this->testCorrectRegistration();

        $faker = app(Generator::class);
        $data = [
            $faker->numerify('79#######'),
            $faker->numerify('####'),
            $faker->words,
            $faker->phoneNumber,
            $faker->uuid,
            'привет мир!',
        ];

        $this->post(sprintf('%s/security/repeat-send-code/%s', env('API_VERSION'), $data[rand(0, sizeof($data) - 1)]));

        $this->seeJson(['success' => false]);
    }

    /**
     * @return void
     */
    public function testIncorrectRepeatSendCode100repeats()
    {
        $this->testCorrectRegistration();

        $userId = self::$DATA->get('user_id');

        for ($i = 0; $i < 100; $i++) {
            $this->post(sprintf('%s/security/repeat-send-code/%s', env('API_VERSION'), $userId));
        }

        $this->seeJson(['success' => false]);
    }
}
