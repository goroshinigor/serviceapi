<?php

namespace App\Infrastructure\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Infrastructure\Services\Remote\PMSIntegration\Branches\FetchBranchesService as PMSFetchBranchesService;
use App\Infrastructure\Services\Remote\AttikaIntegration\Branches\FetchBranchesService as AttikaFetchBranchesService;
use App\Infrastructure\Services\Remote\PMSIntegration\Branches\UpdateBranchesService;

/**
 * Class UpdateBranchesFromPMSCommand.
 */
class UpdateBranchesFromPMSCommand extends Command
{

    /**
     *
     * @var type 
     */
    protected static $defaultName = 'pms:update-branches';

    /**
     *
     * @var PMSFetchBranchesService 
     */
    private $PMSFetchBranchesService;

    /**
     *
     * @var AttikaFetchBranchesService 
     */
    private $attikaFetchBranchesService;

    /**
     *
     * @var UpdateBranchesService 
     */
    private $updateBranchesService;

    /**
     * 
     * @param \App\Infrastructure\Command\FetchBranchesService $brancesSevice
     */
    public function __construct(
        PMSFetchBranchesService $PMSFetchBranchesService,
        AttikaFetchBranchesService $attikaFetchBranchesService,
        UpdateBranchesService $updateBranchesService
    ) {
        parent::__construct(self::$defaultName);
        $this->PMSFetchBranchesService = $PMSFetchBranchesService;
        $this->attikaFetchBranchesService = $attikaFetchBranchesService;
        $this->updateBranchesService = $updateBranchesService;
    }

    /**
     * 
     */
    protected function configure()
    {
        $this
            ->setDescription('Command allows you to fetch new Branches from PMS')
            ->addOption('check-only', null, InputOption::VALUE_NONE, 'Check new only, without fetching to db')
        ;
    }

    /**
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $PMSBranches = $this->PMSFetchBranchesService->fetchBranchesFromPMS();
        $attikaBranches = $this->attikaFetchBranchesService->fetchBranchesFromAttika($PMSBranches);
        if ($input->getOption('check-only')) 
        {
            echo 'Branches fetched: ' . $PMSBranches->totalCountRecords . PHP_EOL;

            return 0;
        }

        $result = $this->updateBranchesService
                ->saveToLocalStorage($PMSBranches, $attikaBranches);

        $io->success('Branches updated sucessfully!');

        return 0;
    }
}
