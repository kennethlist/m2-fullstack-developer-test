<?php
declare(strict_types=1);

namespace Cadence\Movie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Cadence\Movie\Model\MovieImport as MovieImportModel;
use Magento\Framework\App\State;

class MovieImport extends Command
{
    /**
     * @var State
     */
    public $state;
    
    /**
     * @var MovieImportModel
     */
    public $importMovies;

    /**
     * __construct
     *
     * @param State $state
     * @param MovieImportModel $importMovies
     */
    public function __construct(
        State $state,
        MovieImportModel $importMovies
    ) {
        $this->state = $state;
        $this->importMovies = $importMovies;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        $this->importMovies->import($output);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("tmdb:movies:import");
        $this->setDescription("Import popular movies from TMDB as products");
        parent::configure();
    }
}