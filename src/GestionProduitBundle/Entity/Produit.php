<?php

namespace GestionProduitBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
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
     *
     * @Assert\NotBlank(message="Le champ nom est obligatoire")
     * @Assert\Length(
     *     min = 5,
     *     max = 50,
     *     minMessage = "Le nom doit contenir aux minimum 5 carracteres",
     *     maxMessage = "Le nom doit contenir au maximum 50 carracteres"
     * )
     */
    private $nomProd;

    /**
     * @var float
     *
     * @ORM\Column(name="prix_prod", type="float")
     * @Assert\NotBlank(message="Le champ prix est obligatoire")
     * @Assert\GreaterThan(
     *     value=0,
     *     message="Le prix doit être supérieur à 0"
     * )
     */
    private $prixProd;

    /**
     * @var string
     *
     * @ORM\Column(name="description_prod", type="text")
     * @Assert\NotBlank(message="Le champ description est obligatoire")
     */
    private $descriptionProd;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite_prod", type="integer")
     * @Assert\NotBlank(message="Le champ quantite est obligatoire")
     * @Assert\GreaterThan(
     *     value=0,
     *     message="La quantite doit être supérieure à 0"
     * )
     */
    private $quantiteProd;
    /**
     * @ORM\ManyToOne(targetEntity="CategorieProduit")
     * @ORM\JoinColumn(name="cat_id",referencedColumnName="id")
     */
    private $categorie;

    /**
     * @Assert\File(maxSize="6000000")
     */
    private $file;
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
    public function getAbsolutePath()
    {
        return null === $this->imageProd
            ? null
            : $this->getUploadRootDir().'/'.$this->imageProd;
    }
    public function getWebPath()
    {
        return null===$this->imageProd ? null : $this->getUploadDir().'/'.$this->imageProd;
    }
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }
    protected function getUploadDir()
    {
        return 'images';
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    public function UploadProfilePicture()
    {
        $this->file->move($this->getUploadRootDir(),$this->file->getClientOriginalName());
        $this->imageProd=$this->file->getClientOriginalName();
        $this->file=null;
    }
}

