<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    #[Route('admin/image/product/{id}', name: 'app_admin_image_product')]
    public function index(Product $product): Response
    {
        $image = new Image();
        $formImage = $this->createForm(ImageType::class, $image);
        return $this->render('image/index.html.twig', [
            'image'=>$image,
            'product' => $product,
            'formImage' => $formImage
        ]);
    }
    #[Route('/admin/image/add/product/{id}', name: 'app_admin_image_product_add')]
    public function addImage(Product $product, Request $request, EntityManagerInterface $manager)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $image->setProduct($product);
        }
        $manager->persist($image);
        $manager->flush();
        return $this->redirectToRoute('app_admin_image_product', ['id'=>$product->getId()]);
    }
    #[Route('/admin/image/remove/image/{id}', name: 'app_admin_image_product_delete')]
    public function removeFromProduct(Image $image, EntityManagerInterface $manager)
    {
        if ($image){

            $manager->remove($image);
            $manager->flush();
        }
        return $this->redirectToRoute('app_admin_products');
    }
}
