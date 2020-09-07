<?php

namespace App\Command;

use App\Entity\Department;
use App\Entity\Email;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseDefaultValuesFillerCommand extends Command
{
    protected static $defaultName = 'app:fill-db';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * DatabaseDefaultValuesFillerCommand constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Starting fill',
            '============',
            '',
        ]);

        foreach (['Новости', 'Объявления', 'Календарь', 'Закрытая страница', 'Поздравления'] as $item) {
            $tag = new Tag();
            $tag->setName($item);

            $this->entityManager->persist($tag);
        }

        $departments = [
            'Администрация' => [
                'denis0324@gmail.com',
                'alimoffr@gmail.com',
                'shuhrat2500@mail.ru',
                'info@msu.uz'
            ],
            'Библиотека' => [
                'library@msu.uz',
            ]
        ];

        foreach ($departments as $department => $emails) {
            $departmentEntity = new Department();
            $departmentEntity->setName($department);

            foreach ($emails as $email) {
                $emailEntity = new Email();
                $emailEntity->setEmail($email);

                $departmentEntity->addEmail($emailEntity);

                $this->entityManager->persist($emailEntity);
            }

            $this->entityManager->persist($departmentEntity);
        }

        $this->entityManager->flush();

        $output->writeln([
            'Successfully end',
            '============',
            '',
        ]);

        return 0;
    }
}