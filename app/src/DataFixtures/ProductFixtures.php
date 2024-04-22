<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $images = [
            "T-Shirt" => "https://images.squarespace-cdn.com/content/v1/652fe361de902847ef91c566/1708699092638-XEPQNM0TE4GX98E586TD/FRONT+PCM+BLANC+ET+BLEU.png",
            "Jeans" =>"https://img.abercrombie.com/is/image/anf/KIC_155-3577-0040-278_prod1.jpg?policy=product-extra-large",
            "Sweatshirt" =>"https://www.beige-habilleur.com/6980/camber-sweatshirt-col-rond-rouge.jpg",
            "Shoes" =>"https://images.timberland.com/is/image/TimberlandEU/12909713-alt3?wid=720&hei=720&fit=constrain,1&qlt=85,1&op_usm=1,1,6,0"
        ];

        for ($i = 0; $i < 20; $i++) {
           $product = new Product();
           $rand = array_rand($images);
           $product->setName($rand);
           $product->setPhoto($images[$rand]);
           $product->setDescription($rand);
           $product->setPrice(random_int(50, 200));
           $manager->persist($product);
        }
        $manager->flush();
    }
}
