<?php

namespace App\Service;

use Slince\Shopify\Client;
use Slince\Shopify\PrivateAppCredential;

class ShopifyApi
{
    /**
     * @var Client
     */
    private $apiClient;

    public function __construct()
    {
        $credential = new PrivateAppCredential('24732280025cd870a65a3d9eabaab94f', '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', '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');

        $this->apiClient = new Client($credential, 'test-shop129.myshopify.com', [
            'metaCacheDir' => './tmp',
        ]);
    }

    public function getProducts(array $options)
    {
        return $this->apiClient->getProductManager()->findAll($options);
    }

    public function getCollections()
    {
        return $this->apiClient->getCustomCollectionManager()->findAll();
    }
}
