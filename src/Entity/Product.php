<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    use BaseEntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $name;

    /**
     * @ORM\Column(type = "integer")
     */
    private $price;

    /**
     * @ORM\Column(type = "string", length = 255)
     */
    private $picture;

    /**
     * @ORM\Column(name = "create_at", type = "datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(name = "on_shelf",type = "date", nullable = true)
     */
    private $onShelf;

    /**
     * @ORM\Column(name = "off_shelf", type = "date", nullable = true)
     */
    private $offShelf;
}
