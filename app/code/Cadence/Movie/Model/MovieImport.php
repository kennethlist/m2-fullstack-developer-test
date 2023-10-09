<?php
namespace Cadence\Movie\Model;

use Cadence\Movie\Model\TheMovieDbApi;
use Cadence\Movie\Helper\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;

class MovieImport
{
    const DEFAULT_QTY = 100;
    const DEFAULT_PRICE = 5.99;

    /**
     * @var TheMovieDbApi
     */
    public $theMovieDbApi;

    /**
     * @var Config
     */
    public $config;

    /**
     * @var ProductInterfaceFactory
     */
    public $productFactory;

    /**
     * @var ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * __construct
     *
     * @param Config $config
     * @param TheMovieDbApi $theMovieDbApi
     * @param ProductInterfaceFactory $productFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Config $config,
        TheMovieDbApi $theMovieDbApi,
        ProductInterfaceFactory $productFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->theMovieDbApi = $theMovieDbApi;
        $this->config = $config;
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * Run Import
     *
     * @param OutputInterface $output
     * @return void
     */
    public function import($output)
    {
        $output->writeln('Fetching Popular Movies');
        $movies = $this->theMovieDbApi->getPopularMovies();
        foreach ($movies->results as $index => $movie) {
            $data = [
                'sku' => $movie->id,
                'name' => $movie->title,
                'description' => $movie->overview,
            ];

            sleep(.5); // @FIXME use expontential backoff for handling rate limiting
            $data = array_merge($data, $this->theMovieDbApi->getMovieDetails($movie->id));
            sleep(.5);
            $data = array_merge($data, $this->theMovieDbApi->getMovieCredits($movie->id));

            $this->createProduct($data);
            $output->writeln(($index + 1) . '/' . count($movies->results) . ' - ' . $movie->id . ' ' . $movie->title);
        }
        $output->writeln('Finished');
    }

    /**
     * Create Product
     *
     * @param array $data
     * @return void
     */
    public function createProduct($data)
    {
        $product = $this->productFactory->create();

        $product->setSku($data['sku'])
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setGenre($data['genre'])
            ->setYear($data['year'])
            ->setVoteAverage($data['vote_average'])
            ->setActors($data['actors'])
            ->setProducer($data['producer'])
            ->setDirector($data['director'])
            ->setAttributeSetId(Config::MOVIE_ATTRIBUTE_SET_ID)
            ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
            ->setPrice(self::DEFAULT_PRICE)
            ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
            ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL)
            ->setCategoryIds([Config::MOVIE_CATEGORY_ID])
            ->setStockData(
                [
                    'use_config_manage_stock' => 0,
                    'manage_stock' => 1,
                    'is_in_stock' => 1,
                    'qty' => self::DEFAULT_QTY
                ]
            );

        $this->productRepository->save($product);
    }
}
