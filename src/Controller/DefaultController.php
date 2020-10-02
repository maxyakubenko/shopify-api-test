<?php

namespace App\Controller;

use App\Entity\Collection;
use App\Entity\Product;
use App\Service\ShopifyApi;
use Slince\Shopify\Exception\ClientException;
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
        // Пробуем подключиться по апи, выводим ошибку при неудаче
        try {
            //Получаем коллекции по апи
            $collections = $shopify->getCollections();
        } catch (ClientException $e) {
            echo 'Shopify API Error: ',  $e->getMessage(), "\n";
            die();
        }

        $this->save_collections($collections, $shopify);
        $collectionsEntity = $em->getRepository(Collection::class)->findAll();


        return $this->render('base.html.twig', [
            'collections' => $collectionsEntity,
        ]);
    }

    /**
     * @Route("/products/{id}", name="products")
     */
    public function products(Collection $collection)
    {
        $products = $collection->getProducts();

        return $this->render('products.html.twig', [
            'products' => $products,
        ]);
    }

    // Сохранение коллекций
    public function save_collections($collections, ShopifyApi $shopify)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($collections as $collection) {
            $collectionEntity = $em->getRepository(Collection::class)->findOneBy(['api_id' => $collection->getId()]);
            if (!$collectionEntity) {
                $collectionEntity = new Collection();
            }
                $collectionEntity->setTitle($collection->getTitle());
                $collectionEntity->setApiId($collection->getId());
                $collectionEntity->setDateCreatedAt($collection->getPublishedAt());
                $em->persist($collectionEntity);
                $em->flush();

            // Пробуем подключиться по апи, выводим ошибку при неудаче
            try {
                //Получаем продукты данной коллекции
                $products = $shopify->getProducts([
                    'collection_id' => $collection->getId(),
                ]);
            } catch (ClientException $e) {
                echo 'Shopify API Error: ',  $e->getMessage(), "\n";
                die();
            }

            //Если в коллекции есть продукты - сохраняем в бд
            if (count($products) > 0) {
                $this->save_products($products, $collectionEntity);
            }
        }
    }

    // Сохранение продуктов
    public function save_products($products, Collection $collectionEntity)
    {
        $em = $this->getDoctrine()->getManager();
        foreach ($products as $product) {
            $productEntity = $em->getRepository(Product::class)->findOneBy(['api_id' => $product->getId()]);
            if (!$productEntity) {
                $productEntity = new Product();
            }
                $productEntity->setTitle($product->getTitle());
                $productEntity->setApiId($product->getId());
                $productEntity->setDescription($product->getBodyHtml());
                $productEntity->setProductType($product->getProductType());
                $productEntity->setVendor($product->getVendor());
                $productEntity->setDateCreatedAt($product->getCreatedAt());
                $productEntity->addCollection($collectionEntity);
                $collectionEntity->addProduct($productEntity);
                if ($product->getImage()) {
                    $productEntity->setImage($product->getImage()->getSrc());
                }
                $productEntity->addCollection($collectionEntity);
                $collectionEntity->addProduct($productEntity);
                $em->persist($productEntity);
                $em->flush();
            }

    }
}
