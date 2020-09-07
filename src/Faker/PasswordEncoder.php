<?php

namespace App\Faker;

use Faker\Generator;
use Faker\Provider\Base;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class PasswordEncoder extends Base
{
    private EncoderFactoryInterface $encoderFactory;

    /**
     * {@inheritdoc}
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(Generator $generator, EncoderFactoryInterface $encoderFactory)
    {
        parent::__construct($generator);

        $this->encoderFactory = $encoderFactory;
    }

    /**
     * @param string $userClass
     * @param string $plainPassword
     * @param string|null $salt
     *
     * @return string
     */
    public function encodePassword(string $userClass, string $plainPassword, string $salt = ''): string
    {
        $password = $this->encoderFactory->getEncoder($userClass)->encodePassword($plainPassword, $salt);

        return $this->generator->parse($password);
    }
}
