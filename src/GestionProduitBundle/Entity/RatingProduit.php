<?php

namespace GestionProduitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RatingProduit
 *
 * @ORM\Table(name="rating_produit")
 * @ORM\Entity(repositoryClass="GestionProduitBundle\Repository\RatingProduitRepository")
 */
class RatingProduit
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
     * @var int
     *
     * @ORM\Column(name="note", type="integer")
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Produit")
     */
    private $produit;

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
     * Set note
     *
     * @param integer $note
     *
     * @return RatingProduit
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return int
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set user
     *
     * @param \UserBundle\Entity\User $user
     *
     * @return RatingProduit
     */
    public function setUser(\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set produit
     *
     * @param \GestionProduitBundle\Entity\Produit $produit
     *
     * @return RatingProduit
     */
    public function setProduit(\GestionProduitBundle\Entity\Produit $produit = null)
    {
        $this->produit = $produit;

        return $this;
    }

    /**
     * Get produit
     *
     * @return \GestionProduitBundle\Entity\Produit
     */
    public function getProduit()
    {
        return $this->produit;
    }
}
