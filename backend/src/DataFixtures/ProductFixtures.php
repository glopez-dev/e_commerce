<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture implements OrderedFixtureInterface
{

    public function getOrder(): int
    {
        return 2;
    }

    public function load(ObjectManager $manager): void
    {
        $images = [
            "NVIDIA RTX 4090" => "https://assets.nvidia.partners/images/png/nvidia-geforce-rtx-4090.png",
            "Corsair Vengeance DDR5 32Go" => "https://www.corsair.com/medias/sys_master/images/images/h93/h2a/64018927616030/CMK32GX5M2B5600C36/Gallery/CMK32GX5M2B5600C36_01/-CMK32GX5M2B5600C36-Gallery-CMK32GX5M2B5600C36-01.png_515Wx515H",
            "Samsung 990 Pro 2To SSD" => "https://image-us.samsung.com/SamsungUS/home/computing/memory-storage/internal-ssds/01132023/MZ-V9P2T0B_001_Front_Black.jpg",
            "AMD Ryzen 9 7950X" => "https://www.amd.com/content/dam/amd/en/images/products/processors/ryzen/2505503-ryzen9-702x702.png",
        ];

        $users = $manager->getRepository(User::class)->findAll();


        $descriptions = [
            "NVIDIA RTX 4090" => "Carte graphique haut de gamme avec 24Go GDDR6X, architecture Ada Lovelace",
            "Corsair Vengeance DDR5 32Go" => "Kit memoire DDR5 haute performance 5600MHz, dissipateur aluminium",
            "Samsung 990 Pro 2To SSD" => "SSD NVMe M.2 PCIe 4.0, lecture 7450 Mo/s, ecriture 6900 Mo/s",
            "AMD Ryzen 9 7950X" => "Processeur 16 coeurs 32 threads, 5.7GHz boost, socket AM5",
        ];

        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $rand = array_rand($images);

            $user = array_rand($users);
            $product->setSeller($users[$user]);

            $product->setName($rand);
            $product->setPhoto($images[$rand]);
            $product->setDescription($descriptions[$rand]);
            $product->setPrice(random_int(50, 2000));
            $manager->persist($product);
        }
        $manager->flush();
    }
}
