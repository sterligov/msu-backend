<?php


namespace App\MessageHandler;


use App\Message\UserAppeal;
use App\Repository\AppealRepository;
use App\Services\MediaObjectContentUrlResolver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Twig\Environment;

class AppealHandler implements MessageHandlerInterface
{
    private ?MediaObjectContentUrlResolver $urlResolver = null;

    private ?MailerInterface $mailer = null;

    private ?Environment $twig = null;

    private ?AppealRepository $appealRepository = null;

    public function __construct(
        AppealRepository $appealRepository,
        MediaObjectContentUrlResolver $urlResolver,
        MailerInterface $mailer,
        Environment $twig
    ) {
        $this->appealRepository = $appealRepository;
        $this->urlResolver = $urlResolver;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param UserAppeal $userAppeal
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(UserAppeal $userAppeal): ?Email
    {
        $email = null;
        $appeal = $this->appealRepository->find($userAppeal->getId());

        if ($appeal) {
            $file = $appeal->getMediaObject();
            if ($file) {
                $file->setContentUrl($this->urlResolver->resolve($file));
            }

            $message = $this->twig->render('email/appeal.html.twig', [
                'appeal' => $appeal
            ]);

            $emails = [];
            $emailsEntity = $appeal->getDepartment()->getEmails();
            foreach ($emailsEntity as $email) {
                $emails[] = $email->getEmail();
            }

            $email = (new Email())
                ->from('support@msu.uz')
                ->to(...$emails)
                ->subject('Обращение в виртуальную приемную')
                ->text($message);

            $this->mailer->send($email);
        }

        return $email;
    }
}