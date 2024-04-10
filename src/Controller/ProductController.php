<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\QrCodeService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'app_admin_products')]
    public function list(ProductRepository $repository)
    {
        $products = $repository->findAll();
        return $this->render('product/index.html.twig', [
            "products"=>$products
        ]);
    }

    #[Route('/admin/product/create', name: 'app_admin_product_create', priority: 2)]
    #[Route('/admin/product/edit/{id}', name: 'app_admin_product_edit', priority: 2)]
    public function create(Request $request, EntityManagerInterface $manager, Product $product=null, QrCodeService $service)
    {
        $edit =false;
        if ($product){
            $edit = true;
        }
        if (!$edit){
            $product = new Product();
        }
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);
        if ($productForm->isSubmitted() && $productForm->isValid()){
            $qrCode = $service->generateQrCode($product->getName());
            $product->setQrCode($qrCode);
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('app_admin_products');
        }
        return $this->render('product/create.html.twig', [
            'productForm'=>$productForm,
            'edit'=>$edit
        ]);
    }
    #[Route('/admin/product/delete/{id}', name: 'app_admin_product_delete')]
    public function delete(Product $product, EntityManagerInterface $manager)
    {
        $manager->remove($product);
        $manager->flush();
        return $this->redirectToRoute('app_admin_products');
    }

    #[Route('/api/product/{name}', methods: ['GET'])]
    public function displayOneByQrCode(ProductRepository $productRepository, Product $product)
    {
        $linkedProduct = $productRepository->findOneBy(['name'=>$product->getName()]);
        return $this->json($linkedProduct, 200, [], ['groups'=>'display']);
    }

    #[Route('/admin/product/{name}', name: 'app_admin_product_qrcode')]
    public function displayQrCode(ProductRepository $repository, Product $product, Pdf $pdf)
    {
        $linkedProduct = $repository->findOneBy(['name'=>$product->getName()]);
        $qrcode = $this->renderView('product/qrcode.html.twig', ['product'=>$linkedProduct]);
        return new PdfResponse(
            $pdf->getOutputFromHtml($qrcode),
            $linkedProduct->getName().'.pdf'
        );
    }
}
