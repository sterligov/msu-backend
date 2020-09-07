<?php


namespace App\Command;


use App\Entity\Department;
use App\Entity\Email;
use App\Entity\Tag;
use App\Repository\DepartmentRepository;
use App\Repository\EmailRepository;
use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddEmailDepartmentCommand extends Command
{
    protected static $defaultName = 'app:add-email';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var EmailRepository
     */
    private $emailRepository;

    /**
     * @var DepartmentRepository
     */
    private $departmentRepository;

    /**
     * DatabaseDefaultValuesFillerCommand constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, EmailRepository $emailRepository, DepartmentRepository $departmentRepository)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->emailRepository = $emailRepository;
        $this->departmentRepository = $departmentRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create email and department or create relation between it, if it already exist.')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
            ->addArgument('department', InputArgument::REQUIRED, 'Department name')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Starting',
            '============',
            '',
        ]);

        $email = $input->getArgument('email');
        $emailEntity = $this->emailRepository->findBy(['email' => $email]);

        if (!$emailEntity) {
            $emailEntity = new Email();
            $emailEntity->setEmail($email);
        }

        $department = $input->getArgument('department');
        $departmentEntity = $this->emailRepository->findBy(['name' => $department]);

        if (!$departmentEntity) {
            $departmentEntity = new Department();
            $departmentEntity->setName($department);

        }

        $emailEntity->addDepartment($departmentEntity);

        $this->entityManager->persist($emailEntity);
        $this->entityManager->persist($departmentEntity);

        $this->entityManager->flush();

        $output->writeln([
            'Successfully end',
            '============',
            '',
        ]);

        return 0;
    }
}