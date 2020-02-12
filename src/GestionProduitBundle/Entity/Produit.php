<?php

namespace GestionProduitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="GestionProduitBundle\Repository\ProduitRepository")
 */
class Produit
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
     * @ORM\Column(name="image_prod", type="string", length=255)
     */
    private $imageProd;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_prod", type="string", length=255)
     */
    private $nomProd;

    /**
     * @var float
     *
     * @ORM\Column(name="prix_prod", type="float")
     */
    private $prixProd;

    /**
     * @var string
     *
     * @ORM\Column(name="description_prod", type="text")
     */
    private $descriptionProd;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite_prod", type="integer")
     */
    private $quantiteProd;
    /**
     * @ORM\ManyToOne(targetEntity="CategorieProduit")
     * @ORM\JoinColumn(name="cat_id",referencedColumnName="id")
     */
    private $categorie;

    /**
     * @return mixed
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param mixed $categorie
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }

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
     * Set imageProd
     *
     * @param string $imageProd
     *
     * @return Produit
     */
    public function setImageProd($imageProd)
    {
        $this->imageProd = $imageProd;

        return $this;
    }

    /**
     * Get imageProd
     *
     * @return string
     */
    public function getImageProd()
    {
        return $this->imageProd;
    }

    /**
     * Set nomProd
     *
     * @param string $nomProd
     *
     * @return Produit
     */
    public function setNomProd($nomProd)
    {
        $this->nomProd = $nomProd;

        return $this;
    }

    /**
     * Get nomProd
     *
     * @return string
     */
    public function getNomProd()
    {
        return $this->nomProd;
    }

    /**
     * Set prixProd
     *
     * @param float $prixProd
     *
     * @return Produit
     */
    public function setPrixProd($prixProd)
    {
        $this->prixProd = $prixProd;

        return $this;
    }

    /**
     * Get prixProd
     *
     * @return float
     */
    public function getPrixProd()
    {
        return $this->prixProd;
    }

    /**
     * Set descriptionProd
     *
     * @param string $descriptionProd
     *
     * @return Produit
     */
    public function setDescriptionProd($descriptionProd)
    {
        $this->descriptionProd = $descriptionProd;

        return $this;
    }

    /**
     * Get descriptionProd
     *
     * @return string
     */
    public function getDescriptionProd()
    {
        return $this->descriptionProd;
    }

    /**
     * Set quantiteProd
     *
     * @param integer $quantiteProd
     *
     * @return Produit
     */
    public function setQuantiteProd($quantiteProd)
    {
        $this->quantiteProd = $quantiteProd;

        return $this;
    }

    /**
     * Get quantiteProd
     *
     * @return int
     */
    public function getQuantiteProd()
    {
        return $this->quantiteProd;
    }
}

