<?php

namespace App\Controller;

use App\Service\ShopifyApi;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    /**
     * Принимает данные от OIM в момент заполнения формы.
     *
     * @Route("/", name="index")
     */
    public function index(ShopifyApi $shopify)
    {

        dump($shopify->getCollections());
        exit();
    }
}
