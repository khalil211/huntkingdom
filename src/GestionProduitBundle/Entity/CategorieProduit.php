<?php

namespace GestionProduitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CategorieProduit
 *
 * @ORM\Table(name="categorie_produit")
 * @ORM\Entity(repositoryClass="GestionProduitBundle\Repository\CategorieProduitRepository")
 */
class CategorieProduit
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_cat", type="string", length=255)
     */
    private $nomCat;

    /**
     * @var string
     *
     * @ORM\Column(name="description_cat", type="text")
     */
    private $descriptionCat;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nomCat
     *
     * @param string $nomCat
     *
     * @return CategorieProduit
     */
    public function setNomCat($nomCat)
    {
        $this->nomCat = $nomCat;

        return $this;
    }

    /**
     * Get nomCat
     *
     * @return string
     */
    public function getNomCat()
    {
        return $this->nomCat;
    }

    /**
     * Set descriptionCat
     *
     * @param string $descriptionCat
     *
     * @return CategorieProduit
     */
    public function setDescriptionCat($descriptionCat)
    {
        $this->descriptionCat = $descriptionCat;

        return $this;
    }

    /**
     * Get descriptionCat
     *
     * @return string
     */
    public function getDescriptionCat()
    {
        return $this->descriptionCat;
    }
}

