<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\FollowupService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:process-followups',
    description: 'Send due follow-up check-in emails and wipe encrypted email data after send.',
)]
final class ProcessFollowupsCommand extends Command
{
    public function __construct(
        private readonly FollowupService $followupService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sent = $this->followupService->processQueue();
        $output->writeln(sprintf('Sent %d follow-up email(s).', $sent));
        return Command::SUCCESS;
    }
}
