<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Product;
use App\Service\ShopifyApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ShopifyApi $shopify)
    {
        $em = $this->getDoctrine()->getManager();
        $collections = $shopify->getCollections();
        $this->save_collections($collections, $shopify);
        $collectionsEntity = $em->getRepository(Collection::class)->findAll();

        return $this->render('base.html.twig', [
            'collections' => $collectionsEntity,
        ]);
    }

    public function save_collections($collections, ShopifyApi $shopify)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($collections as $collection) {
            $collectionEntity = $em->getRepository(Collection::class)->findOneBy(['api_id' => $collection->getId()]);
            if (!$collectionEntity) {
                $collectionEntity = new Collection();
                $collectionEntity->setTitle($collection->getTitle());
                $collectionEntity->setApiId($collection->getId());
                $collectionEntity->setDescription($collection->getBodyHtml());
                $collectionEntity->setDateCreatedAt($collection->getPublishedAt());
                $em->persist($collectionEntity);
                $em->flush();
            }
            $products = $shopify->getProducts([
                'collection_id' => $collection->getId(),
            ]);
            if (count($products) > 0) {
                $this->save_products($products, $collectionEntity);
            }
        }
    }


    public function save_products($products, Collection $collectionEntity)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($products as $product) {
            $productEntity = $em->getRepository(Product::class)->findOneBy(['api_id' => $product->getId()]);
            if (!$productEntity) {
                $productEntity = new Product();
                $productEntity->setTitle($product->getTitle());
                $productEntity->setApiId($product->getId());
                $productEntity->setDescription($product->getBodyHtml());
                $productEntity->setProductType($product->getProductType());
                $productEntity->setVendor($product->getVendor());
                $productEntity->setDateCreatedAt($product->getCreatedAt());
                $productEntity->addCollection($collectionEntity);
                $collectionEntity->addProduct($productEntity);
                $em->persist($productEntity);
                $em->flush();
            } else {
                $productEntity->addCollection($collectionEntity);
                $collectionEntity->addProduct($productEntity);
                $em->persist($productEntity);
                $em->flush();
            }
        }
    }
}
