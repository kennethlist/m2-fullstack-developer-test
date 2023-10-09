<?php
declare(strict_types=1);

namespace Cadence\Movie\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Cadence\Movie\Model\MovieInfo as MovieInfoModel;

class MovieInfo extends Command
{
    const PRODUCT_SKU_ARGUMENT = "sku";

    /**
     * @var MovieInfoModel
     */
    public $movieInfo;

    /**
     * __construct
     *
     * @param MovieInfoModel $movieInfo
     */
    public function __construct(
        MovieInfoModel $movieInfo
    ) {
        $this->movieInfo = $movieInfo;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $sku = $input->getArgument(self::PRODUCT_SKU_ARGUMENT);
        $data = $this->movieInfo->get($sku);

        if (count($data)) {
            $output->writeln(json_encode(array_reduce($data, function($carry, $item) {
                $carry[$item['attribute_code']] = $item['value'];
                return $carry;
            }, [])));
        } else {
            $output->writeln('Product not found.');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("tmdb:movies:info");
        $this->setDescription("Print movie info by SKU");
        $this->setDefinition([
            new InputArgument(self::PRODUCT_SKU_ARGUMENT, InputArgument::REQUIRED, "sku"),
        ]);
        parent::configure();
    }
}
